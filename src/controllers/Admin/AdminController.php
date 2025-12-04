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

        $fileInput = $_FILES['file_surat_pengantar'] ?? $_FILES['surat_pengantar'] ?? null;

        // URL sukses (kembali ke list)
        $successRedirectUrl = '/docutrack/public/admin/pengajuan-kegiatan';

        $transactionStarted = false;
        $uploadedFilePath = null;
        $previousFilePath = null;

        try {
            $this->connection->begin_transaction();
            $transactionStarted = true;

            $lockStmt = $this->connection->prepare(
                'SELECT suratPengantar FROM tbl_kegiatan WHERE kegiatanId = ? FOR UPDATE'
            );

            if ($lockStmt === false) {
                throw new RuntimeException('Gagal mengambil data kegiatan.');
            }

            $lockStmt->bind_param('i', $kegiatanId);
            $lockStmt->execute();
            $result = $lockStmt->get_result();

            if ($result === false || $result->num_rows === 0) {
                throw new RuntimeException('Data kegiatan tidak ditemukan.');
            }

            $row = $result->fetch_assoc();
            $existingSurat = $row['suratPengantar'] ?? null;
            $lockStmt->close();

            $uploadPlan = $this->prepareSuratUpload($kegiatanId, $fileInput, $existingSurat);

            // Update Data & State Transition:
            // Posisi -> 4 (PPK)
            // Status Utama -> 1 (Menunggu / Pending)
            $updateStmt = $this->connection->prepare(
                'UPDATE tbl_kegiatan
                 SET namaPJ = ?, nip = ?, tanggalMulai = ?, tanggalSelesai = ?, suratPengantar = ?, posisiId = ?, statusUtamaId = ?, umpanBalikVerifikator = NULL
                 WHERE kegiatanId = ?'
            );

            if ($updateStmt === false) {
                throw new RuntimeException('Gagal menyiapkan pembaruan kegiatan.');
            }

            $formattedMulai = $tanggalMulai->format('Y-m-d');
            $formattedSelesai = $tanggalSelesai->format('Y-m-d');
            $posisiPpk = 4;
            $statusMenunggu = 1;

            $updateStmt->bind_param(
                'sssssiii',
                $penanggungJawab,
                $nipPj,
                $formattedMulai,
                $formattedSelesai,
                $uploadPlan['fileName'],
                $posisiPpk,
                $statusMenunggu,
                $kegiatanId
            );

            $updateStmt->execute();
            $updateStmt->close();

            if ($uploadPlan['tempPath'] !== null && $uploadPlan['destinationPath'] !== null) {
                if (!is_uploaded_file($uploadPlan['tempPath'])) {
                    throw new RuntimeException('File surat pengantar tidak valid.');
                }

                if (!move_uploaded_file($uploadPlan['tempPath'], $uploadPlan['destinationPath'])) {
                    throw new RuntimeException('Gagal menyimpan file surat pengantar.');
                }

                $uploadedFilePath = $uploadPlan['destinationPath'];
                $previousFilePath = $uploadPlan['previousFile'];
            } else {
                $uploadedFilePath = null;
                $previousFilePath = null;
            }

            // Catat History
            $historySql = 'INSERT INTO tbl_progress_history (kegiatanId, statusId, changedByUserId) VALUES (?, ?, ';
            $userId = isset($_SESSION['user_id']) && is_numeric($_SESSION['user_id'])
                ? (int) $_SESSION['user_id']
                : null;

            if ($userId === null) {
                $historySql .= 'NULL)';
                $historyStmt = $this->connection->prepare($historySql);
                if ($historyStmt === false) {
                    throw new RuntimeException('Gagal menyiapkan riwayat progres.');
                }
                $historyStmt->bind_param('ii', $kegiatanId, $statusMenunggu);
            } else {
                $historySql .= '?)';
                $historyStmt = $this->connection->prepare($historySql);
                if ($historyStmt === false) {
                    throw new RuntimeException('Gagal menyiapkan riwayat progres.');
                }
                $historyStmt->bind_param('iii', $kegiatanId, $statusMenunggu, $userId);
            }

            $historyStmt->execute();
            $historyStmt->close();

            $this->connection->commit();
            $transactionStarted = false;

            if ($previousFilePath !== null && $previousFilePath !== $uploadedFilePath && is_file($previousFilePath)) {
                @unlink($previousFilePath);
            }

            $this->redirectWithMessage($successRedirectUrl, 'success', 'Rincian kegiatan berhasil disimpan dan dikirim ke PPK.');
        } catch (Throwable $e) {
            if ($transactionStarted) {
                $this->connection->rollback();
            }

            if ($uploadedFilePath !== null && is_file($uploadedFilePath)) {
                @unlink($uploadedFilePath);
            }

            error_log('AdminController::submitRincian Error: ' . $e->getMessage());
            $this->redirectWithMessage($errorRedirectUrl, 'error', 'Terjadi kesalahan saat menyimpan rincian kegiatan.');
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

    private function prepareSuratUpload(int $kegiatanId, ?array $fileInfo, ?string $existingFilename): array
    {
        $uploadDir = DOCUTRACK_ROOT . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . 'surat';

        if (!is_dir($uploadDir) && !mkdir($uploadDir, 0755, true) && !is_dir($uploadDir)) {
            throw new RuntimeException('Direktori upload surat tidak tersedia.');
        }

        if ($fileInfo === null || ($fileInfo['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
            if ($existingFilename === null || $existingFilename === '') {
                throw new RuntimeException('Surat pengantar wajib diunggah.');
            }

            return [
                'fileName' => $existingFilename,
                'tempPath' => null,
                'destinationPath' => null,
                'previousFile' => null,
            ];
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

        $fileName = sprintf(
            'surat_pengantar_%d_%s.%s',
            $kegiatanId,
            date('YmdHis'),
            $extension
        );

        $destinationPath = $uploadDir . DIRECTORY_SEPARATOR . $fileName;

        $previousFilePath = null;
        if ($existingFilename !== null && $existingFilename !== '') {
            $previousFilePath = $uploadDir . DIRECTORY_SEPARATOR . $existingFilename;
        }

        return [
            'fileName' => $fileName,
            'tempPath' => $tempPath,
            'destinationPath' => $destinationPath,
            'previousFile' => $previousFilePath,
        ];
    }
}
