<?php
/**
 * Public Galeri Page
 * File: public/galeri.php
 * Design Reference: Modern & Professional with Navy Blue Theme (Consistent with Arsip)
 */

// Set page title
$page_title = "Galeri Kegiatan - Laboratorium NCS";

// Include public header
require_once __DIR__ . '/../includes/header.php';

// Get filter
$filter_kategori = $_GET['filter'] ?? '';
$search = $_GET['search'] ?? '';

// Build query
$where = ["is_active = true"];
$params = [];

if ($filter_kategori && in_array($filter_kategori, ['agenda', 'kegiatan'])) {
    $where[] = "LOWER(tipe) = ?";
    $params[] = strtolower($filter_kategori);
}

if ($search) {
    $where[] = "(judul ILIKE ? OR deskripsi ILIKE ? OR lokasi ILIKE ?)";
    $search_param = '%' . $search . '%';
    $params[] = $search_param;
    $params[] = $search_param;
    $params[] = $search_param;
}

$where_clause = implode(' AND ', $where);

// Pagination settings
$records_per_page = 9; // 3x3 grid
$current_page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$offset = ($current_page - 1) * $records_per_page;

// Count total records for pagination
$count_query = "SELECT COUNT(*) FROM galeri WHERE " . $where_clause;
$total_kegiatan = countRows($count_query, $params);
$total_pages = max(1, ceil($total_kegiatan / $records_per_page));

// Adjust current page if exceeds total
if ($current_page > $total_pages) $current_page = $total_pages;

// Get data with pagination
$query = "SELECT * FROM galeri WHERE " . $where_clause . " ORDER BY tanggal_kegiatan DESC, created_at DESC LIMIT $records_per_page OFFSET $offset";
$kegiatan_halaman = executeQuery($query, $params);

// Get counts for filter
$count_agenda = countRows("SELECT COUNT(*) FROM galeri WHERE LOWER(tipe) = 'agenda' AND is_active = true");
$count_kegiatan = countRows("SELECT COUNT(*) FROM galeri WHERE LOWER(tipe) = 'kegiatan' AND is_active = true");
$total_galeri = countRows("SELECT COUNT(*) FROM galeri WHERE is_active = true");
?>

