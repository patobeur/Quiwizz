<?php

// Set error reporting for development
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Set a default timezone
date_default_timezone_set('UTC');

// Include the main bootstrap file of the application
require_once __DIR__ . '/../bootstrap.php';

// Include all function files from the lib
require_once __DIR__ . '/../lib/auth.php';
require_once __DIR__ . '/../lib/profile.php';
require_once __DIR__ . '/../lib/db.php';
require_once __DIR__ . '/../lib/response.php';
require_once __DIR__ . '/../lib/security.php';
