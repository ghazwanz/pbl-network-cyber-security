<?php
/**
 * Public Profile Page
 * File: public/profil.php
 * Updated: Hero Section cleaned (No Floating Icons) & Premium Styling
 */

require_once __DIR__ . '/../includes/header.php';

// 1. Inisialisasi Koneksi Database
$pdo = getDBConnection();

// 2. Ambil Data Profil Lab
try {
    $stmt = $pdo->prepare("SELECT * FROM profil_lab LIMIT 1");
    $stmt->execute();
    $profil = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $profil = false;
}

// Default value jika data kosong
if (!$profil) {
    $profil = [
        'visi' => 'Visi belum diatur.',
        'misi' => '[]',
        'sejarah' => 'Deskripsi profil belum diatur.'
    ];
}

// 3. Ambil Data Pengelola Lab
try {
    $stmt_pengelola = $pdo->prepare("SELECT * FROM pengelola WHERE is_active = true");
    $stmt_pengelola->execute();
    $pengelola_list = $stmt_pengelola->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $pengelola_list = [];
}

// Helper function untuk memformat Misi
function formatMisiToList($misiData) {
    $listItems = [];
    $decoded = json_decode($misiData, true);

    if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
        $listItems = $decoded;
    } else {
        $lines = explode("\n", $misiData);
        $listItems = array_filter(array_map('trim', $lines));
    }

    $html = '';
    foreach ($listItems as $index => $item) {
        if (!empty($item)) {
            $cleanItem = preg_replace('/^\d+[\.\)]\s*/', '', $item);
            $num = $index + 1;
            // Updated list style to match modern aesthetic
            $html .= '<li class="flex items-start gap-4 p-4 rounded-2xl hover:bg-white/60 transition-colors duration-300">
                        <div class="mt-1 w-8 h-8 bg-gradient-to-br from-blue-100 to-blue-50 rounded-full flex items-center justify-center flex-shrink-0 border border-blue-100 shadow-sm">
                            <span class="text-blue-600 font-bold text-sm">' . $num . '</span>
                        </div>
                        <p class="text-lg text-gray-600 leading-relaxed">' . htmlspecialchars($cleanItem) . '</p>
                      </li>';
        }
    }
    
    if (empty($html)) {
        return '<li class="text-gray-500 italic p-4">Belum ada data misi.</li>';
    }

    return $html;
}
?>

