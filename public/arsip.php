<?php
/**
 * Public Arsip Page
 * File: public/arsip.php
 * Design Reference: Modern & Professional with Navy Blue Theme
 */

// Include config first for database functions (before any output)
require_once __DIR__ . '/../config/config.php';

// Handle view PDF - MUST be before any HTML output
if (isset($_GET['view']) && is_numeric($_GET['view'])) {
    $arsip_id = (int)$_GET['view'];
    
    // Increment download/view counter
    executeNonQuery(
        "UPDATE arsip SET jumlah_download = jumlah_download + 1 WHERE id = ? AND is_active = true",
        [$arsip_id]
    );
    
    // Get file path
    $arsip = executeQuerySingle("SELECT file_pdf_path, judul FROM arsip WHERE id = ?", [$arsip_id]);
    
    if ($arsip && $arsip['file_pdf_path']) {
        $file_path = UPLOAD_PATH . $arsip['file_pdf_path'];
        if (file_exists($file_path)) {
            header('Content-Type: application/pdf');
            header('Content-Disposition: inline; filename="' . basename($arsip['file_pdf_path']) . '"');
            header('Content-Transfer-Encoding: binary');
            header('Content-Length: ' . filesize($file_path));
            header('Cache-Control: public, max-age=0');
            readfile($file_path);
            exit;
        }
    }
    
    setFlashMessage('error', 'File tidak ditemukan');
    redirect(SITE_URL . '/arsip.php');
}

// Set page title
$page_title = "Arsip Penelitian & Pengabdian - Laboratorium NCS";

// Include public header
require_once __DIR__ . '/../includes/header.php';

// Get filter
$filter_kategori = $_GET['filter'] ?? '';
$search = $_GET['search'] ?? '';

// Build query
$where = ["is_active = true"];
$params = [];

if ($filter_kategori && in_array($filter_kategori, ['penelitian', 'pengabdian'])) {
    $where[] = "kategori = ?";
    $params[] = $filter_kategori;
}

