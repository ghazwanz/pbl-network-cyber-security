<?php
/**
 * Configuration File
 * File: config/config.php
 * 
 * Konfigurasi umum aplikasi
 */

// Error Reporting (Development Mode)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Timezone
date_default_timezone_set('Asia/Jakarta');

// Site Configuration
define('SITE_NAME', 'Laboratorium NCS');
define('SITE_URL', 'http://localhost/pbl-modular/public'); // Sesuaikan dengan URL Anda
define('ADMIN_URL', 'http://localhost/pbl-modular/admin'); // Sesuaikan dengan URL Anda

// Path Configuration
define('ROOT_PATH', dirname(__DIR__)); // Root directory project
define('UPLOAD_PATH', ROOT_PATH . '/uploads');
define('ASSETS_PATH', ROOT_PATH . '/assets');

// URL untuk asset
define('ASSETS_URL', SITE_URL . '/../assets');
define('UPLOAD_URL', SITE_URL . '/../uploads');

// Upload Configuration
define('MAX_FILE_SIZE', 5 * 1024 * 1024); // 5 MB
define('MAX_IMAGE_SIZE', 2 * 1024 * 1024); // 2 MB
define('ALLOWED_IMAGE_TYPES', ['image/jpeg', 'image/jpg', 'image/png', 'image/gif']);
define('ALLOWED_PDF_TYPES', ['application/pdf']);

// Pagination Configuration
define('ITEMS_PER_PAGE', 10);
define('ITEMS_PER_PAGE_PUBLIC', 12);

// Session Configuration
define('SESSION_NAME', 'lab_ncs_session');
define('SESSION_LIFETIME', 3600); // 1 hour in seconds

// Security
define('HASH_ALGORITHM', 'md5'); // Untuk development, gunakan 'password_hash' untuk production
define('CSRF_TOKEN_NAME', 'csrf_token');

// Image Thumbnail Configuration
define('THUMB_WIDTH', 400);
define('THUMB_HEIGHT', 300);

// Date Format
define('DATE_FORMAT', 'd-m-Y');
define('DATETIME_FORMAT', 'd-m-Y H:i:s');

// Include database configuration
require_once __DIR__ . '/database.php';
