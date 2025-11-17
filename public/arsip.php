<?php
/**
 * Public Arsip Page
 * File: public/arsip.php
 * Design Reference: Clean & Modern
 */

// Set page title
$page_title = "Arsip Penelitian & Pengabdian - Laboratorium NCS";

// Include public header
require_once __DIR__ . '/../includes/header.php';

// Handle download counter
if (isset($_GET['download']) && is_numeric($_GET['download'])) {
    $arsip_id = (int)$_GET['download'];
    
    // Increment download counter
    executeNonQuery(
        "UPDATE arsip SET jumlah_download = jumlah_download + 1 WHERE id = ? AND is_active = true",
        [$arsip_id]
    );
    
    // Get file path
    $arsip = executeQuerySingle("SELECT file_pdf_path FROM arsip WHERE id = ?", [$arsip_id]);
    
    if ($arsip && $arsip['file_pdf_path']) {
        $file_path = ROOT_PATH . $arsip['file_pdf_path'];
        if (file_exists($file_path)) {
            header('Content-Type: application/pdf');
            header('Content-Disposition: attachment; filename="' . basename($arsip['file_pdf_path']) . '"');
            header('Content-Length: ' . filesize($file_path));
            readfile($file_path);
            exit;
        }
    }
    
    setFlashMessage('error', 'File tidak ditemukan');
    redirect(SITE_URL . '/arsip.php');
}

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

// Get counts for filter
$count_penelitian = countRows("SELECT COUNT(*) FROM arsip WHERE kategori = 'penelitian' AND is_active = true");
$count_pengabdian = countRows("SELECT COUNT(*) FROM arsip WHERE kategori = 'pengabdian' AND is_active = true");
?>

<style>
/* Clean & Modern Design */
:root {
    --orange-50: #FFF7ED;
    --orange-100: #FFEDD5;
    --orange-500: #F97316;
    --orange-600: #EA580C;
    --orange-700: #C2410C;
    --gray-50: #F9FAFB;
    --gray-800: #1F2937;
    --gray-900: #111827;
}

.arsip-card {
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    border: 1px solid #E5E7EB;
    background: white;
    height: 100%;
}

.arsip-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
    border-color: var(--orange-500);
}

.badge-kategori {
    background: var(--orange-50);
    color: var(--orange-700);
    padding: 0.375rem 0.75rem;
    border-radius: 0.5rem;
    font-size: 0.75rem;
    font-weight: 700;
    letter-spacing: 0.025em;
}

