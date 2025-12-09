<?php

/**
 * Public Layanan Page
 * File: public/layanan.php
 * Design Reference: Modern & Professional (Consistent with Index)
 */

// Set page title
$page_title = "Layanan - Laboratorium NCS";

// Include public header
require_once __DIR__ . '/../includes/header.php';

// Get layanan data from database
$layanan_list = executeQuery("SELECT * FROM layanan WHERE status = 'Aktif' ORDER BY id ASC");

// Get konsultatif data from database
$konsultatif_list = executeQuery("SELECT * FROM konsultatif WHERE status = 'terjawab' ORDER BY created_at DESC LIMIT 10");

// Get sarana data from database
$sarana_list = executeQuery("SELECT * FROM sarana WHERE is_active = true ORDER BY id ASC");

// Count statistics
$count_layanan = countRows("SELECT COUNT(*) FROM layanan WHERE status = 'Aktif'");
$count_konsultatif = countRows("SELECT COUNT(*) FROM konsultatif WHERE status = 'terjawab'");
$count_sarana = countRows("SELECT COUNT(*) FROM sarana WHERE is_active = true");
?>

<!-- Hero Section - Matching Index Style -->
<section class="relative py-32 px-4 overflow-hidden">  
    <div class="absolute inset-0 opacity-[0.5]" style="background-image: url('data:image/svg+xml,%3Csvg width=%2260%22 height=%2260%22 viewBox=%220 0 60 60%22 xmlns=%22http://www.w3.org/2000/svg%22%3E%3Cg fill=%22none%22 fill-rule=%22evenodd%22%3E%3Cg fill=%22%23000%22 fill-opacity=%221%22%3E%3Cpath d=%22M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z%22/%3E%3C/g%3E%3C/g%3E%3C/svg%3E');"></div>

    <!-- Gradient Mesh Background -->
    <div class="absolute inset-0 overflow-hidden pointer-events-none">
        <div class="absolute inset-0 bg-gradient-to-b from-orange-50/80 via-white to-blue-50/60"></div>
        <div class="absolute -left-20 top-0 w-[60%] h-full bg-gradient-to-br from-orange-100/70 via-orange-50/50 to-transparent blur-3xl"></div>
        <div class="absolute -right-20 top-0 w-[60%] h-full bg-gradient-to-bl from-blue-100/60 via-indigo-50/40 to-transparent blur-3xl"></div>
        <div class="absolute inset-0 bg-gradient-to-b from-transparent via-white/30 to-white/60"></div>
        <div class="absolute inset-0 opacity-[0.015]" style="background-image: url('data:image/svg+xml,%3Csvg viewBox=%220 0 256 256%22 xmlns=%22http://www.w3.org/2000/svg%22%3E%3Cfilter id=%22noise%22%3E%3CfeTurbulence type=%22fractalNoise%22 baseFrequency=%220.9%22 numOctaves=%224%22 stitchTiles=%22stitch%22/%3E%3C/filter%3E%3Crect width=%22100%25%22 height=%22100%25%22 filter=%22url(%23noise)%22/%3E%3C/svg%3E');"></div>
    </div>

    <!-- Hero Content -->
    <div class="mx-auto max-w-4xl flex flex-col items-center relative text-center">
        
        <!-- Badge -->
        <div class="inline-flex items-center gap-2.5 px-5 py-2.5 bg-white/80 backdrop-blur-sm border border-orange-200/60 rounded-full text-orange-600 font-semibold text-sm mb-8 shadow-lg shadow-orange-100/50" data-aos="fade-up">
            <span class="w-2 h-2 bg-orange-500 rounded-full animate-pulse"></span>
            <span class="tracking-wide">LAYANAN LABORATORIUM</span>
        </div>

        <!-- Main Heading -->
        <h1 class="text-4xl sm:text-5xl md:text-6xl lg:text-7xl font-medium text-[#1B2D62] leading-[1.1] tracking-tight mb-8" data-aos="fade-up" data-aos-delay="100">
            Jelajahi<br class="hidden sm:block"> 
            <span class="bg-gradient-to-r from-[#1B2D62] via-[#2C4AA4] to-orange-500 bg-clip-text text-transparent">Layanan & Fasilitas</span>
        </h1>

        <p class="text-lg md:text-xl text-gray-600 max-w-2xl mb-10 leading-relaxed" data-aos="fade-up" data-aos-delay="200">
            Solusi komprehensif untuk kebutuhan praktikum, penelitian, dan pengembangan keamanan siber dengan fasilitas standar industri.
        </p>

        <!-- Feature Tags -->
        <div class="flex flex-wrap justify-center gap-3 sm:gap-6 mb-12" data-aos="fade-up" data-aos-delay="300">
            <div class="flex items-center gap-2 px-4 py-2 bg-white/70 backdrop-blur-sm rounded-full border border-gray-200/60 shadow-sm">
                <div class="w-5 h-5 bg-gradient-to-br from-emerald-400 to-emerald-600 rounded-full flex items-center justify-center">
                    <i class="fas fa-check text-white text-[10px]"></i>
                </div>
                <span class="font-medium text-gray-700 text-sm">Praktikum</span>
            </div>
            <div class="flex items-center gap-2 px-4 py-2 bg-white/70 backdrop-blur-sm rounded-full border border-gray-200/60 shadow-sm">
                <div class="w-5 h-5 bg-gradient-to-br from-emerald-400 to-emerald-600 rounded-full flex items-center justify-center">
                    <i class="fas fa-check text-white text-[10px]"></i>
                </div>
                <span class="font-medium text-gray-700 text-sm">Penelitian</span>
            </div>
            <div class="flex items-center gap-2 px-4 py-2 bg-white/70 backdrop-blur-sm rounded-full border border-gray-200/60 shadow-sm">
                <div class="w-5 h-5 bg-gradient-to-br from-emerald-400 to-emerald-600 rounded-full flex items-center justify-center">
                    <i class="fas fa-check text-white text-[10px]"></i>
                </div>
                <span class="font-medium text-gray-700 text-sm">Konsultasi</span>
            </div>
        </div>

        <!-- CTA Button -->
        <div class="flex flex-col sm:flex-row gap-4" data-aos="fade-up" data-aos-delay="400">
            <a href="#layanan-section" class="group inline-flex items-center justify-center gap-3 bg-gradient-to-r from-[#1B2D62] to-[#2C4AA4] text-white font-medium text-base px-8 py-4 rounded-xl shadow-lg shadow-blue-900/25 transition-all duration-300 hover:shadow-xl hover:shadow-blue-900/30 hover:scale-[1.02] active:scale-[0.98] focus:outline-none focus:ring-2 focus:ring-[#1B2D62] focus:ring-offset-2">
                <span>Lihat Layanan</span>
                <i class="fas fa-arrow-down transition-transform duration-300 group-hover:translate-y-1"></i>
            </a>
            <a href="./kontak.php" class="group inline-flex items-center justify-center gap-3 bg-white text-[#1B2D62] font-medium text-base px-8 py-4 rounded-xl border-2 border-gray-200 shadow-sm transition-all duration-300 hover:border-[#1B2D62]/30 hover:shadow-lg hover:bg-gray-50 active:scale-[0.98] focus:outline-none focus:ring-2 focus:ring-[#1B2D62] focus:ring-offset-2">
                <span>Hubungi Kami</span>
                <i class="fas fa-paper-plane text-sm transition-transform duration-300 group-hover:translate-x-0.5 group-hover:-translate-y-0.5"></i>
            </a>
        </div>

    </div>
