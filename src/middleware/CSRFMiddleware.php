<?php

namespace App\Middleware;

/**
 * CSRFMiddleware - Cross-Site Request Forgery Protection
 * 
 * Middleware untuk melindungi aplikasi dari CSRF attacks dengan menggunakan
 * token-based validation pada setiap POST/PUT/DELETE request.
 * 
 * @category Security
 * @package  DocuTrack\Middleware
 * @version  2.0.0
 * @author   DocuTrack Security Team
 * @license  MIT
 * 
 * USAGE:
 * ------
 * 1. Add to routes.php:
 *    'middleware' => ['AuthMiddleware', 'CSRFMiddleware']
 * 
 * 2. In forms (view layer):
 *    <?php echo csrf_field(); ?>
 * 
 * 3. In AJAX requests:
 *    headers: { 'X-CSRF-TOKEN': '<?php echo csrf_token(); ?>' }
 * 
 * SECURITY FEATURES:
 * ------------------
 * - Token regeneration setiap request untuk mencegah fixation
 * - Double submit cookie pattern (optional)
 * - Token expiry (1 hour default)
 * - Origin header validation
 * - Referer header validation (sebagai fallback)
 * 
 * EXCEPTIONS:
 * -----------
 * - GET requests tidak divalidasi (safe method)
 * - HEAD, OPTIONS requests di-bypass
 * - Routes yang explicitly exempt via config
 */

class CSRFMiddleware
{
    /**
     * Token key name di session
     */
    const TOKEN_KEY = 'csrf_token';
    
    /**
     * Token timestamp key
     */
    const TOKEN_TIME_KEY = 'csrf_token_time';
    
    /**
     * Token expiry time (dalam detik) - default 1 jam
     */
    const TOKEN_EXPIRY = 3600;
    
    /**
     * Exempt routes (tidak perlu CSRF check)
     * Untuk API endpoints yang menggunakan Bearer token
     */
    private static $exemptRoutes = [
        '/api/*',  // API routes usually use different auth
    ];

    /**
     * Main validation method - dipanggil dari Router
     * 
     * @throws \Exception Jika CSRF validation gagal
     * @return void
     */
    public static function check()
    {
        // Skip validation untuk safe HTTP methods
        $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        if (in_array($method, ['GET', 'HEAD', 'OPTIONS'])) {
            return;
        }

        // Skip validation untuk exempt routes
        $requestUri = $_SERVER['REQUEST_URI'] ?? '';
        if (self::isExemptRoute($requestUri)) {
            return;
        }

        // Ambil token dari request
        $submittedToken = self::getTokenFromRequest();
        
        // Validasi token
        if (!self::validateToken($submittedToken)) {
            self::handleCSRFViolation();
        }

        // Token valid - regenerate untuk next request (rotating tokens)
        self::regenerateToken();
    }

    /**
     * Generate CSRF token baru dan simpan di session
     * 
     * @return string Generated token
     */
    public static function generateToken(): string
    {
        // Generate cryptographically secure random token
        $token = bin2hex(random_bytes(32)); // 64 characters hex
        
        // Store di session
        $_SESSION[self::TOKEN_KEY] = $token;
        $_SESSION[self::TOKEN_TIME_KEY] = time();
        
        return $token;
    }

    /**
     * Get current CSRF token (generate jika belum ada)
     * 
     * @return string Current token
     */
    public static function getToken(): string
    {
        // Jika token belum ada atau sudah expired, generate baru
        if (!isset($_SESSION[self::TOKEN_KEY]) || self::isTokenExpired()) {
            return self::generateToken();
        }
        
        return $_SESSION[self::TOKEN_KEY];
    }

    /**
     * Validasi token yang di-submit dengan token di session
     * 
     * @param string|null $submittedToken Token dari request
     * @return bool True jika valid, false jika tidak
     */
    private static function validateToken(?string $submittedToken): bool
    {
        // Token harus ada di request
        if (empty($submittedToken)) {
            error_log('CSRF: No token submitted');
            return false;
        }

        // Token harus ada di session
        if (!isset($_SESSION[self::TOKEN_KEY])) {
            error_log('CSRF: No token in session');
            return false;
        }

        // Check expiry
        if (self::isTokenExpired()) {
            error_log('CSRF: Token expired');
            return false;
        }

        // Constant-time comparison untuk mencegah timing attacks
        $sessionToken = $_SESSION[self::TOKEN_KEY];
        if (!hash_equals($sessionToken, $submittedToken)) {
            error_log('CSRF: Token mismatch');
            return false;
        }

        // Additional: Validate Origin/Referer headers
        if (!self::validateOrigin()) {
            error_log('CSRF: Origin validation failed');
            return false;
        }

        return true;
    }

    /**
     * Ambil token dari berbagai sumber request
     * 
     * Priority:
     * 1. POST body (_token field)
     * 2. HTTP Header (X-CSRF-TOKEN)
     * 3. HTTP Header (X-XSRF-TOKEN) - untuk compatibility
     * 
     * @return string|null Token jika ditemukan
     */
    private static function getTokenFromRequest(): ?string
    {
        // 1. POST body
        if (isset($_POST['_token'])) {
            return $_POST['_token'];
        }

        // 2. Custom header (untuk AJAX)
        $headers = getallheaders();
        if (isset($headers['X-CSRF-TOKEN'])) {
            return $headers['X-CSRF-TOKEN'];
        }
        
        if (isset($headers['X-XSRF-TOKEN'])) {
            return $headers['X-XSRF-TOKEN'];
        }

        return null;
    }

