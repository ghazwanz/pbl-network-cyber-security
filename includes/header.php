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

    <!-- AOS CSS -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">

    <!-- Font Awesome CDN (untuk icons) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,200..800;1,200..800&display=swap" rel="stylesheet">

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
                    },
                    fontFamily: {
                        'inter': ['Plus Jakarta Sans', 'sans-serif'],
                    },
                }
            }
        }
    </script>
</head>

<body class="font-inter bg-[#F8FCFF] text-gray-600">

    <?php
    // Get current page name
    $current_page = basename($_SERVER['PHP_SELF']);
    ?>
    <!-- Navbar -->
    <header id="nav" class="fixed transition duration-300 ease-in-out top-0 left-0 right-0 bg-white border-b border-gray-200 z-10">
        <div class="px-4 overflow-hidden">
            <nav class="container mx-auto max-w-7xl flex justify-between items-center py-5">
                <img src="../assets/img/jti.webp" class="h-[2.5rem]">
                <ul class="hidden md:flex items-center space-x-8">
                    <li><a href="./index.php" class="font-medium transition <?php echo ($current_page == 'index.php') ? 'text-[#111]' : 'text-gray-500 hover:text-[#111]'; ?>">Beranda</a></li>
                    <li><a href="./profil.php" class="font-medium transition <?php echo ($current_page == 'profil.php') ? 'text-[#111]' : 'text-gray-500 hover:text-[#111]'; ?>">Profil</a></li>
                    <li><a href="./arsip.php" class="font-medium transition <?php echo ($current_page == 'arsip.php') ? 'text-[#111]' : 'text-gray-500 hover:text-[#111]'; ?>">Arsip</a></li>
                    <li><a href="./galeri.php" class="font-medium transition <?php echo ($current_page == 'galeri.php') ? 'text-[#111]' : 'text-gray-500 hover:text-[#111]'; ?>">Galeri</a></li>
                    <li><a href="./layanan.php" class="font-medium transition <?php echo ($current_page == 'layanan.php') ? 'text-[#111]' : 'text-gray-500 hover:text-[#111]'; ?>">Layanan</a></li>
                    <li><a href="./kontak.php" class="font-medium transition <?php echo ($current_page == 'kontak.php') ? 'text-[#111]' : 'text-gray-500 hover:text-[#111]'; ?>">Kontak</a></li>
                </ul>
                <!-- Mobile Menu Button -->
                <div class="md:hidden flex items-center">
                    <button id="mobile-menu-button" class="text-gray-700 hover:text-blue-600 focus:outline-none">
                        <i class="fas fa-bars text-2xl"></i>
                    </button>
                </div>
            </nav>
            <div id="mobile-menu" class="hidden md:hidden duration-300 ease-in-out bg-white border-t">
                <div class="pt-2 pb-3 space-y-1">
                    <a href="<?php echo SITE_URL; ?>/index.php" class="font-medium transition-all duration-300 ease-in-out block px-3 py-2 <?php echo ($current_page == 'index.php') ? 'bg-blue-50 text-blue-600' : 'text-gray-700 hover:bg-blue-50 hover:text-blue-600'; ?> rounded">Beranda</a>
                    <a href="<?php echo SITE_URL; ?>/profil.php" class="font-medium transition-all duration-300 ease-in-out block px-3 py-2 <?php echo ($current_page == 'profil.php') ? 'bg-blue-50 text-blue-600' : 'text-gray-700 hover:bg-blue-50 hover:text-blue-600'; ?> rounded">Profil</a>
                    <a href="<?php echo SITE_URL; ?>/galeri.php" class="font-medium transition-all duration-300 ease-in-out block px-3 py-2 <?php echo ($current_page == 'galeri.php') ? 'bg-blue-50 text-blue-600' : 'text-gray-700 hover:bg-blue-50 hover:text-blue-600'; ?> rounded">Galeri</a>
                    <a href="<?php echo SITE_URL; ?>/arsip.php" class="font-medium transition-all duration-300 ease-in-out block px-3 py-2 <?php echo ($current_page == 'arsip.php') ? 'bg-blue-50 text-blue-600' : 'text-gray-700 hover:bg-blue-50 hover:text-blue-600'; ?> rounded">Arsip</a>
                    <a href="<?php echo SITE_URL; ?>/layanan.php" class="font-medium transition-all duration-300 ease-in-out block px-3 py-2 <?php echo ($current_page == 'layanan.php') ? 'bg-blue-50 text-blue-600' : 'text-gray-700 hover:bg-blue-50 hover:text-blue-600'; ?> rounded">Layanan</a>
                    <a href="<?php echo SITE_URL; ?>/pengelola.php" class="font-medium transition-all duration-300 ease-in-out block px-3 py-2 <?php echo ($current_page == 'pengelola.php') ? 'bg-blue-50 text-blue-600' : 'text-gray-700 hover:bg-blue-50 hover:text-blue-600'; ?> rounded">Pengelola</a>
                    <a href="<?php echo SITE_URL; ?>/kontak.php" class="font-medium transition-all duration-300 ease-in-out block px-3 py-2 <?php echo ($current_page == 'kontak.php') ? 'bg-blue-50 text-blue-600' : 'text-gray-700 hover:bg-blue-50 hover:text-blue-600'; ?> rounded">Kontak</a>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content Wrapper (margin for fixed navbar) -->
    <div class="pt-16 overflow-clip">