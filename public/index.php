<?php
    require_once __DIR__ . '/../includes/header.php';
    $pdo = getDBConnection();
?> 

<main>
    <section class="relative lg:py-44 py-32 bg-gradient-to-br from-[#F8FCFF] via-white px-4 to-orange-50 overflow-hidden ">  

        <div class="relative max-w-7xl mx-auto">
            <div class="absolute top-0 right-0 w-96 h-96 bg-orange-100 rounded-2xl mix-blend-multiply filter blur-2xl opacity-30 animate-pulse"></div>
            <div class="absolute bottom-0 left-0 w-96 h-96 bg-blue-100 rounded-full mix-blend-multiply filter blur-2xl opacity-30 animate-pulse" style="animation-delay: 1s;"></div>
    
            <div class="hidden md:flex absolute left-8 lg:left-28 top-52 lg:top-60 w-20 h-20 lg:w-28 lg:h-28 bg-white rounded-3xl shadow-2xl items-center justify-center animate-bounce opacity-90" style="animation-duration: 4s;">
                <i class="fas fa-network-wired text-orange-500 text-3xl lg:text-5xl"></i>
            </div>
    
            <div class="hidden md:flex absolute left-20 lg:left-36 bottom-20 lg:top-0 w-24 h-24 lg:w-36 lg:h-36 bg-gradient-to-br from-blue-500 to-blue-600 rounded-full shadow-2xl items-center justify-center animate-bounce opacity-90" style="animation-duration: 5s;">
                <i class="fas fa-shield-alt text-white text-4xl lg:text-7xl"></i>
            </div>
    
            <div class="hidden md:flex absolute right-10 lg:right-36 top-60 bottom-10 lg:top-80 w-16 h-16 lg:w-24 lg:h-24 bg-gradient-to-br from-green-400 to-emerald-600 rounded-3xl shadow-2xl items-center justify-center animate-bounce opacity-85" style="animation-duration: 4.5s;">
                <i class="fas fa-lock text-white text-2xl lg:text-4xl"></i>
            </div>
    
            <div class="hidden md:flex absolute right-20 lg:right-24 bottom-60 lg:top-12 w-20 h-20 lg:w-32 lg:h-32 bg-white rounded-full shadow-xl items-center justify-center animate-bounce opacity-80" style="animation-duration: 3.5s;">
                <i class="fas fa-database text-[#1B2D62] text-3xl lg:text-5xl"></i>
            </div>
        </div>
         

        <div class=" mx-auto max-w-3xl flex flex-col items-center relative text-center">
            
            <div class="inline-flex items-center space-x-2 bg-white border border-gray-200 rounded-full px-5 py-2.5 text-orange-500 font-bold text-sm mb-6 shadow-sm" data-aos="fade-up">
                <img src="../assets/icons/zap1.svg">
                <span>INOVASI TEKNOLOGI KEAMANAN</span>
            </div>

            <h1 class="text-5xl md:text-6xl font-medium text-[#1B2D62] leading-tight mb-6" data-aos="fade-up" data-aos-delay="100">
                Selamat Datang di Laboratorium Network & Cyber security
            </h1>

            <p class="text-lg text-gray-600 max-w-2xl mb-10" data-aos="fade-up" data-aos-delay="200">
                Lorem ipsum dolor sit amet, consectetur adipisicing elit. Ipsam minima voluptatem aperiam commodi accusantium nobis in, illum ipsa sint officia.
            </p>

            <div class="flex flex-col sm:flex-row space-y-4 sm:space-y-0 sm:space-x-8 mb-10" data-aos="fade-up" data-aos-delay="300">
                <div class="flex items-center space-x-2">
                    <img src="../assets/icons/check-circle1.svg">
                    <span class="font-medium text-gray-700">Jaringan</span>
                </div>
                <div class="flex items-center space-x-2">
                    <img src="../assets/icons/check-circle1.svg">
                    <span class="font-medium text-gray-700">Capture The Flag</span>
                </div>
                <div class="flex items-center space-x-2">
                    <img src="../assets/icons/check-circle1.svg">
                    <span class="font-medium text-gray-700">Security System</span>
                </div>
            </div>

            <a href="./layanan.php" class="inline-flex items-center space-x-2 bg-[#1B2D62] text-white font-bold text-base px-7 py-3.5 rounded-lg shadow-sm transition hover:bg-[#2C4AA4] hover:-translate-y-0.5" data-aos="fade-up" data-aos-delay="400">
                <span>Jelajahi Tentang Kami</span>
                <img src="../assets/icons/arrow-up-right1.svg">
            </a>

        </div>
    </section>

    <section class="bg-white py-24 sm:py-32 border-t-4 border-b-4 ">
        <div class="container mx-auto max-w-7xl px-4 grid grid-cols-1 lg:grid-cols-5 lg:gap-16">
            
            <div class="lg:col-span-2 flex flex-col justify-center mb-16 lg:mb-0" data-aos="fade-up">
                
                <div class="inline-flex self-start items-center space-x-2 bg-[#1B2D62] text-white font-bold text-xs rounded-full px-4 py-1.5 mb-6">
                    <img src="../assets/icons/award2.svg" alt="" srcset="">
                    <span>INOVASI KARYA</span>
                </div>

                <h2 class="text-4xl md:text-5xl font-inter font-medium text-gray-900 leading-tight mb-6">
                    Jelajahi Layanan Terbaik Kami
                </h2>
                
                <p class="text-lg text-gray-500 mb-10">
                    Lorem ipsum dolor sit amet, consectetur adipisicing elit. Ipsam minima voluptatem aperiam commodi accusantium nobis in, illum ipsa sint.
                </p>

                <a href="./profil.php" class="self-start inline-flex items-center space-x-2 bg-[#1B2D62] text-white font-bold text-base px-7 py-3.5 rounded-lg shadow-sm transition hover:bg-[#2C4AA4] hover:-translate-y-0.5">
                    <span>Pelajari Lebih Lanjut</span>
                    <img src="../assets/icons/arrow-up-right1.svg">
                </a>
            </div>

            <div class="lg:col-span-3">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    
                    <?php
                        try {
                            $stmt = $pdo->prepare("SELECT * FROM layanan WHERE status = 'Aktif' ORDER BY id ASC LIMIT 4");
                            $stmt->execute();
                            $layananList = $stmt->fetchAll(PDO::FETCH_ASSOC);
                        } catch (PDOException $e) {
                            echo "Error fetching data: " . $e->getMessage();
                            $layananList = [];
                        }
                    ?>

                    <?php if (!empty($layananList)): ?>
                        <?php foreach ($layananList as $index => $item): ?>
                            <div class="bg-white border border-gray-200 rounded-xl p-8 shadow-lg transition duration-300 hover:shadow-xl hover:-translate-y-1"
                                 data-aos="fade-up" 
                                 data-aos-delay="<?php echo ($index * 100); ?>">
                                <div class="inline-flex items-center justify-center w-16 h-16 bg-orange-500 rounded-lg mb-6">
                                    <img src="../uploads<?= htmlspecialchars($item['gambar_path']) ?>" >
                                </div>
                                <h3 class="text-xl font-inter font-medium text-gray-900 mb-2">
                                    <?= htmlspecialchars($item['nama_layanan']) ?>
                                </h3>
                                <p class="text-gray-500">
                                    <?= htmlspecialchars($item['deskripsi']) ?>
                                </p>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p class="text-gray-500 col-span-2" data-aos="fade-up">Belum ada data layanan tersedia.</p>
                    <?php endif; ?>

                </div>
            </div>

        </div>
    </section>  

    <section class="py-24 sm:py-32">
        <div class="container mx-auto max-w-7xl px-4">
            
            <div class="max-w-3xl mx-auto text-center mb-16" data-aos="fade-up">
                <div class="inline-flex items-center space-x-2 bg-white border border-gray-200 rounded-full px-5 py-2.5 text-orange-500 font-bold text-sm mb-6">
                    <img src="../assets/icons/award1.svg" alt="" srcset="">
                    <span>INOVASI KARYA</span>
                </div>

                <h2 class="text-4xl md:text-5xl font-inter font-medium text-gray-900 leading-tight mb-6">
                    Publikasi & Arsip Terbaru
                </h2>
                
                <p class="text-lg text-gray-500">
                    Kumpulan dokumen penelitian dan pengabdian masyarakat terbaru dari Laboratorium Network & Cyber Security.
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">

                <?php
                // Mengambil 3 data arsip terbaru dari database
                try {
                    $stmtArsip = $pdo->prepare("SELECT * FROM arsip WHERE is_active = true ORDER BY created_at DESC LIMIT 3");
                    $stmtArsip->execute();
                    $arsipList = $stmtArsip->fetchAll(PDO::FETCH_ASSOC);

                    foreach ($arsipList as &$arsip_item) {
                        $stmtAuthors = $pdo->prepare("SELECT p.nama_lengkap, p.jabatan, ap.peran FROM arsip_pengelola ap JOIN pengelola p ON ap.pengelola_id = p.id WHERE ap.arsip_id = ? ORDER BY ap.urutan_penulis ASC");
                        $stmtAuthors->execute([$arsip_item['id']]);
                        $arsip_item['penulis_list'] = $stmtAuthors->fetchAll(PDO::FETCH_ASSOC);
                        
                        if (!empty($arsip_item['penulis_list'])) {
                            $names = array_map(function($a) { return $a['nama_lengkap']; }, $arsip_item['penulis_list']);
                            $arsip_item['penulis_display'] = implode(', ', $names);
                        } else {
                            $arsip_item['penulis_display'] = '';
                        }
                    }
                    unset($arsip_item);

                } catch (PDOException $e) {
                    echo "Error fetching Arsip: " . $e->getMessage();
                    $arsipList = [];
                }
                ?>

                <?php if (!empty($arsipList)): ?>
                    <?php foreach ($arsipList as $index => $item): ?>
                        
                        <?php 
                        $item_data = [
                            'id' => $item['id'],
                            'judul' => htmlspecialchars($item['judul']),
                            'kategori' => htmlspecialchars($item['kategori']),
                            'abstrak' => htmlspecialchars($item['abstrak'] ?: 'Dokumen penelitian dan pengabdian masyarakat dari Laboratorium Network & Cyber Security.'),
                            'tahun_publikasi' => htmlspecialchars($item['tahun_publikasi'] ?? ''),
                            'penerbit' => htmlspecialchars($item['penerbit'] ?? ''),
                            'keywords' => htmlspecialchars($item['keywords'] ?? ''),
                            'doi' => htmlspecialchars($item['doi'] ?? ''),
                            'file_size_kb' => $item['file_size_kb'] ?? 0,
                            'jumlah_download' => $item['jumlah_download'] ?? 0,
                            'is_featured' => !empty($item['is_featured']),
                            'penulis_display' => htmlspecialchars($item['penulis_display'] ?? ''),
                            'penulis_list' => array_map(function($p) {
                                return [
                                    'nama' => htmlspecialchars($p['nama_lengkap']),
                                    'jabatan' => htmlspecialchars($p['jabatan'] ?? ''),
                                    'peran' => htmlspecialchars($p['peran'] ?? '')
                                ];
                            }, $item['penulis_list'] ?? [])
                        ];
                        ?>

                        <div class="group bg-white border-2 border-gray-200 rounded-2xl p-6 flex flex-col hover:border-orange-500 hover:shadow-2xl transition-all duration-300 hover:-translate-y-2 cursor-pointer"
                             data-aos="fade-up" 
                             data-aos-delay="<?php echo ($index * 100); ?>"
                             onclick='showArsipDetail(<?php echo json_encode($item_data, JSON_HEX_APOS | JSON_HEX_QUOT); ?>)'
                             data-toggle="modal" 
                             data-target="#modalDetailArsip">
                            
                            <div class="flex items-start justify-between mb-5">
                                <div class="w-16 h-16 bg-gradient-to-br from-orange-500 to-orange-600 rounded-xl flex items-center justify-center flex-shrink-0 group-hover:scale-110 transition-transform duration-300 shadow-lg">
                                    <i class="fas fa-file-pdf text-white text-2xl"></i>
                                </div>
                                <div class="flex flex-col items-end gap-2">
                                    <span class="px-3 py-1.5 text-xs font-bold rounded-lg uppercase tracking-wide
                                        <?php echo $item['kategori'] === 'penelitian' ? 'bg-blue-50 text-blue-700 border border-blue-200' : 'bg-green-50 text-green-700 border border-green-200'; ?>">
                                        <?php echo htmlspecialchars($item['kategori']); ?>
                                    </span>
                                </div>
                            </div>
                            
                            <h3 class="text-xl font-bold text-[#1B2D62] mb-3 line-clamp-2 group-hover:text-[#2C4AA4] transition-colors duration-300">
                                <?php echo htmlspecialchars($item['judul']); ?>
                            </h3>
                            
                            <div class="flex flex-wrap gap-3 mb-4 text-sm text-gray-600">
                                <?php if (!empty($item['tahun_publikasi'])): ?>
                                <div class="flex items-center gap-1.5">
                                    <i class="fas fa-calendar-alt text-orange-500"></i>
                                    <span class="font-semibold"><?php echo htmlspecialchars($item['tahun_publikasi']); ?></span>
                                </div>
                                <?php endif; ?>
                            </div>
                            
                            <p class="text-sm text-gray-600 mb-5 leading-relaxed line-clamp-3 flex-grow">
                                <?php echo htmlspecialchars($item['abstrak'] ?: 'Dokumen penelitian dan pengabdian masyarakat dari Laboratorium Network & Cyber Security.'); ?>
                            </p>
                            
                            <div class="flex items-center justify-between pt-5 border-t-2 border-gray-100 mt-auto">
                                <span class="inline-flex items-center gap-2 text-orange-600 font-bold group-hover:text-orange-700 transition-colors">
                                    <span>Lihat Detail</span>
                                    <i class="fas fa-arrow-right group-hover:translate-x-1 transition-transform"></i>
                                </span>
                                
                                <div class="flex items-center gap-2 text-gray-500">
                                    <div class="flex items-center gap-1.5 px-3 py-1.5 bg-gray-50 rounded-lg">
                                        <i class="fas fa-download text-gray-400"></i>
                                        <span class="font-bold text-sm"><?php echo number_format($item['jumlah_download']); ?></span>
                                    </div>
                                </div>
                            </div>
                        </div>

                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="col-span-1 lg:col-span-3 text-center py-10 text-gray-500 bg-gray-50 rounded-xl border border-dashed border-gray-300" data-aos="fade-up">
                        <p>Belum ada arsip dokumen yang tersedia saat ini.</p>
                    </div>
                <?php endif; ?>

            </div>

            <div class="text-center mt-16" data-aos="fade-up"> 
                <a href="./arsip.php" class="inline-flex items-center space-x-2 bg-[#1B2D62] text-white font-bold text-base px-7 py-3.5 rounded-lg shadow-sm transition hover:bg-[#2C4AA4] hover:-translate-y-0.5">
                    <span>Lihat Semua Dokumen</span>
                    <img src="../assets/icons/arrow-up-right1.svg">
                </a>
            </div>

        </div>
    </section>

    <section class="py-20 pb-32 bg-[#F8FCFF]">
        <div class="container mx-auto max-w-4xl px-4">
            
            <div class="text-center mb-16" data-aos="fade-up">
                <div class="inline-flex items-center space-x-2 bg-white border border-gray-200 rounded-full px-5 py-2.5 text-orange-500 font-bold text-sm mb-6 shadow-sm">
                    <img src="../assets/icons/activity1.svg">
                    <span>SERING DITANYAKAN</span>
                </div>

                <h2 class="text-4xl md:text-5xl font-inter font-medium text-[#1B2D62] leading-tight mb-6">
                    Layanan Konsultatif
                </h2>
                
                <p class="text-lg text-gray-500 max-w-2xl mx-auto">
                    Temukan jawaban atas pertanyaan umum seputar layanan penelitian dan konsultasi kami di bawah ini.
                </p>
            </div>

            <div class="max-w-4xl mx-auto">

                <?php
                try {
                    $stmtFaq = $pdo->prepare("SELECT pertanyaan, jawaban FROM konsultatif WHERE jawaban IS NOT NULL AND jawaban != '' ORDER BY id DESC LIMIT 5");
                    $stmtFaq->execute();
                    $faqList = $stmtFaq->fetchAll(PDO::FETCH_ASSOC);
                } catch (PDOException $e) {
                    echo "Error fetching FAQ: " . $e->getMessage();
                    $faqList = [];
                }
                ?>

                <?php if (!empty($faqList)): ?>
                    <div class="bg-white border-2 border-gray-200 rounded-2xl overflow-hidden shadow-sm" data-aos="fade-up" data-aos-delay="100">
                        <?php foreach ($faqList as $index => $faq): ?>
                            
                            <div class="faq-item <?php echo $index > 0 ? 'border-t border-gray-200' : ''; ?>">
                                 
                                <button class="faq-header flex items-center justify-between w-full px-6 py-5 text-left cursor-pointer hover:bg-orange-50/20 transition-colors duration-300 group">
                                    <span class="text-lg font-semibold text-[#1B2D62] pr-4 group-hover:text-orange-600 transition-colors">
                                        <?= htmlspecialchars($faq['pertanyaan']) ?>
                                    </span>
                                    
                                    <div class="w-10 h-10 bg-orange-50 rounded-xl flex items-center justify-center flex-shrink-0 transition-all duration-300 group-hover:bg-orange-100">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5 text-orange-500 icon-plus transition-transform duration-300">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                                        </svg>
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5 text-orange-500 icon-minus hidden transition-transform duration-300">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14" />
                                        </svg>
                                    </div>
                                </button>
                                
                                <div class="faq-content overflow-hidden max-h-0 transition-all duration-500 ease-in-out">
                                    <div class="px-6 pb-6 pt-0">
                                        <div class="h-px bg-gray-100 mb-4"></div>
                                        <p class="text-gray-600 leading-relaxed text-base">
                                            <?= htmlspecialchars($faq['jawaban']) ?>
                                        </p>
                                    </div>
                                </div>
                            </div>

                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="text-center py-12 bg-white rounded-2xl border-2 border-gray-200" data-aos="fade-up">
                        <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-inbox text-gray-400 text-2xl"></i>
                        </div>
                        <p class="text-gray-500 font-medium">Belum ada pertanyaan yang sering diajukan saat ini.</p>
                    </div>  
                <?php endif; ?>

            </div>

        </div>
    </section>

    <section class="py-24 sm:py-32">
        <div class="container mx-auto max-w-7xl px-4">
            
            <div class="max-w-3xl mx-auto text-center mb-16" data-aos="fade-up">
                
                <div class="inline-flex items-center space-x-2 bg-white border border-gray-200 rounded-full px-5 py-2.5 text-orange-500 font-bold text-sm mb-6">
                    <img src="../assets/icons/activity1.svg">
                    <span>AKTIVITAS KAMI</span>
                </div>

                <h2 class="text-4xl md:text-5xl font-inter font-medium text-gray-900 leading-tight mb-6">
                    Galeri Terbaru
                </h2>
                
                <p class="text-lg text-gray-500">
                    Dokumentasi kegiatan dan aktivitas terbaru dari Laboratorium Network & Cyber Security.
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">

                <?php
                try {
                    $stmtGaleri = $pdo->prepare("SELECT * FROM galeri WHERE is_active = true ORDER BY tanggal_kegiatan DESC LIMIT 3");
                    $stmtGaleri->execute();
                    $galeriList = $stmtGaleri->fetchAll(PDO::FETCH_ASSOC);
                } catch (PDOException $e) {
                    echo "Error fetching Galeri: " . $e->getMessage();
                    $galeriList = [];
                }
                ?>

                <?php if (!empty($galeriList)): ?>
                    <?php foreach ($galeriList as $index => $item): ?>
                        
                        <?php 
                            $gambar_path = !empty($item['gambar_path']) ? '../uploads' . $item['gambar_path'] : '../assets/img/no-image.png';

                            $item_data = [
                                'judul' => htmlspecialchars($item['judul']),
                                'deskripsi' => htmlspecialchars($item['deskripsi'] ?: 'Dokumentasi kegiatan Laboratorium Network & Cyber Security.'),
                                'tipe' => htmlspecialchars($item['tipe']),
                                'lokasi' => htmlspecialchars($item['lokasi'] ?? ''),
                                'tanggal' => !empty($item['tanggal_kegiatan']) ? date('d F Y', strtotime($item['tanggal_kegiatan'])) : '-',
                                'gambar' => $gambar_path,
                                'is_featured' => !empty($item['is_featured'])
                            ];
                        ?>

                        <div class="group bg-white border-2 border-gray-200 rounded-2xl overflow-hidden hover:border-orange-500 hover:shadow-2xl transition-all duration-300 hover:-translate-y-2 cursor-pointer"
                            data-aos="fade-up"
                            data-aos-delay="<?php echo ($index * 100); ?>"
                            onclick='showGaleriDetail(<?php echo json_encode($item_data, JSON_HEX_APOS | JSON_HEX_QUOT); ?>)'
                            data-toggle="modal"
                            data-target="#modalDetailGaleri">
                             
                            <div class="relative h-48 bg-gradient-to-br from-gray-100 to-gray-200 overflow-hidden">
                                <img src="<?= htmlspecialchars($gambar_path) ?>" 
                                     alt="<?= htmlspecialchars($item['judul']) ?>" 
                                     class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                                
                                <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                                
                                <div class="absolute inset-0 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                                    <span class="w-14 h-14 bg-white/90 rounded-full flex items-center justify-center text-orange-600 group-hover:bg-orange-500 group-hover:text-white transition-all duration-300 shadow-xl">
                                        <i class="fas fa-eye text-xl"></i>
                                    </span>
                                </div>
                                
                                <span class="absolute top-4 left-4 px-3 py-1.5 text-xs font-bold rounded-lg uppercase tracking-wide shadow-lg
                                    <?= strtolower($item['tipe']) === 'agenda' ? 'bg-blue-500 text-white' : 'bg-orange-500 text-white'; ?>">
                                    <?= htmlspecialchars($item['tipe']) ?>
                                </span>
                                
                                <?php if (!empty($item['is_featured'])): ?>
                                <span class="absolute top-4 right-4 px-3 py-1.5 text-xs font-bold rounded-lg uppercase tracking-wide shadow-lg bg-yellow-500 text-white">
                                    <i class="fas fa-star mr-1"></i>Featured
                                </span>
                                <?php endif; ?>
                            </div>
                            
                            <div class="p-6">
                                <h3 class="text-xl font-medium text-[#1B2D62] mb-3 group-hover:text-[#2C4AA4] transition-colors duration-300 line-clamp-2">
                                    <?= htmlspecialchars($item['judul']) ?>
                                </h3>
                                
                                <?php if (!empty($item['lokasi'])): ?>
                                <div class="flex items-center gap-2 text-sm text-gray-500 mb-3">
                                    <i class="fas fa-map-marker-alt text-orange-500"></i>
                                    <span><?= htmlspecialchars($item['lokasi']) ?></span>
                                </div>
                                <?php endif; ?>
                                
                                <p class="text-sm text-gray-600 leading-relaxed line-clamp-3">
                                    <?= htmlspecialchars($item['deskripsi'] ?: 'Dokumentasi kegiatan Laboratorium Network & Cyber Security.') ?>
                                </p>
                                
                                <div class="flex items-center justify-between pt-5 mt-5 border-t-2 border-gray-100">
                                    <span class="inline-flex items-center gap-2 text-orange-600 font-medium hover:text-orange-700 transition-colors group/link">
                                        <span>Lihat Detail</span>
                                        <i class="fas fa-arrow-right group-hover/link:translate-x-1 transition-transform"></i>
                                    </span>
                                    
                                    <div class="flex items-center gap-2 text-gray-500">
                                        <div class="flex items-center gap-1.5 px-3 py-1.5 bg-gray-50 rounded-lg">
                                            <i class="fas fa-calendar text-gray-400"></i>
                                            <span class="font-medium text-sm">
                                                <?= !empty($item['tanggal_kegiatan']) ? date('d M Y', strtotime($item['tanggal_kegiatan'])) : '-'; ?>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="col-span-1 lg:col-span-3 text-center py-10 text-gray-500 bg-gray-50 rounded-xl border border-dashed border-gray-300" data-aos="fade-up">
                        <p>Belum ada kegiatan terbaru yang diunggah.</p>
                    </div>
                <?php endif; ?>

            </div>

            <div class="text-center mt-16" data-aos="fade-up">
                <a href="./galeri.php" class="inline-flex items-center space-x-2 bg-[#1B2D62] text-white font-bold text-base px-7 py-3.5 rounded-lg shadow-sm transition hover:bg-[#2C4AA4] hover:-translate-y-0.5">
                    <span>Lihat Semua Kegiatan</span>
                    <img src="../assets/icons/arrow-up-right1.svg">
                </a>
            </div>

        </div>
    </section>

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
                        <a href="./arsip.php" class="inline-flex items-center justify-center gap-2 bg-white/10 backdrop-blur-sm border-2 border-white/30 text-white font-bold px-8 py-4 rounded-xl hover:bg-white/20 transition-all duration-300">
                            <i class="fas fa-book"></i>
                            Lihat Arsip
                        </a>
                    </div>

                </div>
            </div>
        </div>    
    </section>

    <?php include __DIR__ . '/../includes/public/modal_galeri.php'; ?>
    <?php include __DIR__ . '/../includes/public/modal_arsip.php'; ?>
