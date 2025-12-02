<?php
/**
 * Security Helper Functions - DocuTrack
 * ======================================
 * File ini berisi fungsi-fungsi keamanan untuk aplikasi DocuTrack.
 * Include file ini di bootstrap/config aplikasi atau di setiap controller yang membutuhkan.
 * 
 * Ref: DATABASE_AUDIT.md - Pilar 2: Security Hardening
 * Date: December 2, 2025
 */

/**
 * Escape output untuk mencegah XSS (Cross-Site Scripting)
 * Shorthand untuk htmlspecialchars dengan konfigurasi aman
 * 
 * @param string|null $string Input yang akan di-escape
 * @param string $encoding Character encoding (default UTF-8)
 * @return string String yang sudah aman untuk output HTML
 * 
 * @example
 * // Di view:
 * <h1><?= e($kegiatan['namaKegiatan']) ?></h1>
 * <p><?= e($user['alamat']) ?></p>
 */
function e(?string $string, string $encoding = 'UTF-8'): string
{
    if ($string === null) {
        return '';
    }
    
    return htmlspecialchars($string, ENT_QUOTES | ENT_HTML5, $encoding);
}

/**
 * Escape untuk atribut HTML
 * Lebih ketat untuk digunakan di dalam atribut tag
 * 
 * @param string|null $string Input yang akan di-escape
 * @return string String yang aman untuk atribut HTML
 * 
 * @example
 * <input value="<?= eAttr($user['nama']) ?>">
 * <a href="<?= eAttr($url) ?>">Link</a>
 */
function eAttr(?string $string): string
{
    if ($string === null) {
        return '';
    }
    
    return htmlspecialchars($string, ENT_QUOTES | ENT_HTML5, 'UTF-8');
}

/**
 * Escape untuk output di dalam JavaScript string
 * Menggunakan JSON encode untuk handling karakter spesial
 * 
 * @param string|null $string Input yang akan di-escape
 * @return string String yang aman untuk JavaScript (sudah termasuk quotes)
 * 
 * @example
 * <script>var name = <?= eJs($user['nama']) ?>;</script>
 * Note: Output sudah include quotes, jadi tidak perlu tambah quotes lagi
 */
function eJs(?string $string): string
{
    if ($string === null) {
        return '""';
    }
    
    return json_encode($string, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP);
}

/**
 * Sanitize filename untuk upload
 * Menghapus karakter berbahaya dan path traversal
 * 
 * @param string $filename Nama file original
 * @return string Nama file yang sudah disanitasi
 * 
 * @example
 * $safe_name = sanitizeFilename($_FILES['document']['name']);
 */
function sanitizeFilename(string $filename): string
{
    // Ambil hanya basename (hapus path)
    $filename = basename($filename);
    
    // Hapus karakter berbahaya, hanya izinkan alphanumeric, dot, dash, underscore
    $filename = preg_replace('/[^a-zA-Z0-9._-]/', '_', $filename);
    
    // Hapus double dots (path traversal attempt)
    $filename = str_replace('..', '', $filename);
    
    // Hapus multiple underscores
    $filename = preg_replace('/_+/', '_', $filename);
    
    // Batasi panjang (sesuai kolom suratPengantar yang diperbesar ke 255)
    return substr($filename, 0, 200);
}

/**
 * Validasi dan sanitasi URL
 * Mencegah open redirect vulnerability
 * 
 * @param string $url URL yang akan divalidasi
 * @param string $default URL default jika validasi gagal
 * @return string URL yang aman
 */
function sanitizeUrl(string $url, string $default = '/'): string
{
    // Hanya izinkan URL relatif atau URL ke domain yang sama
    $parsed = parse_url($url);
    
    // Jika ada host dan bukan dari domain sendiri, tolak
    if (isset($parsed['host'])) {
        $allowed_hosts = [$_SERVER['HTTP_HOST'] ?? 'localhost'];
        if (!in_array($parsed['host'], $allowed_hosts)) {
            return $default;
        }
    }
    
    // Sanitize path
    $safe_url = filter_var($url, FILTER_SANITIZE_URL);
    
    return $safe_url ?: $default;
}

/**
 * Validasi CSRF Token (untuk form submission)
 * 
 * @param string $token Token dari form
 * @return bool True jika valid
 */
function validateCsrfToken(string $token): bool
{
    if (!isset($_SESSION['csrf_token'])) {
        return false;
    }
    
    return hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Generate CSRF Token
 * 
 * @return string CSRF Token
 */
function generateCsrfToken(): string
{
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    
    return $_SESSION['csrf_token'];
}

/**
 * Render CSRF Token sebagai hidden input
 * 
 * @return string HTML hidden input dengan CSRF token
 * 
 * @example
 * <form method="POST">
 *     <?= csrfField() ?>
 *     <!-- form fields -->
 * </form>
 */
function csrfField(): string
{
    return '<input type="hidden" name="csrf_token" value="' . eAttr(generateCsrfToken()) . '">';
}

/**
 * Validasi apakah request berasal dari internal (bukan external redirect)
 * 
 * @return bool True jika request internal
 */
function isInternalRequest(): bool
{
    if (!isset($_SERVER['HTTP_REFERER'])) {
        return false;
    }
    
    $referer = parse_url($_SERVER['HTTP_REFERER']);
    $host = $_SERVER['HTTP_HOST'] ?? '';
    
    return isset($referer['host']) && $referer['host'] === $host;
}
