<?php
/**
 * Public Arsip Page
 * File: public/arsip.php
 * Design Reference: Active SaaS (Orange Accent)
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

// Get data
$query = "SELECT * FROM arsip WHERE " . $where_clause . " ORDER BY tahun_publikasi DESC, created_at DESC LIMIT 50";
$arsip_list = executeQuery($query, $params);

// Get counts
$count_penelitian = countRows("SELECT COUNT(*) FROM arsip WHERE kategori = 'penelitian' AND is_active = true");
$count_pengabdian = countRows("SELECT COUNT(*) FROM arsip WHERE kategori = 'pengabdian' AND is_active = true");
$total_downloads = executeQuerySingle("SELECT SUM(jumlah_download) as total FROM arsip WHERE is_active = true");
?>

<style>
/* Active SaaS Inspired Design */
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

.hero-gradient {
    background: linear-gradient(135deg, var(--orange-600) 0%, var(--orange-700) 100%);
}

.arsip-card {
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    border: 1px solid #E5E7EB;
    background: white;
}

.arsip-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
    border-color: var(--orange-500);
}

.download-btn {
    background: linear-gradient(135deg, var(--orange-500) 0%, var(--orange-600) 100%);
    color: white;
    transition: all 0.3s ease;
}

.download-btn:hover {
    box-shadow: 0 10px 15px -3px rgba(249, 115, 22, 0.4);
    transform: translateY(-2px);
}

.filter-btn {
    padding: 0.75rem 1.5rem;
    font-weight: 600;
    border-radius: 9999px;
    transition: all 0.3s ease;
    border: 2px solid transparent;
}

.filter-btn:not(.active) {
    background: white;
    color: var(--gray-800);
    border-color: #E5E7EB;
}

.filter-btn:not(.active):hover {
    border-color: var(--orange-500);
    color: var(--orange-600);
}

.filter-btn.active {
    background: linear-gradient(135deg, var(--orange-500) 0%, var(--orange-600) 100%);
    color: white;
    box-shadow: 0 10px 15px -3px rgba(249, 115, 22, 0.3);
}

.badge-kategori {
    background: var(--orange-50);
    color: var(--orange-700);
    padding: 0.25rem 0.75rem;
    border-radius: 9999px;
    font-size: 0.875rem;
    font-weight: 600;
}

.keyword-tag {
    background: #F3F4F6;
    color: #4B5563;
    padding: 0.25rem 0.5rem;
    border-radius: 0.25rem;
    font-size: 0.75rem;
}
</style>

<!-- Hero Section -->
<section class="hero-gradient text-white py-20 relative" data-aos="fade-down">
    <div class="container mx-auto px-4">
        <div class="max-w-4xl mx-auto text-center">
            <h1 class="text-5xl md:text-6xl font-bold mb-6" data-aos="fade-up" data-aos-delay="100">
                Arsip Penelitian & Pengabdian
            </h1>
            <p class="text-xl md:text-2xl text-orange-100 mb-8" data-aos="fade-up" data-aos-delay="200">
                Koleksi hasil penelitian dan pengabdian masyarakat Laboratorium NCS
            </p>
            
            <!-- Stats -->
            <div class="grid grid-cols-3 gap-6 max-w-3xl mx-auto" data-aos="fade-up" data-aos-delay="300">
                <div class="bg-white/10 backdrop-blur-sm rounded-xl p-4">
                    <div class="text-3xl font-bold mb-1"><?php echo $count_penelitian; ?>+</div>
                    <div class="text-orange-100">Penelitian</div>
                </div>
                <div class="bg-white/10 backdrop-blur-sm rounded-xl p-4">
                    <div class="text-3xl font-bold mb-1"><?php echo $count_pengabdian; ?>+</div>
                    <div class="text-orange-100">Pengabdian</div>
                </div>
                <div class="bg-white/10 backdrop-blur-sm rounded-xl p-4">
                    <div class="text-3xl font-bold mb-1"><?php echo number_format($total_downloads['total'] ?? 0); ?></div>
                    <div class="text-orange-100">Download</div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Wave Divider -->
    <div class="absolute bottom-0 left-0 right-0">
        <svg viewBox="0 0 1440 120" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M0 120L60 105C120 90 240 60 360 45C480 30 600 30 720 37.5C840 45 960 60 1080 67.5C1200 75 1320 75 1380 75L1440 75V120H1380C1320 120 1200 120 1080 120C960 120 840 120 720 120C600 120 480 120 360 120C240 120 120 120 60 120H0Z" fill="white"/>
        </svg>
    </div>