</section>

<!-- Layanan Section -->
<section class="relative py-24 sm:py-32 px-4 " id="layanan-section">
    <div class="absolute inset-0 bg-gradient-to-b from-white via-blue-50/30 to-slate-50"></div>
    
    <!-- Decorative circles -->
    <div class="absolute top-20 left-10 w-72 h-72 bg-orange-200/10 rounded-full blur-3xl"></div>
    <div class="absolute bottom-20 right-10 w-96 h-96 bg-blue-200/10 rounded-full blur-3xl"></div>
    
    <div class="relative mx-auto max-w-7xl">
        
        <!-- Section Header with Side Layout -->
        <div class="grid lg:grid-cols-2 gap-12 items-center mb-16">
            <div data-aos="fade-right">
                <div class="inline-flex items-center gap-2.5 px-5 py-2.5 bg-white/80 backdrop-blur-sm border border-orange-200/60 rounded-full text-orange-600 font-semibold text-sm mb-6 shadow-lg shadow-orange-100/50">
                    <span class="w-2 h-2 bg-orange-500 rounded-full animate-pulse"></span>
                    <span class="tracking-wide">LAYANAN UNGGULAN</span>
                </div>

                <h2 class="text-4xl md:text-5xl font-medium text-[#1B2D62] leading-[1.15] tracking-tight mb-6">
                    Layanan Profesional untuk
                    <span class="bg-gradient-to-r from-[#1B2D62] via-[#2C4AA4] to-orange-500 bg-clip-text text-transparent">Kebutuhan Anda</span>
                </h2>
            </div>
            
            <div data-aos="fade-left" class="lg:pl-8">
                <p class="text-lg text-gray-600 leading-relaxed mb-6">
                    Kami menyediakan berbagai layanan profesional yang dirancang khusus untuk mendukung kegiatan akademik, penelitian, dan pengembangan di bidang keamanan siber dengan standar industri.
                </p>
                <div class="flex flex-wrap gap-3">
                    <div class="px-4 py-2 bg-white rounded-xl border border-gray-200 shadow-sm">
                        <span class="text-sm font-medium text-gray-700">📚 Praktikum</span>
                    </div>
                    <div class="px-4 py-2 bg-white rounded-xl border border-gray-200 shadow-sm">
                        <span class="text-sm font-medium text-gray-700">🔬 Penelitian</span>
                    </div>
                    <div class="px-4 py-2 bg-white rounded-xl border border-gray-200 shadow-sm">
                        <span class="text-sm font-medium text-gray-700">💡 Konsultasi</span>
                    </div>
                </div>
            </div>
        </div>

        <?php if ($layanan_list && count($layanan_list) > 0): ?>
            <!-- Layanan Grid - Two Column Layout -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <?php 
                // Icon mapping for different service types
                $icons = ['fa-clock', 'fa-headset', 'fa-shield-alt', 'fa-laptop-code', 'fa-network-wired', 'fa-cogs'];
                foreach ($layanan_list as $index => $layanan): 
                    $icon = $icons[$index % count($icons)];
                    $colors = [
                        ['bg' => 'bg-blue-50', 'icon' => 'text-blue-600', 'hover' => 'group-hover:bg-blue-600'],
                        ['bg' => 'bg-orange-50', 'icon' => 'text-orange-600', 'hover' => 'group-hover:bg-orange-600'],
                        ['bg' => 'bg-purple-50', 'icon' => 'text-purple-600', 'hover' => 'group-hover:bg-purple-600'],
                        ['bg' => 'bg-emerald-50', 'icon' => 'text-emerald-600', 'hover' => 'group-hover:bg-emerald-600'],
                        ['bg' => 'bg-pink-50', 'icon' => 'text-pink-600', 'hover' => 'group-hover:bg-pink-600'],
                        ['bg' => 'bg-indigo-50', 'icon' => 'text-indigo-600', 'hover' => 'group-hover:bg-indigo-600']
                    ];
                    $color = $colors[$index % count($colors)];
                ?>
                    <div class="group bg-white rounded-2xl p-6 border-2 border-gray-100 hover:border-orange-200 hover:shadow-xl transition-all duration-300"
                        data-aos="fade-up"
                        data-aos-delay="<?php echo ($index * 50); ?>">
                        
                        <div class="flex items-start gap-6">
                            <!-- Icon -->
                            <div class="flex-shrink-0 w-16 h-16 <?php echo $color['bg']; ?> rounded-2xl flex items-center justify-center <?php echo $color['hover']; ?> transition-colors duration-300">
                                <i class="fas <?php echo $icon; ?> <?php echo $color['icon']; ?> text-2xl group-hover:text-white transition-colors duration-300"></i>
                            </div>

                            <!-- Content -->
                            <div class="flex-1 min-w-0">
                                <!-- Title -->
                                <h3 class="text-xl font-semibold text-[#1B2D62] mb-2 group-hover:text-orange-600 transition-colors duration-300">
                                    <?php echo htmlspecialchars($layanan['nama_layanan']); ?>
                                </h3>

                                <!-- Description -->
                                <p class="text-base text-gray-600 leading-relaxed">
                                    <?php echo htmlspecialchars($layanan['deskripsi'] ?? 'Layanan profesional dari Laboratorium Network & Cyber Security.'); ?>
                                </p>
                            </div>

                            <!-- Arrow -->
                            <div class="flex-shrink-0 opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                                <i class="fas fa-arrow-right text-orange-500 text-xl"></i>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
                            
        <?php else: ?>
            <!-- Empty State -->
            <div class="text-center py-16 bg-white rounded-3xl border-2 border-dashed border-gray-200" data-aos="fade-up">
                <i class="fas fa-inbox text-gray-400 text-6xl mb-4"></i>
                <h3 class="text-2xl font-bold text-gray-900 mb-2">Belum Ada Layanan</h3>
                <p class="text-gray-600">Layanan akan segera tersedia. Silakan kunjungi kembali.</p>
            </div>
        <?php endif; ?>

    </div>
