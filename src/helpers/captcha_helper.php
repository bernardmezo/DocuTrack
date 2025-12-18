<?php

/**
 * Generate random CAPTCHA code
 */
function generateCaptchaCode($length = 6) {
    $characters = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';
    $captcha = '';
    for ($i = 0; $i < $length; $i++) {
        $captcha .= $characters[rand(0, strlen($characters) - 1)];
    }
    return $captcha;
}

/**
 * Create CAPTCHA image
 */
function createCaptchaImage($code) {
    // Create image
    $width = 200;
    $height = 60;
    $image = imagecreatetruecolor($width, $height);
    
    // Colors
    $bgColor = imagecolorallocate($image, 240, 240, 240);
    $textColor = imagecolorallocate($image, 10, 37, 64); // #0A2540
    $lineColor = imagecolorallocate($image, 66, 153, 225); // #4299E1
    $noiseColor = imagecolorallocate($image, 200, 200, 200);
    
    // Fill background
    imagefilledrectangle($image, 0, 0, $width, $height, $bgColor);
    
    // Add noise dots
    for ($i = 0; $i < 100; $i++) {
        imagesetpixel($image, rand(0, $width), rand(0, $height), $noiseColor);
    }
    
    // Add random lines
    for ($i = 0; $i < 5; $i++) {
        imageline($image, rand(0, $width), rand(0, $height), 
                  rand(0, $width), rand(0, $height), $lineColor);
    }
    
    // Add text
    $fontSize = 24;
    $angle = rand(-10, 10);
    $x = 20;
    $y = 40;
    
    // Use built-in font if TTF not available
    $fontPath = __DIR__ . '/../../public/assets/fonts/arial.ttf';
    if (file_exists($fontPath)) {
        imagettftext($image, $fontSize, $angle, $x, $y, $textColor, $fontPath, $code);
    } else {
        // Fallback to imagestring
        imagestring($image, 5, $x, $y - 20, $code, $textColor);
    }
    
    // Output image
    header('Content-Type: image/png');
    imagepng($image);
    imagedestroy($image);
}