</section>

<!-- Filter & Search Section -->
<section class="py-12 bg-white">
    <div class="container mx-auto px-4">
        <form method="GET" class="space-y-6" data-aos="fade-up">
            <!-- Filter Buttons -->
            <div class="flex justify-center items-center gap-4 flex-wrap">
                <a href="?" class="filter-btn <?php echo empty($filter_kategori) ? 'active' : ''; ?>">
                    <i class="fas fa-th mr-2"></i>Semua
                    <span class="ml-2 inline-flex items-center justify-center w-6 h-6 text-xs rounded-full <?php echo empty($filter_kategori) ? 'bg-white/20' : 'bg-gray-200'; ?>">
                        <?php echo $count_penelitian + $count_pengabdian; ?>
                    </span>
                </a>
                
                <a href="?filter=penelitian" class="filter-btn <?php echo $filter_kategori === 'penelitian' ? 'active' : ''; ?>">
                    <i class="fas fa-flask mr-2"></i>Penelitian
                    <span class="ml-2 inline-flex items-center justify-center w-6 h-6 text-xs rounded-full <?php echo $filter_kategori === 'penelitian' ? 'bg-white/20' : 'bg-gray-200'; ?>">
                        <?php echo $count_penelitian; ?>
                    </span>
                </a>
                
                <a href="?filter=pengabdian" class="filter-btn <?php echo $filter_kategori === 'pengabdian' ? 'active' : ''; ?>">
                    <i class="fas fa-hands-helping mr-2"></i>Pengabdian
                    <span class="ml-2 inline-flex items-center justify-center w-6 h-6 text-xs rounded-full <?php echo $filter_kategori === 'pengabdian' ? 'bg-white/20' : 'bg-gray-200'; ?>">
                        <?php echo $count_pengabdian; ?>
                    </span>
                </a>
            </div>
            
            <!-- Search Box -->
            <div class="max-w-2xl mx-auto">
                <div class="relative">
                    <input 
                        type="text" 
                        name="search" 
                        value="<?php echo htmlspecialchars($search); ?>"
                        placeholder="Cari judul, abstrak, penerbit, atau keywords..." 
                        class="w-full px-6 py-4 pr-32 text-lg border-2 border-gray-200 rounded-full focus:border-orange-500 focus:outline-none focus:ring-2 focus:ring-orange-200"
                    >
                    <?php if ($filter_kategori): ?>
                    <input type="hidden" name="filter" value="<?php echo htmlspecialchars($filter_kategori); ?>">
                    <?php endif; ?>
                    <button type="submit" class="absolute right-2 top-1/2 -translate-y-1/2 bg-gradient-to-r from-orange-500 to-orange-600 text-white px-6 py-2 rounded-full hover:shadow-lg transition-all">
                        <i class="fas fa-search mr-2"></i>Cari
                    </button>
                </div>
            </div>
        </form>
    </div>
</section>

