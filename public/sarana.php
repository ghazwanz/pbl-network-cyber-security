<?php
/**
 * Public Sarana Page
 * File: public/sarana.php
 * Design Reference: Active SaaS (Orange Accent)
 */

// Set page title
$page_title = "Sarana & Prasarana - Laboratorium NCS";

// Include public header
require_once __DIR__ . '/../includes/header.php';

// Get filter
$filter_kondisi = $_GET['filter'] ?? '';
$search = $_GET['search'] ?? '';

// Build query
$where = ["is_active = true"];
$params = [];

if ($filter_kondisi && in_array($filter_kondisi, ['Baik', 'Rusak Ringan', 'Rusak Berat'])) {
    $where[] = "kondisi = ?";
    $params[] = $filter_kondisi;
}

if ($search) {
    $where[] = "(nama_barang ILIKE ? OR deskripsi ILIKE ? OR lokasi ILIKE ?)";
    $search_param = '%' . $search . '%';
    $params[] = $search_param;
    $params[] = $search_param;
    $params[] = $search_param;
}

$where_clause = implode(' AND ', $where);

// Get data
$query = "SELECT * FROM sarana WHERE " . $where_clause . " ORDER BY created_at DESC LIMIT 50";
$sarana_list = executeQuery($query, $params);

// Get counts
$count_baik = countRows("SELECT COUNT(*) FROM sarana WHERE kondisi = 'Baik' AND is_active = true");
$count_rusak_ringan = countRows("SELECT COUNT(*) FROM sarana WHERE kondisi = 'Rusak Ringan' AND is_active = true");
$count_rusak_berat = countRows("SELECT COUNT(*) FROM sarana WHERE kondisi = 'Rusak Berat' AND is_active = true");
$total_sarana = countRows("SELECT COUNT(*) FROM sarana WHERE is_active = true");
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

.sarana-card {
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    border: 1px solid #E5E7EB;
    background: white;
    border-radius: 1rem;
    overflow: hidden;
}

.sarana-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
    border-color: var(--orange-500);
}