if ($search) {
    $where[] = "(judul ILIKE ? OR abstrak ILIKE ? OR penerbit ILIKE ? OR keywords ILIKE ?)";
    $search_param = '%' . $search . '%';
    $params[] = $search_param;
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
$count_query = "SELECT COUNT(*) FROM arsip WHERE " . $where_clause;
$total_records = countRows($count_query, $params);
$total_pages = ceil($total_records / $records_per_page);

// Get data with pagination
$query = "SELECT * FROM arsip WHERE " . $where_clause . " ORDER BY tahun_publikasi DESC, created_at DESC LIMIT $records_per_page OFFSET $offset";
$arsip_list = executeQuery($query, $params);

// Get authors for each arsip from arsip_pengelola relation
foreach ($arsip_list as &$arsip_item) {
    $authors_query = "SELECT p.nama_lengkap, p.jabatan, p.bidang_keahlian 
                      FROM arsip_pengelola ap 
                      JOIN pengelola p ON ap.pengelola_id = p.id 
                      WHERE ap.arsip_id = ? ";
    $authors = executeQuery($authors_query, [$arsip_item['id']]);
    $arsip_item['penulis_list'] = $authors;
    
    // Create display string for penulis
    if (!empty($authors)) {
        $author_names = array_map(function($a) { return $a['nama_lengkap']; }, $authors);
        $arsip_item['penulis_display'] = implode(', ', $author_names);
    } else {
        $arsip_item['penulis_display'] = '';
    }
}
unset($arsip_item); // Break reference

// Get counts for filter
$count_penelitian = countRows("SELECT COUNT(*) FROM arsip WHERE kategori = 'penelitian' AND is_active = true");
$count_pengabdian = countRows("SELECT COUNT(*) FROM arsip WHERE kategori = 'pengabdian' AND is_active = true");
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
            <i class="fas fa-file-pdf text-orange-500 text-lg md:text-xl"></i>
        </div>
        
        <div class="absolute left-8 md:left-24 lg:left-40 top-48 md:top-56 w-12 h-12 md:w-16 md:h-16 bg-gradient-to-br from-blue-500 to-blue-600 rounded-2xl shadow-xl flex items-center justify-center animate-bounce opacity-90" style="animation-duration: 4s;">
            <i class="fas fa-flask text-white text-xl md:text-2xl"></i>
        </div>
        
        <div class="absolute left-2 md:left-16 lg:left-32 bottom-32 md:bottom-40 w-9 h-9 md:w-12 md:h-12 bg-white rounded-xl shadow-lg flex items-center justify-center animate-bounce opacity-70" style="animation-duration: 3.5s;">
            <i class="fas fa-book text-[#1B2D62] text-sm md:text-lg"></i>
        </div>
        
        <div class="hidden md:flex absolute left-6 lg:left-12 bottom-56 lg:bottom-64 w-10 h-10 lg:w-14 lg:h-14 bg-gradient-to-br from-orange-400 to-orange-500 rounded-xl shadow-xl items-center justify-center animate-bounce opacity-80" style="animation-duration: 4.5s;">
            <i class="fas fa-graduation-cap text-white text-lg lg:text-xl"></i>
        </div>
        
        <!-- Floating Widget Icons - Right Side -->
        <div class="absolute right-4 md:right-10 lg:right-20 top-28 md:top-36 w-11 h-11 md:w-14 md:h-14 bg-gradient-to-br from-green-500 to-green-600 rounded-2xl shadow-xl flex items-center justify-center animate-bounce opacity-85" style="animation-duration: 3.5s;">
            <i class="fas fa-hands-helping text-white text-lg md:text-xl"></i>
        </div>
        
        <div class="absolute right-8 md:right-20 lg:right-36 top-52 md:top-60 w-10 h-10 md:w-12 md:h-12 bg-white rounded-xl shadow-lg flex items-center justify-center animate-bounce opacity-75" style="animation-duration: 4s;">
            <i class="fas fa-search text-blue-500 text-base md:text-lg"></i>
        </div>
        
        <div class="absolute right-3 md:right-14 lg:right-28 bottom-36 md:bottom-44 w-12 h-12 md:w-16 md:h-16 bg-gradient-to-br from-purple-500 to-purple-600 rounded-2xl shadow-xl flex items-center justify-center animate-bounce opacity-80" style="animation-duration: 3s;">
            <i class="fas fa-file-alt text-white text-xl md:text-2xl"></i>
        </div>
        
        <div class="hidden md:flex absolute right-6 lg:right-10 bottom-60 lg:bottom-72 w-9 h-9 lg:w-11 lg:h-11 bg-white rounded-lg shadow-lg items-center justify-center animate-bounce opacity-70" style="animation-duration: 4.2s;">
            <i class="fas fa-download text-orange-500 text-sm lg:text-base"></i>
        </div>
    </div>
    
    <div class="mx-auto px-4 relative z-[5]">
        <div class="max-w-5xl mx-auto text-center">
            <!-- Badge -->
            <div class="inline-flex items-center gap-2 px-5 py-2.5 bg-white border-2 border-orange-200 rounded-full text-orange-600 font-bold mb-6 shadow-lg" data-aos="fade-up">
                <i class="fas fa-bolt text-orange-500"></i>
                <span class="text-sm tracking-wide">PUSAT RISET & DOKUMENTASI</span>
            </div>
            
            <!-- Heading -->
            <h1 class="text-5xl md:text-6xl font-medium text-[#1B2D62] mb-6 leading-tight" data-aos="fade-up" data-aos-delay="100">
                Arsip Penelitian & Pengabdian
            </h1>
            
            <!-- Subtitle -->
            <p class="text-xl md:text-2xl text-gray-600 leading-relaxed mb-10" data-aos="fade-up" data-aos-delay="200">
                Repositori resmi publikasi ilmiah dan laporan pengabdian<br class="hidden md:block">
                Laboratorium Network & Cyber Security
            </p>
            
            <!-- Stats Bar -->
            <div class="flex flex-wrap justify-center gap-6 md:gap-10" data-aos="fade-up" data-aos-delay="300">
                <div class="flex items-center gap-3 bg-white px-6 py-4 rounded-xl shadow-lg border border-gray-100">
                    <div class="w-12 h-12 bg-gradient-to-br from-orange-500 to-orange-600 rounded-lg flex items-center justify-center">
                        <i class="fas fa-flask text-white text-xl"></i>
                    </div>
                    <div class="text-left">
                        <p class="text-sm text-gray-600 font-semibold">Penelitian</p>
                        <p class="text-2xl font-bold text-[#1B2D62]"><?php echo number_format($count_penelitian); ?></p>
                    </div>
                </div>
                
                <div class="flex items-center gap-3 bg-white px-6 py-4 rounded-xl shadow-lg border border-gray-100">
                    <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg flex items-center justify-center">
                        <i class="fas fa-hands-helping text-white text-xl"></i>
                    </div>
                    <div class="text-left">
                        <p class="text-sm text-gray-600 font-semibold">Pengabdian</p>
                        <p class="text-2xl font-bold text-[#1B2D62]"><?php echo number_format($count_pengabdian); ?></p>
                    </div>
                </div>
                
                <div class="flex items-center gap-3 bg-white px-6 py-4 rounded-xl shadow-lg border border-gray-100">
                    <div class="w-12 h-12 bg-gradient-to-br from-green-500 to-green-600 rounded-lg flex items-center justify-center">
                        <i class="fas fa-file-alt text-white text-xl"></i>
                    </div>
                    <div class="text-left">
                        <p class="text-sm text-gray-600 font-semibold">Total Arsip</p>
                        <p class="text-2xl font-bold text-[#1B2D62]"><?php echo number_format($total_records); ?></p>
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
                        <label class="block text-gray-900 font-medium mb-3 text-2xl">Cari Dokumen</label>
                        <div class="relative">
                            <i class="fas fa-search absolute left-5 top-1/2 -translate-y-1/2 text-gray-400 text-lg"></i>
                            <input 
                                type="text" 
                                name="search" 
                                value="<?php echo htmlspecialchars($search); ?>"
                                placeholder="Cari berdasarkan judul, deskripsi, atau kata kunci..." 
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
                                <option value="penelitian" <?php echo $filter_kategori === 'penelitian' ? 'selected' : ''; ?>>📚 Penelitian</option>
                                <option value="pengabdian" <?php echo $filter_kategori === 'pengabdian' ? 'selected' : ''; ?>>🤝 Pengabdian</option>
                            </select>
                            <i class="fas fa-chevron-down absolute right-5 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none"></i>
                        </div>
                    </div>
                    
                    <!-- Search Button (Desktop) -->
                    <div class="hidden md:flex items-end">
                        <button type="submit" class="px-8 py-4 bg-gradient-to-r from-orange-500 to-orange-600 text-white font-bold rounded-xl hover:shadow-xl hover:scale-105 transition-all duration-300 whitespace-nowrap">
                            <i class="fas fa-search text-lg mr-2"></i>Cari
                        </button>
                    </div>
                </div>
                
                <!-- Search Button (Mobile) -->
                <div class="md:hidden">
                    <button type="submit" class="w-full bg-gradient-to-r from-orange-500 to-orange-600 text-white font-bold px-6 py-4 rounded-xl hover:shadow-lg transition-all">
                        <i class="fas fa-search mr-2"></i>Cari Dokumen
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

<!-- Archive List Section -->
<section class="py-16 pb-32 bg-[#F8FCFF]">
    <div class="container mx-auto px-4">
        
        <?php if ($arsip_list && count($arsip_list) > 0): ?>
        
        <!-- Results Info -->
        <div class="max-w-7xl mx-auto mb-8" data-aos="fade-up">
            <p class="text-gray-600 font-semibold">
                Menampilkan <span class="text-[#1B2D62] font-bold"><?php echo count($arsip_list); ?></span> dari 
                <span class="text-[#1B2D62] font-bold"><?php echo number_format($total_records); ?></span> dokumen
                <?php if ($search || $filter_kategori): ?>
                    <span class="text-orange-600">
                        (<?php echo $filter_kategori ? ucfirst($filter_kategori) : 'Semua'; ?>)
                    </span>
                <?php endif; ?>
            </p>
        </div>
        
        <!-- Card Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 max-w-7xl mx-auto">
            <?php foreach ($arsip_list as $index => $item): 
                // Prepare item data for modal
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
            ?>
            
            <div class="group bg-white border-2 border-gray-200 rounded-2xl p-6 flex flex-col hover:border-orange-500 hover:shadow-2xl transition-all duration-300 hover:-translate-y-2 cursor-pointer" 
                 data-aos="fade-up" 
                 data-aos-delay="<?php echo ($index * 50); ?>"
                 onclick='showArsipDetail(<?php echo json_encode($item_data, JSON_HEX_APOS | JSON_HEX_QUOT); ?>)'
                 data-toggle="modal" 
                 data-target="#modalDetailArsip">
                
                <!-- Header: Icon & Badge -->
                <div class="flex items-start justify-between mb-5">
                    <div class="w-16 h-16 bg-gradient-to-br from-orange-500 to-orange-600 rounded-xl flex items-center justify-center flex-shrink-0 group-hover:scale-110 transition-transform duration-300 shadow-lg">
                        <i class="fas fa-file-pdf text-white text-2xl"></i>
                    </div>
                    <div class="flex flex-col items-end gap-2">
                        <span class="px-3 py-1.5 text-xs font-bold rounded-lg uppercase tracking-wide
                            <?php echo $item['kategori'] === 'penelitian' ? 'bg-blue-50 text-blue-700 border border-blue-200' : 'bg-green-50 text-green-700 border border-green-200'; ?>">
                            <?php echo htmlspecialchars($item['kategori']); ?>
                        </span>
                        <?php if (!empty($item['is_featured'])): ?>
                        <span class="px-2 py-1 bg-yellow-100 text-yellow-700 text-xs font-bold rounded-lg border border-yellow-200">
                            <i class="fas fa-star mr-1"></i>Featured
                        </span>
                        <?php endif; ?>
                    </div>
                </div>
                
                <!-- Title -->
                <h3 class="text-xl font-bold text-[#1B2D62] mb-3 line-clamp-2 group-hover:text-[#2C4AA4] transition-colors duration-300">
                    <?php echo htmlspecialchars($item['judul']); ?>
                </h3>
                
                <!-- Meta Info -->
                <div class="flex flex-wrap gap-3 mb-4 text-sm text-gray-600">
                    <?php if (!empty($item['tahun_publikasi'])): ?>
                    <div class="flex items-center gap-1.5">
                        <i class="fas fa-calendar-alt text-orange-500"></i>
                        <span class="font-semibold"><?php echo htmlspecialchars($item['tahun_publikasi']); ?></span>
                    </div>
                    <?php endif; ?>
                    
                    <?php if (!empty($item['penulis_display'])): ?>
                    <div class="flex items-center gap-1.5">
                        <i class="fas fa-users text-blue-500"></i>
                        <span class="font-semibold line-clamp-1"><?php echo htmlspecialchars($item['penulis_display']); ?></span>
                    </div>
                    <?php endif; ?>
                </div>
                
                <!-- Abstract/Description -->
                <p class="text-sm text-gray-600 mb-5 leading-relaxed line-clamp-3 flex-grow">
                    <?php echo htmlspecialchars($item['abstrak'] ?: 'Dokumen penelitian dan pengabdian masyarakat dari Laboratorium Network & Cyber Security.'); ?>
                </p>
                
                <!-- Footer: Action & Stats -->
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
               class="w-11 h-11 flex items-center justify-center border-2 border-gray-300 rounded-xl hover:border-orange-500 hover:bg-orange-50 hover:text-orange-600 transition-all bg-white font-bold">
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
                echo '<a href="' . $base_url . 'page=1" class="w-11 h-11 flex items-center justify-center border-2 border-gray-300 rounded-xl hover:border-orange-500 hover:bg-orange-50 hover:text-orange-600 transition-all bg-white font-bold">1</a>';
                if ($start_page > 2) {
                    echo '<span class="w-11 h-11 flex items-center justify-center text-gray-400 font-bold">...</span>';
                }
            }
            
            for ($i = $start_page; $i <= $end_page; $i++):
            ?>
                <?php if ($i == $current_page): ?>
                <span class="w-11 h-11 flex items-center justify-center border-2 border-orange-500 rounded-xl bg-gradient-to-r from-orange-500 to-orange-600 text-white font-bold shadow-lg scale-110">
                    <?php echo $i; ?>
                </span>
                <?php else: ?>
                <a href="<?php echo $base_url; ?>page=<?php echo $i; ?>" 
                   class="w-11 h-11 flex items-center justify-center border-2 border-gray-300 rounded-xl hover:border-orange-500 hover:bg-orange-50 hover:text-orange-600 transition-all bg-white font-bold">
                    <?php echo $i; ?>
                </a>
                <?php endif; ?>
            <?php endfor; ?>
            
            <?php
            if ($end_page < $total_pages) {
                if ($end_page < $total_pages - 1) {
                    echo '<span class="w-11 h-11 flex items-center justify-center text-gray-400 font-bold">...</span>';
                }
                echo '<a href="' . $base_url . 'page=' . $total_pages . '" class="w-11 h-11 flex items-center justify-center border-2 border-gray-300 rounded-xl hover:border-orange-500 hover:bg-orange-50 hover:text-orange-600 transition-all bg-white font-bold">' . $total_pages . '</a>';
            }
            ?>
            
            <!-- Next Button -->
            <?php if ($current_page < $total_pages): ?>
            <a href="<?php echo $base_url; ?>page=<?php echo $current_page + 1; ?>" 
               class="w-11 h-11 flex items-center justify-center border-2 border-gray-300 rounded-xl hover:border-orange-500 hover:bg-orange-50 hover:text-orange-600 transition-all bg-white font-bold">
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
                <i class="fas fa-search text-orange-400 text-6xl"></i>
            </div>
            
            <h3 class="text-3xl font-bold text-[#1B2D62] mb-4">
                <?php if ($search): ?>
                Hasil Pencarian Tidak Ditemukan
                <?php elseif ($filter_kategori): ?>
                Belum Ada Arsip <?php echo ucfirst($filter_kategori); ?>
                <?php else: ?>
                Arsip Belum Tersedia
                <?php endif; ?>
            </h3>
            
            <p class="text-lg text-gray-600 mb-8 leading-relaxed">
                <?php if ($search): ?>
                Tidak ada hasil untuk pencarian "<strong class="text-orange-600"><?php echo htmlspecialchars($search); ?></strong>".<br>
                Coba gunakan kata kunci yang berbeda atau lihat semua arsip.
                <?php elseif ($filter_kategori): ?>
                Belum ada dokumen <strong class="text-orange-600"><?php echo ucfirst($filter_kategori); ?></strong> yang dipublikasikan.<br>
                Silakan coba kategori lain atau lihat semua arsip.
                <?php else: ?>
                Saat ini belum ada arsip yang dipublikasikan.<br>
                Silakan kembali lagi nanti untuk melihat update terbaru.
                <?php endif; ?>
            </p>
            
            <?php if ($search || $filter_kategori): ?>
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="?" class="inline-flex items-center justify-center gap-2 bg-gradient-to-r from-orange-500 to-orange-600 text-white font-bold px-8 py-4 rounded-xl hover:shadow-xl hover:scale-105 transition-all duration-300">
                    <i class="fas fa-arrow-left"></i>
                    Lihat Semua Arsip
                </a>
                <button onclick="document.querySelector('input[name=search]').focus()" class="inline-flex items-center justify-center gap-2 bg-white border-2 border-gray-300 text-gray-700 font-bold px-8 py-4 rounded-xl hover:border-orange-500 hover:text-orange-600 transition-all duration-300">
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

