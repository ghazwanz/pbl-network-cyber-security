<?php
/**
 * Admin Header Template
 * File: includes/admin_header.php
 * 
 * Header untuk halaman admin (backend)
 */

// Start output buffering to prevent "headers already sent" errors
if (!ob_get_level()) {
    ob_start();
}

// Load configuration
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/functions.php';
require_once __DIR__ . '/session.php';

// Proteksi halaman admin
requireLogin();

// Get current user
$current_user = getCurrentUser();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    
    <title><?php echo isset($page_title) ? $page_title . ' - Admin Panel' : 'Admin Panel - ' . SITE_NAME; ?></title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="<?php echo ASSETS_URL; ?>/img/favicon.png">
    
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Font Awesome CDN -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Custom Admin CSS -->
    <link rel="stylesheet" href="<?php echo ASSETS_URL; ?>/css/admin.css">
    
    <!-- Tailwind Config -->
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#3B82F6',
                        secondary: '#10B981',
                        danger: '#EF4444',
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-gray-100 font-sans antialiased">
    
    <div class="flex h-screen overflow-hidden">
        
        <!-- Sidebar -->
        <aside id="sidebar" class="bg-gray-800 text-white w-64 flex-shrink-0 hidden md:flex flex-col">
            <!-- Logo -->
            <div class="flex items-center justify-center h-16 bg-gray-900">
                <span class="text-xl font-bold">Admin Panel</span>
            </div>
            
            <!-- Navigation -->
            <nav class="flex-1 overflow-y-auto py-4">
                <ul class="space-y-1 px-3">
                    <li>
                        <a href="<?php echo ADMIN_URL; ?>/index.php" class="flex items-center px-4 py-3 rounded hover:bg-gray-700 transition <?php echo (basename($_SERVER['PHP_SELF']) == 'index.php') ? 'bg-gray-700' : ''; ?>">
                            <i class="fas fa-home mr-3"></i>
                            <span>Dashboard</span>
                        </a>
                    </li>
                    
                    <li class="pt-4 pb-2 px-4">
                        <span class="text-xs font-semibold text-gray-400 uppercase">Content Management</span>
                    </li>
                    
                    <li>
                        <a href="<?php echo ADMIN_URL; ?>/galeri.php" class="flex items-center px-4 py-3 rounded hover:bg-gray-700 transition <?php echo (basename($_SERVER['PHP_SELF']) == 'galeri.php') ? 'bg-gray-700' : ''; ?>">
                            <i class="fas fa-images mr-3"></i>
                            <span>Galeri & Agenda</span>
                        </a>
                    </li>
                    
                    <li>
                        <a href="<?php echo ADMIN_URL; ?>/pengelola.php" class="flex items-center px-4 py-3 rounded hover:bg-gray-700 transition <?php echo (basename($_SERVER['PHP_SELF']) == 'pengelola.php') ? 'bg-gray-700' : ''; ?>">
                            <i class="fas fa-users mr-3"></i>
                            <span>Pengelola</span>
                        </a>
                    </li>
                    
                    <li>
                        <a href="<?php echo ADMIN_URL; ?>/arsip.php" class="flex items-center px-4 py-3 rounded hover:bg-gray-700 transition <?php echo (basename($_SERVER['PHP_SELF']) == 'arsip.php') ? 'bg-gray-700' : ''; ?>">
                            <i class="fas fa-file-pdf mr-3"></i>
                            <span>Arsip</span>
                        </a>
                    </li>
                    
                    <li>
                        <a href="<?php echo ADMIN_URL; ?>/sarana.php" class="flex items-center px-4 py-3 rounded hover:bg-gray-700 transition <?php echo (basename($_SERVER['PHP_SELF']) == 'sarana.php') ? 'bg-gray-700' : ''; ?>">
                            <i class="fas fa-laptop mr-3"></i>
                            <span>Sarana Prasarana</span>
                        </a>
                    </li>
                    
                    <li>
                        <a href="<?php echo ADMIN_URL; ?>/konsultatif.php" class="flex items-center px-4 py-3 rounded hover:bg-gray-700 transition <?php echo (basename($_SERVER['PHP_SELF']) == 'konsultatif.php') ? 'bg-gray-700' : ''; ?>">
                            <i class="fas fa-comments mr-3"></i>
                            <span>Konsultatif</span>
                        </a>
                    </li>
                    
                    <li class="pt-4 pb-2 px-4">
                        <span class="text-xs font-semibold text-gray-400 uppercase">Settings</span>
                    </li>
                    
                    <li>
                        <a href="<?php echo ADMIN_URL; ?>/profil.php" class="flex items-center px-4 py-3 rounded hover:bg-gray-700 transition <?php echo (basename($_SERVER['PHP_SELF']) == 'profil_lab.php') ? 'bg-gray-700' : ''; ?>">
                            <i class="fas fa-building mr-3"></i>
                            <span>Profil Lab</span>
                        </a>
                    </li>
                </ul>
            </nav>
            
            <!-- User Info -->
            <div class="p-4 bg-gray-900">
                <div class="flex items-center">
                    <div class="w-10 h-10 rounded-full bg-blue-500 flex items-center justify-center">
                        <i class="fas fa-user"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-semibold"><?php echo htmlspecialchars($current_user['nama_lengkap']); ?></p>
                        <p class="text-xs text-gray-400"><?php echo htmlspecialchars($current_user['role']); ?></p>
                    </div>
                </div>
            </div>
        </aside>
        
        <!-- Main Content Area -->
        <div class="flex-1 flex flex-col overflow-hidden">
            
            <!-- Top Navigation Bar -->
            <header class="bg-white shadow-sm h-16 flex items-center justify-between px-6">
                <!-- Mobile Menu Button -->
                <button id="mobile-sidebar-toggle" class="md:hidden text-gray-600 hover:text-gray-900">
                    <i class="fas fa-bars text-xl"></i>
                </button>
                
                <!-- Page Title -->
                <h1 class="text-xl font-semibold text-gray-800">
                    <?php echo isset($page_title) ? $page_title : 'Dashboard'; ?>
                </h1>
                
                <!-- Right Menu -->
                <div class="flex items-center space-x-4">
                    <!-- View Site Link -->
                    <a href="<?php echo SITE_URL; ?>/index.php" target="_blank" class="text-gray-600 hover:text-blue-600 transition" title="Lihat Website">
                        <i class="fas fa-external-link-alt"></i>
                    </a>
                    
                    <!-- Logout -->
                    <a href="<?php echo ADMIN_URL; ?>/logout.php" class="text-gray-600 hover:text-red-600 transition" title="Logout" onclick="return confirm('Yakin ingin logout?')">
                        <i class="fas fa-sign-out-alt"></i>
                    </a>
                </div>
            </header>
            
            <!-- Main Content -->
            <main class="flex-1 overflow-y-auto p-6">
                
                <!-- Flash Message -->
                <?php echo displayFlashMessage(); ?>
