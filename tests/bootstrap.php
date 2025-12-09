<?php
// tests/bootstrap.php

// Define a root constant for the test environment
if (!defined('DOCUTRACK_ROOT')) {
    define('DOCUTRACK_ROOT', dirname(__DIR__));
}

// Include the main application bootstrap file
require_once DOCUTRACK_ROOT . '/src/bootstrap.php';
