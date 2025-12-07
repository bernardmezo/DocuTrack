<?php
/**
 * URL Helper Functions - DocuTrack
 * =================================
 * File ini berisi fungsi-fungsi helper untuk URL.
 * 
 * @package DocuTrack
 * @category Helper
 * @author DocuTrack Team
 * @since 2.0.0
 */

if (!function_exists('baseUrl')) {
    /**
     * Mengembalikan base URL aplikasi.
     * Menggunakan nilai dari $config['app']['base_url'] yang diinisialisasi di bootstrap.php.
     *
     * @param string $path Path opsional yang akan digabungkan dengan base URL.
     * @return string URL lengkap.
     * 
     * @example
     * ```php
     * echo baseUrl();          // Output: http://localhost/docutrack/public
     * echo baseUrl('assets/css/style.css'); // Output: http://localhost/docutrack/public/assets/css/style.css
     * ```
     */
    function baseUrl(string $path = ''): string
    {
        global $config; // Akses variabel $config global

        $base = $config['app']['base_url'] ?? '/';
        
        // Hapus trailing slash dari base URL jika ada
        if (substr($base, -1) === '/') {
            $base = rtrim($base, '/');
        }

        // Tambahkan leading slash ke path jika tidak ada
        if ($path !== '' && substr($path, 0, 1) !== '/') {
            $path = '/' . $path;
        }
        
        return $base . $path;
    }
}

if (!function_exists('isActive')) {
    /**
     * Menentukan apakah link navigasi aktif.
     * Membandingkan URL saat ini dengan target link,
     * dengan memperhitungkan base URL.
     *
     * @param string $currentUrl URL saat ini (biasanya dari $_SERVER['REQUEST_URI']).
     * @param string $targetPath Path relatif target link (contoh: 'admin/dashboard').
     * @param string $activeClass Kelas CSS untuk status aktif.
     * @param string $inactiveClass Kelas CSS untuk status tidak aktif.
     * @return string Kelas CSS yang sesuai.
     */
    function isActive(string $currentUrl, string $targetPath, string $activeClass = '', string $inactiveClass = ''): string
    {
        // Pastikan $currentUrl dimulai dengan base URL untuk perbandingan yang akurat
        $base = baseUrl();
        
        // Hapus protokol dan host dari $currentUrl dan $base untuk perbandingan path murni
        $parsedCurrent = parse_url($currentUrl, PHP_URL_PATH);
        $parsedBase = parse_url($base, PHP_URL_PATH);

        // Hapus trailing slash dari base path
        $trimmedBasePath = rtrim($parsedBase, '/');
        
        // Hapus base path dari current URL untuk mendapatkan path relatif aplikasi
        if (strpos($parsedCurrent, $trimmedBasePath) === 0) {
            $currentRelativePath = substr($parsedCurrent, strlen($trimmedBasePath));
        } else {
            $currentRelativePath = $parsedCurrent; // Fallback jika base path tidak ditemukan
        }
        
        // Pastikan target path juga dimulai dengan slash untuk konsistensi
        if (substr($targetPath, 0, 1) !== '/') {
            $targetPath = '/' . $targetPath;
        }

        // Jika current URL sama persis dengan target path (setelah normalisasi)
        // atau current URL dimulai dengan target path diikuti oleh slash (untuk sub-path)
        if ($currentRelativePath === $targetPath || strpos($currentRelativePath, $targetPath . '/') === 0) {
            return $activeClass ?: 'bg-white text-[#114177] font-extrabold shadow-lg shadow-white/50';
        }

        return $inactiveClass ?: 'text-gray-200 hover:bg-white/10 hover:text-white transition-colors font-medium';
    }
}
