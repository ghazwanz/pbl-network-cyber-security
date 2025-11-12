<?php
/**
 * Helper Functions
 * File: includes/functions.php
 * 
 * Berisi fungsi-fungsi helper umum untuk aplikasi
 */

/**
 * Redirect ke URL tertentu
 * @param string $url URL tujuan
 */
function redirect($url) {
    // Check if headers are already sent
    if (headers_sent($filename, $linenum)) {
        // If headers are already sent, use JavaScript redirect as fallback
        echo "<script type='text/javascript'>window.location.href = '$url';</script>";
        echo "<noscript><meta http-equiv='refresh' content='0;url=$url' /></noscript>";
        exit();
    } else {
        // Use PHP header redirect if possible
        header("Location: " . $url);
        exit();
    }
}

/**
 * Sanitasi input user
 * @param string $data Data input
 * @return string Data yang sudah dibersihkan
 */
function sanitize($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    return $data;
}

/**
 * Validasi email
 * @param string $email Email yang akan divalidasi
 * @return bool True jika valid, false jika tidak
 */
function isValidEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

/**
 * Format tanggal Indonesia
 * @param string $date Tanggal dalam format Y-m-d
 * @return string Tanggal dalam format Indonesia
 */
function formatDateIndo($date) {
    $bulan = [
        1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
        'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
    ];
    
    $split = explode('-', $date);
    return $split[2] . ' ' . $bulan[(int)$split[1]] . ' ' . $split[0];
}

/**
 * Upload file image
 * @param array $file $_FILES array
 * @param string $target_dir Target directory (relatif dari UPLOAD_PATH)
 * @param string $prefix Prefix nama file (optional)
 * @return array ['success' => bool, 'message' => string, 'filename' => string]
 */
function uploadImage($file, $target_dir, $prefix = '') {
    $response = [
        'success' => false,
        'message' => '',
        'filename' => ''
    ];
    
    // Validasi file
    if (!isset($file['tmp_name']) || empty($file['tmp_name'])) {
        $response['message'] = 'Tidak ada file yang diupload';
        return $response;
    }
    
    // Validasi ukuran
    if ($file['size'] > MAX_IMAGE_SIZE) {
        $response['message'] = 'Ukuran file terlalu besar. Maksimal ' . (MAX_IMAGE_SIZE / 1024 / 1024) . ' MB';
        return $response;
    }
    
    // Validasi tipe file
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime_type = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);
    
    if (!in_array($mime_type, ALLOWED_IMAGE_TYPES)) {
        $response['message'] = 'Tipe file tidak diizinkan. Hanya JPG, PNG, dan GIF';
        return $response;
    }
    
    // Generate nama file unik
    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = $prefix . '_' . uniqid() . '_' . time() . '.' . $extension;
    $target_path = UPLOAD_PATH . '/' . $target_dir . '/' . $filename;
    
    // Pastikan folder ada
    if (!file_exists(UPLOAD_PATH . '/' . $target_dir)) {
        mkdir(UPLOAD_PATH . '/' . $target_dir, 0777, true);
    }
    
    // Upload file
    if (move_uploaded_file($file['tmp_name'], $target_path)) {
        $response['success'] = true;
        $response['message'] = 'File berhasil diupload';
        $response['filename'] = $filename;
    } else {
        $response['message'] = 'Gagal mengupload file';
    }
    
    return $response;
}

/**
 * Upload file PDF
 * @param array $file $_FILES array
 * @param string $target_dir Target directory (relatif dari UPLOAD_PATH)
 * @param string $prefix Prefix nama file (optional)
 * @return array ['success' => bool, 'message' => string, 'filename' => string, 'filesize' => int]
 */
function uploadPDF($file, $target_dir, $prefix = '') {
    $response = [
        'success' => false,
        'message' => '',
        'filename' => '',
        'filesize' => 0
    ];
    
    // Validasi file
    if (!isset($file['tmp_name']) || empty($file['tmp_name'])) {
        $response['message'] = 'Tidak ada file yang diupload';
        return $response;
    }
    
    // Validasi ukuran
    if ($file['size'] > MAX_FILE_SIZE) {
        $response['message'] = 'Ukuran file terlalu besar. Maksimal ' . (MAX_FILE_SIZE / 1024 / 1024) . ' MB';
        return $response;
    }
    
    // Validasi tipe file
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime_type = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);
    
    if (!in_array($mime_type, ALLOWED_PDF_TYPES)) {
        $response['message'] = 'Tipe file tidak diizinkan. Hanya PDF';
        return $response;
    }
    
    // Generate nama file unik
    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = $prefix . '_' . uniqid() . '_' . time() . '.' . $extension;
    $target_path = UPLOAD_PATH . '/' . $target_dir . '/' . $filename;
    
    // Pastikan folder ada
    if (!file_exists(UPLOAD_PATH . '/' . $target_dir)) {
        mkdir(UPLOAD_PATH . '/' . $target_dir, 0777, true);
    }
    
    // Upload file
    if (move_uploaded_file($file['tmp_name'], $target_path)) {
        $response['success'] = true;
        $response['message'] = 'File berhasil diupload';
        $response['filename'] = $filename;
        $response['filesize'] = round($file['size'] / 1024); // dalam KB
    } else {
        $response['message'] = 'Gagal mengupload file';
    }
    
    return $response;
}