<!-- Modal Detail Arsip -->
<div id="modalDetailArsip" class="fixed inset-0 bg-slate-950/60 backdrop-blur-sm flex justify-center items-center opacity-0 pointer-events-none transition-all duration-300 ease-out z-[9999]">
    <div class="bg-white rounded-2xl shadow-2xl border border-gray-200 w-full max-w-4xl scale-95 transition-transform duration-300 max-h-[90vh] overflow-y-auto mx-4">
        
        <!-- Modal Header -->
        <div class="p-5 pb-3 flex justify-between items-center border-b border-gray-200 sticky top-0 bg-white z-10 rounded-t-2xl">
            <h1 class="text-lg text-[#1B2D62] font-semibold flex items-center gap-2">
                <div class="w-8 h-8 bg-gradient-to-br from-orange-500 to-orange-600 rounded-lg flex items-center justify-center">
                    <i class="fas fa-file-pdf text-white text-sm"></i>
                </div>
                <span>Detail Arsip</span>
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
                
                <!-- Header with Icon and Badge -->
                <div class="flex items-start gap-4">
                    <div class="w-20 h-20 bg-gradient-to-br from-orange-500 to-orange-600 rounded-xl flex items-center justify-center flex-shrink-0 shadow-lg">
                        <i class="fas fa-file-pdf text-white text-3xl"></i>
                    </div>
                    <div class="flex-1">
                        <div class="flex flex-wrap gap-2 mb-2">
                            <span id="detail-kategori" class="px-3 py-1.5 text-xs font-bold rounded-lg uppercase tracking-wide"></span>
                            <span id="detail-featured-badge" class="hidden px-3 py-1.5 bg-gradient-to-r from-yellow-400 to-yellow-500 text-white text-xs font-bold rounded-lg">
                                <i class="fas fa-star mr-1"></i>Featured
                            </span>
                        </div>
                        <h2 id="detail-judul" class="text-2xl font-bold text-[#1B2D62] leading-tight"></h2>
                    </div>
                </div>

                <!-- Info Grid -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    <div class="bg-gradient-to-br from-orange-50 to-orange-100 p-4 rounded-xl border border-orange-200">
                        <label class="text-xs font-bold text-orange-600 uppercase tracking-wide flex items-center gap-1">
                            <i class="fas fa-calendar-alt"></i> Tahun Publikasi
                        </label>
                        <div id="detail-tahun" class="mt-2 text-lg font-bold text-gray-800"></div>
                    </div>
                    <div class="bg-gradient-to-br from-blue-50 to-blue-100 p-4 rounded-xl border border-blue-200">
                        <label class="text-xs font-bold text-blue-600 uppercase tracking-wide flex items-center gap-1">
                            <i class="fas fa-building"></i> Penerbit
                        </label>
                        <div id="detail-penerbit" class="mt-2 text-sm font-semibold text-gray-800 line-clamp-2"></div>
                    </div>
                    <div class="bg-gradient-to-br from-green-50 to-green-100 p-4 rounded-xl border border-green-200">
                        <label class="text-xs font-bold text-green-600 uppercase tracking-wide flex items-center gap-1">
                            <i class="fas fa-download"></i> Total Unduhan
                        </label>
                        <div id="detail-download" class="mt-2 text-lg font-bold text-gray-800"></div>
                    </div>
                    <div class="bg-gradient-to-br from-purple-50 to-purple-100 p-4 rounded-xl border border-purple-200">
                        <label class="text-xs font-bold text-purple-600 uppercase tracking-wide flex items-center gap-1">
                            <i class="fas fa-file"></i> Ukuran File
                        </label>
                        <div id="detail-filesize" class="mt-2 text-lg font-bold text-gray-800"></div>
                    </div>
                </div>
                
                <!-- Authors Section -->
                <div class="bg-gray-50 p-5 rounded-xl border border-gray-200">
                    <label class="text-xs font-bold text-gray-500 uppercase tracking-wide flex items-center gap-1 mb-3">
                        <i class="fas fa-users"></i> Penulis / Kontributor
                    </label>
                    <div id="detail-penulis-list" class="space-y-2"></div>
                </div>

                <!-- Abstract -->
                <div class="bg-gray-50 p-5 rounded-xl border border-gray-200">
                    <label class="text-xs font-bold text-gray-500 uppercase tracking-wide flex items-center gap-1">
                        <i class="fas fa-align-left"></i> Abstrak
                    </label>
                    <p id="detail-abstrak" class="text-gray-700 text-sm mt-2 leading-relaxed"></p>
                </div>
                
                <!-- Keywords & DOI -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="bg-orange-50 p-4 rounded-xl border border-orange-200">
                        <label class="text-xs font-bold text-orange-600 uppercase tracking-wide flex items-center gap-1">
                            <i class="fas fa-tags"></i> Kata Kunci
                        </label>
                        <div id="detail-keywords" class="mt-2 flex flex-wrap gap-2"></div>
                    </div>
                    <div id="detail-doi-container" class="bg-blue-50 p-4 rounded-xl border border-blue-200">
                        <label class="text-xs font-bold text-blue-600 uppercase tracking-wide flex items-center gap-1">
                            <i class="fas fa-link"></i> DOI
                        </label>
                        <a id="detail-doi" href="#" target="_blank" class="mt-2 text-sm font-semibold text-blue-700 hover:underline block break-all"></a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Footer -->
        <div class="p-5 pt-3 flex flex-col sm:flex-row justify-end gap-3 border-t border-gray-200 sticky bottom-0 bg-white rounded-b-2xl">
            <button type="button" data-dismiss="modal" class="inline-flex items-center justify-center px-6 py-2.5 rounded-xl text-sm font-medium border-2 border-gray-300 text-gray-700 hover:bg-gray-100 transition-all duration-300">
                <i class="fas fa-times mr-2"></i>Tutup
            </button>
            <a id="detail-download-btn" target="_blank" class="inline-flex items-center justify-center px-6 py-2.5 rounded-xl text-sm font-medium bg-gradient-to-r from-orange-500 to-orange-600 text-white hover:shadow-lg hover:scale-105 transition-all duration-300">
                <i class="fas fa-file-pdf mr-2"></i>Lihat PDF
            </a>
        </div>
    </div>
