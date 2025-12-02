<?php
/**
 * Public Profile Page
 * File: public/profile.php
 * Design Reference: Modern & Professional with Navy Blue Theme
 */

$page_title = "Profil - Laboratorium NCS";
require_once __DIR__ . '/../includes/header.php';

// 1. Ambil Data Profil Lab (Visi, Misi, Sejarah/Deskripsi)
$profil = executeQuerySingle("SELECT * FROM profil_lab LIMIT 1");

// Default value jika data kosong (untuk mencegah error)
if (!$profil) {
    $profil = [
        'visi' => 'Visi belum diatur.',
        'misi' => 'Misi belum diatur.',
        'sejarah' => 'Deskripsi profil belum diatur.'
    ];
}

// 2. Ambil Data Pengelola Lab (Diurutkan berdasarkan urutan_tampil)
// Pastikan kolom 'foto_path', 'nama_lengkap', 'jabatan', 'pendidikan_terakhir' sudah terisi di DB
$pengelola_list = executeQuery("SELECT * FROM pengelola WHERE is_active = true ORDER BY urutan_tampil ASC");

// Helper function untuk memformat Misi (jika disimpan sebagai teks panjang dengan enter)
function formatMisiToList($misiText) {
    $lines = explode("\n", $misiText); // Pecah berdasarkan baris baru
    $html = '';
    foreach ($lines as $index => $line) {
        $line = trim($line);
        if (!empty($line)) {
            // Hapus angka di depan jika ada (misal "1. Mengembangkan...") agar rapi di list
            $line = preg_replace('/^\d+[\.\)]\s*/', '', $line); 
            
            $num = $index + 1;
            $html .= '<li class="flex items-start gap-4">
                        <div class="mt-1.5 w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center flex-shrink-0">
                            <span class="text-blue-600 font-bold text-sm">' . $num . '</span>
                        </div>
                        <p class="text-lg text-gray-600 leading-relaxed">' . htmlspecialchars($line) . '</p>
                      </li>';
        }
    }
    return $html;
}
?>