<!-- Hero Section -->
<section class="relative lg:py-44 py-32 bg-gradient-to-br from-[#F8FCFF] via-white to-orange-50">
    <!-- Decorative Blur Elements -->
    <div class="absolute top-0 right-0 w-96 h-96 bg-orange-100 rounded-full mix-blend-multiply filter blur-2xl opacity-30 animate-pulse"></div>
    <div class="absolute bottom-0 left-0 w-96 h-96 bg-blue-100 rounded-full mix-blend-multiply filter blur-2xl opacity-30 animate-pulse" style="animation-delay: 1s;"></div>
    
    <!-- Floating Widget Icons Container -->
    <div class="absolute inset-0 max-w-7xl mx-auto">
        <!-- Floating Widget Icons - Left Side -->
        <div class="absolute left-4 md:left-10 lg:left-20 top-24 md:top-32 w-10 h-10 md:w-14 md:h-14 bg-white rounded-2xl shadow-xl flex items-center justify-center animate-bounce opacity-80" style="animation-duration: 3s;">
            <i class="fas fa-camera text-orange-500 text-lg md:text-xl"></i>
        </div>
        
        <div class="absolute left-8 md:left-24 lg:left-40 top-48 md:top-56 w-12 h-12 md:w-16 md:h-16 bg-gradient-to-br from-blue-500 to-blue-600 rounded-2xl shadow-xl flex items-center justify-center animate-bounce opacity-90" style="animation-duration: 4s;">
            <i class="fas fa-image text-white text-xl md:text-2xl"></i>
        </div>
        
        <div class="absolute left-2 md:left-16 lg:left-32 bottom-32 md:bottom-40 w-9 h-9 md:w-12 md:h-12 bg-white rounded-xl shadow-lg flex items-center justify-center animate-bounce opacity-70" style="animation-duration: 3.5s;">
            <i class="fas fa-video text-[#1B2D62] text-sm md:text-lg"></i>
        </div>
        
        <div class="hidden md:flex absolute left-6 lg:left-12 bottom-56 lg:bottom-64 w-10 h-10 lg:w-14 lg:h-14 bg-gradient-to-br from-orange-400 to-orange-500 rounded-xl shadow-xl items-center justify-center animate-bounce opacity-80" style="animation-duration: 4.5s;">
            <i class="fas fa-calendar-check text-white text-lg lg:text-xl"></i>
        </div>
        
        <!-- Floating Widget Icons - Right Side -->
        <div class="absolute right-4 md:right-10 lg:right-20 top-28 md:top-36 w-11 h-11 md:w-14 md:h-14 bg-gradient-to-br from-green-500 to-green-600 rounded-2xl shadow-xl flex items-center justify-center animate-bounce opacity-85" style="animation-duration: 3.5s;">
            <i class="fas fa-photo-video text-white text-lg md:text-xl"></i>
        </div>
        
        <div class="absolute right-8 md:right-20 lg:right-36 top-52 md:top-60 w-10 h-10 md:w-12 md:h-12 bg-white rounded-xl shadow-lg flex items-center justify-center animate-bounce opacity-75" style="animation-duration: 4s;">
            <i class="fas fa-users text-blue-500 text-base md:text-lg"></i>
        </div>
        
        <div class="absolute right-3 md:right-14 lg:right-28 bottom-36 md:bottom-44 w-12 h-12 md:w-16 md:h-16 bg-gradient-to-br from-purple-500 to-purple-600 rounded-2xl shadow-xl flex items-center justify-center animate-bounce opacity-80" style="animation-duration: 3s;">
            <i class="fas fa-award text-white text-xl md:text-2xl"></i>
        </div>
        
        <div class="hidden md:flex absolute right-6 lg:right-10 bottom-60 lg:bottom-72 w-9 h-9 lg:w-11 lg:h-11 bg-white rounded-lg shadow-lg items-center justify-center animate-bounce opacity-70" style="animation-duration: 4.2s;">
            <i class="fas fa-shield-alt text-orange-500 text-sm lg:text-base"></i>
        </div>
    </div>
    
    <div class="mx-auto px-4 relative z-[5]">
        <div class="max-w-5xl mx-auto text-center">
            <!-- Badge -->
            <div class="inline-flex items-center gap-2 px-5 py-2.5 bg-white border-2 border-orange-200 rounded-full text-orange-600 font-medium mb-6 shadow-lg" data-aos="fade-up">
                <i class="fas fa-images text-orange-500"></i>
                <span class="text-sm tracking-wide">INOVASI TEKNOLOGI KEKINIAN</span>
            </div>
            
            <!-- Heading -->
            <h1 class="text-5xl md:text-6xl font-medium text-[#1B2D62] mb-6 leading-tight" data-aos="fade-up" data-aos-delay="100">
                Galeri Kegiatan
            </h1>
            
            <!-- Subtitle -->
            <p class="text-xl md:text-2xl text-gray-600 leading-relaxed mb-10" data-aos="fade-up" data-aos-delay="200">
                Dokumentasi kegiatan dan aktivitas<br class="hidden md:block">
                Laboratorium Network & Cyber Security
            </p>
            
            <!-- Stats Bar -->
            <div class="flex flex-wrap justify-center gap-6 md:gap-10" data-aos="fade-up" data-aos-delay="300">
                <div class="flex items-center gap-3 bg-white px-6 py-4 rounded-xl shadow-lg border border-gray-100">
                    <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg flex items-center justify-center">
                        <i class="fas fa-calendar-alt text-white text-xl"></i>
                    </div>
                    <div class="text-left">
                        <p class="text-sm text-gray-600 font-semibold">Agenda</p>
                        <p class="text-2xl font-medium text-[#1B2D62]"><?php echo number_format($count_agenda); ?></p>
                    </div>
                </div>
                
                <div class="flex items-center gap-3 bg-white px-6 py-4 rounded-xl shadow-lg border border-gray-100">
                    <div class="w-12 h-12 bg-gradient-to-br from-orange-500 to-orange-600 rounded-lg flex items-center justify-center">
                        <i class="fas fa-camera text-white text-xl"></i>
                    </div>
                    <div class="text-left">
                        <p class="text-sm text-gray-600 font-semibold">Kegiatan</p>
                        <p class="text-2xl font-medium text-[#1B2D62]"><?php echo number_format($count_kegiatan); ?></p>
                    </div>
                </div>
                
                <div class="flex items-center gap-3 bg-white px-6 py-4 rounded-xl shadow-lg border border-gray-100">
                    <div class="w-12 h-12 bg-gradient-to-br from-green-500 to-green-600 rounded-lg flex items-center justify-center">
                        <i class="fas fa-photo-video text-white text-xl"></i>
                    </div>
                    <div class="text-left">
                        <p class="text-sm text-gray-600 font-semibold">Total Galeri</p>
                        <p class="text-2xl font-medium text-[#1B2D62]"><?php echo number_format($total_galeri); ?></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Filter & Search Section -->
