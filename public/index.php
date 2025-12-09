<?php
    require_once __DIR__ . '/../includes/header.php';
?> 

<main>
    <section class="relative lg:py-44 py-32 px-4 overflow-hidden">  
        <div class="absolute inset-0 opacity-[0.5]" style="background-image: url('data:image/svg+xml,%3Csvg width=%2260%22 height=%2260%22 viewBox=%220 0 60 60%22 xmlns=%22http://www.w3.org/2000/svg%22%3E%3Cg fill=%22none%22 fill-rule=%22evenodd%22%3E%3Cg fill=%22%23000%22 fill-opacity=%221%22%3E%3Cpath d=%22M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z%22/%3E%3C/g%3E%3C/g%3E%3C/svg%3E');"></div>

        <!-- Gradient Mesh Background - Inspired by Prismo style -->
        <div class="absolute inset-0 overflow-hidden pointer-events-none">
            <!-- Base gradient layer -->
            <div class="absolute inset-0 bg-gradient-to-b from-orange-50/80 via-white to-blue-50/60"></div>
            
            <!-- Left gradient blob - Orange/Peach -->
            <div class="absolute -left-20 top-0 w-[60%] h-full bg-gradient-to-br from-orange-100/70 via-orange-50/50 to-transparent blur-3xl"></div>
            
            <!-- Right gradient blob - Blue/Purple -->
            <div class="absolute -right-20 top-0 w-[60%] h-full bg-gradient-to-bl from-blue-100/60 via-indigo-50/40 to-transparent blur-3xl"></div>
            
            <!-- Center fade overlay -->
            <div class="absolute inset-0 bg-gradient-to-b from-transparent via-white/30 to-white/60"></div>
            
            <!-- Subtle noise texture overlay for depth -->
            <div class="absolute inset-0 opacity-[0.015]" style="background-image: url('data:image/svg+xml,%3Csvg viewBox=%220 0 256 256%22 xmlns=%22http://www.w3.org/2000/svg%22%3E%3Cfilter id=%22noise%22%3E%3CfeTurbulence type=%22fractalNoise%22 baseFrequency=%220.9%22 numOctaves=%224%22 stitchTiles=%22stitch%22/%3E%3C/filter%3E%3Crect width=%22100%25%22 height=%22100%25%22 filter=%22url(%23noise)%22/%3E%3C/svg%3E');"></div>
        </div>

        <!-- Floating Icons - Positioned around hero content -->
        <div class="hidden md:block absolute inset-0 max-w-7xl mx-auto pointer-events-none">
            <!-- Left Top Icon -->
            <div class="absolute left-4 lg:left-12 xl:left-20 top-1/4 w-14 h-14 lg:w-18 lg:h-18 xl:w-20 xl:h-20 bg-white rounded-2xl shadow-lg border border-gray-100 flex items-center justify-center opacity-90 animate-float">
                <i class="fas fa-shield-alt text-[#1B2D62] text-xl lg:text-2xl xl:text-3xl"></i>
            </div>

            <!-- Left Bottom Icon -->
            <div class="absolute left-8 lg:left-20 xl:left-32 bottom-1/4 w-14 h-14 lg:w-18 lg:h-18 xl:w-20 xl:h-20 bg-white rounded-2xl shadow-lg border border-gray-100 flex items-center justify-center opacity-90 animate-float-delayed">
                <i class="fas fa-lock text-[#1B2D62] text-xl lg:text-2xl xl:text-3xl"></i>
            </div>

            <!-- Right Top Icon -->
            <div class="absolute right-4 lg:right-12 xl:right-20 top-1/4 w-14 h-14 lg:w-18 lg:h-18 xl:w-20 xl:h-20 bg-white rounded-2xl shadow-lg border border-gray-100 flex items-center justify-center opacity-90 animate-float-delayed">
                <i class="fas fa-network-wired text-orange-500 text-xl lg:text-2xl xl:text-3xl"></i>
            </div>

            <!-- Right Bottom Icon -->
            <div class="absolute right-8 lg:right-20 xl:right-32 bottom-1/4 w-14 h-14 lg:w-18 lg:h-18 xl:w-20 xl:h-20 bg-white rounded-2xl shadow-lg border border-gray-100 flex items-center justify-center opacity-90 animate-float">
                <i class="fas fa-database text-orange-500 text-xl lg:text-2xl xl:text-3xl"></i>
            </div>
        </div>
         
        <!-- Hero Content -->
        <div class="mx-auto max-w-4xl flex flex-col items-center relative text-center">
            
            <!-- Badge -->
            <div class="inline-flex items-center gap-2.5 px-5 py-2.5 bg-white/80 backdrop-blur-sm border border-orange-200/60 rounded-full text-orange-600 font-semibold text-sm mb-8 shadow-lg shadow-orange-100/50" data-aos="fade-up">
                <span class="w-2 h-2 bg-orange-500 rounded-full animate-pulse"></span>
                <span class="tracking-wide">INOVASI TEKNOLOGI KEAMANAN</span>
            </div>

            <!-- Main Heading with Gradient Text -->
            <h1 class="text-4xl sm:text-5xl md:text-6xl lg:text-7xl font-medium text-[#1B2D62] leading-[1.1] tracking-tight mb-8" data-aos="fade-up" data-aos-delay="100">
                Selamat Datang di<br class="hidden sm:block"> 
                <span class="bg-gradient-to-r from-[#1B2D62] via-[#2C4AA4] to-orange-500 bg-clip-text text-transparent">Network & Cyber Security</span>
            </h1>

            <p class="text-lg md:text-xl text-gray-600 max-w-2xl mb-10 leading-relaxed" data-aos="fade-up" data-aos-delay="200">
                Pusat inovasi dan penelitian keamanan siber untuk membangun ekosistem digital yang aman dan terpercaya.
            </p>

            <!-- Feature Tags -->
            <div class="flex flex-wrap justify-center gap-3 sm:gap-6 mb-12" data-aos="fade-up" data-aos-delay="300">
                <div class="flex items-center gap-2 px-4 py-2 bg-white/70 backdrop-blur-sm rounded-full border border-gray-200/60 shadow-sm">
                    <div class="w-5 h-5 bg-gradient-to-br from-emerald-400 to-emerald-600 rounded-full flex items-center justify-center">
                        <i class="fas fa-check text-white text-[10px]"></i>
                    </div>
                    <span class="font-medium text-gray-700 text-sm">Jaringan</span>
                </div>
                <div class="flex items-center gap-2 px-4 py-2 bg-white/70 backdrop-blur-sm rounded-full border border-gray-200/60 shadow-sm">
                    <div class="w-5 h-5 bg-gradient-to-br from-emerald-400 to-emerald-600 rounded-full flex items-center justify-center">
                        <i class="fas fa-check text-white text-[10px]"></i>
                    </div>
                    <span class="font-medium text-gray-700 text-sm">Capture The Flag</span>
                </div>
                <div class="flex items-center gap-2 px-4 py-2 bg-white/70 backdrop-blur-sm rounded-full border border-gray-200/60 shadow-sm">
                    <div class="w-5 h-5 bg-gradient-to-br from-emerald-400 to-emerald-600 rounded-full flex items-center justify-center">
                        <i class="fas fa-check text-white text-[10px]"></i>
                    </div>
                    <span class="font-medium text-gray-700 text-sm">Security System</span>
                </div>
            </div>

            <!-- CTA Button with Enhanced Styling -->
            <div class="flex flex-col sm:flex-row gap-4" data-aos="fade-up" data-aos-delay="400">
                <a href="./layanan.php" class="group inline-flex items-center justify-center gap-3 bg-gradient-to-r from-[#1B2D62] to-[#2C4AA4] text-white font-medium text-base px-8 py-4 rounded-xl shadow-lg shadow-blue-900/25 transition-all duration-300 hover:shadow-xl hover:shadow-blue-900/30 hover:scale-[1.02] active:scale-[0.98] focus:outline-none focus:ring-2 focus:ring-[#1B2D62] focus:ring-offset-2">
                    <span>Jelajahi Tentang Kami</span>
                    <i class="fas fa-arrow-right transition-transform duration-300 group-hover:translate-x-1"></i>
                </a>
                <a href="./profil.php" class="group inline-flex items-center justify-center gap-3 bg-white text-[#1B2D62] font-medium text-base px-8 py-4 rounded-xl border-2 border-gray-200 shadow-sm transition-all duration-300 hover:border-[#1B2D62]/30 hover:shadow-lg hover:bg-gray-50 active:scale-[0.98] focus:outline-none focus:ring-2 focus:ring-[#1B2D62] focus:ring-offset-2">
                    <span>Lihat Profil</span>
                    <i class="fas fa-external-link-alt text-sm transition-transform duration-300 group-hover:translate-x-0.5 group-hover:-translate-y-0.5"></i>
                </a>
            </div>

        </div>
    </section>

    <section class="relative py-24 sm:py-32 px-4 overflow-hidden">
        <!-- Background dengan subtle pattern -->
        <div class="absolute inset-0 bg-gradient-to-b from-slate-50 via-white to-orange-50/30"></div>
        <!-- <div class="absolute inset-0 opacity-[0.03]" style="background-image: url('data:image/svg+xml,%3Csvg width=%2260%22 height=%2260%22 viewBox=%220 0 60 60%22 xmlns=%22http://www.w3.org/2000/svg%22%3E%3Cg fill=%22none%22 fill-rule=%22evenodd%22%3E%3Cg fill=%22%23000%22 fill-opacity=%221%22%3E%3Cpath d=%22M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z%22/%3E%3C/g%3E%3C/g%3E%3C/svg%3E');"></div>
         -->
        <div class="relative mx-auto max-w-7xl">
            
            <!-- Section Header - Centered -->
            <div class="max-w-3xl mx-auto text-center mb-16" data-aos="fade-up">
                <div class="inline-flex items-center gap-2.5 px-5 py-2.5 bg-white/80 backdrop-blur-sm border border-orange-200/60 rounded-full text-orange-600 font-semibold text-sm mb-6 shadow-lg shadow-orange-100/50">
                    <span class="w-2 h-2 bg-orange-500 rounded-full animate-pulse"></span>
                    <span class="tracking-wide">LAYANAN UNGGULAN</span>
                </div>

                <h2 class="text-4xl md:text-5xl lg:text-6xl font-medium text-[#1B2D62] leading-[1.1] tracking-tight mb-6">
                    Jelajahi Layanan<br class="hidden sm:block">
                    <span class="bg-gradient-to-r from-[#1B2D62] via-[#2C4AA4] to-orange-500 bg-clip-text text-transparent">Terbaik Kami</span>
                </h2>
                
                <p class="text-lg md:text-xl text-gray-600 max-w-2xl mx-auto leading-relaxed">
                    Solusi komprehensif untuk kebutuhan keamanan siber dan jaringan dengan standar industri terkini.
                </p>
            </div>

            <!-- Bento Grid Layout -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 lg:gap-6">
                
                <?php
                    $layananList = executeQuery("SELECT * FROM layanan WHERE status = 'Aktif' ORDER BY id ASC LIMIT 4");
                    if ($layananList === false) $layananList = [];
                ?>

                <?php if (!empty($layananList)): ?>
                    <?php 
                    // Card configurations for bento grid
                    $cardConfigs = [
                        [
                            'span' => 'md:col-span-2 lg:col-span-2 lg:row-span-2',
                            'height' => 'min-h-[320px] lg:min-h-[400px]',
                            'icon' => 'fa-shield-alt',
                            'gradient' => 'from-[#1B2D62] to-[#2C4AA4]',
                            'glow' => 'shadow-blue-500/20',
                            'pattern' => 'radial-gradient(circle at 20% 80%, rgba(255,255,255,0.1) 0%, transparent 50%)',
                            'tag' => 'Popular',
                            'tagColor' => 'bg-orange-500'
                        ],
                        [
                            'span' => 'lg:col-span-2',
                            'height' => 'min-h-[180px] lg:min-h-[190px]',
                            'icon' => 'fa-network-wired',
                            'gradient' => 'from-orange-500 to-orange-600',
                            'glow' => 'shadow-orange-500/20',
                            'pattern' => 'radial-gradient(circle at 80% 20%, rgba(255,255,255,0.15) 0%, transparent 40%)',
                            'tag' => 'New',
                            'tagColor' => 'bg-emerald-500'
                        ],
                        [
                            'span' => 'lg:col-span-1',
                            'height' => 'min-h-[180px] lg:min-h-[190px]',
                            'icon' => 'fa-laptop-code',
                            'gradient' => 'from-violet-500 to-purple-600',
                            'glow' => 'shadow-violet-500/20',
                            'pattern' => 'linear-gradient(135deg, rgba(255,255,255,0.1) 0%, transparent 50%)',
                            'tag' => '',
                            'tagColor' => ''
                        ],
                        [
                            'span' => 'lg:col-span-1',
                            'height' => 'min-h-[180px] lg:min-h-[190px]',
                            'icon' => 'fa-cogs',
                            'gradient' => 'from-emerald-500 to-teal-600',
                            'glow' => 'shadow-emerald-500/20',
                            'pattern' => 'radial-gradient(circle at 50% 0%, rgba(255,255,255,0.12) 0%, transparent 60%)',
                            'tag' => '',
                            'tagColor' => ''
                        ]
                    ];
                    
                    foreach ($layananList as $index => $item): 
                        $config = $cardConfigs[$index % count($cardConfigs)];
                        $isLarge = $index === 0;
                    ?>
                        <!-- Card <?php echo $index + 1; ?> -->
                        <div class="group relative <?php echo $config['span']; ?> <?php echo $config['height']; ?> rounded-3xl overflow-hidden cursor-pointer transition-all duration-500 hover:scale-[1.02] hover:shadow-2xl <?php echo $config['glow']; ?>"
                             data-aos="fade-up" 
                             data-aos-delay="<?php echo ($index * 100); ?>">
                            
                            <!-- Gradient Background -->
                            <div class="absolute inset-0 bg-gradient-to-br <?php echo $config['gradient']; ?> opacity-95"></div>
                            
                            <!-- Pattern Overlay -->
                            <div class="absolute inset-0 opacity-60" style="background: <?php echo $config['pattern']; ?>"></div>
                            
                            <!-- Glassmorphic shine effect -->
                            <div class="absolute inset-0 opacity-0 group-hover:opacity-100 transition-opacity duration-500" style="background: linear-gradient(105deg, transparent 40%, rgba(255,255,255,0.2) 45%, rgba(255,255,255,0.3) 50%, rgba(255,255,255,0.2) 55%, transparent 60%); background-size: 200% 100%; animation: shine 1.5s ease-in-out;"></div>
                            
                            <!-- Content -->
                            <div class="relative h-full p-6 lg:p-8 flex flex-col justify-between z-10">
                                
                                <!-- Top: Tag & Icon -->
                                <div class="flex items-start justify-between mb-4">
                                    <!-- Icon with glow -->
                                    <div class="w-14 h-14 <?php echo $isLarge ? 'lg:w-20 lg:h-20' : 'lg:w-16 lg:h-16'; ?> bg-white/20 backdrop-blur-sm rounded-2xl flex items-center justify-center group-hover:bg-white/30 group-hover:scale-110 transition-all duration-300 shadow-lg">
                                        <i class="fas <?php echo $config['icon']; ?> text-white <?php echo $isLarge ? 'text-2xl lg:text-4xl' : 'text-xl lg:text-2xl'; ?>"></i>
                                    </div>
                                    
                                    <?php if (!empty($config['tag'])): ?>
                                    <!-- Tag Label -->
                                    <span class="<?php echo $config['tagColor']; ?> text-white text-xs font-medium px-3 py-1.5 rounded-full uppercase tracking-wider shadow-lg">
                                        <?php echo $config['tag']; ?>
                                    </span>
                                    <?php endif; ?>
                                </div>
                                
                                <!-- Bottom: Title, Description & CTA -->
                                <div class="mt-auto">
                                    <h3 class="text-xl <?php echo $isLarge ? 'lg:text-3xl' : 'lg:text-xl'; ?> font-medium text-white mb-2 lg:mb-3 leading-tight">
                                        <?= htmlspecialchars($item['nama_layanan']) ?>
                                    </h3>
                                    
                                    <p class="text-white/80 text-sm <?php echo $isLarge ? 'lg:text-base line-clamp-3' : 'line-clamp-2'; ?> leading-relaxed mb-4 break-words">
                                        <?= htmlspecialchars($item['deskripsi']) ?>
                                    </p>
                                    
                                    <!-- Learn More CTA -->
                                    <div class="flex items-center gap-2 text-white/90 font-semibold text-sm group-hover:text-white transition-colors">
                                        <span>Pelajari Lebih Lanjut</span>
                                        <i class="fas fa-arrow-right text-xs group-hover:translate-x-2 transition-transform duration-300"></i>
                                    </div>
                                </div>
                                
                            </div>
                            
                            <!-- Hover Overlay -->
                            <div class="absolute inset-0 bg-black/0 group-hover:bg-black/10 transition-colors duration-300"></div>
                            
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="col-span-full text-center py-16 bg-gray-50 rounded-3xl border-2 border-dashed border-gray-200" data-aos="fade-up">
                        <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-inbox text-gray-400 text-2xl"></i>
                        </div>
                        <p class="text-gray-500 font-medium">Belum ada data layanan tersedia.</p>
                    </div>
                <?php endif; ?>

            </div>

            <!-- CTA Button -->
            <div class="text-center mt-12" data-aos="fade-up">
                <a href="./layanan.php" class="group inline-flex items-center justify-center gap-3 bg-gradient-to-r from-[#1B2D62] to-[#2C4AA4] text-white font-medium text-base px-8 py-4 rounded-xl shadow-lg shadow-blue-900/25 transition-all duration-300 hover:shadow-xl hover:shadow-blue-900/30 hover:scale-[1.02] active:scale-[0.98]">
                    <span>Lihat Semua Layanan</span>
                    <i class="fas fa-arrow-right transition-transform duration-300 group-hover:translate-x-1"></i>
                </a>
            </div>

        </div>
    </section>

    <section class="relative py-24 sm:py-32 overflow-hidden px-4">
        <!-- Background -->
        <!-- <div class="absolute inset-0 bg-gradient-to-b from-white via-slate-100/30 to-white"></div> -->
        
        <div class="relative mx-auto max-w-7xl">
            
            <!-- Section Header -->
            <div class="max-w-3xl mx-auto text-center mb-12" data-aos="fade-up">
                <div class="inline-flex items-center gap-2.5 px-5 py-2.5 bg-white/80 backdrop-blur-sm border border-orange-200/60 rounded-full text-orange-600 font-semibold text-sm mb-6 shadow-lg shadow-orange-100/50">
                    <span class="w-2 h-2 bg-orange-500 rounded-full animate-pulse"></span>
                    <span class="tracking-wide">PUBLIKASI & ARSIP</span>
                </div>

                <h2 class="text-4xl md:text-5xl lg:text-6xl font-medium text-[#1B2D62] leading-[1.1] tracking-tight mb-6">
                    Dokumen<br class="hidden sm:block">
                    <span class="bg-gradient-to-r from-[#1B2D62] via-[#2C4AA4] to-orange-500 bg-clip-text text-transparent">Penelitian Terbaru</span>
                </h2>
                
                <p class="text-lg md:text-xl text-gray-600 max-w-2xl mx-auto leading-relaxed">
                    Kumpulan dokumen penelitian dan pengabdian masyarakat terbaru dari Laboratorium Network & Cyber Security.
                </p>
            </div>

            <!-- Filter Chips -->
            <div class="flex justify-center mb-12" data-aos="fade-up" data-aos-delay="100">
                <div class="inline-flex items-center p-1.5 bg-gray-100/80 backdrop-blur-sm rounded-2xl gap-2">
                    <button type="button" class="arsip-filter-btn active px-6 py-3 rounded-xl text-sm font-medium transition-all duration-300" data-filter="all">
                        <span class="flex items-center gap-2">
                            <i class="fas fa-layer-group"></i>
                            Semua
                        </span>
                    </button>
                    <button type="button" class="arsip-filter-btn px-6 py-3 rounded-xl text-sm font-medium transition-all duration-300" data-filter="penelitian">
                        <span class="flex items-center gap-2">
                            <i class="fas fa-flask"></i>
                            Penelitian
                        </span>
                    </button>
                    <button type="button" class="arsip-filter-btn px-6 py-3 rounded-xl text-sm font-medium transition-all duration-300" data-filter="pengabdian">
                        <span class="flex items-center gap-2">
                            <i class="fas fa-hands-helping"></i>
                            Pengabdian
                        </span>
                    </button>
                </div>
            </div>

            <!-- Cards Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 lg:gap-8" id="arsip-grid">

                <?php
                // Mengambil 3 data arsip terbaru dari database
                $arsipList = executeQuery("SELECT * FROM arsip WHERE is_active = true and is_featured = true ORDER BY created_at DESC LIMIT 3");
                if ($arsipList === false) $arsipList = [];

                foreach ($arsipList as &$arsip_item) {
                    $arsip_item['penulis_list'] = executeQuery(
                        "SELECT p.nama_lengkap, p.jabatan, p.foto_path FROM arsip_pengelola ap JOIN pengelola p ON ap.pengelola_id = p.id WHERE ap.arsip_id = ?",
                        [$arsip_item['id']]
                    );
                    if ($arsip_item['penulis_list'] === false) $arsip_item['penulis_list'] = [];
                    
                    if (!empty($arsip_item['penulis_list'])) {
                        $names = array_map(function($a) { return $a['nama_lengkap']; }, $arsip_item['penulis_list']);
                        $arsip_item['penulis_display'] = implode(', ', $names);
                    } else {
                        $arsip_item['penulis_display'] = '';
                    }
                }
                unset($arsip_item);
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
                                ];
                            }, $item['penulis_list'] ?? [])
                        ];
                        
                        $kategoriLower = strtolower($item['kategori']);
                        ?>

                        <!-- Arsip Card -->
                        <div class="arsip-card group relative bg-white rounded-3xl overflow-hidden cursor-pointer transition-all duration-500 hover:shadow-2xl hover:shadow-orange-200/50"
                             data-aos="fade-up" 
                             data-aos-delay="<?php echo ($index * 100); ?>"
                             data-category="<?php echo $kategoriLower; ?>"
                             onclick='showArsipDetail(<?php echo json_encode($item_data, JSON_HEX_APOS | JSON_HEX_QUOT); ?>)'
                             data-toggle="modal" 
                             data-target="#modalDetailArsip">
                            
                            <!-- Border -->
                            <div class="absolute inset-0 rounded-3xl border-2 border-gray-100 group-hover:border-orange-300 transition-colors duration-300"></div>
                            
                            <div class="relative p-6 lg:p-7">
                                
                                <!-- Top Section: Category Badge & Year -->
                                <div class="flex items-center justify-between mb-5">
                                    <span class="inline-flex items-center gap-2 px-4 py-2 text-xs font-medium rounded-full uppercase tracking-wider
                                        <?php echo $kategoriLower === 'penelitian' 
                                            ? 'bg-blue-100 text-blue-700 border border-blue-200' 
                                            : 'bg-emerald-100 text-emerald-700 border border-emerald-200'; ?>">
                                        <i class="fas <?php echo $kategoriLower === 'penelitian' ? 'fa-flask' : 'fa-hands-helping'; ?>"></i>
                                        <?php echo htmlspecialchars($item['kategori']); ?>
                                    </span>
                                    
                                    <?php if (!empty($item['tahun_publikasi'])): ?>
                                    <span class="text-sm font-medium text-gray-400">
                                        <?php echo htmlspecialchars($item['tahun_publikasi']); ?>
                                    </span>
                                    <?php endif; ?>
                                </div>
                                
                                <!-- Title -->
                                <h3 class="text-xl font-medium text-[#1B2D62] mb-4 line-clamp-2 group-hover:text-orange-600 transition-colors duration-300 leading-tight">
                                    <?php echo htmlspecialchars($item['judul']); ?>
                                </h3>
                                
                                <!-- Abstract -->
                                <p class="text-sm text-gray-500 mb-6 leading-relaxed line-clamp-3">
                                    <?php echo htmlspecialchars($item['abstrak'] ?: 'Dokumen penelitian dan pengabdian masyarakat dari Laboratorium Network & Cyber Security.'); ?>
                                </p>
                                
                                <!-- Authors Section with Overlapping Avatars -->
                                <?php if (!empty($item['penulis_list'])): ?>
                                <div class="flex items-center gap-3 mb-6">
                                    <!-- Overlapping Avatars -->
                                    <div class="flex -space-x-3">
                                        <?php 
                                        $maxAvatars = 3;
                                        $totalAuthors = count($item['penulis_list']);
                                        $displayAuthors = array_slice($item['penulis_list'], 0, $maxAvatars);
                                        $colors = ['from-blue-500 to-blue-600', 'from-orange-500 to-orange-600', 'from-violet-500 to-violet-600'];
                                        
                                        foreach ($displayAuthors as $idx => $author): 
                                            $initials = '';
                                            $nameParts = explode(' ', $author['nama_lengkap']);
                                            foreach ($nameParts as $part) {
                                                $initials .= strtoupper(substr($part, 0, 1));
                                                if (strlen($initials) >= 2) break;
                                            }
                                            $colorClass = $colors[$idx % count($colors)];
                                        ?>
                                            <div class="w-9 h-9 rounded-full bg-gradient-to-br <?php echo $colorClass; ?> flex items-center justify-center ring-2 ring-white shadow-md" title="<?php echo htmlspecialchars($author['nama_lengkap']); ?>">
                                                <span class="text-white text-xs font-medium"><?php echo $initials; ?></span>
                                            </div>
                                        <?php endforeach; ?>
                                        
                                        <?php if ($totalAuthors > $maxAvatars): ?>
                                            <div class="w-9 h-9 rounded-full bg-gray-200 flex items-center justify-center ring-2 ring-white">
                                                <span class="text-gray-600 text-xs font-medium">+<?php echo $totalAuthors - $maxAvatars; ?></span>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                    
                                    <!-- Author Names -->
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-medium text-gray-700 truncate">
                                            <?php echo htmlspecialchars($item['penulis_display']); ?>
                                        </p>
                                    </div>
                                </div>
                                <?php endif; ?>
                                
                                <!-- Footer: Download Button & Stats -->
                                <div class="flex items-center justify-between pt-5 border-t border-gray-100">
                                    <!-- Download Progress Style Button -->
                                    <div class="group/btn relative inline-flex items-center gap-2 px-5 py-2.5 bg-gradient-to-r from-orange-500 to-orange-600 hover:from-orange-600 hover:to-orange-700 text-white text-sm font-medium rounded-xl transition-all duration-300 shadow-lg shadow-orange-500/25 hover:shadow-xl overflow-hidden">
                                        <!-- Progress bar animation on hover -->
                                        <div class="absolute inset-0 bg-white/20 -translate-x-full group-hover/btn:translate-x-0 transition-transform duration-500"></div>
                                        <i class="fas fa-eye relative z-10"></i>
                                        <span class="relative z-10">Lihat Detail</span>
                                    </div>
                                    
                                    <!-- Download Stats -->
                                    <div class="flex items-center gap-3">
                                        <div class="flex items-center gap-1.5 text-gray-400">
                                            <i class="fas fa-download text-sm"></i>
                                            <span class="font-medium text-sm"><?php echo number_format($item['jumlah_download']); ?></span>
                                        </div>
                                    </div>
                                </div>
                                
                            </div>
                            
                        </div>

                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="col-span-full text-center py-16 bg-gray-50 rounded-3xl border-2 border-dashed border-gray-200" data-aos="fade-up">
                        <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-folder-open text-gray-400 text-2xl"></i>
                        </div>
                        <p class="text-gray-500 font-medium">Belum ada arsip dokumen yang tersedia saat ini.</p>
                    </div>
                <?php endif; ?>

            </div>

            <!-- CTA Button -->
            <div class="text-center mt-12" data-aos="fade-up">
                <a href="./arsip.php" class="group inline-flex items-center justify-center gap-3 bg-gradient-to-r from-[#1B2D62] to-[#2C4AA4] text-white font-medium text-base px-8 py-4 rounded-xl shadow-lg shadow-blue-900/25 transition-all duration-300 hover:shadow-xl hover:shadow-blue-900/30 hover:scale-[1.02] active:scale-[0.98]">
                    <span>Lihat Semua Dokumen</span>
                    <i class="fas fa-arrow-right transition-transform duration-300 group-hover:translate-x-1"></i>
                </a>
            </div>

        </div>
    </section>

    <section class="relative py-24 sm:py-32 overflow-hidden px-4">
        <!-- Decorative Blur Elements -->
        <div class="absolute top-20 left-10 w-72 h-72 bg-orange-200/20 rounded-full blur-3xl"></div>
        <div class="absolute bottom-20 right-10 w-96 h-96 bg-blue-200/20 rounded-full blur-3xl"></div>
        
        <div class="relative mx-auto max-w-7xl">
            
            <!-- Two Column Layout -->
            <div class="grid lg:grid-cols-5 gap-12 lg:gap-16 items-start">
                
                <!-- Left Column: Header (Sticky) -->
                <div class="lg:col-span-2" data-aos="fade-right">
                    <div class="inline-flex items-center gap-2.5 px-5 py-2.5 bg-white/80 backdrop-blur-sm border border-orange-200/60 rounded-full text-orange-600 font-semibold text-sm mb-6 shadow-lg shadow-orange-100/50">
                        <span class="w-2 h-2 bg-orange-500 rounded-full animate-pulse"></span>
                        <span class="tracking-wide">LAYANAN KONSULTATIF</span>
                    </div>

                    <h2 class="text-4xl md:text-5xl font-medium text-[#1B2D62] leading-[1.1] tracking-tight mb-6">
                        Pertanyaan yang<br>
                        <span class="bg-gradient-to-r from-[#1B2D62] via-[#2C4AA4] to-orange-500 bg-clip-text text-transparent">Sering Diajukan</span>
                    </h2>
                    
                    <p class="text-lg text-gray-600 leading-relaxed">
                        Temukan jawaban atas pertanyaan umum seputar layanan penelitian dan konsultasi kami di bawah ini.
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
                                             data-accordion-target="#faq-home-<?php echo $index; ?>"
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
                                        <div id="faq-home-<?php echo $index; ?>" class="overflow-hidden transition-all duration-500 ease-in-out">
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

    <section class="relative py-24 sm:py-32 overflow-hidden px-4">
        <!-- Background Elements -->
        <!-- <div class="absolute inset-0 bg-gradient-to-b from-white via-slate-50/50 to-white"></div> -->
        
        <!-- Decorative Blur Elements -->
        <div class="absolute top-40 right-0 w-80 h-80 bg-orange-100/30 rounded-full blur-3xl"></div>
        <div class="absolute bottom-20 left-0 w-96 h-96 bg-blue-100/20 rounded-full blur-3xl"></div>
        
        <div class="relative mx-auto max-w-7xl">
            
            <!-- Section Header -->
            <div class="max-w-3xl mx-auto text-center mb-16" data-aos="fade-up">
                <div class="inline-flex items-center gap-2.5 px-5 py-2.5 bg-white/80 backdrop-blur-sm border border-orange-200/60 rounded-full text-orange-600 font-semibold text-sm mb-6 shadow-lg shadow-orange-100/50">
                    <span class="w-2 h-2 bg-orange-500 rounded-full animate-pulse"></span>
                    <span class="tracking-wide">AKTIVITAS KAMI</span>
                </div>

                <h2 class="text-4xl md:text-5xl lg:text-6xl font-medium text-[#1B2D62] leading-[1.1] tracking-tight mb-6">
                    Galeri<br class="hidden sm:block">
                    <span class="bg-gradient-to-r from-[#1B2D62] via-[#2C4AA4] to-orange-500 bg-clip-text text-transparent">Kegiatan Terbaru</span>
                </h2>
                
                <p class="text-lg md:text-xl text-gray-600 max-w-2xl mx-auto leading-relaxed">
                    Dokumentasi kegiatan dan aktivitas terbaru dari Laboratorium Network & Cyber Security.
                </p>
            </div>

            <!-- Gallery Grid - Modern Bento Style -->
            <div data-aos="fade-up" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 lg:gap-8">

                <?php
                $galeriList = executeQuery("SELECT * FROM galeri WHERE is_active = true and is_featured = true ORDER BY tanggal_kegiatan DESC LIMIT 3");
                if ($galeriList === false) $galeriList = [];
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

                        <!-- Gallery Card -->
                        <div class="galeri-card group relative rounded-3xl overflow-hidden cursor-pointer"
                            onclick='showGaleriDetail(<?php echo json_encode($item_data, JSON_HEX_APOS | JSON_HEX_QUOT); ?>)'
                            data-toggle="modal"
                            data-target="#modalDetailGaleri">
                            
                            <!-- Image Container -->
                            <div class="relative h-[360px] bg-gradient-to-br from-gray-200 to-gray-300 overflow-hidden">
                                <img src="<?= htmlspecialchars($gambar_path) ?>" 
                                     alt="<?= htmlspecialchars($item['judul']) ?>" 
                                     class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110">
                                
                                <!-- Gradient Overlay -->
                                <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/20 to-transparent opacity-60 group-hover:opacity-80 transition-opacity duration-300"></div>
                                
                                <!-- Top Badges -->
                                <div class="absolute top-4 left-4 right-4 flex items-start justify-between">
                                    <!-- Type Badge -->
                                    <span class="px-4 py-2 text-xs font-medium rounded-xl uppercase tracking-wider backdrop-blur-md shadow-lg
                                        <?= strtolower($item['tipe']) === 'agenda' 
                                            ? 'bg-blue-500/90 text-white' 
                                            : 'bg-orange-500/90 text-white'; ?>">
                                        <i class="fas <?= strtolower($item['tipe']) === 'agenda' ? 'fa-calendar-alt' : 'fa-images'; ?> mr-1.5"></i>
                                        <?= htmlspecialchars($item['tipe']) ?>
                                    </span>
                                    
                                    <?php if (!empty($item['is_featured'])): ?>
                                    <!-- Featured Badge -->
                                    <span class="px-3 py-2 text-xs font-medium rounded-xl uppercase tracking-wider bg-yellow-500/90 text-white backdrop-blur-md shadow-lg">
                                        <i class="fas fa-star"></i>
                                    </span>
                                    <?php endif; ?>
                                </div>
                                
                                <!-- Center Play/View Button -->
                                <div class="absolute inset-0 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-all duration-300 transform group-hover:scale-100 scale-50">
                                    <div class="w-16 h-16 bg-white/20 backdrop-blur-md rounded-full flex items-center justify-center border-2 border-white/40 shadow-2xl group-hover:bg-orange-500 transition-all duration-300">
                                        <i class="fas fa-expand text-white text-xl"></i>
                                    </div>
                                </div>
                                
                                <!-- Bottom Content Overlay -->
                                <div class="absolute bottom-0 left-0 right-0 p-6 transform translate-y-2 group-hover:translate-y-0 transition-transform duration-300">
                                    <!-- Date Badge -->
                                    <div class="inline-flex items-center gap-2 px-3 py-1.5 bg-white/20 backdrop-blur-md rounded-lg text-white/90 text-xs font-medium mb-3">
                                        <i class="fas fa-calendar-day"></i>
                                        <span><?= !empty($item['tanggal_kegiatan']) ? date('d M Y', strtotime($item['tanggal_kegiatan'])) : '-'; ?></span>
                                    </div>
                                    
                                    <!-- Title -->
                                    <h3 class="text-xl font-medium text-white mb-2 line-clamp-2 leading-tight">
                                        <?= htmlspecialchars($item['judul']) ?>
                                    </h3>
                                    
                                    <!-- Location -->
                                    <?php if (!empty($item['lokasi'])): ?>
                                    <div class="flex items-center gap-2 text-white/80 text-sm">
                                        <i class="fas fa-map-marker-alt text-orange-400"></i>
                                        <span class="line-clamp-1"><?= htmlspecialchars($item['lokasi']) ?></span>
                                    </div>
                                    <?php endif; ?>
                                </div>
                                
                            </div>
                            
                            <!-- Hover Border Effect -->
                            <div class="absolute inset-0 rounded-3xl border-2 border-transparent group-hover:border-orange-400/50 transition-colors duration-300 pointer-events-none"></div>
                            
                        </div>

                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="col-span-full text-center py-16 bg-white/80 backdrop-blur-sm rounded-3xl border-2 border-dashed border-gray-200" data-aos="fade-up">
                        <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-6">
                            <i class="fas fa-images text-gray-400 text-3xl"></i>
                        </div>
                        <h3 class="text-xl font-medium text-gray-700 mb-2">Belum Ada Galeri</h3>
                        <p class="text-gray-500">Kegiatan terbaru akan ditampilkan di sini.</p>
                    </div>
                <?php endif; ?>

            </div>

            <!-- CTA Button -->
            <div class="text-center mt-12" data-aos="fade-up">
                <a href="./galeri.php" class="group inline-flex items-center justify-center gap-3 bg-gradient-to-r from-[#1B2D62] to-[#2C4AA4] text-white font-medium text-base px-8 py-4 rounded-xl shadow-lg shadow-blue-900/25 transition-all duration-300 hover:shadow-xl hover:shadow-blue-900/30 hover:scale-[1.02] active:scale-[0.98]">
                    <span>Lihat Semua Kegiatan</span>
                    <i class="fas fa-arrow-right transition-transform duration-300 group-hover:translate-x-1"></i>
                </a>
            </div>

        </div>
    </section>

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

    <?php include __DIR__ . '/../includes/public/modal_galeri.php'; ?>
    <?php include __DIR__ . '/../includes/public/modal_arsip.php'; ?>