<main class="overflow-hidden">

    <section class="relative pt-32 pb-20 lg:pt-44 lg:pb-32 bg-gradient-to-br from-[#F8FCFF] via-white to-blue-50">
        <div class="absolute top-0 left-0 w-96 h-96 bg-blue-100 rounded-full mix-blend-multiply filter blur-2xl opacity-30 animate-pulse"></div>
        <div class="absolute bottom-0 right-0 w-96 h-96 bg-orange-100 rounded-full mix-blend-multiply filter blur-2xl opacity-30 animate-pulse" style="animation-delay: 1s;"></div>
        
        <div class="absolute left-10 top-40 w-16 h-16 border-4 border-orange-300 rounded-full opacity-40 animate-spin" style="animation-duration: 20s;"></div>
        <div class="absolute right-10 bottom-40 w-10 h-10 bg-blue-400 rotate-45 opacity-30 animate-bounce"></div>

        <div class="container mx-auto max-w-7xl px-4 relative z-10">
            
            <div class="text-center max-w-4xl mx-auto mb-16" data-aos="fade-up">
                <div class="inline-flex items-center gap-2 px-5 py-2.5 bg-white border-2 border-orange-200 rounded-full text-orange-600 font-bold mb-6 shadow-lg">
                    <i class="fas fa-university text-orange-500"></i>
                    <span class="text-sm tracking-wide">PROFIL KAMI</span>
                </div>

                <h1 class="text-5xl md:text-6xl font-medium text-[#1B2D62] mb-6 leading-tight">
                    Tentang Laboratorium
                </h1>
                
                <p class="text-xl text-gray-600 leading-relaxed">
                    <?= nl2br(htmlspecialchars($profil['sejarah'] ?? '')) ?>
                </p>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 lg:gap-8" data-aos="fade-up" data-aos-delay="200">
                <div class="relative h-[400px] lg:h-[600px] rounded-3xl overflow-hidden shadow-2xl group">
                    <div class="absolute inset-0 bg-gradient-to-t from-black/50 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300 z-10"></div>
                    <img src="../assets/img/room1.png" class="w-full h-full object-cover transform group-hover:scale-105 transition-transform duration-700" alt="Lab Room 1">
                </div>

                <div class="flex flex-col gap-6 lg:gap-8">
                    <div class="relative h-[250px] lg:h-[288px] rounded-3xl overflow-hidden shadow-xl group">
                        <div class="absolute inset-0 bg-gradient-to-t from-black/50 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300 z-10"></div>
                        <img src="../assets/img/room2.png" class="w-full h-full object-cover transform group-hover:scale-105 transition-transform duration-700" alt="Lab Room 2">
                    </div>

                    <div class="relative h-[250px] lg:h-[288px] rounded-3xl overflow-hidden shadow-xl group">
                        <div class="absolute inset-0 bg-gradient-to-t from-black/50 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300 z-10"></div>
                        <img src="../assets/img/room3.png" class="w-full h-full object-cover transform group-hover:scale-105 transition-transform duration-700" alt="Lab Room 3">
                    </div>
                </div>
            </div>

        </div>
    </section>

    <section class="py-20 bg-white relative">
        <div class="container mx-auto max-w-7xl px-4">

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-10 lg:gap-16 mb-20 items-start" data-aos="fade-right">
                <div>
                    <div class="inline-flex items-center gap-2 px-4 py-2 bg-[#1B2D62] text-white rounded-full font-semibold mb-6">
                        <i class="fas fa-bullseye text-orange-400"></i>
                        <span class="text-sm tracking-wide uppercase">Visi Kami</span>
                    </div>

                    <h2 class="text-3xl md:text-4xl font-medium text-[#1B2D62] leading-snug">
                        Laboratorium <br class="hidden lg:block">
                        Unggulan Jaringan & <br class="hidden lg:block">
                        Keamanan Siber
                    </h2>
                </div>

                <div class="bg-blue-50 p-8 rounded-2xl border-l-4 border-orange-500">
                    <p class="text-lg text-gray-700 leading-relaxed">
                        "<?= htmlspecialchars($profil['visi']) ?>"
                    </p>
                </div>
            </div>

            <div class="border-t border-gray-100 my-10"></div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-10 lg:gap-16 pt-10" data-aos="fade-left">
                <div>
                    <div class="inline-flex items-center gap-2 px-4 py-2 bg-[#1B2D62] text-white rounded-full font-semibold mb-6">
                        <i class="fas fa-rocket text-orange-400"></i>
                        <span class="text-sm tracking-wide uppercase">Misi Kami</span>
                    </div>

                    <h2 class="text-3xl md:text-4xl font-medium text-[#1B2D62] leading-snug">
                        Praktikum <br class="hidden lg:block">
                        Berkualitas Jaringan <br class="hidden lg:block">
                        & Keamanan Siber
                    </h2>
                </div>

                <div>
                    <ul class="space-y-6">
                        <?= formatMisiToList($profil['misi']) ?>
                    </ul>
                </div>
            </div>

        </div>
    </section>