<section class="py-20">
    <div class="mx-auto px-4">
        <div class="max-w-7xl mx-auto">
            <form method="GET" class="space-y-6" data-aos="fade-up">
                <div class="flex flex-col md:flex-row gap-4">
                    <!-- Search Box -->
                    <div class="flex-1">
                        <label class="block text-gray-900 font-medium mb-3 text-2xl">Cari Kegiatan</label>
                        <div class="relative">
                            <i class="fas fa-search absolute left-5 top-1/2 -translate-y-1/2 text-gray-400 text-lg"></i>
                            <input 
                                type="text" 
                                name="search" 
                                value="<?php echo htmlspecialchars($search); ?>"
                                placeholder="Cari berdasarkan judul atau deskripsi..." 
                                class="w-full pl-14 pr-4 py-4 text-base border-2 border-gray-300 rounded-xl focus:border-orange-500 focus:outline-none focus:ring-4 focus:ring-orange-100 transition-all"
                            >
                        </div>
                    </div>
                    
                    <!-- Category Filter -->
                    <div class="md:w-72">
                        <label class="block text-gray-900 font-medium mb-3 text-2xl">Kategori</label>
                        <div class="relative">
                            <select 
                                name="filter" 
                                onchange="this.form.submit()"
                                class="w-full px-5 py-4 text-base border-2 border-gray-300 rounded-xl focus:border-orange-500 focus:outline-none focus:ring-4 focus:ring-orange-100 appearance-none bg-white cursor-pointer transition-all font-medium text-gray-700"
                            >
                                <option value="">Semua Kategori</option>
                                <option value="agenda" <?php echo $filter_kategori === 'agenda' ? 'selected' : ''; ?>>📅 Agenda</option>
                                <option value="kegiatan" <?php echo $filter_kategori === 'kegiatan' ? 'selected' : ''; ?>>📸 Kegiatan</option>
                            </select>
                            <i class="fas fa-chevron-down absolute right-5 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none"></i>
                        </div>
                    </div>
                    
                    <!-- Search Button (Desktop) -->
                    <div class="hidden md:flex items-end">
                        <button type="submit" class="px-8 py-4 bg-gradient-to-r from-orange-500 to-orange-600 text-white font-medium rounded-xl hover:shadow-xl hover:scale-105 transition-all duration-300 whitespace-nowrap">
                            <i class="fas fa-search mr-2"></i>Cari
                        </button>
                    </div>
                </div>
                
                <!-- Search Button (Mobile) -->
                <div class="md:hidden">
                    <button type="submit" class="w-full bg-gradient-to-r from-orange-500 to-orange-600 text-white font-medium px-6 py-4 rounded-xl hover:shadow-lg transition-all">
                        <i class="fas fa-search mr-2"></i>Cari Kegiatan
                    </button>
                </div>
                
                <!-- Active Filters Display -->
                <?php if ($search || $filter_kategori): ?>
                <div class="flex flex-wrap items-center gap-3 pt-2" data-aos="fade-up" data-aos-delay="100">
                    <span class="text-sm text-gray-600 font-semibold">Filter Aktif:</span>
                    
                    <?php if ($search): ?>
                    <span class="inline-flex items-center gap-2 px-4 py-2 bg-orange-50 border border-orange-200 rounded-lg text-orange-700 text-sm font-semibold">
                        <i class="fas fa-search"></i>
                        "<?php echo htmlspecialchars($search); ?>"
                        <a href="?<?php echo $filter_kategori ? 'filter=' . urlencode($filter_kategori) : ''; ?>" class="ml-1 hover:text-orange-900">
                            <i class="fas fa-times"></i>
                        </a>
                    </span>
                    <?php endif; ?>
                    
                    <?php if ($filter_kategori): ?>
                    <span class="inline-flex items-center gap-2 px-4 py-2 bg-blue-50 border border-blue-200 rounded-lg text-blue-700 text-sm font-semibold">
                        <i class="fas fa-filter"></i>
                        <?php echo ucfirst($filter_kategori); ?>
                        <a href="?<?php echo $search ? 'search=' . urlencode($search) : ''; ?>" class="ml-1 hover:text-blue-900">
                            <i class="fas fa-times"></i>
                        </a>
                    </span>
                    <?php endif; ?>
                    
                    <a href="?" class="text-sm text-gray-500 hover:text-orange-600 font-semibold underline">
                        Hapus Semua Filter
                    </a>
                </div>
                <?php endif; ?>
            </form>
        </div>
    </div>
