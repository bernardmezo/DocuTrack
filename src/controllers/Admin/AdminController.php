<?php
declare(strict_types=1);

namespace Controllers\Admin;

require_once __DIR__ . '/../../core/Database.php';
require_once __DIR__ . '/../../core/Controller.php';

use Core\Database;
use DateTimeImmutable;
use mysqli;
use RuntimeException;
use Throwable;

class AdminController extends \Controller
{
    private const SURAT_MAX_SIZE_BYTES = 5_242_880; // 5 MB

    private mysqli $connection;

    public function __construct(?mysqli $connection = null)
    {
        $this->connection = $connection ?? Database::getInstance()->getConnection();
        parent::__construct($this->connection);
    }

    public function submitRincian(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo 'Method Not Allowed';
            exit;
        }

        $kegiatanId = $this->parsePositiveInt($_POST['id_kegiatan'] ?? $_POST['kegiatan_id'] ?? null);

        if ($kegiatanId === null) {
            $this->redirectWithMessage('/docutrack/public/admin/pengajuan-kegiatan', 'error', 'ID kegiatan tidak valid.');
        }

        // URL untuk kembali ke form jika ada error
        $errorRedirectUrl = '/docutrack/public/admin/pengajuan-kegiatan/show/' . $kegiatanId . '?mode=rincian';

        $penanggungJawab = trim((string) ($_POST['penanggung_jawab'] ?? ''));
        if ($penanggungJawab === '') {
            $this->redirectWithMessage($errorRedirectUrl, 'error', 'Nama penanggung jawab wajib diisi.');
        }

        $nipPj = trim((string) ($_POST['nim_nip_pj'] ?? ''));
        if ($nipPj === '') {
            $this->redirectWithMessage($errorRedirectUrl, 'error', 'NIM / NIP penanggung jawab wajib diisi.');
        }

        $tanggalMulai = $this->parseDate((string) ($_POST['tanggal_mulai'] ?? ''));
        $tanggalSelesai = $this->parseDate((string) ($_POST['tanggal_selesai'] ?? ''));

        if ($tanggalMulai === null || $tanggalSelesai === null) {
            $this->redirectWithMessage($errorRedirectUrl, 'error', 'Tanggal pelaksanaan tidak valid.');
        }

        if ($tanggalMulai > $tanggalSelesai) {
            $this->redirectWithMessage($errorRedirectUrl, 'error', 'Tanggal selesai harus sama atau setelah tanggal mulai.');
        }

        $fileInput = $_FILES['surat_pengantar'] ?? null;

        // URL sukses (kembali ke list)
        $successRedirectUrl = '/docutrack/public/admin/pengajuan-kegiatan';

        $transactionStarted = false;
        $uploadedFilePath = null;
        $previousFilePath = null;