<section class="py-24 px-4">
        <div class="mx-auto max-w-7xl">
            
            <div class="text-center max-w-3xl mx-auto mb-16" data-aos="fade-up">
                <div class="inline-flex items-center gap-2 px-4 py-2 bg-white border-2 border-orange-200 rounded-full text-orange-600 font-semibold mb-4">
                    <i class="fas fa-users text-orange-500"></i>
                    <span class="text-sm uppercase tracking-wide">TIM KAMI</span>
                </div>
                
                <h2 class="text-4xl md:text-5xl font-medium text-[#1B2D62]">
                    Pengelola Lab
                </h2>
                <div class="mt-4 text-gray-600">
                    Berikut adalah tim pengelola laboratorium kami yang berdedikasi.
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                
                <?php if ($pengelola_list && count($pengelola_list) > 0): ?>
                    <?php foreach ($pengelola_list as $index => $p): 
                        // Cek foto
                        $imgSrc = !empty($p['foto_path']) && file_exists("../uploads" . $p['foto_path']) 
                                  ? UPLOAD_URL . $p['foto_path'] 
                                  : ASSETS_URL . '/img/no-image.png'; 
                    ?>
                    
                    <div class="group" 
                         data-aos="fade-up" 
                         data-aos-delay="<?= ($index * 100) ?>">
                        
                        <div class="aspect-[4/4] w-full overflow-hidden rounded-2xl bg-gray-200 mb-5 relative shadow-md">
                            <img src="<?= htmlspecialchars($imgSrc) ?>" 
                                 alt="<?= htmlspecialchars($p['nama_lengkap']) ?>"
                                 class="h-full w-full object-cover object-top transition-transform duration-700 group-hover:scale-105 filter grayscale-[10%] group-hover:grayscale-0">
                            
                            <div class="absolute inset-x-0 bottom-0 h-1/4 bg-gradient-to-t from-black/20 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                        </div>
                        
                        <div class="flex justify-between items-start px-1">
                            <div>
                                <h3 class="text-xl font-bold text-[#1B2D62] leading-tight mb-1">
                                    <?= htmlspecialchars($p['nama_lengkap']) ?>
                                </h3>
                                
                                <p class="text-gray-500 text-sm font-medium uppercase tracking-wide">
                                    <?= htmlspecialchars($p['jabatan']) ?>
                                </p>
                            </div>

                            <a href="#" class="text-gray-300 hover:text-[#0077b5] transition-colors duration-300 transform hover:scale-110">
                                <svg class="w-6 h-6 fill-current" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                </svg>
                            </a>
                        </div>
                    </div>

                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="col-span-3 text-center py-10 w-full">
                        <p class="text-gray-500 italic">Belum ada data pengelola.</p>
                    </div>
                <?php endif; ?>

            </div>
        </div>
    </section>

    <section class="px-4 pb-20 bg-[#F8FCFF]">
        <div data-aos="fade-up" class="sm:py-20 py-16 bg-gradient-to-r from-[#1B2D62] to-[#2C4AA4] mx-auto rounded-2xl max-w-7xl">
            <div class="mx-auto sm:px-12 px-6">
                <div class="max-w-4xl mx-auto text-center">
                    <div class="inline-flex items-center gap-2 px-4 py-2 bg-white/10 border border-white/20 rounded-full text-white font-semibold mb-6">
                        <i class="fas fa-envelope text-orange-400"></i>
                        <span class="text-sm">HUBUNGI KAMI</span>
                    </div>
                    
                    <h2 class="text-4xl md:text-5xl font-medium text-white mb-6 font-inter">
                        Tertarik Berkolaborasi?
                    </h2>
                    
                    <p class="text-xl text-white/80 mb-10 font-inter leading-relaxed">
                        Kami terbuka untuk kerjasama penelitian, pengabdian masyarakat,<br class="hidden md:block">
                        dan kunjungan industri.
                    </p>
                    
                    <div class="flex flex-col sm:flex-row gap-4 justify-center">
                        <a href="./kontak.php" class="inline-flex items-center justify-center gap-2 bg-gradient-to-r from-orange-500 to-orange-600 text-white font-bold px-8 py-4 rounded-xl hover:shadow-2xl hover:scale-105 transition-all duration-300">
                            <i class="fas fa-paper-plane"></i>
                            Hubungi Kami
                        </a>
                        <a href="./layanan.php" class="inline-flex items-center justify-center gap-2 bg-white/10 backdrop-blur-sm border-2 border-white/30 text-white font-bold px-8 py-4 rounded-xl hover:bg-white/20 transition-all duration-300">
                            <i class="fas fa-concierge-bell"></i>
                            Lihat Layanan
                        </a>
                    </div>
                </div>
            </div>
        </div>    
    </section>

</main>

<?php
    require_once __DIR__ . '/../includes/footer.php';
?>