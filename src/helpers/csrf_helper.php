<?php

/**
 * CSRF Helper Functions
 *
 * Helper functions untuk CSRF protection di views dan controllers.
 * File ini di-load otomatis oleh bootstrap.php
 *
 * @package DocuTrack\Helpers
 * @version 2.0.0
 */

if (!function_exists('csrf_token')) {
    /**
     * Get current CSRF token
     *
     * Mengambil atau generate CSRF token untuk current session.
     * Token ini digunakan untuk validasi form submissions dan AJAX requests.
     *
     * @return string CSRF token (64 character hex string)
     *
     * @example
     * // Di JavaScript
     * const token = '<?php echo csrf_token(); ?>';
     *
     * // Di AJAX request
     * headers: { 'X-CSRF-TOKEN': '<?php echo csrf_token(); ?>' }
     */
    function csrf_token(): string
    {
        return \App\Middleware\CSRFMiddleware::getToken();
    }
}

if (!function_exists('csrf_field')) {
    /**
     * Generate hidden input field dengan CSRF token
     *
     * Digunakan di dalam <form> tags untuk menyisipkan CSRF token.
     * Output: <input type="hidden" name="_token" value="...token...">
     *
     * @return string HTML input element
     *
     * @example
     * <form method="POST" action="/admin/akun/update">
     *     <?php echo csrf_field(); ?>
     *     <input type="text" name="username">
     *     <button type="submit">Update</button>
     * </form>
     */
    function csrf_field(): string
    {
        $token = csrf_token();
        return '<input type="hidden" name="_token" value="' . htmlspecialchars($token, ENT_QUOTES, 'UTF-8') . '">';
    }
}

if (!function_exists('csrf_meta')) {
    /**
     * Generate meta tag untuk CSRF token (untuk global JavaScript access)
     *
     * Digunakan di <head> section untuk expose token ke JavaScript.
     * Berguna untuk SPA atau AJAX-heavy applications.
     *
     * @return string HTML meta tag
     *
     * @example
     * // Di header.php
     * <head>
     *     <?php echo csrf_meta(); ?>
     * </head>
     *
     * // Di JavaScript
     * const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
     */
    function csrf_meta(): string
    {
        $token = csrf_token();
        return '<meta name="csrf-token" content="' . htmlspecialchars($token, ENT_QUOTES, 'UTF-8') . '">';
    }
}

if (!function_exists('verify_csrf')) {
    /**
     * Manually verify CSRF token (untuk custom validation)
     *
     * Biasanya tidak diperlukan karena CSRFMiddleware sudah handle otomatis.
     * Tapi berguna untuk edge cases atau custom endpoints.
     *
     * @param string|null $token Token to verify (null = ambil dari request)
     * @return bool True jika valid, false jika tidak
     *
     * @example
     * if (!verify_csrf($_POST['_token'] ?? null)) {
     *     die('Invalid CSRF token');
     * }
     */
    function verify_csrf(?string $token = null): bool
    {
        if ($token === null) {
            // Ambil dari POST atau header
            $token = $_POST['_token'] ?? null;
            if ($token === null) {
                $headers = getallheaders();
                $token = $headers['X-CSRF-TOKEN'] ?? $headers['X-XSRF-TOKEN'] ?? null;
            }
        }

        if (empty($token) || !isset($_SESSION['csrf_token'])) {
            return false;
        }

        return hash_equals($_SESSION['csrf_token'], $token);
    }
}

if (!function_exists('regenerate_csrf_token')) {
    /**
     * Force regenerate CSRF token
     *
     * Berguna setelah sensitive operations (login, password change, dll)
     * untuk mencegah session fixation.
     *
     * @return string New token
     *
     * @example
     * // Setelah login success
     * session_regenerate_id(true);
     * regenerate_csrf_token();
     */
    function regenerate_csrf_token(): string
    {
        return \App\Middleware\CSRFMiddleware::generateToken();
    }
}
