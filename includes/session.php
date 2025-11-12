<?php
/**
 * Session Handler
 * File: includes/session.php
 * 
 * Manajemen session untuk authentication
 */

// Mulai session jika belum
if (session_status() === PHP_SESSION_NONE) {
    session_name(SESSION_NAME);
    session_start();
}

/**
 * Check apakah user sudah login
 * @return bool True jika sudah login, false jika belum
 */
function isLoggedIn() {
    return isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;
}

/**
 * Get data user yang sedang login
 * @return array|null Data user atau null
 */
function getCurrentUser() {
    if (isLoggedIn() && isset($_SESSION['admin_data'])) {
        return $_SESSION['admin_data'];
    }
    return null;
}

/**
 * Set session login
 * @param array $user_data Data user dari database
 */
function setLoginSession($user_data) {
    $_SESSION['admin_logged_in'] = true;
    $_SESSION['admin_data'] = [
        'id' => $user_data['id'],
        'username' => $user_data['username'],
        'nama_lengkap' => $user_data['nama_lengkap'],
        'email' => $user_data['email'],
        'role' => $user_data['role']
    ];
    $_SESSION['login_time'] = time();
}

/**
 * Destroy session login
 */
function destroyLoginSession() {
    $_SESSION = [];
    
    if (isset($_COOKIE[session_name()])) {
        setcookie(session_name(), '', time() - 42000, '/');
    }
    
    session_destroy();
}

/**
 * Check session timeout
 * @return bool True jika timeout, false jika masih valid
 */
function isSessionTimeout() {
    if (!isset($_SESSION['login_time'])) {
        return true;
    }
    
    $elapsed_time = time() - $_SESSION['login_time'];
    
    if ($elapsed_time >= SESSION_LIFETIME) {
        return true;
    }
    
    // Update login time untuk reset timeout
    $_SESSION['login_time'] = time();
    return false;
}

/**
 * Middleware untuk proteksi halaman admin
 * Redirect ke login jika belum login atau session timeout
 */
function requireLogin() {
    if (!isLoggedIn() || isSessionTimeout()) {
        setFlashMessage('error', 'Silakan login terlebih dahulu');
        redirect(ADMIN_URL . '/login.php');
    }
}

/**
 * Middleware untuk redirect ke dashboard jika sudah login
 * Digunakan di halaman login
 */
function redirectIfLoggedIn() {
    if (isLoggedIn() && !isSessionTimeout()) {
        redirect(ADMIN_URL . '/index.php');
    }
}

/**
 * Generate CSRF Token
 * @return string CSRF Token
 */
function generateCSRFToken() {
    if (!isset($_SESSION[CSRF_TOKEN_NAME])) {
        $_SESSION[CSRF_TOKEN_NAME] = bin2hex(random_bytes(32));
    }
    return $_SESSION[CSRF_TOKEN_NAME];
}

/**
 * Verify CSRF Token
 * @param string $token Token yang akan diverifikasi
 * @return bool True jika valid, false jika tidak
 */
function verifyCSRFToken($token) {
    if (!isset($_SESSION[CSRF_TOKEN_NAME])) {
        return false;
    }
    return hash_equals($_SESSION[CSRF_TOKEN_NAME], $token);
}
