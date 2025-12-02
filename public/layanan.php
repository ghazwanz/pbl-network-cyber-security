<?php

/**
 * Public Layanan Page
 * File: public/layanan.php
 * Design Reference: Modern & Professional with Navy Blue Theme (Consistent with Arsip)
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

<!-- Hero Section -->
<section class="relative lg:py-44 py-32 bg-gradient-to-br from-[#F8FCFF] via-white to-blue-50">
    <div class="relative max-w-7xl mx-auto">
        <div class="absolute top-0 left-0 w-96 h-96 bg-blue-100 rounded-full mix-blend-multiply filter blur-2xl opacity-30 animate-pulse"></div>
        <div class="absolute bottom-0 right-0 w-96 h-96 bg-orange-100 rounded-full mix-blend-multiply filter blur-2xl opacity-30 animate-pulse" style="animation-delay: 1s;"></div>
        <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[500px] h-[500px] bg-gradient-to-r from-purple-100 to-pink-100 rounded-full mix-blend-multiply filter blur-3xl opacity-20 animate-pulse" style="animation-delay: 2s;"></div>
    
        <!-- Geometric Floating Shapes - Left Side (Different from Arsip) -->
        <div class="absolute left-4 md:left-8 lg:left-16 top-20 md:top-28 w-16 h-16 md:w-20 md:h-20 border-4 border-orange-300 rounded-full opacity-40 animate-spin" style="animation-duration: 20s;"></div>
    
        <div class="absolute left-12 md:left-28 lg:left-44 top-40 md:top-52 w-8 h-8 md:w-10 md:h-10 bg-gradient-to-br from-blue-400 to-blue-600 rotate-45 shadow-lg opacity-70 animate-pulse" style="animation-duration: 3s;"></div>
    
        <div class="absolute left-6 md:left-20 lg:left-36 bottom-28 md:bottom-36 w-14 h-14 md:w-18 md:h-18 border-4 border-dashed border-blue-300 rounded-2xl opacity-50 animate-spin" style="animation-duration: 15s; animation-direction: reverse;"></div>
    
        <div class="hidden md:block absolute left-2 lg:left-10 bottom-52 lg:bottom-60 w-6 h-6 lg:w-8 lg:h-8 bg-orange-400 rounded-full opacity-60 animate-ping" style="animation-duration: 2s;"></div>
    
        <div class="absolute left-16 md:left-36 lg:left-56 top-64 md:top-72 w-3 h-3 md:w-4 md:h-4 bg-green-500 rounded-full opacity-80 animate-bounce" style="animation-duration: 2.5s;"></div>
    
        <!-- Geometric Floating Shapes - Right Side (Different from Arsip) -->
        <div class="absolute right-4 md:right-12 lg:right-24 top-24 md:top-32 w-12 h-12 md:w-16 md:h-16 bg-gradient-to-br from-orange-400 to-red-500 rounded-xl rotate-12 shadow-xl opacity-60 animate-bounce" style="animation-duration: 4s;"></div>
    
        <div class="absolute right-16 md:right-32 lg:right-48 top-44 md:top-56 w-20 h-20 md:w-24 md:h-24 border-4 border-purple-300 rounded-full opacity-30 animate-spin" style="animation-duration: 25s;"></div>
    
        <div class="absolute right-8 md:right-20 lg:right-36 bottom-32 md:bottom-40 w-10 h-10 md:w-14 md:h-14 bg-gradient-to-br from-green-400 to-teal-500 rounded-2xl -rotate-12 shadow-lg opacity-70 animate-pulse" style="animation-duration: 3.5s;"></div>
    
        <div class="hidden md:block absolute right-2 lg:right-8 bottom-56 lg:bottom-64 w-5 h-5 lg:w-6 lg:h-6 bg-blue-500 rounded-full opacity-50 animate-ping" style="animation-duration: 3s;"></div>
    
        <div class="absolute right-24 md:right-44 lg:right-64 top-72 md:top-80 w-6 h-6 md:w-8 md:h-8 border border-orange-400 rotate-45 opacity-60 animate-spin" style="animation-duration: 8s;"></div>
    
        <!-- Decorative Lines -->
        <div class="hidden lg:block absolute left-0 top-1/3 w-32 h-1 bg-gradient-to-r from-transparent via-orange-300 to-transparent opacity-50"></div>
        <div class="hidden lg:block absolute right-0 bottom-1/3 w-32 h-1 bg-gradient-to-r from-transparent via-blue-300 to-transparent opacity-50"></div>
    </div>

    <div class="container mx-auto px-4 relative z-[5]">
        <div class="max-w-5xl mx-auto text-center">
            <!-- Badge -->
            <div class="inline-flex items-center gap-2 px-5 py-2.5 bg-white border border-orange-200 rounded-full text-orange-600 font-bold mb-6 shadow-lg" data-aos="fade-up">
                <i class="fas fa-concierge-bell text-orange-500"></i>
                <span class="text-sm tracking-wide">LAYANAN LABORATORIUM</span>
            </div>

            <!-- Heading -->
            <h1 class="text-5xl md:text-6xl font-medium text-[#1B2D62] mb-6 leading-tight" data-aos="fade-up" data-aos-delay="100">
                Layanan & Fasilitas
            </h1>

            <!-- Subtitle -->
            <p class="text-xl md:text-2xl text-gray-600 leading-relaxed mb-10" data-aos="fade-up" data-aos-delay="200">
                Jelajahi layanan dan fasilitas yang kami sediakan untuk<br class="hidden md:block">
                mendukung kebutuhan praktikum, penelitian, dan pengembangan
            </p>

            <!-- Stats Bar - Simple Style -->
            <div class="flex flex-wrap justify-center gap-8 md:gap-16" data-aos="fade-up" data-aos-delay="300">
                <div class="text-center">
                    <div class="w-14 h-14 bg-blue-100 rounded-2xl flex items-center justify-center mx-auto mb-3">
                        <i class="fas fa-cogs text-blue-600 text-xl"></i>
                    </div>
                    <p class="text-3xl font-bold text-[#1B2D62] mb-1"><?php echo number_format($count_layanan); ?></p>
                    <p class="text-sm text-gray-500 font-medium">Total Layanan</p>
                </div>

                <div class="text-center">
                    <div class="w-14 h-14 bg-orange-100 rounded-2xl flex items-center justify-center mx-auto mb-3">
                        <i class="fas fa-server text-orange-600 text-xl"></i>
                    </div>
                    <p class="text-3xl font-bold text-[#1B2D62] mb-1"><?php echo number_format($count_sarana); ?></p>
                    <p class="text-sm text-gray-500 font-medium">Sarana & Prasarana</p>
                </div>

                <div class="text-center">
                    <div class="w-14 h-14 bg-green-100 rounded-2xl flex items-center justify-center mx-auto mb-3">
                        <i class="fas fa-comments text-green-600 text-xl"></i>
                    </div>
                    <p class="text-3xl font-bold text-[#1B2D62] mb-1"><?php echo number_format($count_konsultatif); ?></p>
                    <p class="text-sm text-gray-500 font-medium">FAQ Terjawab</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Layanan Section -->
<section class="py-20 bg-white">
    <div class="container mx-auto px-4">
        <div class="max-w-7xl mx-auto">

            <!-- Section Header -->
            <div class="text-center mb-16" data-aos="fade-up">
                <div class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-orange-200 rounded-full text-orange-600 font-semibold mb-4">
                    <i class="fas fa-star text-orange-500"></i>
                    <span class="text-sm">APA YANG KAMI TAWARKAN</span>
                </div>
                <h2 class="text-4xl md:text-5xl font-medium text-[#1B2D62] mb-4">
                    Layanan Kami
                </h2>
                <p class="text-lg text-gray-600 max-w-2xl mx-auto">
                    Berbagai layanan profesional untuk mendukung kegiatan akademik dan penelitian Anda
                </p>
            </div>

            <?php if ($layanan_list && count($layanan_list) > 0): ?>
                <!-- Layanan Grid - Simple Card Style -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <?php 
                    // Icon mapping for different service types
                    $icons = ['fa-clock', 'fa-headset', 'fa-shield-alt', 'fa-laptop-code', 'fa-network-wired', 'fa-cogs'];
                    foreach ($layanan_list as $index => $layanan): 
                    $icon = $icons[$index % count($icons)];
                    ?>
                        <div class="bg-gray-100 rounded-2xl p-6 hover:bg-white hover:shadow-xl transition-all duration-300 group"
                            data-aos="fade-up"
                            data-aos-delay="<?php echo ($index * 100); ?>">

                            <!-- Icon -->
                            <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center mb-5 group-hover:bg-blue-500 transition-colors duration-300">
                                <i class="fas <?php echo $icon; ?> text-blue-600 text-lg group-hover:text-white transition-colors duration-300"></i>
                            </div>

                            <!-- Title -->
                            <h3 class="text-lg font-medium text-[#1B2D62] mb-3">
                                <?php echo htmlspecialchars($layanan['nama_layanan']); ?>
                            </h3>

                            <!-- Description -->
                            <p class="text-sm text-gray-500 leading-relaxed">
                                <?php echo htmlspecialchars($layanan['deskripsi'] ?? 'Layanan profesional dari Laboratorium Network & Cyber Security.'); ?>
                            </p>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <!-- Empty State -->
                <div class="text-center py-16" data-aos="fade-up">
                    <div class="w-24 h-24 bg-gradient-to-br from-gray-100 to-gray-200 rounded-full flex items-center justify-center mx-auto mb-6">
                        <i class="fas fa-inbox text-gray-400 text-4xl"></i>
                    </div>
                    <h3 class="text-2xl font-medium text-[#1B2D62] mb-2">Belum Ada Layanan</h3>
                    <p class="text-gray-600">Layanan akan segera tersedia. Silakan kunjungi kembali.</p>
                </div>
            <?php endif; ?>

        </div>
    </div>
</section>

<!-- Sarana & Prasarana Section -->
<section class="py-20 bg-[#F8FCFF]">
    <div class="container mx-auto px-4">
        <div class="max-w-7xl mx-auto">

            <!-- Section Header -->
            <div class="text-center mb-16" data-aos="fade-up">
                <div class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-orange-200 rounded-full text-orange-600 font-semibold mb-4">
                    <i class="fas fa-server text-orange-500"></i>
                    <span class="text-sm">FASILITAS LABORATORIUM</span>
                </div>
                <h2 class="text-4xl md:text-5xl font-medium text-[#1B2D62] mb-4">
                    Sarana & Prasarana
                </h2>
                <p class="text-lg text-gray-600 max-w-2xl mx-auto">
                    Perangkat dan fasilitas yang tersedia untuk mendukung kegiatan praktikum dan penelitian
                </p>
            </div>

            <?php if ($sarana_list && count($sarana_list) > 0): ?>
                <!-- Sarana Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                    <?php foreach ($sarana_list as $index => $sarana): ?>
                        <div class="group bg-white border border-gray-200 rounded-2xl overflow-hidden hover:border-green-500 hover:shadow-2xl transition-all duration-300"
                            data-aos="fade-up"
                            data-aos-delay="<?php echo ($index * 100); ?>">

                            <!-- Card Image -->
                            <div class="relative h-48 bg-gradient-to-br from-gray-100 to-gray-200 overflow-hidden">
                                <?php
                                $gambar_url = !empty($sarana['gambar']) && file_exists("../uploads" . $sarana['gambar'])
                                    ? UPLOAD_URL . $sarana['gambar']
                                    : ASSETS_URL . '/img/no-image.png';
                                ?>
                                <img src="<?php echo $gambar_url; ?>" alt="<?php echo htmlspecialchars($sarana['nama_sarana']); ?>" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">

                                <!-- Quantity Badge -->
                                <span class="absolute top-4 right-4 px-3 py-1.5 text-xs font-bold rounded-lg shadow-lg bg-[#1B2D62] text-white">
                                    <i class="fas fa-cubes mr-1"></i><?php echo htmlspecialchars($sarana['jumlah'] ?? '1'); ?> Unit
                                </span>

                                <!-- Condition Badge -->
                                <?php 
                                $kondisi = strtolower($sarana['kondisi'] ?? 'baik');
                                $kondisi_class = $kondisi === 'baik' ? 'bg-green-500' : ($kondisi === 'rusak' ? 'bg-red-500' : 'bg-yellow-500');
                                ?>
                                <span class="absolute top-4 left-4 px-3 py-1.5 text-xs font-bold rounded-lg shadow-lg <?php echo $kondisi_class; ?> text-white">
                                    <?php echo htmlspecialchars(ucfirst($sarana['kondisi'] ?? 'Baik')); ?>
                                </span>
                            </div>

                            <!-- Card Content -->
                            <div class="p-6">
                                <!-- Title -->
                                <h3 class="text-xl font-medium text-[#1B2D62] mb-3 group-hover:text-green-600 transition-colors">
                                    <?php echo htmlspecialchars($sarana['nama_sarana']); ?>
                                </h3>

                                <!-- Description -->
                                <p class="text-sm text-gray-600 leading-relaxed line-clamp-2 mb-4">
                                    <?php echo htmlspecialchars($sarana['deskripsi'] ?? 'Perangkat laboratorium.'); ?>
                                </p>

                                <!-- Specifications -->
                                <?php if (!empty($sarana['spesifikasi'])): ?>
                                <div class="bg-gray-50 rounded-xl p-4">
                                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">
                                        <i class="fas fa-microchip mr-1"></i>Spesifikasi
                                    </p>
                                    <p class="text-sm text-gray-700 leading-relaxed line-clamp-2">
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
                <div class="text-center py-16 bg-white rounded-2xl border border-gray-200" data-aos="fade-up">
                    <div class="w-24 h-24 bg-gradient-to-br from-orange-100 to-orange-200 rounded-full flex items-center justify-center mx-auto mb-6">
                        <i class="fas fa-server text-orange-400 text-4xl"></i>
                    </div>
                    <h3 class="text-2xl font-medium text-[#1B2D62] mb-2">Belum Ada Sarana</h3>
                    <p class="text-gray-600">Data sarana akan segera tersedia. Silakan kunjungi kembali.</p>
                </div>
            <?php endif; ?>

        </div>
    </div>
</section>

<!-- Konsultatif / FAQ Section -->
<section class="py-20 pb-32 bg-[#F8FCFF]">
    <div class="mx-auto px-4">
        <div class="max-w-4xl mx-auto">

            <!-- Section Header -->
            <div class="text-center mb-16" data-aos="fade-up">
                <div class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-orange-200 rounded-full text-orange-600 font-semibold mb-4">
                    <i class="fas fa-question-circle text-orange-500"></i>
                    <span class="text-sm">SERING DITANYAKAN</span>
                </div>
                <h2 class="text-4xl md:text-5xl font-medium text-[#1B2D62] mb-4">
                    Layanan Konsultatif
                </h2>
                <p class="text-lg text-gray-600 max-w-2xl mx-auto">
                    Temukan jawaban atas pertanyaan yang sering diajukan seputar layanan laboratorium kami
                </p>
            </div>

            <?php if ($konsultatif_list && count($konsultatif_list) > 0): ?>
                <!-- FAQ Accordion - Material Tailwind Style -->
                <div class="bg-white border border-gray-200 rounded-2xl overflow-hidden" data-aos="fade-up" data-aos-delay="100" data-accordion-container data-accordion-mode="exclusive">
                    <?php foreach ($konsultatif_list as $index => $faq): ?>
                        <div
                            class="flex items-center justify-between w-full px-6 py-5 text-left cursor-pointer hover:bg-orange-50/20 transition-colors duration-300 <?php echo $index > 0 ? 'border-t border-gray-200' : ''; ?>"
                            data-accordion-toggle
                            data-accordion-target="#faq-<?php echo $index; ?>"
                            aria-expanded="false">
                            <span class="text-lg font-semibold text-[#1B2D62] pr-4">
                                <?php echo htmlspecialchars($faq['pertanyaan']); ?>
                            </span>
                            <div class="w-10 h-10 bg-orange-50 rounded-xl flex items-center justify-center flex-shrink-0 transition-all duration-300">
                                <svg data-accordion-icon-close xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                                </svg>
                                <svg data-accordion-icon-open xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14" />
                                </svg>
                            </div>
                        </div>
                        <div id="faq-<?php echo $index; ?>" class="overflow-hidden transition-all duration-300">
                            <div class="px-6 pb-6 pt-0">
                                <div class="h-px bg-gray-200 mb-4"></div>
                                <p class="text-gray-600 leading-relaxed">
                                    <?php echo nl2br(htmlspecialchars($faq['jawaban'])); ?>
                                </p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <!-- Empty State -->
                <div class="text-center py-16 bg-white rounded-2xl border border-gray-200" data-aos="fade-up">
                    <div class="w-24 h-24 bg-gradient-to-br from-blue-100 to-blue-200 rounded-full flex items-center justify-center mx-auto mb-6">
                        <i class="fas fa-comments text-blue-400 text-4xl"></i>
                    </div>
                    <h3 class="text-2xl font-medium text-[#1B2D62] mb-2">Belum Ada FAQ</h3>
                    <p class="text-gray-600 mb-6">Pertanyaan yang sering ditanyakan akan ditampilkan di sini.</p>
                    <a href="kontak.php" class="inline-flex items-center gap-2 bg-gradient-to-r from-orange-500 to-orange-600 text-white font-bold px-6 py-3 rounded-xl hover:shadow-lg hover:scale-105 transition-all duration-300">
                        <i class="fas fa-paper-plane"></i>
                        <span>Ajukan Pertanyaan</span>
                    </a>
                </div>
            <?php endif; ?>

            <!-- CTA Box -->
            <!-- <div class="mt-12 bg-gradient-to-r from-[#1B2D62] to-[#2C4AA4] rounded-2xl p-8 md:p-10 text-center" data-aos="fade-up" data-aos-delay="200">
                <h3 class="text-2xl md:text-3xl font-medium text-white mb-4">
                    Masih Punya Pertanyaan?
                </h3>
                <p class="text-blue-100 mb-6">
                    Jangan ragu untuk menghubungi kami. Tim kami siap membantu Anda.
                </p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <a href="kontak.php" class="inline-flex items-center justify-center gap-2 bg-white text-[#1B2D62] font-bold px-8 py-4 rounded-xl hover:bg-orange-50 hover:scale-105 transition-all duration-300">
                        <i class="fas fa-envelope"></i>
                        <span>Hubungi Kami</span>
                    </a>
                    <a href="mailto:labncs@mail.com" class="inline-flex items-center justify-center gap-2 bg-transparent border border-white text-white font-bold px-8 py-4 rounded-xl hover:bg-white/10 transition-all duration-300">
                        <i class="fas fa-paper-plane"></i>
                        <span>Email Langsung</span>
                    </a>
                </div>
            </div> -->

        </div>
    </div>
</section>

<!-- Contact CTA Section -->
<section class="px-4 pb-20">
    <div data-aos="fade-up" class="sm:py-20 py-16 bg-gradient-to-r from-[#1B2D62] to-[#2C4AA4] mx-auto rounded-2xl max-w-7xl">
        <div class="mx-auto sm:px-12 px-6">
                <div class="max-w-4xl mx-auto text-center">
                    <div class="inline-flex items-center gap-2 px-4 py-2 bg-white/10 border border-white/20 rounded-full text-white font-semibold mb-6">
                        <i class="fas fa-envelope text-orange-400"></i>
                        <span class="text-sm">HUBUNGI KAMI</span>
                    </div>
                    
                    <h2 class="text-4xl md:text-5xl font-medium text-white mb-6 font-inter">
                        Butuh Bantuan Lebih Lanjut?
                    </h2>
                    
                    <p class="text-xl text-white/80 mb-10 font-inter leading-relaxed">
                        Jika Anda memiliki pertanyaan atau membutuhkan informasi lebih lanjut<br class="hidden md:block">
                        tentang layanan kami, jangan ragu untuk menghubungi tim kami.
                    </p>
                    
                    <div class="flex flex-col sm:flex-row gap-4 justify-center">
                        <a href="./kontak.php" class="inline-flex items-center justify-center gap-2 bg-gradient-to-r from-orange-500 to-orange-600 text-white font-bold px-8 py-4 rounded-xl hover:shadow-2xl hover:scale-105 transition-all duration-300">
                            <i class="fas fa-paper-plane"></i>
                            Hubungi Kami
                        </a>
                        <a href="./arsip.php" class="inline-flex items-center justify-center gap-2 bg-white/10 backdrop-blur-sm border border-white/30 text-white font-bold px-8 py-4 rounded-xl hover:bg-white/20 transition-all duration-300">
                            <i class="fas fa-book"></i>
                            Lihat Arsip
                        </a>
                    </div>
                </div>
            </div>
    </div>    
</section>

<?php
// Include public footer
require_once __DIR__ . '/../includes/footer.php';
?>