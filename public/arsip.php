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
            <h1 class="text-4xl sm:text-5xl md:text-6xl lg:text-7xl font-medium text-[#1B2D62] leading-[1.1] tracking-tight mb-8" data-aos="fade-up" data-aos-delay="100">
                Arsip Penelitian &<br class="hidden sm:block"> 
                <span class="bg-gradient-to-r from-[#1B2D62] via-[#2C4AA4] to-orange-500 bg-clip-text text-transparent">Pengabdian</span>
            </h1>
            
            <!-- Subtitle -->
            <p class="text-xl md:text-2xl text-gray-600 leading-relaxed mb-10" data-aos="fade-up" data-aos-delay="200">
                Repositori resmi publikasi ilmiah dan laporan pengabdian<br class="hidden md:block">
                Laboratorium Network & Cyber Security
            </p>
            
            <!-- Stats Bar -->
            <div class="flex flex-wrap justify-center gap-6 md:gap-10" data-aos="fade-up" data-aos-delay="300">
                <div class="flex items-center gap-3 bg-white/80 backdrop-blur-sm px-6 py-4 rounded-xl shadow-lg border border-gray-100">
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

<!-- Filter Section with Chips -->
<section class="relative py-12">
    <!-- Background -->
    <div class="absolute inset-0 bg-gradient-to-b from-slate-50 via-white to-orange-50/30"></div>
    
    <div class="relative mx-auto max-w-7xl px-4">
        
        <!-- Section Title -->
        <div class="text-center mb-8" data-aos="fade-up">
            <h2 class="text-2xl md:text-3xl font-semibold text-[#1B2D62] mb-2">Cari Dokumen</h2>
            <p class="text-gray-600">Filter berdasarkan kategori atau gunakan pencarian</p>
        </div>
        
        <!-- Search Bar -->
        <div class="max-w-3xl mx-auto mb-12" data-aos="fade-up" data-aos-delay="100">
            <form method="GET" action="">
                <div class="relative">
                    <i class="fas fa-search absolute left-5 top-1/2 -translate-y-1/2 text-gray-400 text-lg"></i>
                    <input 
                        type="text" 
                        name="search" 
                        id="search-input"
                        value="<?php echo htmlspecialchars($search); ?>"
                        placeholder="Cari berdasarkan judul, deskripsi, penerbit, atau kata kunci..." 
                        class="w-full pl-14 pr-32 py-4 text-base border-2 border-gray-300 rounded-xl focus:border-orange-500 focus:outline-none focus:ring-4 focus:ring-orange-100 transition-all"
                    >
                    <?php if ($filter_kategori): ?>
                    <!-- Hidden input untuk preserve filter -->
                    <input type="hidden" name="filter" value="<?php echo htmlspecialchars($filter_kategori); ?>">
                    <?php endif; ?>
                    <button type="submit" class="absolute right-2 top-1/2 -translate-y-1/2 px-6 py-2.5 bg-gradient-to-r from-orange-500 to-orange-600 text-white font-semibold rounded-lg hover:shadow-lg transition-all duration-300">
                        <i class="fas fa-search mr-2"></i>Cari
                    </button>
                </div>
            </form>
        </div>
        
        <!-- Filter Chips <a> -->
        <div class="flex justify-center mb-6" data-aos="fade-up" data-aos-delay="200">
            <div class="inline-flex items-center p-1.5 bg-gray-100/80 backdrop-blur-sm rounded-2xl gap-2">
                <a href="?<?php echo $search ? 'search=' . urlencode($search) : ''; ?>" 
                   class="arsip-filter-btn <?php echo empty($filter_kategori) ? 'active' : ''; ?> px-6 py-3 rounded-xl text-sm font-medium transition-all duration-300">
                    <span class="flex items-center gap-2">
                        <i class="fas fa-layer-group"></i>
                        Semua
                    </span>
                </a>
                <a href="?filter=penelitian<?php echo $search ? '&search=' . urlencode($search) : ''; ?>" 
                   class="arsip-filter-btn <?php echo $filter_kategori === 'penelitian' ? 'active' : ''; ?> px-6 py-3 rounded-xl text-sm font-medium transition-all duration-300">
                    <span class="flex items-center gap-2">
                        <i class="fas fa-flask"></i>
                        Penelitian
                    </span>
                </a>
                <a href="?filter=pengabdian<?php echo $search ? '&search=' . urlencode($search) : ''; ?>" 
                   class="arsip-filter-btn <?php echo $filter_kategori === 'pengabdian' ? 'active' : ''; ?> px-6 py-3 rounded-xl text-sm font-medium transition-all duration-300">
                    <span class="flex items-center gap-2">
                        <i class="fas fa-hands-helping"></i>
                        Pengabdian
                    </span>
                </a>
            </div>
        </div>
        
        <!-- Active Filters Display -->
        <?php if ($search || $filter_kategori): ?>
        <div class="flex flex-wrap justify-center items-center gap-3" data-aos="fade-up" data-aos-delay="300">
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
            
            <a href="?" class="text-sm text-orange-600 hover:text-orange-700 font-semibold hover:underline">
                <i class="fas fa-redo-alt mr-1"></i>Reset Filter
            </a>
        </div>
        <?php endif; ?>
        
    </div>
</section>

<!-- Archive List Section -->
<section class="pt-0 pb-32 bg-[#F8FCFF]">
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
            
            <!-- Arsip Card -->
            <div class="group relative bg-white rounded-3xl overflow-hidden cursor-pointer transition-all duration-500 hover:shadow-2xl hover:shadow-orange-200/50"
                data-aos="fade-up" 
                data-aos-delay="<?php echo ($index * 100); ?>"
                onclick='showArsipDetail(<?php echo json_encode($item_data, JSON_HEX_APOS | JSON_HEX_QUOT); ?>)'
                data-toggle="modal" 
                data-target="#modalDetailArsip">
                
                <!-- Border -->
                <div class="absolute inset-0 rounded-3xl border-2 border-gray-100 group-hover:border-orange-300 transition-colors duration-300"></div>
                
                <div class="relative p-6 lg:p-7">
                    
                    <!-- Top Section: Category Badge & Year -->
                    <div class="flex items-center justify-between mb-5">
                        <span class="inline-flex items-center gap-2 px-4 py-2 text-xs font-medium rounded-full uppercase tracking-wider
                            <?php echo $item['kategori'] === 'penelitian' 
                                ? 'bg-blue-100 text-blue-700 border border-blue-200' 
                                : 'bg-emerald-100 text-emerald-700 border border-emerald-200'; ?>">
                            <i class="fas <?php echo $item['kategori'] === 'penelitian' ? 'fa-flask' : 'fa-hands-helping'; ?>"></i>
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