</section>

<!-- Sarana & Prasarana Section -->
<section class="relative py-24 sm:py-32 overflow-hidden px-4">
    <div class="absolute inset-0 bg-gradient-to-b from-slate-50 via-orange-50/20 to-white"></div>
    
    <div class="relative mx-auto max-w-7xl">
        
        <!-- Section Header - Left Aligned -->
        <div class="mb-16 mx-auto text-center" data-aos="fade-up">
            <div class="inline-flex items-center gap-2.5 px-5 py-2.5 bg-white/80 backdrop-blur-sm border border-orange-200/60 rounded-full text-orange-600 font-semibold text-sm mb-6 shadow-lg shadow-orange-100/50">
                <span class="w-2 h-2 bg-orange-500 rounded-full animate-pulse"></span>
                <span class="tracking-wide">SARANA & PRASARANA</span>
            </div>

            <h2 class="text-4xl md:text-5xl lg:text-6xl font-medium text-[#1B2D62] leading-[1.1] tracking-tight mb-6">
                Perangkat<br class="hidden sm:block">
                <span class="bg-gradient-to-r from-[#1B2D62] via-[#2C4AA4] to-orange-500 bg-clip-text text-transparent">Laboratorium</span>
            </h2>
            
            <p class="text-lg md:text-xl text-gray-600 max-w-2xl mx-auto leading-relaxed">
                Perangkat dan fasilitas yang tersedia untuk mendukung kegiatan praktikum dan penelitian dengan standar industri terkini.
            </p>
        </div>

        <?php if ($sarana_list && count($sarana_list) > 0): ?>
            <!-- Sarana Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 lg:gap-8">
                <?php foreach ($sarana_list as $index => $sarana): ?>
                    <!-- Sarana Card -->
                    <div class="group relative bg-white rounded-3xl overflow-hidden transition-all duration-500 hover:shadow-xl hover:shadow-orange-200/50" data-aos="fade-up" data-aos-delay="<?php echo ($index * 100); ?>">
                        
                        <!-- Border -->
                        <div class="absolute inset-0 rounded-3xl border-2 border-gray-100 group-hover:border-orange-300 transition-colors duration-300 z-10"></div>
                        
                        <!-- Image -->
                        <div class="relative h-48 bg-gradient-to-br from-gray-200 to-gray-300 overflow-hidden">
                            <?php
                            $gambar_url = !empty($sarana['gambar']) && file_exists("../uploads" . $sarana['gambar'])
                                ? UPLOAD_URL . $sarana['gambar']
                                : ASSETS_URL . '/img/no-image.png';
                            ?>
                            <img src="<?php echo $gambar_url; ?>" alt="<?php echo htmlspecialchars($sarana['nama_sarana']); ?>" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110">
                            
                            <!-- Badges -->
                            <div class="absolute top-4 right-4 flex flex-col gap-2">
                                <!-- Quantity -->
                                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-gray-900/90 text-white text-xs font-semibold rounded-lg backdrop-blur-sm shadow-lg">
                                    <i class="fas fa-cubes"></i><?php echo htmlspecialchars($sarana['jumlah'] ?? '1'); ?> Unit
                                </span>
                                
                                <!-- Condition -->
                                <?php 
                                $kondisi = strtolower($sarana['kondisi'] ?? 'baik');
                                $kondisi_class = $kondisi === 'baik' ? 'bg-emerald-500' : ($kondisi === 'rusak berat' ? 'bg-red-500' : 'bg-amber-500');
                                ?>
                                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 <?php echo $kondisi_class; ?> text-white text-xs font-semibold rounded-lg backdrop-blur-sm shadow-lg">
                                    <i class="fas fa-circle text-xs"></i><?php echo htmlspecialchars(ucfirst($sarana['kondisi'] ?? 'Baik')); ?>
                                </span>
                            </div>
                        </div>

                        <!-- Content -->
                        <div class="relative p-6 z-10">
                            
                            <!-- Title -->
                            <h3 class="text-lg font-medium text-[#1B2D62] mb-2 group-hover:text-orange-600 transition-colors duration-300">
                                <?php echo htmlspecialchars($sarana['nama_sarana']); ?>
                            </h3>

                            <!-- Description -->
                            <p class="text-sm text-gray-600 leading-relaxed mb-4">
                                <?php echo htmlspecialchars($sarana['deskripsi'] ?? 'Perangkat laboratorium profesional.'); ?>
                            </p>

                            <!-- Specifications -->
                            <?php if (!empty($sarana['spesifikasi'])): ?>
                            <div class="bg-gray-50 rounded-xl p-4 border border-gray-200">
                                <p class="text-xs font-semibold text-gray-600 uppercase tracking-wide mb-2">
                                    <i class="fas fa-microchip mr-1"></i>Spesifikasi
                                </p>
                                <p class="text-xs text-gray-700 line-clamp-2">
                                    <?php echo htmlspecialchars($sarana['spesifikasi']); ?>
                                </p>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <!-- Empty State -->
            <div class="text-center py-16 bg-white rounded-3xl border-2 border-dashed border-gray-200" data-aos="fade-up">
                <i class="fas fa-server text-gray-400 text-6xl mb-4"></i>
                <h3 class="text-2xl font-bold text-gray-900 mb-2">Belum Ada Sarana</h3>
                <p class="text-gray-600">Data sarana akan segera tersedia. Silakan kunjungi kembali.</p>
            </div>
        <?php endif; ?>

    </div>