</main>

<?php
    require_once __DIR__ . '/../includes/footer.php';
?>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const faqHeaders = document.querySelectorAll(".faq-header");

        faqHeaders.forEach(header => {
            header.addEventListener("click", function() {
                const currentItem = this.parentElement;
                const content = this.nextElementSibling;
                const iconPlus = this.querySelector('.icon-plus');
                const iconMinus = this.querySelector('.icon-minus');
                
                const isOpen = !content.classList.contains('max-h-0');

                document.querySelectorAll('.faq-item').forEach(item => {
                    if (item !== currentItem) {
                        const otherContent = item.querySelector('.faq-content');
                        const otherPlus = item.querySelector('.icon-plus');
                        const otherMinus = item.querySelector('.icon-minus');
                        const otherHeader = item.querySelector('.faq-header');

                        otherContent.style.maxHeight = '0px';
                        otherContent.classList.add('max-h-0');
                        
                        if(otherPlus) otherPlus.classList.remove('hidden');
                        if(otherMinus) otherMinus.classList.add('hidden');
                        
                        otherHeader.classList.remove('bg-orange-50/30');
                    }
                });

                if (isOpen) {
                    content.style.maxHeight = '0px';
                    content.classList.add('max-h-0');
                    
                    iconPlus.classList.remove('hidden');
                    iconMinus.classList.add('hidden');
                    
                    this.classList.remove('bg-orange-50/30');

                } else {
                    content.classList.remove('max-h-0');
                    content.style.maxHeight = content.scrollHeight + "px";
                    
                    iconPlus.classList.add('hidden');
                    iconMinus.classList.remove('hidden');
                    
                    this.classList.add('bg-orange-50/30');
                }
            });
        });
    });

    // Fungsi Pop Up Modal Arsip
    function showArsipDetail(data) {
        $('#detail-judul-arsip').text(data.judul);
        
        const kategoriClass = data.kategori.toLowerCase() === 'penelitian' 
            ? 'bg-blue-100 text-blue-700 border border-blue-200' 
            : 'bg-green-100 text-green-700 border border-green-200';
        
        const kategoriIcon = data.kategori.toLowerCase() === 'penelitian' ? '<i class="fas fa-flask mr-1"></i>' : '<i class="fas fa-hands-helping mr-1"></i>';
        
        $('#detail-kategori-arsip').removeClass().addClass(`px-3 py-1.5 text-xs font-bold rounded-lg uppercase tracking-wide ${kategoriClass}`)
            .html(`${kategoriIcon} ${data.kategori}`);
        
        if (data.is_featured) {
            $('#detail-featured-badge-arsip').removeClass('hidden');
        } else {
            $('#detail-featured-badge-arsip').addClass('hidden');
        }
        
        $('#detail-tahun-arsip').text(data.tahun_publikasi || '-');
        $('#detail-penerbit-arsip').text(data.penerbit || 'Tidak tersedia');
        $('#detail-download-arsip').text(parseInt(data.jumlah_download).toLocaleString());
        
        const fileSizeKB = data.file_size_kb || 0;
        let fileSizeDisplay = '-';
        if (fileSizeKB > 0) {
            if (fileSizeKB >= 1024) {
                fileSizeDisplay = (fileSizeKB / 1024).toFixed(2) + ' MB';
            } else {
                fileSizeDisplay = fileSizeKB + ' KB';
            }
        }
        $('#detail-filesize-arsip').text(fileSizeDisplay);
        
        let authorsHtml = '';
        if (data.penulis_list && data.penulis_list.length > 0) {
            data.penulis_list.forEach(function(author, index) {
                const peranBadge = author.peran ? `<span class="px-2 py-0.5 bg-blue-100 text-blue-700 text-xs rounded-full font-medium">${author.peran}</span>` : '';
                authorsHtml += `
                    <div class="flex items-center gap-3 p-3 bg-white rounded-lg border border-gray-200">
                        <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-blue-600 rounded-full flex items-center justify-center flex-shrink-0">
                            <span class="text-white font-bold text-sm">${index + 1}</span>
                        </div>
                        <div class="flex-1">
                            <div class="font-semibold text-gray-800">${author.nama}</div>
                            ${author.jabatan ? `<div class="text-xs text-gray-500">${author.jabatan}</div>` : ''}
                        </div>
                        ${peranBadge}
                    </div>
                `;
            });
        } else {
            authorsHtml = '<div class="text-gray-500 text-sm italic">Belum ada data penulis</div>';
        }
        $('#detail-penulis-list-arsip').html(authorsHtml);
        
        $('#detail-abstrak-arsip').text(data.abstrak);
        
        let keywordsHtml = '';
        if (data.keywords) {
            const keywords = data.keywords.split(',');
            keywords.forEach(function(keyword) {
                keyword = keyword.trim();
                if (keyword) {
                    keywordsHtml += `<span class="px-3 py-1 bg-orange-100 text-orange-700 text-xs font-semibold rounded-full">${keyword}</span>`;
                }
            });
        } else {
            keywordsHtml = '<span class="text-gray-500 text-sm italic">Tidak ada kata kunci</span>';
        }
        $('#detail-keywords-arsip').html(keywordsHtml);
        
        if (data.doi) {
            const doiUrl = data.doi.startsWith('http') ? data.doi : `https://doi.org/${data.doi}`;
            $('#detail-doi-arsip').attr('href', doiUrl).text(data.doi);
            $('#detail-doi-container-arsip').show();
        } else {
            $('#detail-doi-container-arsip').hide();
        }
        
        $('#detail-download-btn-arsip').attr('href', `./arsip.php?view=${data.id}`);
    }

    function showGaleriDetail(data) {
        // Set image
        $('#detail-gambar').attr('src', data.gambar);
        
        // Set title
        $('#detail-judul').text(data.judul);
        
        // Set category with badge
        const tipeClass = data.tipe.toLowerCase() === 'agenda' 
            ? 'bg-blue-500 text-white' 
            : 'bg-orange-500 text-white';
        $('#detail-tipe').html(`<span class="px-3 py-1.5 rounded-lg text-xs font-bold uppercase tracking-wide ${tipeClass}">${data.tipe}</span>`);
        
        // Set date
        $('#detail-tanggal').text(data.tanggal);
        
        // Set location
        $('#detail-lokasi').text(data.lokasi || 'Tidak tersedia');
        
        // Set description
        $('#detail-deskripsi').text(data.deskripsi);
        
        // Show/hide featured badge
        if (data.is_featured) {
            $('#detail-featured-container').removeClass('hidden');
        } else {
            $('#detail-featured-container').addClass('hidden');
        }
    }
</script>