/**
 * Delete file
 * @param string $filepath Path file yang akan dihapus (relatif dari UPLOAD_PATH)
 * @return bool True jika berhasil, false jika gagal
 */
function deleteFile($filepath) {
    $full_path = UPLOAD_PATH . $filepath;
    
    if (file_exists($full_path)) {
        return unlink($full_path);
    }
    
    return false;
}

/**
 * Generate slug dari string
 * @param string $text String yang akan dijadikan slug
 * @return string Slug
 */
function generateSlug($text) {
    // Replace non letter or digits by -
    $text = preg_replace('~[^\pL\d]+~u', '-', $text);
    
    // Transliterate
    $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
    
    // Remove unwanted characters
    $text = preg_replace('~[^-\w]+~', '', $text);
    
    // Trim
    $text = trim($text, '-');
    
    // Remove duplicate -
    $text = preg_replace('~-+~', '-', $text);
    
    // Lowercase
    $text = strtolower($text);
    
    if (empty($text)) {
        return 'n-a';
    }
    
    return $text;
}

/**
 * Truncate text
 * @param string $text Text yang akan dipotong
 * @param int $length Panjang maksimal
 * @param string $suffix Suffix (default: ...)
 * @return string Text yang sudah dipotong
 */
function truncateText($text, $length = 100, $suffix = '...') {
    if (strlen($text) <= $length) {
        return $text;
    }
    
    return substr($text, 0, $length) . $suffix;
}

/**
 * Create pagination HTML
 * @param int $current_page Halaman saat ini
 * @param int $total_pages Total halaman
 * @param string $base_url Base URL (tanpa parameter page)
 * @return string HTML pagination
 */
function createPagination($current_page, $total_pages, $base_url) {
    if ($total_pages <= 1) {
        return '';
    }
    
    $html = '<div class="flex justify-center items-center space-x-2 mt-6">';
    
    // Check if base_url already contains parameters
    $separator = (strpos($base_url, '?') !== false) ? '&' : '?';
    
    // Previous button
    if ($current_page > 1) {
        $prev_url = $base_url . $separator . 'page=' . ($current_page - 1);
        $html .= '<a href="' . $prev_url . '" class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">Previous</a>';
    }
    
    // Page numbers
    for ($i = 1; $i <= $total_pages; $i++) {
        $active_class = ($i == $current_page) ? 'bg-blue-600 text-white' : 'bg-gray-200 hover:bg-gray-300';
        $page_url = $base_url . $separator . 'page=' . $i;
        $html .= '<a href="' . $page_url . '" class="px-4 py-2 rounded ' . $active_class . '">' . $i . '</a>';
    }
    
    // Next button
    if ($current_page < $total_pages) {
        $next_url = $base_url . $separator . 'page=' . ($current_page + 1);
        $html .= '<a href="' . $next_url . '" class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">Next</a>';
    }
    
    $html .= '</div>';
    
    return $html;
}

/**
 * Flash message (session-based)
 * @param string $type Type pesan (success, error, warning, info)
 * @param string $message Pesan
 */
function setFlashMessage($type, $message) {
    if (!isset($_SESSION)) {
        session_start();
    }
    
    $_SESSION['flash_message'] = [
        'type' => $type,
        'message' => $message
    ];
}

/**
 * Get flash message dan hapus dari session
 * @return array|null Flash message atau null
 */
function getFlashMessage() {
    if (!isset($_SESSION)) {
        session_start();
    }
    
    if (isset($_SESSION['flash_message'])) {
        $flash = $_SESSION['flash_message'];
        unset($_SESSION['flash_message']);
        return $flash;
    }
    
    return null;
}

/**
 * Display flash message HTML
 * @return string HTML flash message
 */
function displayFlashMessage() {
    $flash = getFlashMessage();
    
    if (!$flash) {
        return '';
    }
    
    $color_class = [
        'success' => 'bg-green-100 border-green-400 text-green-700',
        'error' => 'bg-red-100 border-red-400 text-red-700',
        'warning' => 'bg-yellow-100 border-yellow-400 text-yellow-700',
        'info' => 'bg-blue-100 border-blue-400 text-blue-700'
    ];
    
    $class = isset($color_class[$flash['type']]) ? $color_class[$flash['type']] : $color_class['info'];
    
    $html = '<div class="' . $class . ' border px-4 py-3 rounded relative mb-4" role="alert">';
    $html .= '<span class="block sm:inline">' . htmlspecialchars($flash['message']) . '</span>';
    $html .= '</div>';
    
    return $html;
}

/**
 * Debug helper
 * @param mixed $data Data yang akan di-debug
 * @param bool $die Die after dump
 */
function dd($data, $die = true) {
    echo '<pre>';
    var_dump($data);
    echo '</pre>';
    
    if ($die) {
        die();
    }
}