</main>

<?php
    require_once __DIR__ . '/../includes/footer.php';
?>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Arsip Filter Chips
        const filterBtns = document.querySelectorAll('.arsip-filter-btn');
        const arsipCards = document.querySelectorAll('.arsip-card');
        const arsipGrid = document.getElementById('arsip-grid');

        filterBtns.forEach(btn => {
            btn.addEventListener('click', function() {
                // Remove active from all buttons
                filterBtns.forEach(b => b.classList.remove('active'));
                // Add active to clicked button
                this.classList.add('active');

                const filter = this.getAttribute('data-filter');

                // Animate cards
                arsipCards.forEach(card => {
                    const category = card.getAttribute('data-category');
                    
                    if (filter === 'all' || category === filter) {
                        card.classList.remove('hidden-filter');
                        card.classList.add('show-filter');
                        card.style.display = '';
                    } else {
                        card.classList.add('hidden-filter');
                        card.classList.remove('show-filter');
                        setTimeout(() => {
                            if (card.classList.contains('hidden-filter')) {
                                card.style.display = 'none';
                            }
                        }, 300);
                    }
                });
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
        
        $('#detail-kategori-arsip').removeClass().addClass(`px-3 py-1.5 text-xs font-medium rounded-lg uppercase tracking-wide ${kategoriClass}`)
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
                authorsHtml += `
                    <div class="flex items-center gap-3 p-3 bg-white rounded-lg border border-gray-200">
                        <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-blue-600 rounded-full flex items-center justify-center flex-shrink-0">
                            <span class="text-white font-medium text-sm">${index + 1}</span>
                        </div>
                        <div class="flex-1">
                            <div class="font-semibold text-gray-800">${author.nama}</div>
                            ${author.jabatan ? `<div class="text-xs text-gray-500">${author.jabatan}</div>` : ''}
                        </div>
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
        $('#detail-tipe').html(`<span class="px-3 py-1.5 rounded-lg text-xs font-medium uppercase tracking-wide ${tipeClass}">${data.tipe}</span>`);
        
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