.sarana-image {
    width: 100%;
    height: 200px;
    object-fit: cover;
    background: linear-gradient(135deg, #F3F4F6 0%, #E5E7EB 100%);
}

.badge-kondisi {
    padding: 0.375rem 0.875rem;
    border-radius: 9999px;
    font-size: 0.875rem;
    font-weight: 600;
    display: inline-flex;
    align-items: center;
    gap: 0.375rem;
}

.kondisi-baik {
    background: #D1FAE5;
    color: #065F46;
}

.kondisi-rusak-ringan {
    background: #FEF3C7;
    color: #92400E;
}

.kondisi-rusak-berat {
    background: #FEE2E2;
    color: #991B1B;
}

.filter-btn {
    padding: 0.75rem 1.5rem;
    font-weight: 600;
    border-radius: 9999px;
    transition: all 0.3s ease;
    border: 2px solid transparent;
    cursor: pointer;
    text-decoration: none;
    display: inline-block;
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

.price-tag {
    font-size: 1.25rem;
    font-weight: 700;
    color: var(--orange-600);
}

.stat-icon {
    width: 48px;
    height: 48px;
    border-radius: 0.75rem;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
}
</style>

<!-- Hero Section -->
<section class="hero-gradient text-white py-20 relative" data-aos="fade-down">
    <div class="container mx-auto px-4">
        <div class="max-w-4xl mx-auto text-center">
            <h1 class="text-5xl md:text-6xl font-bold mb-6" data-aos="fade-up" data-aos-delay="100">
                Sarana & Prasarana
            </h1>
            <p class="text-xl md:text-2xl text-orange-100 mb-8" data-aos="fade-up" data-aos-delay="200">
                Fasilitas dan peralatan laboratorium untuk mendukung penelitian dan praktikum
            </p>
            
            <!-- Stats -->
            <div class="grid grid-cols-4 gap-6 max-w-4xl mx-auto" data-aos="fade-up" data-aos-delay="300">
                <div class="bg-white/10 backdrop-blur-sm rounded-xl p-4">
                    <div class="text-3xl font-bold mb-1"><?php echo $total_sarana; ?></div>
                    <div class="text-orange-100 text-sm">Total Sarana</div>
                </div>
                <div class="bg-white/10 backdrop-blur-sm rounded-xl p-4">
                    <div class="text-3xl font-bold mb-1"><?php echo $count_baik; ?></div>
                    <div class="text-orange-100 text-sm">Kondisi Baik</div>
                </div>
                <div class="bg-white/10 backdrop-blur-sm rounded-xl p-4">
                    <div class="text-3xl font-bold mb-1"><?php echo $count_rusak_ringan; ?></div>
                    <div class="text-orange-100 text-sm">Rusak Ringan</div>
                </div>
                <div class="bg-white/10 backdrop-blur-sm rounded-xl p-4">
                    <div class="text-3xl font-bold mb-1"><?php echo $count_rusak_berat; ?></div>
                    <div class="text-orange-100 text-sm">Rusak Berat</div>
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
                <a href="?" class="filter-btn <?php echo empty($filter_kondisi) ? 'active' : ''; ?>">
                    <i class="fas fa-th mr-2"></i>Semua
                    <span class="ml-2 inline-flex items-center justify-center w-6 h-6 text-xs rounded-full <?php echo empty($filter_kondisi) ? 'bg-white/20' : 'bg-gray-200'; ?>">
                        <?php echo $total_sarana; ?>
                    </span>
                </a>
                
                <a href="?filter=Baik" class="filter-btn <?php echo $filter_kondisi === 'Baik' ? 'active' : ''; ?>">
                    <i class="fas fa-check-circle mr-2"></i>Kondisi Baik
                    <span class="ml-2 inline-flex items-center justify-center w-6 h-6 text-xs rounded-full <?php echo $filter_kondisi === 'Baik' ? 'bg-white/20' : 'bg-gray-200'; ?>">
                        <?php echo $count_baik; ?>
                    </span>
                </a>
                
                <a href="?filter=Rusak+Ringan" class="filter-btn <?php echo $filter_kondisi === 'Rusak Ringan' ? 'active' : ''; ?>">
                    <i class="fas fa-exclamation-triangle mr-2"></i>Rusak Ringan
                    <span class="ml-2 inline-flex items-center justify-center w-6 h-6 text-xs rounded-full <?php echo $filter_kondisi === 'Rusak Ringan' ? 'bg-white/20' : 'bg-gray-200'; ?>">
                        <?php echo $count_rusak_ringan; ?>
                    </span>
                </a>
                
                <a href="?filter=Rusak+Berat" class="filter-btn <?php echo $filter_kondisi === 'Rusak Berat' ? 'active' : ''; ?>">
                    <i class="fas fa-times-circle mr-2"></i>Rusak Berat
                    <span class="ml-2 inline-flex items-center justify-center w-6 h-6 text-xs rounded-full <?php echo $filter_kondisi === 'Rusak Berat' ? 'bg-white/20' : 'bg-gray-200'; ?>">
                        <?php echo $count_rusak_berat; ?>
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
                        placeholder="Cari nama barang, deskripsi, atau lokasi..." 
                        class="w-full px-6 py-4 pr-32 text-lg border-2 border-gray-200 rounded-full focus:border-orange-500 focus:outline-none focus:ring-2 focus:ring-orange-200"
                    >
                    <?php if ($filter_kondisi): ?>
                    <input type="hidden" name="filter" value="<?php echo htmlspecialchars($filter_kondisi); ?>">
                    <?php endif; ?>
                    <button type="submit" class="absolute right-2 top-1/2 -translate-y-1/2 bg-gradient-to-r from-orange-500 to-orange-600 text-white px-6 py-2 rounded-full hover:shadow-lg transition-all">
                        <i class="fas fa-search mr-2"></i>Cari
                    </button>
                </div>
            </div>
        </form>
    </div>
</section>

<!-- Sarana List -->
<section class="py-16 bg-gray-50">
    <div class="container mx-auto px-4">
        
        <?php if ($sarana_list && count($sarana_list) > 0): ?>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php foreach ($sarana_list as $index => $item): ?>
            
            <div class="sarana-card" data-aos="fade-up" data-aos-delay="<?php echo ($index * 50); ?>">
                <!-- Image -->
                <?php if ($item['foto_path']): ?>
                <img src="<?php echo SITE_URL . htmlspecialchars($item['foto_path']); ?>" 
                     alt="<?php echo htmlspecialchars($item['nama_barang']); ?>" 
                     class="sarana-image">
                <?php else: ?>
                <div class="sarana-image flex items-center justify-center">
                    <i class="fas fa-box text-gray-400 text-5xl"></i>
                </div>
                <?php endif; ?>
                
                <!-- Content -->
                <div class="p-6">
                    <!-- Condition Badge -->
                    <div class="mb-3">
                        <span class="badge-kondisi kondisi-<?php echo strtolower(str_replace(' ', '-', $item['kondisi'])); ?>">
                            <?php
                            $kondisi_icons = [
                                'Baik' => 'fa-check-circle',
                                'Rusak Ringan' => 'fa-exclamation-triangle',
                                'Rusak Berat' => 'fa-times-circle'
                            ];
                            ?>
                            <i class="fas <?php echo $kondisi_icons[$item['kondisi']]; ?>"></i>
                            <?php echo htmlspecialchars($item['kondisi']); ?>
                        </span>
                    </div>
                    
                    <!-- Name -->
                    <h3 class="text-xl font-bold text-gray-900 mb-2">
                        <?php echo htmlspecialchars($item['nama_barang']); ?>
                    </h3>
                    
                    <!-- Quantity & Location -->
                    <div class="flex items-center gap-4 mb-3 text-sm text-gray-600">
                        <span>
                            <i class="fas fa-layer-group text-orange-500 mr-1"></i>
                            <?php echo number_format($item['jumlah']); ?> unit
                        </span>
                        <?php if ($item['lokasi']): ?>
                        <span>
                            <i class="fas fa-map-marker-alt text-orange-500 mr-1"></i>
                            <?php echo htmlspecialchars($item['lokasi']); ?>
                        </span>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Description -->
                    <?php if ($item['deskripsi']): ?>
                    <p class="text-gray-600 text-sm mb-4 line-clamp-2">
                        <?php echo htmlspecialchars($item['deskripsi']); ?>
                    </p>
                    <?php endif; ?>
                    
                    <!-- Footer -->
                    <div class="pt-4 border-t border-gray-200">
                        <div class="flex items-center justify-between">
                            <div>
                                <?php if ($item['harga_perolehan']): ?>
                                <div class="price-tag">
                                    Rp <?php echo number_format($item['harga_perolehan'], 0, ',', '.'); ?>
                                </div>
                                <?php endif; ?>
                                <?php if ($item['tanggal_perolehan']): ?>
                                <div class="text-xs text-gray-500 mt-1">
                                    <i class="fas fa-calendar mr-1"></i>
                                    <?php echo date('d/m/Y', strtotime($item['tanggal_perolehan'])); ?>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <?php endforeach; ?>
        </div>
        
        <?php else: ?>
        
        <!-- Empty State -->
        <div class="text-center py-16 max-w-lg mx-auto" data-aos="fade-up">
            <i class="fas fa-box-open text-gray-300 text-8xl mb-6"></i>
            <h3 class="text-2xl font-bold text-gray-700 mb-3">
                Sarana Tidak Ditemukan
            </h3>
            <p class="text-gray-500 text-lg mb-6">
                <?php if ($search): ?>
                Tidak ada hasil untuk pencarian "<?php echo htmlspecialchars($search); ?>".
                <?php else: ?>
                Belum ada sarana dengan kondisi <?php echo $filter_kondisi ? htmlspecialchars($filter_kondisi) : ''; ?>.
                <?php endif; ?>
            </p>
            <a href="?" class="inline-block bg-gradient-to-r from-orange-500 to-orange-600 text-white font-semibold px-6 py-3 rounded-lg hover:shadow-lg transition-all">
                <i class="fas fa-arrow-left mr-2"></i>Lihat Semua Sarana
            </a>
        </div>
        
        <?php endif; ?>
        
    </div>
</section>

<!-- CTA Section -->
<section class="py-16 bg-white">
    <div class="container mx-auto px-4">
        <div class="max-w-4xl mx-auto text-center" data-aos="fade-up">
            <div class="bg-gradient-to-r from-orange-500 to-orange-600 rounded-2xl p-12 text-white">
                <h3 class="text-3xl font-bold mb-4">
                    Butuh Informasi Lebih Lanjut?
                </h3>
                <p class="text-xl text-orange-100 mb-8">
                    Hubungi kami untuk konsultasi penggunaan sarana laboratorium
                </p>
                <a href="<?php echo SITE_URL; ?>/konsultatif.php" 
                   class="inline-block bg-white text-orange-600 font-semibold px-8 py-4 rounded-lg hover:bg-orange-50 transition-all text-lg">
                    <i class="fas fa-comments mr-2"></i>Hubungi Kami
                </a>
            </div>
        </div>
    </div>
</section>

<?php
// Include public footer
require_once __DIR__ . '/../includes/footer.php';
?>