.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.line-clamp-3 {
    display: -webkit-box;
    -webkit-line-clamp: 3;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>

<!-- Hero Section -->
<section class="bg-gray-50 py-20 relative" data-aos="fade-down">
    <div class="container mx-auto px-4">
        <div class="max-w-4xl mx-auto text-center">
            <!-- Badge -->
            <div class="inline-flex items-center gap-2 px-4 py-2 bg-orange-50 border border-orange-200 rounded-full text-orange-600 font-semibold mb-6" data-aos="fade-up" data-aos-delay="100">
                <i class="fas fa-bolt"></i>
                <span>INOVASI TEKNOLOGI KEAMANAN</span>
            </div>
            
            <h1 class="text-4xl md:text-5xl font-bold text-gray-900 mb-4" data-aos="fade-up" data-aos-delay="200">
                Pusat Riset & Dokumentasi
            </h1>
            <p class="text-lg md:text-xl text-gray-600 leading-relaxed" data-aos="fade-up" data-aos-delay="300">
                Repositori resmi untuk semua publikasi ilmiah dan laporan<br>
                pengabdian Laboratorium Network & Cyber Security.
            </p>
        </div>
    </div>
</section>

<!-- Filter & Search Section -->
<section class="py-12 bg-white">
    <div class="container mx-auto px-4">
        <div class="max-w-6xl mx-auto">
            <form method="GET" class="space-y-6" data-aos="fade-up">
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- Search Box - Left Side -->
                    <div class="lg:col-span-2">
                        <label class="block text-gray-700 font-semibold mb-3 text-lg">Cari Dokumen</label>
                        <div class="relative">
                            <i class="fas fa-search absolute left-5 top-1/2 -translate-y-1/2 text-gray-400"></i>
                            <input 
                                type="text" 
                                name="search" 
                                value="<?php echo htmlspecialchars($search); ?>"
                                placeholder="Cari berdasarkan judul atau deskripsi..." 
                                class="w-full pl-12 pr-4 py-4 text-base border-2 border-gray-300 rounded-xl focus:border-orange-500 focus:outline-none focus:ring-2 focus:ring-orange-200 transition-all"
                            >
                        </div>
                    </div>
                    
                    <!-- Category Filter - Right Side -->
                    <div>
                        <label class="block text-gray-700 font-semibold mb-3 text-lg">Kategori</label>
                        <div class="relative">
                            <select 
                                name="filter" 
                                onchange="this.form.submit()"
                                class="w-full px-4 py-4 text-base border-2 border-gray-300 rounded-xl focus:border-orange-500 focus:outline-none focus:ring-2 focus:ring-orange-200 appearance-none bg-white cursor-pointer transition-all"
                            >
                                <option value="">Semua Kategori</option>
                                <option value="penelitian" <?php echo $filter_kategori === 'penelitian' ? 'selected' : ''; ?>>Penelitian</option>
                                <option value="pengabdian" <?php echo $filter_kategori === 'pengabdian' ? 'selected' : ''; ?>>Pengabdian</option>
                            </select>
                            <i class="fas fa-chevron-down absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none"></i>
                        </div>
                    </div>
                </div>
                
                <!-- Search Button (Mobile Friendly) -->
                <div class="lg:hidden">
                    <button type="submit" class="w-full bg-gradient-to-r from-orange-500 to-orange-600 text-white font-semibold px-6 py-4 rounded-xl hover:shadow-lg transition-all">
                        <i class="fas fa-search mr-2"></i>Cari Dokumen
                    </button>
                </div>
            </form>
        </div>
    </div>
</section>

<!-- Archive List -->
<section class="py-16 bg-gray-50">
    <div class="container mx-auto px-4">
        
        <?php if ($arsip_list && count($arsip_list) > 0): ?>
        
        <!-- Card Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 max-w-7xl mx-auto">
            <?php foreach ($arsip_list as $index => $item): ?>
            
            <div class="arsip-card rounded-2xl p-6 flex flex-col" data-aos="fade-up" data-aos-delay="<?php echo ($index * 50); ?>">
                <!-- Icon & Badge -->
                <div class="flex items-start justify-between mb-4">
                    <div class="w-14 h-14 bg-orange-500 rounded-xl flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-file-alt text-white text-2xl"></i>
                    </div>
                    <span class="badge-kategori">
                        <?php echo strtoupper($item['kategori']); ?>
                    </span>
                </div>
                
                <!-- Title -->
                <h3 class="text-lg font-bold text-gray-900 mb-3 line-clamp-2 hover:text-orange-600 transition-colors">
                    <?php echo htmlspecialchars($item['judul']); ?>
                </h3>
                
                <!-- Abstract/Description -->
                <p class="text-sm text-gray-600 mb-4 leading-relaxed line-clamp-3 flex-grow">
                    <?php echo htmlspecialchars($item['abstrak'] ?: 'Tidak ada deskripsi'); ?>
                </p>
                
                <!-- Footer -->
                <div class="flex items-center justify-between pt-4 border-t border-gray-200 mt-auto">
                    <a href="?download=<?php echo $item['id']; ?>" 
                       class="text-orange-600 font-semibold hover:text-orange-700 transition-colors inline-flex items-center gap-2">
                        Unduh PDF
                        <i class="fas fa-arrow-right"></i>
                    </a>
                    
                    <div class="flex items-center gap-1 text-sm text-gray-500">
                        <i class="fas fa-download"></i>
                        <span class="font-semibold"><?php echo number_format($item['jumlah_download']); ?></span>
                    </div>
                </div>
            </div>
            
            <?php endforeach; ?>
        </div>
        
        <!-- Pagination -->
        <?php if ($total_pages > 1): ?>
        <?php
        // Build pagination URL dengan filter & search
        $base_url = '?';
        $url_params = [];
        if ($filter_kategori) $url_params[] = 'filter=' . urlencode($filter_kategori);
        if ($search) $url_params[] = 'search=' . urlencode($search);
        if (!empty($url_params)) $base_url .= implode('&', $url_params) . '&';
        ?>
        
        <div class="flex justify-center items-center gap-2 mt-12" data-aos="fade-up">
            <!-- Previous Button -->
            <?php if ($current_page > 1): ?>
            <a href="<?php echo $base_url; ?>page=<?php echo $current_page - 1; ?>" 
               class="w-10 h-10 flex items-center justify-center border-2 border-gray-300 rounded-lg hover:border-orange-500 hover:text-orange-500 transition-all bg-white">
                <i class="fas fa-chevron-left"></i>
            </a>
            <?php else: ?>
            <span class="w-10 h-10 flex items-center justify-center border-2 border-gray-200 rounded-lg bg-gray-100 text-gray-400 cursor-not-allowed">
                <i class="fas fa-chevron-left"></i>
            </span>
            <?php endif; ?>
            
            <!-- Page Numbers -->
            <?php
            // Tampilkan maksimal 5 page numbers (current ± 2)
            $start_page = max(1, $current_page - 2);
            $end_page = min($total_pages, $current_page + 2);
            
            // Adjust jika di awal atau akhir
            if ($current_page <= 3) {
                $end_page = min(5, $total_pages);
            }
            if ($current_page > $total_pages - 3) {
                $start_page = max(1, $total_pages - 4);
            }
            
            // Tampilkan page 1 jika tidak termasuk range
            if ($start_page > 1) {
                echo '<a href="' . $base_url . 'page=1" class="w-10 h-10 flex items-center justify-center border-2 border-gray-300 rounded-lg hover:border-orange-500 hover:text-orange-500 transition-all bg-white font-semibold">1</a>';
                if ($start_page > 2) {
                    echo '<span class="w-10 h-10 flex items-center justify-center text-gray-400">...</span>';
                }
            }
            
            // Loop page numbers
            for ($i = $start_page; $i <= $end_page; $i++):
            ?>
                <?php if ($i == $current_page): ?>
                <span class="w-10 h-10 flex items-center justify-center border-2 border-orange-500 rounded-lg bg-orange-500 text-white font-semibold shadow-lg">
                    <?php echo $i; ?>
                </span>
                <?php else: ?>
                <a href="<?php echo $base_url; ?>page=<?php echo $i; ?>" 
                   class="w-10 h-10 flex items-center justify-center border-2 border-gray-300 rounded-lg hover:border-orange-500 hover:text-orange-500 transition-all bg-white font-semibold">
                    <?php echo $i; ?>
                </a>
                <?php endif; ?>
            <?php endfor; ?>
            
            <?php
            // Tampilkan page terakhir jika tidak termasuk range
            if ($end_page < $total_pages) {
                if ($end_page < $total_pages - 1) {
                    echo '<span class="w-10 h-10 flex items-center justify-center text-gray-400">...</span>';
                }
                echo '<a href="' . $base_url . 'page=' . $total_pages . '" class="w-10 h-10 flex items-center justify-center border-2 border-gray-300 rounded-lg hover:border-orange-500 hover:text-orange-500 transition-all bg-white font-semibold">' . $total_pages . '</a>';
            }
            ?>
            
            <!-- Next Button -->
            <?php if ($current_page < $total_pages): ?>
            <a href="<?php echo $base_url; ?>page=<?php echo $current_page + 1; ?>" 
               class="w-10 h-10 flex items-center justify-center border-2 border-gray-300 rounded-lg hover:border-orange-500 hover:text-orange-500 transition-all bg-white">
                <i class="fas fa-chevron-right"></i>
            </a>
            <?php else: ?>
            <span class="w-10 h-10 flex items-center justify-center border-2 border-gray-200 rounded-lg bg-gray-100 text-gray-400 cursor-not-allowed">
                <i class="fas fa-chevron-right"></i>
            </span>
            <?php endif; ?>
        </div>
        <?php endif; ?>
        
        <?php else: ?>
        
        <!-- Empty State -->
        <div class="text-center py-16 max-w-lg mx-auto" data-aos="fade-up">
            <div class="w-24 h-24 bg-gray-200 rounded-full flex items-center justify-center mx-auto mb-6">
                <i class="fas fa-file-pdf text-gray-400 text-5xl"></i>
            </div>
            <h3 class="text-2xl font-bold text-gray-700 mb-3">
                Arsip Tidak Ditemukan
            </h3>
            <p class="text-gray-500 text-lg mb-6">
                <?php if ($search): ?>
                Tidak ada hasil untuk pencarian "<strong><?php echo htmlspecialchars($search); ?></strong>".
                <?php elseif ($filter_kategori): ?>
                Belum ada arsip <strong><?php echo ucfirst($filter_kategori); ?></strong> yang dipublikasikan.
                <?php else: ?>
                Belum ada arsip yang dipublikasikan. Silakan cek kembali nanti.
                <?php endif; ?>
            </p>
            <?php if ($search || $filter_kategori): ?>
            <a href="?" class="inline-flex items-center gap-2 bg-gradient-to-r from-orange-500 to-orange-600 text-white font-semibold px-6 py-3 rounded-lg hover:shadow-lg transition-all">
                <i class="fas fa-arrow-left"></i>
                Lihat Semua Arsip
            </a>
            <?php endif; ?>
        </div>
        
        <?php endif; ?>
        
    </div>
</section>

<?php
// Include public footer
require_once __DIR__ . '/../includes/footer.php';
?>