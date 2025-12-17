<?php

namespace App\Core;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

class Mailer
{
    private $mail;

    public function __construct()
    {
        $this->mail = new PHPMailer(true);
        $this->setup();
    }

    private function setup()
    {
        try {
            // Server settings
            // Mengambil konfigurasi dari environment variable atau hardcode sementara (Sebaiknya gunakan .env)
            $this->mail->isSMTP();
            $this->mail->Host       = $_ENV['SMTP_HOST'] ?? 'smtp.gmail.com';
            $this->mail->SMTPAuth   = true;
            $this->mail->Username   = $_ENV['SMTP_USER'] ?? 'docutrack.system@gmail.com'; // Ganti dengan email asli
            $this->mail->Password   = $_ENV['SMTP_PASS'] ?? 'your_app_password';         // Ganti dengan app password
            $this->mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $this->mail->Port       = $_ENV['SMTP_PORT'] ?? 587;

            // Default Sender
            $this->mail->setFrom($this->mail->Username, 'DocuTrack System');
            $this->mail->isHTML(true);
        } catch (Exception $e) {
            error_log("Mailer Setup Error: {$this->mail->ErrorInfo}");
        }
    }

    /**
     * Send Email
     *
     * @param string $to Email penerima
     * @param string $subject Subjek email
     * @param string $viewPath Path ke file view (relatif terhadap src/views/emails/)
     * @param array $data Data yang akan dikirim ke view
     * @return bool
     */
    public function send($to, $subject, $viewPath, $data = [])
    {
        try {
            // Render HTML Body
            $body = $this->renderView($viewPath, $data);

            // Recipients
            $this->mail->addAddress($to);

            // Content
            $this->mail->Subject = $subject;
            $this->mail->Body    = $body;
            $this->mail->AltBody = strip_tags($body); // Plain text version

            $this->mail->send();
            return true;
        } catch (Exception $e) {
            error_log("Message could not be sent. Mailer Error: {$this->mail->ErrorInfo}");
            return false;
        }
    }

    /**
     * Render PHP View to String
     */
    private function renderView($viewPath, $data)
    {
        extract($data);
        ob_start();

        // Define absolute path to views
        $fullPath = __DIR__ . '/../../src/views/emails/' . $viewPath . '.php';

        if (file_exists($fullPath)) {
            include $fullPath;
        } else {
            // Fallback content if view not found
            echo "<h1>{$data['judul']}</h1><p>{$data['pesan']}</p>";
            error_log("Email view not found: " . $fullPath);
        }

        return ob_get_clean();
    }
}
