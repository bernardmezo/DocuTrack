<?php

namespace App\Controllers;

use App\Core\Controller;

require_once DOCUTRACK_ROOT . '/src/helpers/captcha_helper.php';

class CaptchaController extends Controller
{
    public function generate()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        // Generate new CAPTCHA code
        $captchaCode = generateCaptchaCode(6);
        $_SESSION['captcha_code'] = $captchaCode;
        $_SESSION['captcha_time'] = time();
        
        // Create and output image
        createCaptchaImage($captchaCode);
        exit;
    }
    
    public function refresh()
    {
        $this->generate();
    }
}
