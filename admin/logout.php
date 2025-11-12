<?php
/**
 * Admin Logout
 * File: admin/logout.php
 */

// Load configuration
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/session.php';

// Destroy session
destroyLoginSession();

// Set flash message
setFlashMessage('success', 'Anda telah berhasil logout');

// Redirect ke login
redirect(ADMIN_URL . '/login.php');