        try {
            $this->connection->begin_transaction();
            $transactionStarted = true;


            $uploadPlan = $this->prepareSuratUpload($kegiatanId, $fileInput);


            $umpanBalikVerifikator = null;
            // Update Data & State Transition:
            // Posisi -> 4 (PPK)
            // Status Utama -> 1 (Menunggu / Pending)
            $updateStmt = $this->connection->prepare(
                'UPDATE tbl_kegiatan
                 SET namaPJ = ?, nip = ?, tanggalMulai = ?, tanggalSelesai = ?, suratPengantar = ?, posisiId = ?, umpanBalikVerifikator = ?
                 WHERE kegiatanId = ?'
            );

            if ($updateStmt === false) {
                throw new RuntimeException('Gagal menyiapkan pembaruan kegiatan.');
            }

            $formattedMulai = $tanggalMulai->format('Y-m-d');
            $formattedSelesai = $tanggalSelesai->format('Y-m-d');
            $posisiPpk = 4;

            $updateStmt->bind_param(
                'sssssisi',
                $penanggungJawab,
                $nipPj,
                $formattedMulai,
                $formattedSelesai,
                $uploadPlan['fileName'],
                $posisiPpk,
                $umpanBalikVerifikator,
                $kegiatanId
            );

            // [PERBAIKAN UTAMA] Cek hasil eksekusi!
            if (!$updateStmt->execute()) {
                throw new RuntimeException('Gagal update database: ' . $updateStmt->error);
            }
            $updateStmt->close();
            $this->connection->commit();

            
            $this->redirectWithMessage($successRedirectUrl, 'success', 'Rincian kegiatan berhasil disimpan dan dikirim ke PPK.');
        } catch (Throwable $e) {
            if ($transactionStarted) {
                $this->connection->rollback();
            }

            if ($uploadedFilePath !== null && is_file($uploadedFilePath)) {
                @unlink($uploadedFilePath);
            }

            error_log('AdminController::submitRincian Error: ' . $e->getMessage());
            die(''. $e->getMessage());
            // $this->redirectWithMessage($errorRedirectUrl, 'error', 'Terjadi kesalahan saat menyimpan rincian kegiatan.');
        }
    }

    private function parsePositiveInt($value): ?int
    {
        if ($value === null) {
            return null;
        }

        if (is_int($value)) {
            return $value > 0 ? $value : null;
        }

        $filtered = trim((string) $value);

        if ($filtered === '' || !ctype_digit($filtered)) {
            return null;
        }

        $intValue = (int) $filtered;

        return $intValue > 0 ? $intValue : null;
    }

    private function parseDate(string $value): ?DateTimeImmutable
    {
        $trimmed = trim($value);

        if ($trimmed === '') {
            return null;
        }

        $date = DateTimeImmutable::createFromFormat('Y-m-d', $trimmed);
        if ($date === false) {
            return null;
        }

        $errors = DateTimeImmutable::getLastErrors();
        if ($errors !== false && ($errors['warning_count'] > 0 || $errors['error_count'] > 0)) {
            return null;
        }

        return $date;
    }

    private function prepareSuratUpload($kegiatanId, $fileInfo): array
    {
        $uploadDir = realpath(__DIR__ . '/../../../public/uploads/surat/');  

        if (!is_dir($uploadDir) && !mkdir($uploadDir, 0755, true) && !is_dir($uploadDir)) {
            throw new RuntimeException('Direktori upload surat tidak tersedia.');
        }

        if (($fileInfo['error'] ?? UPLOAD_ERR_OK) !== UPLOAD_ERR_OK) {
            throw new RuntimeException('Terjadi kesalahan saat mengunggah surat pengantar.');
        }

        if (($fileInfo['size'] ?? 0) > self::SURAT_MAX_SIZE_BYTES) {
            throw new RuntimeException('Ukuran file surat pengantar melebihi 5MB.');
        }

        $originalName = $fileInfo['name'] ?? '';
        $extension = strtolower((string) pathinfo($originalName, PATHINFO_EXTENSION));
        $allowedExtensions = ['pdf', 'doc', 'docx'];

        if (!in_array($extension, $allowedExtensions, true)) {
            throw new RuntimeException('Format file surat pengantar harus PDF atau DOC/DOCX.');
        }

        $tempPath = $fileInfo['tmp_name'] ?? null;
        if ($tempPath === null || $tempPath === '') {
            throw new RuntimeException('Path file surat pengantar tidak valid.');
        }

        // nama file baru untuk disimpan
        $fileName = sprintf(
            'surat_pengantar_%d_%s.%s',
            $kegiatanId,
            date('YmdHis'),
            $extension
        );

        if(move_uploaded_file($tempPath, $uploadDir . DIRECTORY_SEPARATOR . $fileName)) {
            // Jika berhasil dipindahkan ke direktori tujuan, kembalikan path tujuan
            $destinationPath = $uploadDir . DIRECTORY_SEPARATOR . $fileName;
        } else {
            throw new RuntimeException('Gagal memindahkan file surat pengantar ke direktori tujuan.');
        }

        $previousFilePath = null;

        return [
            'fileName' => $fileName,
            'filePath' => $destinationPath,
            'previousFilePath' => $previousFilePath
        ];
    }
}
