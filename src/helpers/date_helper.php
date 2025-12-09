<?php

/**
 * Date Helper Functions
 * ====================
 * Helper untuk manipulasi dan formatting tanggal dalam Bahasa Indonesia.
 *
 * @package DocuTrack
 * @category Helper
 * @author DocuTrack Team
 * @since 1.0.0
 */

if (!function_exists('getMonthName')) {
    /**
     * Konversi nomor bulan (1-12) ke nama bulan dalam Bahasa Indonesia.
     *
     * Fungsi ini mengkonversi integer bulan (1-12) menjadi nama bulan lengkap
     * dalam Bahasa Indonesia. Jika input tidak valid, akan mengembalikan
     * nilai input asli.
     *
     * @param int|string $monthNumber Nomor bulan (1-12)
     * @return string Nama bulan dalam Bahasa Indonesia (contoh: "Januari", "Februari")
     *
     * @example
     * ```php
     * echo getMonthName(1);  // Output: "Januari"
     * echo getMonthName(12); // Output: "Desember"
     * echo getMonthName(0);  // Output: "0" (invalid input)
     * ```
     */
    function getMonthName($monthNumber)
    {
        // Mapping bulan ke Bahasa Indonesia
        $months = [
            1  => 'Januari',
            2  => 'Februari',
            3  => 'Maret',
            4  => 'April',
            5  => 'Mei',
            6  => 'Juni',
            7  => 'Juli',
            8  => 'Agustus',
            9  => 'September',
            10 => 'Oktober',
            11 => 'November',
            12 => 'Desember'
        ];

        // Cast ke integer untuk keamanan
        $monthInt = (int) $monthNumber;

        // Return nama bulan jika valid, atau nilai asli jika tidak
        return $months[$monthInt] ?? (string) $monthNumber;
    }
}

if (!function_exists('getMonthShortName')) {
    /**
     * Konversi nomor bulan (1-12) ke nama bulan singkat (3 huruf).
     *
     * @param int|string $monthNumber Nomor bulan (1-12)
     * @return string Nama bulan singkat (contoh: "Jan", "Feb")
     *
     * @example
     * ```php
     * echo getMonthShortName(1);  // Output: "Jan"
     * echo getMonthShortName(12); // Output: "Des"
     * ```
     */
    function getMonthShortName($monthNumber)
    {
        $shortMonths = [
            1  => 'Jan',
            2  => 'Feb',
            3  => 'Mar',
            4  => 'Apr',
            5  => 'Mei',
            6  => 'Jun',
            7  => 'Jul',
            8  => 'Agt',
            9  => 'Sep',
            10 => 'Okt',
            11 => 'Nov',
            12 => 'Des'
        ];

        $monthInt = (int) $monthNumber;
        return $shortMonths[$monthInt] ?? (string) $monthNumber;
    }
}

if (!function_exists('formatTanggalIndonesia')) {
    /**
     * Format tanggal ke format Indonesia (dd Bulan yyyy).
     *
     * @param string $date String tanggal yang dapat diparsing (format Y-m-d, dll)
     * @param bool $includeDay Sertakan nama hari jika true
     * @return string Tanggal terformat (contoh: "15 Januari 2025" atau "Senin, 15 Januari 2025")
     *
     * @example
     * ```php
     * echo formatTanggalIndonesia('2025-01-15');        // Output: "15 Januari 2025"
     * echo formatTanggalIndonesia('2025-01-15', true);  // Output: "Rabu, 15 Januari 2025"
     * ```
     */
    function formatTanggalIndonesia($date, $includeDay = false)
    {
        if (empty($date)) {
            return '-';
        }

        $timestamp = strtotime($date);
        if ($timestamp === false) {
            return $date; // Return original if parsing fails
        }

        $day = date('j', $timestamp);
        $month = getMonthName((int) date('n', $timestamp));
        $year = date('Y', $timestamp);

        $formatted = "{$day} {$month} {$year}";

        if ($includeDay) {
            $dayNames = [
                'Sunday'    => 'Minggu',
                'Monday'    => 'Senin',
                'Tuesday'   => 'Selasa',
                'Wednesday' => 'Rabu',
                'Thursday'  => 'Kamis',
                'Friday'    => 'Jumat',
                'Saturday'  => 'Sabtu'
            ];

            $dayName = $dayNames[date('l', $timestamp)] ?? '';
            $formatted = "{$dayName}, {$formatted}";
        }

        return $formatted;
    }
}