</section>

<!-- Gallery List Section -->
<section class="py-16 pb-32 bg-[#F8FCFF]">
    <div class="container mx-auto px-4">
        
        <?php if ($kegiatan_halaman && count($kegiatan_halaman) > 0): ?>
        
        <!-- Results Info -->
        <div class="max-w-7xl mx-auto mb-8" data-aos="fade-up">
            <p class="text-gray-600 font-semibold">
                Menampilkan <span class="text-[#1B2D62] font-medium"><?php echo count($kegiatan_halaman); ?></span> dari 
                <span class="text-[#1B2D62] font-medium"><?php echo number_format($total_kegiatan); ?></span> kegiatan
                <?php if ($search || $filter_kategori): ?>
                    <span class="text-orange-600">
                        (<?php echo $filter_kategori ? ucfirst($filter_kategori) : 'Semua'; ?>)
                    </span>
                <?php endif; ?>
            </p>
        </div>
        
        <!-- Card Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 max-w-7xl mx-auto">
            <?php foreach ($kegiatan_halaman as $index => $item): ?>
            
            <?php 
            // Prepare item data for modal
            $gambar_url = !empty($item['gambar_path']) && file_exists("../uploads" . $item['gambar_path']) 
                ? UPLOAD_URL . $item['gambar_path'] 
                : ASSETS_URL . '/img/no-image.png';
            
            $item_data = [
                'judul' => htmlspecialchars($item['judul']),
                'deskripsi' => htmlspecialchars($item['deskripsi'] ?: 'Dokumentasi kegiatan Laboratorium Network & Cyber Security.'),
                'tipe' => htmlspecialchars($item['tipe']),
                'lokasi' => htmlspecialchars($item['lokasi'] ?? ''),
                'tanggal' => !empty($item['tanggal_kegiatan']) ? date('d F Y', strtotime($item['tanggal_kegiatan'])) : '-',
                'gambar' => $gambar_url,
                'is_featured' => !empty($item['is_featured'])
            ];
            ?>
            <div class="group bg-white border-2 border-gray-200 rounded-2xl overflow-hidden hover:border-orange-500 hover:shadow-2xl transition-all duration-300 hover:-translate-y-2 cursor-pointer" 
                 data-aos="fade-up" 
                 data-aos-delay="<?php echo ($index * 50); ?>"
                 onclick='showGaleriDetail(<?php echo json_encode($item_data); ?>)'
                 data-toggle="modal" 
                 data-target="#modalDetailGaleri">
                
                <!-- Card Image -->
                <div class="relative h-48 bg-gradient-to-br from-gray-100 to-gray-200 overflow-hidden">
                    <img src="<?php echo $gambar_url; ?>" alt="<?php echo htmlspecialchars($item['judul']); ?>" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                    
                    <!-- Overlay on Hover -->
                    <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                    
                    <!-- View Button on Hover -->
                    <div class="absolute inset-0 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                        <span class="w-14 h-14 bg-white/90 rounded-full flex items-center justify-center text-orange-600 group-hover:bg-orange-500 group-hover:text-white transition-all duration-300 shadow-xl">
                            <i class="fas fa-eye text-xl"></i>
                        </span>
                    </div>
                    
                    <!-- Category Badge -->
                    <span class="absolute top-4 left-4 px-3 py-1.5 text-xs font-bold rounded-lg uppercase tracking-wide shadow-lg
                        <?php echo strtolower($item['tipe']) === 'agenda' 
                            ? 'bg-blue-500 text-white' 
                            : 'bg-orange-500 text-white'; ?>">
                        <?php echo htmlspecialchars($item['tipe']); ?>
                    </span>
                    
                    <!-- Featured Badge -->
                    <?php if (!empty($item['is_featured'])): ?>
                    <span class="absolute top-4 right-4 px-3 py-1.5 text-xs font-bold rounded-lg uppercase tracking-wide shadow-lg bg-yellow-500 text-white">
                        <i class="fas fa-star mr-1"></i>Featured
                    </span>
                    <?php endif; ?>
                </div>
                
                <!-- Card Content -->
                <div class="p-6">
                    <!-- Title -->
                    <h3 class="text-xl font-medium text-[#1B2D62] mb-3 group-hover:text-[#2C4AA4] transition-colors duration-300 line-clamp-2">
                        <?php echo htmlspecialchars($item['judul']); ?>
                    </h3>
                    
                    <!-- Location if available -->
                    <?php if (!empty($item['lokasi'])): ?>
                    <div class="flex items-center gap-2 text-sm text-gray-500 mb-3">
                        <i class="fas fa-map-marker-alt text-orange-500"></i>
                        <span><?php echo htmlspecialchars($item['lokasi']); ?></span>
                    </div>
                    <?php endif; ?>
                    
                    <!-- Description -->
                    <p class="text-sm text-gray-600 leading-relaxed line-clamp-3">
                        <?php echo htmlspecialchars($item['deskripsi'] ?: 'Dokumentasi kegiatan Laboratorium Network & Cyber Security.'); ?>
                    </p>
                    
                    <!-- Footer -->
                    <div class="flex items-center justify-between pt-5 mt-5 border-t-2 border-gray-100">
                        <span class="inline-flex items-center gap-2 text-orange-600 font-medium hover:text-orange-700 transition-colors group/link">
                            <span>Lihat Detail</span>
                            <i class="fas fa-arrow-right group-hover/link:translate-x-1 transition-transform"></i>
                        </span>
                        
                        <div class="flex items-center gap-2 text-gray-500">
                            <div class="flex items-center gap-1.5 px-3 py-1.5 bg-gray-50 rounded-lg">
                                <i class="fas fa-calendar text-gray-400"></i>
                                <span class="font-medium text-sm">
                                    <?php echo !empty($item['tanggal_kegiatan']) ? date('d M Y', strtotime($item['tanggal_kegiatan'])) : '-'; ?>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <?php endforeach; ?>
        </div>
        
        <!-- Pagination -->
        <?php if ($total_pages > 1): ?>
        <?php
        // Build pagination URL
        $base_url = '?';
        $url_params = [];
        if ($filter_kategori) $url_params[] = 'filter=' . urlencode($filter_kategori);
        if ($search) $url_params[] = 'search=' . urlencode($search);
        if (!empty($url_params)) $base_url .= implode('&', $url_params) . '&';
        ?>
        
        <div class="flex justify-center items-center gap-2 mt-16" data-aos="fade-up">
            <!-- Previous Button -->
            <?php if ($current_page > 1): ?>
            <a href="<?php echo $base_url; ?>page=<?php echo $current_page - 1; ?>" 
               class="w-11 h-11 flex items-center justify-center border-2 border-gray-300 rounded-xl hover:border-orange-500 hover:bg-orange-50 hover:text-orange-600 transition-all bg-white font-medium">
                <i class="fas fa-chevron-left"></i>
            </a>
            <?php else: ?>
            <span class="w-11 h-11 flex items-center justify-center border-2 border-gray-200 rounded-xl bg-gray-50 text-gray-400 cursor-not-allowed">
                <i class="fas fa-chevron-left"></i>
            </span>
            <?php endif; ?>
            
            <!-- Page Numbers -->
            <?php
            $start_page = max(1, $current_page - 2);
            $end_page = min($total_pages, $current_page + 2);
            
            if ($current_page <= 3) {
                $end_page = min(5, $total_pages);
            }
            if ($current_page > $total_pages - 3) {
                $start_page = max(1, $total_pages - 4);
            }
            
            if ($start_page > 1) {
                echo '<a href="' . $base_url . 'page=1" class="w-11 h-11 flex items-center justify-center border-2 border-gray-300 rounded-xl hover:border-orange-500 hover:bg-orange-50 hover:text-orange-600 transition-all bg-white font-medium">1</a>';
                if ($start_page > 2) {
                    echo '<span class="w-11 h-11 flex items-center justify-center text-gray-400 font-medium">...</span>';
                }
            }
            
            for ($i = $start_page; $i <= $end_page; $i++):
            ?>
                <?php if ($i == $current_page): ?>
                <span class="w-11 h-11 flex items-center justify-center border-2 border-orange-500 rounded-xl bg-gradient-to-r from-orange-500 to-orange-600 text-white font-medium shadow-lg scale-110">
                    <?php echo $i; ?>
                </span>
                <?php else: ?>
                <a href="<?php echo $base_url; ?>page=<?php echo $i; ?>" 
                   class="w-11 h-11 flex items-center justify-center border-2 border-gray-300 rounded-xl hover:border-orange-500 hover:bg-orange-50 hover:text-orange-600 transition-all bg-white font-medium">
                    <?php echo $i; ?>
                </a>
                <?php endif; ?>
            <?php endfor; ?>
            
            <?php
            if ($end_page < $total_pages) {
                if ($end_page < $total_pages - 1) {
                    echo '<span class="w-11 h-11 flex items-center justify-center text-gray-400 font-medium">...</span>';
                }
                echo '<a href="' . $base_url . 'page=' . $total_pages . '" class="w-11 h-11 flex items-center justify-center border-2 border-gray-300 rounded-xl hover:border-orange-500 hover:bg-orange-50 hover:text-orange-600 transition-all bg-white font-medium">' . $total_pages . '</a>';
            }
            ?>
            
            <!-- Next Button -->
            <?php if ($current_page < $total_pages): ?>
            <a href="<?php echo $base_url; ?>page=<?php echo $current_page + 1; ?>" 
               class="w-11 h-11 flex items-center justify-center border-2 border-gray-300 rounded-xl hover:border-orange-500 hover:bg-orange-50 hover:text-orange-600 transition-all bg-white font-medium">
                <i class="fas fa-chevron-right"></i>
            </a>
            <?php else: ?>
            <span class="w-11 h-11 flex items-center justify-center border-2 border-gray-200 rounded-xl bg-gray-50 text-gray-400 cursor-not-allowed">
                <i class="fas fa-chevron-right"></i>
            </span>
            <?php endif; ?>
        </div>
        <?php endif; ?>
        
        <?php else: ?>
        
        <!-- Empty State -->
        <div class="text-center py-20 max-w-2xl mx-auto" data-aos="fade-up">
            <div class="w-32 h-32 bg-gradient-to-br from-orange-100 to-orange-50 rounded-full flex items-center justify-center mx-auto mb-8 shadow-lg">
                <i class="fas fa-images text-orange-400 text-6xl"></i>
            </div>
            
            <h3 class="text-3xl font-medium text-[#1B2D62] mb-4">
                <?php if ($search): ?>
                Hasil Pencarian Tidak Ditemukan
                <?php elseif ($filter_kategori): ?>
                Belum Ada <?php echo ucfirst($filter_kategori); ?>
                <?php else: ?>
                Galeri Belum Tersedia
                <?php endif; ?>
            </h3>
            
            <p class="text-lg text-gray-600 mb-8 leading-relaxed">
                <?php if ($search): ?>
                Tidak ada hasil untuk pencarian "<strong class="text-orange-600"><?php echo htmlspecialchars($search); ?></strong>".<br>
                Coba gunakan kata kunci yang berbeda atau lihat semua galeri.
                <?php elseif ($filter_kategori): ?>
                Belum ada <strong class="text-orange-600"><?php echo ucfirst($filter_kategori); ?></strong> yang dipublikasikan.<br>
                Silakan coba kategori lain atau lihat semua galeri.
                <?php else: ?>
                Saat ini belum ada galeri yang dipublikasikan.<br>
                Silakan kembali lagi nanti untuk melihat update terbaru.
                <?php endif; ?>
            </p>
            
            <?php if ($search || $filter_kategori): ?>
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="?" class="inline-flex items-center justify-center gap-2 bg-gradient-to-r from-orange-500 to-orange-600 text-white font-medium px-8 py-4 rounded-xl hover:shadow-xl hover:scale-105 transition-all duration-300">
                    <i class="fas fa-arrow-left"></i>
                    Lihat Semua Galeri
                </a>
                <button onclick="document.querySelector('input[name=search]').focus()" class="inline-flex items-center justify-center gap-2 bg-white border-2 border-gray-300 text-gray-700 font-medium px-8 py-4 rounded-xl hover:border-orange-500 hover:text-orange-600 transition-all duration-300">
                    <i class="fas fa-search"></i>
                    Cari Ulang
                </button>
            </div>
            <?php endif; ?>
        </div>
        
        <?php endif; ?>
        
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
                        <a href="./arsip.php" class="inline-flex items-center justify-center gap-2 bg-white/10 backdrop-blur-sm border-2 border-white/30 text-white font-bold px-8 py-4 rounded-xl hover:bg-white/20 transition-all duration-300">
                            <i class="fas fa-book"></i>
                            Lihat Arsip
                        </a>
                    </div>
                </div>
            </div>
    </div>    