</section>

<!-- Konsultatif / FAQ Section -->
<section class="relative py-24 sm:py-32 overflow-hidden px-4">
    <div class="absolute inset-0 bg-gradient-to-b from-slate-50 via-white to-orange-50/30"></div>
    
    <div class="relative mx-auto max-w-7xl">
        
        <!-- Two Column Layout -->
        <div class="grid lg:grid-cols-5 gap-12 lg:gap-16 items-start">
            
            <!-- Left Column: Header -->
            <div class="lg:col-span-2" data-aos="fade-right">
                <div class="inline-flex items-center gap-2.5 px-5 py-2.5 bg-white/80 backdrop-blur-sm border border-orange-200/60 rounded-full text-orange-600 font-semibold text-sm mb-6 shadow-lg shadow-orange-100/50">
                    <span class="w-2 h-2 bg-orange-500 rounded-full animate-pulse"></span>
                    <span class="tracking-wide">LAYANAN KONSULTATIF</span>
                </div>

                <h2 class="text-4xl md:text-5xl font-medium text-[#1B2D62] leading-[1.1] tracking-tight mb-6">
                    Pertanyaan<br>
                    <span class="bg-gradient-to-r from-[#1B2D62] via-[#2C4AA4] to-orange-500 bg-clip-text text-transparent">yang Sering Diajukan</span>
                </h2>
                
                <p class="text-lg text-gray-600 leading-relaxed">
                    Temukan jawaban atas pertanyaan umum seputar layanan penelitian dan konsultasi laboratorium kami di bawah ini.
                </p>
            </div>
            
            <!-- Right Column: FAQ Items -->
            <div class="lg:col-span-3">
                
                <?php
                $faqList = executeQuery("SELECT pertanyaan, jawaban FROM konsultatif WHERE jawaban IS NOT NULL AND jawaban != '' ORDER BY id DESC LIMIT 5");
                if ($faqList === false) $faqList = [];
                ?>

                <?php if (!empty($faqList)): ?>
                    <div class="space-y-4" data-accordion-container data-accordion-mode="exclusive" data-aos="fade-up">
                        <?php foreach ($faqList as $index => $faq): ?>
                            
                            <div class="group">
                                <div class="relative bg-white/80 backdrop-blur-sm rounded-2xl border-2 border-gray-100 overflow-hidden transition-all duration-300 hover:border-orange-200 hover:shadow-xl hover:shadow-orange-100/50">
                                    
                                    <!-- Question Header -->
                                    <div class="flex items-start gap-4 w-full p-6 text-left cursor-pointer transition-colors duration-300"
                                         data-accordion-toggle
                                         data-accordion-target="#faq-layanan-<?php echo $index; ?>"
                                         aria-expanded="false">
                                        <!-- Number Badge -->
                                        <div class="flex-shrink-0 w-10 h-10 bg-gradient-to-br from-orange-100 to-orange-50 rounded-xl flex items-center justify-center font-medium text-orange-600 group-hover:from-orange-500 group-hover:to-orange-600 group-hover:text-white transition-all duration-300">
                                            <?php echo str_pad($index + 1, 2, '0', STR_PAD_LEFT); ?>
                                        </div>
                                        
                                        <!-- Question Text -->
                                        <div class="flex-1 pt-1.5">
                                            <span class="text-lg font-semibold text-[#1B2D62] group-hover:text-orange-600 transition-colors duration-300 leading-tight">
                                                <?= htmlspecialchars($faq['pertanyaan']) ?>
                                            </span>
                                        </div>
                                        
                                        <!-- Toggle Icon -->
                                        <div class="flex-shrink-0 w-10 h-10 bg-gray-50 rounded-xl flex items-center justify-center transition-all duration-300 group-hover:bg-orange-50">
                                            <svg data-accordion-icon-close xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5 text-gray-400 transition-all duration-300 group-hover:text-orange-500">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                                            </svg>
                                            <svg data-accordion-icon-open xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5 text-orange-500 transition-all duration-300">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14" />
                                            </svg>
                                        </div>
                                    </div>
                                    
                                    <!-- Answer Content -->
                                    <div id="faq-layanan-<?php echo $index; ?>" class="overflow-hidden transition-all duration-500 ease-in-out max-h-0">
                                        <div class="px-6 pb-6 pl-20">
                                            <div class="relative pl-6 border-l-2 border-orange-200">
                                                <p class="text-gray-600 leading-relaxed">
                                                    <?= htmlspecialchars($faq['jawaban']) ?>
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                    
                                </div>
                            </div>

                        <?php endforeach; ?>
                    </div>
                    
                <?php else: ?>
                    <div class="text-center py-16 bg-white/80 backdrop-blur-sm rounded-3xl border-2 border-dashed border-gray-200" data-aos="fade-up">
                        <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-6">
                            <i class="fas fa-question-circle text-gray-400 text-3xl"></i>
                        </div>
                        <h3 class="text-xl font-medium text-gray-700 mb-2">Belum Ada FAQ</h3>
                        <p class="text-gray-500">Pertanyaan yang sering diajukan akan muncul di sini.</p>
                    </div>
                <?php endif; ?>

            </div>
            
        </div>

    </div>