<main class="overflow-hidden">

    <section class="relative lg:py-44 py-32 px-4 overflow-hidden">  
        <div class="absolute inset-0 opacity-[0.5]" style="background-image: url('data:image/svg+xml,%3Csvg width=%2260%22 height=%2260%22 viewBox=%220 0 60 60%22 xmlns=%22http://www.w3.org/2000/svg%22%3E%3Cg fill=%22none%22 fill-rule=%22evenodd%22%3E%3Cg fill=%22%23000%22 fill-opacity=%221%22%3E%3Cpath d=%22M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z%22/%3E%3C/g%3E%3C/g%3E%3C/svg%3E');"></div>

        <div class="absolute inset-0 overflow-hidden pointer-events-none">
            <div class="absolute inset-0 bg-gradient-to-b from-orange-50/80 via-white to-blue-50/60"></div>
            <div class="absolute -left-20 top-0 w-[60%] h-full bg-gradient-to-br from-orange-100/70 via-orange-50/50 to-transparent blur-3xl"></div>
            <div class="absolute -right-20 top-0 w-[60%] h-full bg-gradient-to-bl from-blue-100/60 via-indigo-50/40 to-transparent blur-3xl"></div>
            <div class="absolute inset-0 bg-gradient-to-b from-transparent via-white/30 to-white/60"></div>
            <div class="absolute inset-0 opacity-[0.015]" style="background-image: url('data:image/svg+xml,%3Csvg viewBox=%220 0 256 256%22 xmlns=%22http://www.w3.org/2000/svg%22%3E%3Cfilter id=%22noise%22%3E%3CfeTurbulence type=%22fractalNoise%22 baseFrequency=%220.9%22 numOctaves=%224%22 stitchTiles=%22stitch%22/%3E%3C/filter%3E%3Crect width=%22100%25%22 height=%22100%25%22 filter=%22url(%23noise)%22/%3E%3C/svg%3E');"></div>
        </div>

        <div class="container mx-auto max-w-7xl px-4 relative ">
            
            <div class="mx-auto max-w-4xl flex flex-col items-center text-center mb-16">
                
                <div class="inline-flex items-center gap-2.5 px-5 py-2.5 bg-white/80 backdrop-blur-sm border border-orange-200/60 rounded-full text-orange-600 font-semibold text-sm mb-8 shadow-lg shadow-orange-100/50" data-aos="fade-up">
                    <span class="w-2 h-2 bg-orange-500 rounded-full animate-pulse"></span>
                    <span class="tracking-wide">PROFIL KAMI</span>
                </div>

                <h1 class="text-5xl md:text-6xl lg:text-7xl font-medium text-[#1B2D62] leading-[1.1] tracking-tight mb-8" data-aos="fade-up" data-aos-delay="100">
                    Tentang <br class="hidden sm:block">
                    <span class="bg-gradient-to-r from-[#1B2D62] via-[#2C4AA4] to-orange-500 bg-clip-text text-transparent">Laboratorium</span>
                </h1>

                <div class="text-lg md:text-xl text-gray-600 leading-relaxed max-w-3xl" data-aos="fade-up" data-aos-delay="200">
                    <?= nl2br(htmlspecialchars($profil['sejarah'] ?? '')) ?>
                </div>

            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 lg:gap-8" data-aos="fade-up" data-aos-delay="300">
                <div class="relative h-[400px] lg:h-[600px] rounded-3xl overflow-hidden shadow-2xl group border-4 border-white">
                    <div class="absolute inset-0 bg-gradient-to-t from-black/50 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300 z-10"></div>
                    <img src="../assets/img/room1.png" class="w-full h-full object-cover transform group-hover:scale-105 transition-transform duration-700" alt="Lab Room 1">
                </div>

                <div class="flex flex-col gap-6 lg:gap-8">
                    <div class="relative h-[250px] lg:h-[288px] rounded-3xl overflow-hidden shadow-xl group border-4 border-white">
                        <div class="absolute inset-0 bg-gradient-to-t from-black/50 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300 z-10"></div>
                        <img src="../assets/img/room2.png" class="w-full h-full object-cover transform group-hover:scale-105 transition-transform duration-700" alt="Lab Room 2">
                    </div>

                    <div class="relative h-[250px] lg:h-[288px] rounded-3xl overflow-hidden shadow-xl group border-4 border-white">
                        <div class="absolute inset-0 bg-gradient-to-t from-black/50 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300 z-10"></div>
                        <img src="../assets/img/room3.png" class="w-full h-full object-cover transform group-hover:scale-105 transition-transform duration-700" alt="Lab Room 3">
                    </div>
                </div>
            </div>

        </div>
    </section>

    <section class="py-24 bg-white relative border-t border-gray-100 overflow-hidden">
        <div class="absolute top-0 right-0 w-96 h-96 bg-blue-50/50 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute bottom-0 left-0 w-96 h-96 bg-orange-50/50 rounded-full blur-3xl pointer-events-none"></div>

        <div class="container mx-auto max-w-7xl px-4 relative">

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-10 lg:gap-16 mb-20 items-start" data-aos="fade-right">
                <div>
                    <div class="inline-flex items-center gap-2 px-4 py-2 bg-[#1B2D62] text-white rounded-full font-semibold mb-6 shadow-lg shadow-blue-900/20">
                        <i class="fas fa-bullseye text-orange-400"></i>
                        <span class="text-sm tracking-wide uppercase">Visi Kami</span>
                    </div>

                    <h2 class="text-3xl md:text-4xl lg:text-5xl font-medium text-[#1B2D62] leading-snug">
                        Laboratorium <br class="hidden lg:block">
                        Unggulan Jaringan & <br class="hidden lg:block">
                        <span class="bg-gradient-to-r from-[#1B2D62] to-[#2C4AA4] bg-clip-text text-transparent">Keamanan Siber</span>
                    </h2>
                </div>

                <div class="bg-gradient-to-br from-blue-50 to-white p-8 lg:p-10 rounded-3xl border border-blue-100 border-l-8 border-l-orange-500 shadow-sm relative">
                    <p class="text-lg md:text-xl text-gray-700 leading-relaxed italic relative z-10 pl-4">
                        "<?= htmlspecialchars($profil['visi'] ?? '') ?>"
                    </p>
                </div>
            </div>

            <div class="border-t border-gray-100 my-10"></div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-10 lg:gap-16 pt-10" data-aos="fade-left">
                <div>
                    <div class="inline-flex items-center gap-2 px-4 py-2 bg-[#1B2D62] text-white rounded-full font-semibold mb-6 shadow-lg shadow-blue-900/20">
                        <i class="fas fa-rocket text-orange-400"></i>
                        <span class="text-sm tracking-wide uppercase">Misi Kami</span>
                    </div>

                    <h2 class="text-3xl md:text-4xl lg:text-5xl font-medium text-[#1B2D62] leading-snug">
                        Praktikum <br class="hidden lg:block">
                        Berkualitas Jaringan <br class="hidden lg:block">
                        <span class="bg-gradient-to-r from-[#1B2D62] to-[#2C4AA4] bg-clip-text text-transparent">& Keamanan Siber</span>
                    </h2>
                </div>

                <div>
                    <ul class="space-y-4">
                        <?= formatMisiToList($profil['misi'] ?? '') ?>
                    </ul>
                </div>
            </div>

        </div>
    </section>

    <section class="py-24 px-4 bg-[#F8FCFF] relative overflow-hidden">
        <div class="absolute inset-0 bg-gradient-to-b from-slate-50/50 via-white to-slate-50/50"></div>
        
        <div class="mx-auto max-w-7xl relative">
            
            <div class="text-center max-w-3xl mx-auto mb-16" data-aos="fade-up">
                <div class="inline-flex items-center gap-2.5 px-5 py-2.5 bg-white/80 backdrop-blur-sm border border-orange-200/60 rounded-full text-orange-600 font-semibold text-sm mb-6 shadow-lg shadow-orange-100/50">
                    <i class="fas fa-users"></i>
                    <span class="tracking-wide">TIM KAMI</span>
                </div>
                
                <h2 class="text-4xl md:text-5xl font-medium text-[#1B2D62] mb-4">
                    Pengelola Lab
                </h2>
                <div class="text-lg text-gray-600">
                    Tim berdedikasi di balik operasional laboratorium.
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                
                <?php if ($pengelola_list && count($pengelola_list) > 0): ?>
                    <?php foreach ($pengelola_list as $index => $p): 
                        // Setup path foto
                        $imgSrc = !empty($p['foto_path']) && file_exists("../uploads" . $p['foto_path']) 
                                  ? UPLOAD_URL . $p['foto_path'] 
                                  : ASSETS_URL . '/img/no-image.png'; 
                    ?>
                    
                    <div class="group bg-white rounded-3xl p-4 border border-gray-100 shadow-sm hover:shadow-2xl hover:shadow-blue-900/10 transition-all duration-500" 
                         data-aos="fade-up" 
                         data-aos-delay="<?= ($index * 100) ?>">
                        
                        <a href="detail_pengelola.php?id=<?= $p['id'] ?>" class="block cursor-pointer overflow-hidden rounded-2xl relative aspect-[4/5] mb-5">
                            <img src="<?= htmlspecialchars($imgSrc) ?>" 
                                 alt="<?= htmlspecialchars($p['nama_lengkap']) ?>"
                                 class="h-full w-full object-cover object-top transition-transform duration-700 group-hover:scale-110 filter grayscale-[10%] group-hover:grayscale-0">
                            
                            <div class="absolute inset-0 bg-gradient-to-t from-[#1B2D62]/90 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                            
                            <div class="absolute bottom-0 left-0 right-0 p-6 translate-y-4 group-hover:translate-y-0 opacity-0 group-hover:opacity-100 transition-all duration-300">
                                <span class="inline-flex items-center gap-2 text-white font-medium text-sm">
                                    Lihat Profil Lengkap <i class="fas fa-arrow-right"></i>
                                </span>
                            </div>
                        </a>
                        
                        <div class="px-2 pb-2 text-center">
                            <a href="detail_pengelola.php?id=<?= $p['id'] ?>" class="hover:text-orange-600 transition-colors">
                                <h3 class="text-xl font-bold text-[#1B2D62] leading-tight mb-2">
                                    <?= htmlspecialchars($p['nama_lengkap']) ?>
                                </h3>
                            </a>
                            
                            <p class="text-orange-500 text-sm font-semibold uppercase tracking-wide">
                                <?= htmlspecialchars($p['jabatan']) ?>
                            </p>
                        </div>
                    </div>

                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="col-span-3 text-center py-16 w-full bg-white rounded-3xl border-2 border-dashed border-gray-200">
                        <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-user-slash text-gray-400 text-2xl"></i>
                        </div>
                        <p class="text-gray-500 italic">Belum ada data pengelola.</p>
                    </div>
                <?php endif; ?>

            </div>
        </div>
    </section>

    <section class="relative px-4 pb-24 bg-[#F8FCFF] overflow-hidden">
        <div class="absolute inset-0 pointer-events-none">
            <div class="absolute top-0 left-1/4 w-96 h-96 bg-orange-200/20 rounded-full blur-3xl"></div>
            <div class="absolute bottom-0 right-1/4 w-80 h-80 bg-blue-200/20 rounded-full blur-3xl"></div>
        </div>
        
        <div data-aos="fade-up" class="relative max-w-7xl mx-auto">
            <div class="relative overflow-hidden rounded-3xl">
                <div class="absolute inset-0 bg-gradient-to-br from-[#1B2D62] via-[#243a73] to-[#2C4AA4]"></div>
                
                <div class="absolute inset-0 opacity-10">
                    <div class="absolute inset-0" style="background-image: url('data:image/svg+xml,%3Csvg width=%2260%22 height=%2260%22 viewBox=%220 0 60 60%22 xmlns=%22http://www.w3.org/2000/svg%22%3E%3Cg fill=%22none%22 fill-rule=%22evenodd%22%3E%3Cg fill=%22%23ffffff%22 fill-opacity=%221%22%3E%3Cpath d=%22M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z%22/%3E%3C/g%3E%3C/g%3E%3C/svg%3E');"></div>
                </div>
                
                <div class="absolute top-10 left-10 w-20 h-20 bg-white/5 rounded-2xl rotate-12 hidden lg:block"></div>
                <div class="absolute bottom-10 right-10 w-32 h-32 bg-orange-500/10 rounded-full hidden lg:block"></div>
                <div class="absolute top-1/2 right-20 w-16 h-16 bg-white/5 rounded-xl -rotate-12 hidden lg:block"></div>
                
                <div class="absolute -top-20 -right-20 w-64 h-64 bg-orange-500/20 rounded-full blur-3xl"></div>
                <div class="absolute -bottom-20 -left-20 w-64 h-64 bg-blue-400/20 rounded-full blur-3xl"></div>
                
                <div class="relative px-6 py-16 sm:px-12 sm:py-20 lg:py-24">
                    <div class="max-w-4xl mx-auto text-center">
                        
                        <div class="inline-flex items-center gap-2.5 px-5 py-2.5 bg-white/10 backdrop-blur-sm border border-white/20 rounded-full text-white font-semibold text-sm mb-8" data-aos="fade-up">
                            <span class="w-2 h-2 bg-orange-400 rounded-full animate-pulse"></span>
                            <span class="tracking-wide">KOLABORASI</span>
                        </div>
                        
                        <h2 class="text-4xl md:text-5xl lg:text-6xl font-medium text-white mb-6 leading-tight" data-aos="fade-up">
                            Tertarik untuk<br class="hidden sm:block">
                            <span class="bg-gradient-to-r from-orange-400 via-orange-300 to-yellow-300 bg-clip-text text-transparent">Bekerjasama?</span>
                        </h2>
                        
                        <p class="text-lg md:text-xl text-white/80 mb-12 leading-relaxed max-w-2xl mx-auto" data-aos="fade-up">
                            Kami terbuka untuk kerjasama penelitian, pengabdian masyarakat, dan kunjungan industri. Mari ciptakan inovasi bersama.
                        </p>
                        
                        <div class="flex flex-col sm:flex-row gap-4 justify-center" data-aos="fade-up">
                            <a href="./kontak.php" class="group inline-flex items-center justify-center gap-3 bg-gradient-to-r from-orange-500 to-orange-600 text-white font-medium text-base px-8 py-4 rounded-xl shadow-lg shadow-orange-500/30 transition-all duration-300 hover:shadow-xl hover:shadow-orange-500/40 hover:scale-[1.02] active:scale-[0.98]">
                                <i class="fas fa-paper-plane transition-transform duration-300 group-hover:-translate-y-0.5 group-hover:translate-x-0.5"></i>
                                <span>Hubungi Kami</span>
                            </a>
                            <a href="./layanan.php" class="group inline-flex items-center justify-center gap-3 bg-white/10 backdrop-blur-sm border-2 border-white/30 text-white font-medium text-base px-8 py-4 rounded-xl transition-all duration-300 hover:bg-white/20 active:scale-[0.98]">
                                <i class="fas fa-concierge-bell"></i>
                                <span>Lihat Layanan</span>
                            </a>
                        </div>
                        
                    </div>
                </div>
                
            </div>
        </div>
    </section>

</main>

<?php
    require_once __DIR__ . '/../includes/footer.php';
?>