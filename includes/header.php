<?php
/**
 * Public Header Template
 * File: includes/header.php
 * 
 * Header untuk halaman publik (pengunjung)
 */

// Load configuration
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/functions.php';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    
    <!-- SEO Meta Tags -->
    <meta name="description" content="<?php echo isset($page_description) ? $page_description : 'Laboratorium NCS - Network and Cybersecurity Laboratory'; ?>">
    <meta name="keywords" content="<?php echo isset($page_keywords) ? $page_keywords : 'laboratorium, network, cybersecurity, penelitian, pengabdian'; ?>">
    <meta name="author" content="Laboratorium NCS">
    
    <!-- Open Graph Meta Tags -->
    <meta property="og:title" content="<?php echo isset($page_title) ? $page_title . ' - ' . SITE_NAME : SITE_NAME; ?>">
    <meta property="og:description" content="<?php echo isset($page_description) ? $page_description : 'Laboratorium NCS - Network and Cybersecurity Laboratory'; ?>">
    <meta property="og:type" content="website">
    
    <title><?php echo isset($page_title) ? $page_title . ' - ' . SITE_NAME : SITE_NAME; ?></title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="<?php echo ASSETS_URL; ?>/img/favicon.png">
    
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- AOS (Animate On Scroll) CDN -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    
    <!-- Font Awesome CDN (untuk icons) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Custom CSS (jika diperlukan) -->
    <link rel="stylesheet" href="<?php echo ASSETS_URL; ?>/css/style.css">
    
    <!-- Tailwind Config (optional customization) -->
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#3B82F6',
                        secondary: '#10B981',
                        accent: '#F59E0B',
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-gray-50 font-sans antialiased">
    
    <!-- Navbar -->
    <nav class="bg-white shadow-lg fixed w-full top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <!-- Logo -->
                <div class="flex items-center">
                    <a href="/index.php" class="flex items-center space-x-3">
                        <img src="<?php echo ASSETS_URL; ?>/img/logo.png" alt="Logo NCS" class="h-10 w-10" onerror="this.style.display='none'">
                        <span class="text-xl font-bold text-blue-600"><?php echo SITE_NAME; ?></span>
                    </a>
                </div>
                
                <!-- Desktop Menu -->
                <div class="hidden md:flex items-center space-x-8">
                    <a href="index.php" class="text-gray-700 hover:text-blue-600 transition duration-200 <?php echo (basename($_SERVER['PHP_SELF']) == 'index.php') ? 'text-blue-600 font-semibold' : ''; ?>">
                        Beranda
                    </a>
                    <a href="profil.php" class="text-gray-700 hover:text-blue-600 transition duration-200 <?php echo (basename($_SERVER['PHP_SELF']) == 'profil.php') ? 'text-blue-600 font-semibold' : ''; ?>">
                        Profil
                    </a>
                    <a href="galeri.php" class="text-gray-700 hover:text-blue-600 transition duration-200 <?php echo (basename($_SERVER['PHP_SELF']) == 'galeri.php') ? 'text-blue-600 font-semibold' : ''; ?>">
                        Galeri
                    </a>
                    <a href="arsip.php" class="text-gray-700 hover:text-blue-600 transition duration-200 <?php echo (basename($_SERVER['PHP_SELF']) == 'arsip.php') ? 'text-blue-600 font-semibold' : ''; ?>">
                        Arsip
                    </a>
                    <a href="layanan.php" class="text-gray-700 hover:text-blue-600 transition duration-200 <?php echo (basename($_SERVER['PHP_SELF']) == 'layanan.php') ? 'text-blue-600 font-semibold' : ''; ?>">
                        Layanan
                    </a>
                    <a href="pengelola.php" class="text-gray-700 hover:text-blue-600 transition duration-200 <?php echo (basename($_SERVER['PHP_SELF']) == 'pengelola.php') ? 'text-blue-600 font-semibold' : ''; ?>">
                        Pengelola
                    </a>
                </div>
                
                <!-- Mobile Menu Button -->
                <div class="md:hidden flex items-center">
                    <button id="mobile-menu-button" class="text-gray-700 hover:text-blue-600 focus:outline-none">
                        <i class="fas fa-bars text-2xl"></i>
                    </button>
                </div>
            </div>
        </div>
        
        <!-- Mobile Menu -->
        <div id="mobile-menu" class="hidden md:hidden bg-white border-t">
            <div class="px-2 pt-2 pb-3 space-y-1">
                <a href="<?php echo SITE_URL; ?>/index.php" class="block px-3 py-2 text-gray-700 hover:bg-blue-50 hover:text-blue-600 rounded">Beranda</a>
                <a href="<?php echo SITE_URL; ?>/profil.php" class="block px-3 py-2 text-gray-700 hover:bg-blue-50 hover:text-blue-600 rounded">Profil</a>
                <a href="<?php echo SITE_URL; ?>/galeri.php" class="block px-3 py-2 text-gray-700 hover:bg-blue-50 hover:text-blue-600 rounded">Galeri</a>
                <a href="<?php echo SITE_URL; ?>/arsip.php" class="block px-3 py-2 text-gray-700 hover:bg-blue-50 hover:text-blue-600 rounded">Arsip</a>
                <a href="<?php echo SITE_URL; ?>/layanan.php" class="block px-3 py-2 text-gray-700 hover:bg-blue-50 hover:text-blue-600 rounded">Layanan</a>
                <a href="<?php echo SITE_URL; ?>/pengelola.php" class="block px-3 py-2 text-gray-700 hover:bg-blue-50 hover:text-blue-600 rounded">Pengelola</a>
            </div>
        </div>
    </nav>
    
    <!-- Main Content Wrapper (margin for fixed navbar) -->
    <div class="pt-16">