</div>

<script>
// Function to show arsip detail modal
function showArsipDetail(data) {
    // Set title
    $('#detail-judul').text(data.judul);
    
    // Set category with appropriate color
    const kategoriClass = data.kategori.toLowerCase() === 'penelitian' 
        ? 'bg-blue-100 text-blue-700 border border-blue-200' 
        : 'bg-green-100 text-green-700 border border-green-200';
    const kategoriIcon = data.kategori.toLowerCase() === 'penelitian' ? '📚' : '🤝';
    $('#detail-kategori').removeClass().addClass(`px-3 py-1.5 text-xs font-bold rounded-lg uppercase tracking-wide ${kategoriClass}`)
        .html(`${kategoriIcon} ${data.kategori}`);
    
    // Show/hide featured badge
    if (data.is_featured) {
        $('#detail-featured-badge').removeClass('hidden');
    } else {
        $('#detail-featured-badge').addClass('hidden');
    }
    
    // Set year
    $('#detail-tahun').text(data.tahun_publikasi || '-');
    
    // Set publisher
    $('#detail-penerbit').text(data.penerbit || 'Tidak tersedia');
    
    // Set download count
    $('#detail-download').text(data.jumlah_download.toLocaleString());
    
    // Set file size
    const fileSizeKB = data.file_size_kb || 0;
    let fileSizeDisplay = '-';
    if (fileSizeKB > 0) {
        if (fileSizeKB >= 1024) {
            fileSizeDisplay = (fileSizeKB / 1024).toFixed(2) + ' MB';
        } else {
            fileSizeDisplay = fileSizeKB + ' KB';
        }
    }
    $('#detail-filesize').text(fileSizeDisplay);
    
    // Set authors list
    let authorsHtml = '';
    if (data.penulis_list && data.penulis_list.length > 0) {
        data.penulis_list.forEach(function(author, index) {
            authorsHtml += `
                <div class="flex items-center gap-3 p-3 bg-white rounded-lg border border-gray-200">
                    <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-blue-600 rounded-full flex items-center justify-center flex-shrink-0">
                        <span class="text-white font-bold text-sm">${index + 1}</span>
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
    $('#detail-penulis-list').html(authorsHtml);
    
    // Set abstract
    $('#detail-abstrak').text(data.abstrak);
    
    // Set keywords
    let keywordsHtml = '';
    if (data.keywords) {
        const keywords = data.keywords.split(',');
        keywords.forEach(function(keyword) {
            keyword = keyword.trim();
            if (keyword) {
                keywordsHtml += `<span class="px-3 py-1 bg-orange-100 text-orange-700 text-xs font-semibold rounded-full">${keyword}</span>`;
            }
        });
    }
    if (!keywordsHtml) {
        keywordsHtml = '<span class="text-gray-500 text-sm italic">Tidak ada kata kunci</span>';
    }
    $('#detail-keywords').html(keywordsHtml);
    
    // Set DOI
    if (data.doi) {
        const doiUrl = data.doi.startsWith('http') ? data.doi : `https://doi.org/${data.doi}`;
        $('#detail-doi').attr('href', doiUrl).text(data.doi);
        $('#detail-doi-container').show();
    } else {
        $('#detail-doi-container').hide();
    }
    
    // Set view PDF button URL (opens in new tab)
    $('#detail-download-btn').attr('href', `?view=${data.id}`);
}
</script>

<?php
// Include public footer
require_once __DIR__ . '/../includes/footer.php';
?>