</section>

<!-- Modal Detail Galeri -->
<div id="modalDetailGaleri" aria-hidden="true" class="fixed inset-0 bg-slate-950/60 backdrop-blur-sm flex justify-center items-center opacity-0 pointer-events-none transition-all duration-300 ease-out z-[9999]">
    <div class="bg-white rounded-2xl shadow-2xl border border-gray-200 w-full max-w-3xl scale-95 transition-transform duration-300 max-h-[90vh] overflow-y-auto mx-4">
        
        <!-- Modal Header -->
        <div class="p-5 pb-3 flex justify-between items-center border-b border-gray-200 sticky top-0 bg-white z-10 rounded-t-2xl">
            <h1 class="text-lg text-[#1B2D62] font-semibold flex items-center gap-2">
                <div class="w-8 h-8 bg-gradient-to-br from-orange-500 to-orange-600 rounded-lg flex items-center justify-center">
                    <i class="fas fa-images text-white text-sm"></i>
                </div>
                <span>Detail Galeri</span>
            </h1>
            <button type="button" data-dismiss="modal" class="inline-grid place-items-center text-gray-500 hover:bg-gray-100 rounded-lg min-w-[40px] min-h-[40px] transition-all">
                <svg width="1.5em" height="1.5em" stroke-width="1.5" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" color="currentColor" class="h-5 w-5">
                    <path d="M6.75827 17.2426L12.0009 12M17.2435 6.75736L12.0009 12M12.0009 12L6.75827 6.75736M12.0009 12L17.2435 17.2426" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"></path>
                </svg>
            </button>
        </div>
        
        <!-- Modal Body -->
        <div class="p-6">
            <div class="flex flex-col gap-6">
                
                <!-- Image Section -->
                <div class="w-full">
                    <div class="rounded-xl overflow-hidden shadow-lg border border-gray-200 bg-gradient-to-br from-gray-100 to-gray-200">
                        <img id="detail-gambar" src="" alt="Detail Gambar" class="w-full max-h-[400px] object-cover">
                    </div>
                </div>

                <!-- Content Section -->
                <div class="w-full space-y-5">
                    
                    <!-- Title -->
                    <div>
                        <label class="text-xs font-bold text-gray-500 uppercase tracking-wide">Judul Kegiatan</label>
                        <h4 id="detail-judul" class="text-2xl font-semibold text-[#1B2D62] mt-1"></h4>
                    </div>

                    <!-- Info Grid -->
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div class="bg-gradient-to-br from-blue-50 to-blue-100 p-4 rounded-xl border border-blue-200">
                            <label class="text-xs font-bold text-blue-600 uppercase tracking-wide flex items-center gap-1">
                                <i class="fas fa-tag"></i> Kategori
                            </label>
                            <div id="detail-tipe" class="mt-2"></div>
                        </div>
                        <div class="bg-gradient-to-br from-orange-50 to-orange-100 p-4 rounded-xl border border-orange-200">
                            <label class="text-xs font-bold text-orange-600 uppercase tracking-wide flex items-center gap-1">
                                <i class="fas fa-calendar-alt"></i> Tanggal
                            </label>
                            <div id="detail-tanggal" class="mt-2 text-sm font-semibold text-gray-800"></div>
                        </div>
                        <div class="bg-gradient-to-br from-green-50 to-green-100 p-4 rounded-xl border border-green-200">
                            <label class="text-xs font-bold text-green-600 uppercase tracking-wide flex items-center gap-1">
                                <i class="fas fa-map-marker-alt"></i> Lokasi
                            </label>
                            <div id="detail-lokasi" class="mt-2 text-sm font-semibold text-gray-800"></div>
                        </div>
                    </div>

                    <!-- Description -->
                    <div class="bg-gray-50 p-5 rounded-xl border border-gray-200">
                        <label class="text-xs font-bold text-gray-500 uppercase tracking-wide flex items-center gap-1">
                            <i class="fas fa-align-left"></i> Deskripsi
                        </label>
                        <p id="detail-deskripsi" class="text-gray-700 text-sm mt-2 leading-relaxed break-words"></p>
                    </div>
                    
                    <!-- Featured Badge -->
                    <div id="detail-featured-container" class="hidden">
                        <span class="inline-flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-yellow-400 to-yellow-500 text-white font-semibold rounded-lg shadow-md">
                            <i class="fas fa-star"></i>
                            <span>Konten Unggulan</span>
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Footer -->
        <div class="p-5 pt-3 flex justify-end gap-3 border-t border-gray-200 sticky bottom-0 bg-white rounded-b-2xl">
            <button type="button" data-dismiss="modal" class="inline-flex items-center justify-center px-6 py-2.5 rounded-xl text-sm font-medium bg-gradient-to-r from-orange-500 to-orange-600 text-white hover:shadow-lg hover:scale-105 transition-all duration-300">
                <i class="fas fa-times mr-2"></i>Tutup
            </button>
        </div>
    </div>
</div>

<script>
// Function to show galeri detail modal
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

<?php
// Include public footer
require_once __DIR__ . '/../includes/footer.php';
?>