    /**
     * Check apakah token sudah expired
     * 
     * @return bool True jika expired
     */
    private static function isTokenExpired(): bool
    {
        if (!isset($_SESSION[self::TOKEN_TIME_KEY])) {
            return true;
        }

        $tokenAge = time() - $_SESSION[self::TOKEN_TIME_KEY];
        return $tokenAge > self::TOKEN_EXPIRY;
    }

    /**
     * Regenerate token untuk rotating token pattern
     * Dipanggil setelah successful validation
     * 
     * @return void
     */
    private static function regenerateToken(): void
    {
        self::generateToken();
    }

    /**
     * Validasi Origin atau Referer header
     * Untuk mencegah CSRF dari domain lain
     * 
     * @return bool True jika valid
     */
    private static function validateOrigin(): bool
    {
        $allowedOrigins = self::getAllowedOrigins();

        // Check Origin header (lebih reliable)
        if (isset($_SERVER['HTTP_ORIGIN'])) {
            return in_array($_SERVER['HTTP_ORIGIN'], $allowedOrigins);
        }

        // Fallback: Check Referer header
        if (isset($_SERVER['HTTP_REFERER'])) {
            $refererHost = parse_url($_SERVER['HTTP_REFERER'], PHP_URL_HOST);
            $allowedHosts = array_map(function($origin) {
                return parse_url($origin, PHP_URL_HOST);
            }, $allowedOrigins);
            
            return in_array($refererHost, $allowedHosts);
        }

        // Jika tidak ada Origin/Referer, allow (some browsers/tools tidak send)
        // Tapi log untuk monitoring
        error_log('CSRF: No Origin/Referer header present');
        return true;
    }

    /**
     * Get allowed origins dari environment atau config
     * 
     * @return array List of allowed origins
     */
    private static function getAllowedOrigins(): array
    {
        $baseUrl = getenv('APP_URL') ?: 'http://localhost';
        
        return [
            $baseUrl,
            // Add production domains jika ada
            // 'https://docutrack.pnj.ac.id',
        ];
    }

    /**
     * Check apakah route exempt dari CSRF protection
     * 
     * @param string $uri Request URI
     * @return bool True jika exempt
     */
    private static function isExemptRoute(string $uri): bool
    {
        foreach (self::$exemptRoutes as $pattern) {
            // Simple wildcard matching
            $pattern = str_replace('*', '.*', $pattern);
            if (preg_match('#^' . $pattern . '$#', $uri)) {
                return true;
            }
        }
        
        return false;
    }

    /**
     * Handle CSRF violation - log dan terminate request
     * 
     * @throws \Exception
     * @return void (never returns)
     */
    private static function handleCSRFViolation(): void
    {
        // Log untuk security audit
        $logData = [
            'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'unknown',
            'request_uri' => $_SERVER['REQUEST_URI'] ?? 'unknown',
            'method' => $_SERVER['REQUEST_METHOD'] ?? 'unknown',
            'referer' => $_SERVER['HTTP_REFERER'] ?? 'none',
            'user_id' => $_SESSION['user_id'] ?? 'anonymous',
        ];
        
        error_log('CSRF VIOLATION: ' . json_encode($logData));

        // Audit log ke database (jika function tersedia)
        if (function_exists('logSecurityEvent')) {
            logSecurityEvent(
                $_SESSION['user_id'] ?? 0,
                'CSRF_VIOLATION',
                'CSRF token validation failed',
                json_encode($logData)
            );
        }

        // Response dengan 419 status (CSRF Token Mismatch)
        http_response_code(419);
        
        // Jika AJAX request, return JSON
        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
            strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false,
                'error' => 'CSRF token validation failed. Please refresh the page and try again.',
                'code' => 'CSRF_TOKEN_MISMATCH'
            ]);
            exit;
        }

        // Tampilkan error page untuk regular request
        ?>
        <!DOCTYPE html>
        <html>
        <head>
            <title>Security Error - CSRF Token Mismatch</title>
            <style>
                body { font-family: Arial, sans-serif; margin: 50px; background: #f5f5f5; }
                .error-container { background: white; padding: 30px; border-radius: 8px; max-width: 600px; margin: 0 auto; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
                h1 { color: #d32f2f; }
                .code { background: #f5f5f5; padding: 10px; border-radius: 4px; font-family: monospace; }
                .btn { display: inline-block; padding: 10px 20px; background: #1976d2; color: white; text-decoration: none; border-radius: 4px; margin-top: 20px; }
            </style>
        </head>
        <body>
            <div class="error-container">
                <h1>🔒 Security Error</h1>
                <p><strong>CSRF Token Validation Failed</strong></p>
                <p>Your request could not be completed due to a security check failure. This usually happens when:</p>
                <ul>
                    <li>Your session has expired</li>
                    <li>You submitted a form that was open for too long</li>
                    <li>You're attempting an unauthorized action</li>
                </ul>
                <div class="code">Error Code: 419 - CSRF_TOKEN_MISMATCH</div>
                <a href="javascript:history.back()" class="btn">← Go Back</a>
                <a href="/docutrack/public/" class="btn">🏠 Home</a>
            </div>
        </body>
        </html>
        <?php
        exit;
    }
}