<!-- Archive List -->
<section class="py-16 bg-gray-50">
    <div class="container mx-auto px-4">
        
        <?php if ($arsip_list && count($arsip_list) > 0): ?>
        
        <div class="grid grid-cols-1 gap-6 max-w-4xl mx-auto">
            <?php foreach ($arsip_list as $index => $item): ?>
            <?php
            // Get authors
            $authors = executeQuery(
                "SELECT p.nama_lengkap, ap.peran 
                 FROM arsip_pengelola ap 
                 JOIN pengelola p ON ap.pengelola_id = p.id 
                 WHERE ap.arsip_id = ? 
                 ORDER BY ap.urutan_penulis",
                [$item['id']]
            );
            ?>
            
            <div class="arsip-card rounded-xl p-6" data-aos="fade-up" data-aos-delay="<?php echo ($index * 50); ?>">
                <div class="flex flex-col md:flex-row gap-6">
                    <!-- PDF Icon -->
                    <div class="flex-shrink-0">
                        <div class="w-20 h-20 bg-gradient-to-br from-red-500 to-red-600 rounded-xl flex items-center justify-center shadow-lg">
                            <i class="fas fa-file-pdf text-white text-3xl"></i>
                        </div>
                    </div>
                    
                    <!-- Content -->
                    <div class="flex-1">
                        <!-- Category & Year -->
                        <div class="flex items-center gap-3 mb-3">
                            <span class="badge-kategori">
                                <i class="fas <?php echo $item['kategori'] === 'penelitian' ? 'fa-flask' : 'fa-hands-helping'; ?> mr-1"></i>
                                <?php echo ucfirst($item['kategori']); ?>
                            </span>
                            <span class="text-gray-500 text-sm font-semibold">
                                <i class="fas fa-calendar mr-1"></i><?php echo $item['tahun_publikasi']; ?>
                            </span>
                            <?php if ($item['is_featured']): ?>
                            <span class="bg-yellow-100 text-yellow-800 text-xs px-2 py-1 rounded-full font-semibold">
                                <i class="fas fa-star mr-1"></i>Featured
                            </span>
                            <?php endif; ?>
                        </div>
                        
                        <!-- Title -->
                        <h3 class="text-xl font-bold text-gray-900 mb-2 hover:text-orange-600 transition-colors">
                            <?php echo htmlspecialchars($item['judul']); ?>
                        </h3>
                        
                        <!-- Authors -->
                        <?php if ($authors): ?>
                        <p class="text-sm text-blue-600 mb-3">
                            <i class="fas fa-user-edit mr-1"></i>
                            <?php 
                            $author_names = array_map(function($a) { return $a['nama_lengkap']; }, $authors);
                            echo htmlspecialchars(implode(', ', $author_names));
                            ?>
                        </p>
                        <?php endif; ?>
                        
                        <!-- Abstract -->
                        <?php if ($item['abstrak']): ?>
                        <p class="text-gray-600 mb-4 leading-relaxed line-clamp-3">
                            <?php echo htmlspecialchars($item['abstrak']); ?>
                        </p>
                        <?php endif; ?>
                        
                        <!-- Publisher -->
                        <?php if ($item['penerbit']): ?>
                        <p class="text-sm text-gray-500 mb-3">
                            <i class="fas fa-book mr-1 text-orange-500"></i>
                            <?php echo htmlspecialchars($item['penerbit']); ?>
                        </p>
                        <?php endif; ?>
                        
                        <!-- Keywords -->
                        <?php if ($item['keywords']): ?>
                        <div class="flex flex-wrap gap-2 mb-4">
                            <?php 
                            $keywords = array_map('trim', explode(',', $item['keywords']));
                            foreach ($keywords as $keyword): 
                            ?>
                            <span class="keyword-tag">
                                <i class="fas fa-tag mr-1"></i><?php echo htmlspecialchars($keyword); ?>
                            </span>
                            <?php endforeach; ?>
                        </div>
                        <?php endif; ?>
                        
                        <!-- Footer -->
                        <div class="flex items-center justify-between pt-4 border-t">
                            <div class="flex items-center gap-4 text-sm text-gray-500">
                                <span>
                                    <i class="fas fa-download text-purple-500 mr-1"></i>
                                    <?php echo number_format($item['jumlah_download']); ?> download
                                </span>
                            </div>
                            
                            <a href="?download=<?php echo $item['id']; ?>" 
                               class="download-btn px-6 py-2 rounded-lg font-semibold inline-flex items-center gap-2 shadow-md">
                                <i class="fas fa-download"></i>
                                Download PDF
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        
        <?php else: ?>
        
        <!-- Empty State -->
        <div class="text-center py-16 max-w-lg mx-auto" data-aos="fade-up">
            <i class="fas fa-file-pdf text-gray-300 text-8xl mb-6"></i>
            <h3 class="text-2xl font-bold text-gray-700 mb-3">
                Arsip Tidak Ditemukan
            </h3>
            <p class="text-gray-500 text-lg mb-6">
                <?php if ($search): ?>
                Tidak ada hasil untuk pencarian "<?php echo htmlspecialchars($search); ?>".
                <?php else: ?>
                Belum ada arsip <?php echo $filter_kategori ? ucfirst($filter_kategori) : ''; ?> yang dipublikasikan.
                <?php endif; ?>
            </p>
            <a href="?" class="inline-block bg-gradient-to-r from-orange-500 to-orange-600 text-white font-semibold px-6 py-3 rounded-lg hover:shadow-lg transition-all">
                <i class="fas fa-arrow-left mr-2"></i>Lihat Semua Arsip
            </a>
        </div>
        
        <?php endif; ?>
        
    </div>
</section>

<?php
// Include public footer
require_once __DIR__ . '/../includes/footer.php';
?>