</section>

<!-- Contact CTA Section -->
<section class="relative px-4 pb-24 overflow-hidden">
        <!-- Background Decorative Elements -->
        <div class="absolute inset-0 pointer-events-none">
            <div class="absolute top-0 left-1/4 w-96 h-96 bg-orange-200/20 rounded-full blur-3xl"></div>
            <div class="absolute bottom-0 right-1/4 w-80 h-80 bg-blue-200/20 rounded-full blur-3xl"></div>
        </div>
        
        <div data-aos="fade-up" class="relative max-w-7xl mx-auto">
            <!-- Main CTA Card -->
            <div class="relative overflow-hidden rounded-3xl">
                <!-- Gradient Background -->
                <div class="absolute inset-0 bg-gradient-to-br from-[#1B2D62] via-[#243a73] to-[#2C4AA4]"></div>
                
                <!-- Animated Background Pattern -->
                <div class="absolute inset-0 opacity-10">
                    <div class="absolute inset-0" style="background-image: url('data:image/svg+xml,%3Csvg width=%2260%22 height=%2260%22 viewBox=%220 0 60 60%22 xmlns=%22http://www.w3.org/2000/svg%22%3E%3Cg fill=%22none%22 fill-rule=%22evenodd%22%3E%3Cg fill=%22%23ffffff%22 fill-opacity=%221%22%3E%3Cpath d=%22M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z%22/%3E%3C/g%3E%3C/g%3E%3C/svg%3E');"></div>
                </div>
                
                <!-- Floating Decorative Elements -->
                <div class="absolute top-10 left-10 w-20 h-20 bg-white/5 rounded-2xl rotate-12 hidden lg:block"></div>
                <div class="absolute bottom-10 right-10 w-32 h-32 bg-orange-500/10 rounded-full hidden lg:block"></div>
                <div class="absolute top-1/2 right-20 w-16 h-16 bg-white/5 rounded-xl -rotate-12 hidden lg:block"></div>
                
                <!-- Glowing Orbs -->
                <div class="absolute -top-20 -right-20 w-64 h-64 bg-orange-500/20 rounded-full blur-3xl"></div>
                <div class="absolute -bottom-20 -left-20 w-64 h-64 bg-blue-400/20 rounded-full blur-3xl"></div>
                
                <!-- Content -->
                <div class="relative px-6 py-16 sm:px-12 sm:py-20 lg:py-24">
                    <div class="max-w-4xl mx-auto text-center">
                        
                        <!-- Badge -->
                        <div class="inline-flex items-center gap-2.5 px-5 py-2.5 bg-white/10 backdrop-blur-sm border border-white/20 rounded-full text-white font-semibold text-sm mb-8" data-aos="fade-up">
                            <span class="w-2 h-2 bg-orange-400 rounded-full animate-pulse"></span>
                            <span class="tracking-wide">HUBUNGI KAMI</span>
                        </div>
                        
                        <!-- Heading -->
                        <h2 class="text-4xl md:text-5xl lg:text-6xl font-medium text-white mb-6 leading-tight" data-aos="fade-up">
                            Butuh Bantuan<br class="hidden sm:block">
                            <span class="bg-gradient-to-r from-orange-400 via-orange-300 to-yellow-300 bg-clip-text text-transparent">Lebih Lanjut?</span>
                        </h2>
                        
                        <!-- Description -->
                        <p class="text-lg md:text-xl text-white/80 mb-12 leading-relaxed max-w-2xl mx-auto" data-aos="fade-up">
                            Jika Anda memiliki pertanyaan atau membutuhkan informasi lebih lanjut tentang layanan kami, jangan ragu untuk menghubungi tim kami.
                        </p>
                        
                        <!-- CTA Buttons -->
                        <div class="flex flex-col sm:flex-row gap-4 justify-center" data-aos="fade-up">
                            <a href="./kontak.php" class="group inline-flex items-center justify-center gap-3 bg-gradient-to-r from-orange-500 to-orange-600 text-white font-medium text-base px-8 py-4 rounded-xl shadow-lg shadow-orange-500/30 transition-all duration-300 hover:shadow-xl hover:shadow-orange-500/40 hover:scale-[1.02] active:scale-[0.98]">
                                <i class="fas fa-paper-plane transition-transform duration-300 group-hover:-translate-y-0.5 group-hover:translate-x-0.5"></i>
                                <span>Hubungi Kami</span>
                            </a>
                        </div>
                        
                        <!-- Trust Indicators -->
                        <div class="mt-12 pt-10 border-t border-white/10" data-aos="fade-up">
                            <div class="flex flex-wrap justify-center items-center gap-8 md:gap-12">
                                <div class="flex items-center gap-3 text-white/70">
                                    <div class="w-10 h-10 bg-white/10 rounded-xl flex items-center justify-center">
                                        <i class="fas fa-clock text-orange-400"></i>
                                    </div>
                                    <span class="text-sm font-medium">Respon Cepat</span>
                                </div>
                                <div class="flex items-center gap-3 text-white/70">
                                    <div class="w-10 h-10 bg-white/10 rounded-xl flex items-center justify-center">
                                        <i class="fas fa-shield-alt text-orange-400"></i>
                                    </div>
                                    <span class="text-sm font-medium">Konsultasi Gratis</span>
                                </div>
                                <div class="flex items-center gap-3 text-white/70">
                                    <div class="w-10 h-10 bg-white/10 rounded-xl flex items-center justify-center">
                                        <i class="fas fa-users text-orange-400"></i>
                                    </div>
                                    <span class="text-sm font-medium">Tim Profesional</span>
                                </div>
                            </div>
                        </div>
                        
                    </div>
                </div>
                
            </div>
        </div>
    </section>

<?php
// Include public footer
require_once __DIR__ . '/../includes/footer.php';
?>