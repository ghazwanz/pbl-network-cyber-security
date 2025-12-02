<?php
/**
 * Public Profile Page
 * File: public/profil.php
 * Updated: Hero Section match Index.php style
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
    $stmt_pengelola = $pdo->prepare("SELECT * FROM pengelola WHERE is_active = true ORDER BY urutan_tampil ASC");
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
            $html .= '<li class="flex items-start gap-4">
                        <div class="mt-1.5 w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center flex-shrink-0">
                            <span class="text-blue-600 font-bold text-sm">' . $num . '</span>
                        </div>
                        <p class="text-lg text-gray-600 leading-relaxed">' . htmlspecialchars($cleanItem) . '</p>
                      </li>';
        }
    }
    
    if (empty($html)) {
        return '<li class="text-gray-500 italic">Belum ada data misi.</li>';
    }

    return $html;
}
?>

<main class="overflow-hidden">

    <section class="relative lg:py-44 py-32 bg-gradient-to-br from-[#F8FCFF] via-white px-4 to-orange-50 overflow-hidden">  

        <div class="relative max-w-7xl mx-auto pointer-events-none">
            <div class="absolute top-0 right-0 w-96 h-96 bg-orange-100 rounded-2xl mix-blend-multiply filter blur-2xl opacity-30 animate-pulse"></div>
            <div class="absolute bottom-0 left-0 w-96 h-96 bg-blue-100 rounded-full mix-blend-multiply filter blur-2xl opacity-30 animate-pulse" style="animation-delay: 1s;"></div>
        </div>
         
        <div class="container mx-auto max-w-7xl px-4 relative ">
            
            <div class="mx-auto max-w-4xl flex flex-col items-center text-center mb-16">
                
                <div class="inline-flex items-center gap-2 px-5 py-2.5 bg-white border-2 border-orange-200 rounded-full text-orange-600 font-bold mb-6 shadow-lg" data-aos="fade-up">
                    <i class="fas fa-university"></i>
                    <span>PROFIL KAMI</span>
                </div>

                <h1 class="text-5xl md:text-6xl font-medium text-[#1B2D62] leading-tight mb-6" data-aos="fade-up" data-aos-delay="100">
                    Tentang Laboratorium
                </h1>

                <div class="text-lg text-gray-600 leading-relaxed max-w-3xl" data-aos="fade-up" data-aos-delay="200">
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

    <section class="py-20 bg-white relative border-t border-gray-100">
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
                    <p class="text-lg text-gray-700 leading-relaxed italic">
                        "<?= htmlspecialchars($profil['visi'] ?? '') ?>"
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
                        <?= formatMisiToList($profil['misi'] ?? '') ?>
                    </ul>
                </div>
            </div>

        </div>
    </section>

    <section class="py-24 px-4 bg-[#F8FCFF]">
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
                        // Setup path foto
                        $imgSrc = !empty($p['foto_path']) && file_exists("../uploads" . $p['foto_path']) 
                                  ? UPLOAD_URL . $p['foto_path'] 
                                  : ASSETS_URL . '/img/no-image.png'; 
                    ?>
                    
                    <div class="group" 
                         data-aos="fade-up" 
                         data-aos-delay="<?= ($index * 100) ?>">
                        
                        <a href="detail_pengelola.php?id=<?= $p['id'] ?>" class="block cursor-pointer">
                            <div class="aspect-[4/4] w-full overflow-hidden rounded-2xl bg-gray-200 mb-5 relative shadow-md">
                                <img src="<?= htmlspecialchars($imgSrc) ?>" 
                                     alt="<?= htmlspecialchars($p['nama_lengkap']) ?>"
                                     class="h-full w-full object-cover object-top transition-transform duration-700 group-hover:scale-105 filter grayscale-[10%] group-hover:grayscale-0">
                                
                                <div class="absolute inset-x-0 bottom-0 h-1/4 bg-gradient-to-t from-black/60 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-end justify-center pb-4">
                                    <span class="text-white text-sm font-semibold tracking-wider">LIHAT PROFIL <i class="fas fa-arrow-right ml-1"></i></span>
                                </div>
                            </div>
                        </a>
                        
                        <div class="px-1">
                            <a href="detail_pengelola.php?id=<?= $p['id'] ?>" class="hover:underline decoration-blue-900">
                                <h3 class="text-xl font-bold text-[#1B2D62] leading-tight mb-1">
                                    <?= htmlspecialchars($p['nama_lengkap']) ?>
                                </h3>
                            </a>
                            
                            <p class="text-gray-500 text-sm font-medium uppercase tracking-wide">
                                <?= htmlspecialchars($p['jabatan']) ?>
                            </p>
                        </div>
                    </div>

                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="col-span-3 text-center py-10 w-full bg-white rounded-xl border border-dashed border-gray-300">
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