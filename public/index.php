<?php
/**
 * Homepage
 * File: public/index.php
 * Design Reference: Active SaaS (Orange Accent)
 */

// Set page variables
$page_title = "Beranda - Laboratorium NCS";
$page_description = "Selamat datang di Website Laboratorium NCS - Network and Computer System Laboratory";
$page_keywords = "laboratorium, network, computer system, ncs, penelitian, pengabdian";

// Include header
require_once __DIR__ . '/../includes/header.php';

// Get statistics
$total_sarana = countRows("SELECT COUNT(*) FROM sarana WHERE is_active = true");
$total_pengelola = countRows("SELECT COUNT(*) FROM pengelola WHERE is_active = true");
$total_arsip = countRows("SELECT COUNT(*) FROM arsip WHERE is_active = true");
$total_galeri = countRows("SELECT COUNT(*) FROM galeri WHERE is_active = true");

// Get latest galeri (6 items)
$latest_galeri = executeQuery(
    "SELECT * FROM galeri WHERE is_active = true ORDER BY created_at DESC LIMIT 6"
);

// Get team preview (4 members)
$team_preview = executeQuery(
    "SELECT * FROM pengelola WHERE is_active = true ORDER BY urutan ASC LIMIT 4"
);

// Get latest arsip (3 items)
$latest_arsip = executeQuery(
    "SELECT a.*, 
            (SELECT COUNT(*) FROM arsip_pengelola WHERE arsip_id = a.id) as author_count
     FROM arsip a 
     WHERE a.is_active = true 
     ORDER BY a.created_at DESC 
     LIMIT 3"
);
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
    position: relative;
    overflow: hidden;
}

.hero-gradient::before {
    content: '';
    position: absolute;
    top: -50%;
    right: -10%;
    width: 500px;
    height: 500px;
    background: rgba(255, 255, 255, 0.1);
    border-radius: 50%;
    animation: float 20s infinite ease-in-out;
}

@keyframes float {
    0%, 100% { transform: translate(0, 0) scale(1); }
    50% { transform: translate(-50px, 50px) scale(1.1); }
}

.feature-card {
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    border: 1px solid #E5E7EB;
    background: white;
    border-radius: 1rem;
    padding: 2rem;
}

.feature-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
    border-color: var(--orange-500);
}

.feature-icon {
    width: 64px;
    height: 64px;
    background: linear-gradient(135deg, var(--orange-500), var(--orange-600));
    border-radius: 1rem;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 1.5rem;
}

.stat-card {
    background: white;
    border-radius: 1rem;
    padding: 2rem;
    text-align: center;
    transition: all 0.3s ease;
    border: 2px solid transparent;
}

.stat-card:hover {
    border-color: var(--orange-500);
    transform: scale(1.05);
}

.stat-number {
    font-size: 3rem;
    font-weight: 800;
    background: linear-gradient(135deg, var(--orange-500), var(--orange-700));
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

.gallery-card {
    border-radius: 1rem;
    overflow: hidden;
    transition: all 0.3s ease;
    position: relative;
}

.gallery-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.15);
}

.gallery-image {
    width: 100%;
    height: 250px;
    object-fit: cover;
}

.gallery-overlay {
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    background: linear-gradient(to top, rgba(0,0,0,0.8), transparent);
    padding: 1.5rem;
    color: white;
}

.team-card {
    background: white;
    border-radius: 1rem;
    overflow: hidden;
    transition: all 0.3s ease;
    border: 1px solid #E5E7EB;
}

.team-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
    border-color: var(--orange-500);
}

.team-photo {
    width: 100%;
    height: 280px;
    object-fit: cover;
    background: linear-gradient(135deg, #F3F4F6, #E5E7EB);
}

.arsip-card {
    background: white;
    border: 1px solid #E5E7EB;
    border-radius: 1rem;
    padding: 1.5rem;
    transition: all 0.3s ease;
}

.arsip-card:hover {
    border-color: var(--orange-500);
    box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
}

.btn-primary {
    background: linear-gradient(135deg, var(--orange-500), var(--orange-600));
    color: white;
    padding: 1rem 2.5rem;
    border-radius: 0.75rem;
    font-weight: 600;
    transition: all 0.3s ease;
    display: inline-block;
    text-decoration: none;
}

.btn-primary:hover {
    box-shadow: 0 10px 15px -3px rgba(249, 115, 22, 0.4);
    transform: translateY(-2px);
    color: white;
}

.btn-secondary {
    background: white;
    color: var(--orange-600);
    border: 2px solid var(--orange-600);
    padding: 1rem 2.5rem;
    border-radius: 0.75rem;
    font-weight: 600;
    transition: all 0.3s ease;
    display: inline-block;
    text-decoration: none;
}

.btn-secondary:hover {
    background: var(--orange-50);
    color: var(--orange-700);
    transform: translateY(-2px);
}
</style>

<!-- Hero Section -->
<section class="hero-gradient text-white py-24 relative" data-aos="fade-down">
    <div class="container mx-auto px-4">
        <div class="max-w-5xl mx-auto text-center relative z-10">
            <h1 class="text-5xl md:text-7xl font-bold mb-6" data-aos="fade-up" data-aos-delay="100">
                Laboratorium Network & Computer System
            </h1>
            <p class="text-xl md:text-2xl text-orange-100 mb-8" data-aos="fade-up" data-aos-delay="200">
                Pusat penelitian, pengabdian, dan pembelajaran di bidang jaringan komputer dan sistem informasi
            </p>
            <div class="flex justify-center gap-4 flex-wrap" data-aos="fade-up" data-aos-delay="300">
                <a href="<?php echo SITE_URL; ?>/about.php" class="btn-secondary">
                    <i class="fas fa-info-circle mr-2"></i>Tentang Kami
                </a>
                <a href="<?php echo SITE_URL; ?>/konsultatif.php" class="btn-primary">
                    <i class="fas fa-paper-plane mr-2"></i>Hubungi Kami
                </a>
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

<!-- Features Section -->
<section class="py-20 bg-white">
    <div class="container mx-auto px-4">
        <div class="text-center mb-16" data-aos="fade-up">
            <h2 class="text-4xl md:text-5xl font-bold text-gray-900 mb-4">
                Layanan Kami
            </h2>
            <p class="text-xl text-gray-600 max-w-2xl mx-auto">
                Berbagai layanan dan fasilitas untuk mendukung penelitian, pengabdian, dan pembelajaran
            </p>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 max-w-6xl mx-auto">
            <!-- Feature 1 -->
            <div class="feature-card" data-aos="fade-up" data-aos-delay="100">
                <div class="feature-icon">
                    <i class="fas fa-flask text-white text-3xl"></i>
                </div>
                <h3 class="text-2xl font-bold mb-3 text-gray-900">Penelitian</h3>
                <p class="text-gray-600 leading-relaxed">
                    Fasilitas dan dukungan untuk penelitian berkualitas di bidang jaringan komputer dan sistem informasi
                </p>
            </div>
            
            <!-- Feature 2 -->
            <div class="feature-card" data-aos="fade-up" data-aos-delay="200">
                <div class="feature-icon">
                    <i class="fas fa-hands-helping text-white text-3xl"></i>
                </div>
                <h3 class="text-2xl font-bold mb-3 text-gray-900">Pengabdian</h3>
                <p class="text-gray-600 leading-relaxed">
                    Program pengabdian masyarakat untuk memberikan kontribusi nyata kepada komunitas
                </p>
            </div>
            
            <!-- Feature 3 -->
            <div class="feature-card" data-aos="fade-up" data-aos-delay="300">
                <div class="feature-icon">
                    <i class="fas fa-graduation-cap text-white text-3xl"></i>
                </div>
                <h3 class="text-2xl font-bold mb-3 text-gray-900">Praktikum</h3>
                <p class="text-gray-600 leading-relaxed">
                    Sarana praktikum lengkap untuk mendukung pembelajaran mahasiswa
                </p>
            </div>
        </div>
    </div>
</section>

<!-- Statistics Section -->
<section class="py-20 bg-gray-50">
    <div class="container mx-auto px-4">
        <div class="text-center mb-16" data-aos="fade-up">
            <h2 class="text-4xl md:text-5xl font-bold text-gray-900 mb-4">
                Laboratorium dalam Angka
            </h2>
            <p class="text-xl text-gray-600">
                Data dan statistik Laboratorium NCS
            </p>
        </div>
        
        <div class="grid grid-cols-2 md:grid-cols-4 gap-6 max-w-6xl mx-auto">
            <div class="stat-card" data-aos="zoom-in" data-aos-delay="100">
                <i class="fas fa-box text-orange-500 text-4xl mb-4"></i>
                <div class="stat-number"><?php echo $total_sarana; ?>+</div>
                <p class="text-gray-600 font-semibold mt-2">Sarana Praktikum</p>
            </div>
            
            <div class="stat-card" data-aos="zoom-in" data-aos-delay="200">
                <i class="fas fa-users text-orange-500 text-4xl mb-4"></i>
                <div class="stat-number"><?php echo $total_pengelola; ?>+</div>
                <p class="text-gray-600 font-semibold mt-2">Tim Pengelola</p>
            </div>
            
            <div class="stat-card" data-aos="zoom-in" data-aos-delay="300">
                <i class="fas fa-file-pdf text-orange-500 text-4xl mb-4"></i>
                <div class="stat-number"><?php echo $total_arsip; ?>+</div>
                <p class="text-gray-600 font-semibold mt-2">Publikasi</p>
            </div>
            
            <div class="stat-card" data-aos="zoom-in" data-aos-delay="400">
                <i class="fas fa-images text-orange-500 text-4xl mb-4"></i>
                <div class="stat-number"><?php echo $total_galeri; ?>+</div>
                <p class="text-gray-600 font-semibold mt-2">Dokumentasi</p>
            </div>
        </div>
    </div>
</section>

<!-- Latest Galeri Section -->
<?php if ($latest_galeri && count($latest_galeri) > 0): ?>
<section class="py-20 bg-white">
    <div class="container mx-auto px-4">
        <div class="flex justify-between items-end mb-12" data-aos="fade-up">
            <div>
                <h2 class="text-4xl md:text-5xl font-bold text-gray-900 mb-4">
                    Galeri Terbaru
                </h2>
                <p class="text-xl text-gray-600">
                    Dokumentasi kegiatan dan agenda laboratorium
                </p>
            </div>
            <a href="<?php echo SITE_URL; ?>/galeri.php" class="hidden md:inline-block text-orange-600 font-semibold hover:text-orange-700 transition-colors">
                Lihat Semua <i class="fas fa-arrow-right ml-2"></i>
            </a>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php foreach ($latest_galeri as $index => $item): ?>
            <div class="gallery-card" data-aos="fade-up" data-aos-delay="<?php echo ($index * 100); ?>">
                <?php if ($item['gambar_path']): ?>
                <img src="<?php echo SITE_URL . htmlspecialchars($item['gambar_path']); ?>" 
                     alt="<?php echo htmlspecialchars($item['judul']); ?>" 
                     class="gallery-image">
                <?php else: ?>
                <div class="gallery-image flex items-center justify-center bg-gray-200">
                    <i class="fas fa-image text-gray-400 text-5xl"></i>
                </div>
                <?php endif; ?>
                
                <div class="gallery-overlay">
                    <div class="flex items-center gap-2 mb-2">
                        <span class="text-xs font-semibold px-2 py-1 bg-orange-500 rounded-full">
                            <?php echo ucfirst($item['tipe']); ?>
                        </span>
                        <span class="text-xs text-orange-100">
                            <i class="fas fa-calendar mr-1"></i>
                            <?php echo date('d M Y', strtotime($item['tanggal_kegiatan'])); ?>
                        </span>
                    </div>
                    <h3 class="font-bold text-lg line-clamp-2">
                        <?php echo htmlspecialchars($item['judul']); ?>
                    </h3>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        
        <div class="text-center mt-8 md:hidden">
            <a href="<?php echo SITE_URL; ?>/galeri.php" class="btn-primary">
                Lihat Semua Galeri
            </a>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- Latest Arsip Section -->
<?php if ($latest_arsip && count($latest_arsip) > 0): ?>
<section class="py-20 bg-gray-50">
    <div class="container mx-auto px-4">
        <div class="flex justify-between items-end mb-12" data-aos="fade-up">
            <div>
                <h2 class="text-4xl md:text-5xl font-bold text-gray-900 mb-4">
                    Publikasi Terbaru
                </h2>
                <p class="text-xl text-gray-600">
                    Hasil penelitian dan pengabdian masyarakat
                </p>
            </div>
            <a href="<?php echo SITE_URL; ?>/arsip.php" class="hidden md:inline-block text-orange-600 font-semibold hover:text-orange-700 transition-colors">
                Lihat Semua <i class="fas fa-arrow-right ml-2"></i>
            </a>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 max-w-6xl mx-auto">
            <?php foreach ($latest_arsip as $index => $item): ?>
            <div class="arsip-card" data-aos="fade-up" data-aos-delay="<?php echo ($index * 100); ?>">
                <div class="flex items-center gap-3 mb-3">
                    <div class="w-12 h-12 bg-gradient-to-br from-red-500 to-red-600 rounded-lg flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-file-pdf text-white text-xl"></i>
                    </div>
                    <div class="flex-1">
                        <span class="text-xs font-semibold text-orange-600">
                            <?php echo ucfirst($item['kategori']); ?>
                        </span>
                        <p class="text-xs text-gray-500">
                            <?php echo $item['tahun_publikasi']; ?>
                        </p>
                    </div>
                </div>
                
                <h3 class="font-bold text-gray-900 mb-2 line-clamp-2">
                    <?php echo htmlspecialchars($item['judul']); ?>
                </h3>
                
                <p class="text-sm text-gray-600 mb-3 line-clamp-2">
                    <?php echo htmlspecialchars($item['abstrak']); ?>
                </p>
                
                <div class="flex items-center justify-between text-xs text-gray-500 pt-3 border-t">
                    <span>
                        <i class="fas fa-user-edit mr-1"></i>
                        <?php echo $item['author_count']; ?> Penulis
                    </span>
                    <span>
                        <i class="fas fa-download mr-1"></i>
                        <?php echo $item['jumlah_download']; ?>x
                    </span>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        
        <div class="text-center mt-8 md:hidden">
            <a href="<?php echo SITE_URL; ?>/arsip.php" class="btn-primary">
                Lihat Semua Publikasi
            </a>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- Team Preview Section -->
<?php if ($team_preview && count($team_preview) > 0): ?>
<section class="py-20 bg-white">
    <div class="container mx-auto px-4">
        <div class="flex justify-between items-end mb-12" data-aos="fade-up">
            <div>
                <h2 class="text-4xl md:text-5xl font-bold text-gray-900 mb-4">
                    Tim Kami
                </h2>
                <p class="text-xl text-gray-600">
                    Pengelola Laboratorium NCS
                </p>
            </div>
            <a href="<?php echo SITE_URL; ?>/pengelola.php" class="hidden md:inline-block text-orange-600 font-semibold hover:text-orange-700 transition-colors">
                Lihat Semua <i class="fas fa-arrow-right ml-2"></i>
            </a>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 max-w-6xl mx-auto">
            <?php foreach ($team_preview as $index => $member): ?>
            <div class="team-card" data-aos="fade-up" data-aos-delay="<?php echo ($index * 100); ?>">
                <?php if ($member['foto_path']): ?>
                <img src="<?php echo SITE_URL . htmlspecialchars($member['foto_path']); ?>" 
                     alt="<?php echo htmlspecialchars($member['nama_lengkap']); ?>" 
                     class="team-photo">
                <?php else: ?>
                <div class="team-photo flex items-center justify-center">
                    <i class="fas fa-user text-gray-400 text-6xl"></i>
                </div>
                <?php endif; ?>
                
                <div class="p-4 text-center">
                    <h3 class="font-bold text-lg text-gray-900 mb-1">
                        <?php echo htmlspecialchars($member['nama_lengkap']); ?>
                    </h3>
                    <p class="text-sm font-semibold text-orange-600 mb-2">
                        <?php echo htmlspecialchars($member['jabatan']); ?>
                    </p>
                    <?php if ($member['email']): ?>
                    <a href="mailto:<?php echo htmlspecialchars($member['email']); ?>" 
                       class="text-xs text-gray-500 hover:text-orange-600 transition-colors">
                        <i class="fas fa-envelope mr-1"></i><?php echo htmlspecialchars($member['email']); ?>
                    </a>
                    <?php endif; ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        
        <div class="text-center mt-8 md:hidden">
            <a href="<?php echo SITE_URL; ?>/pengelola.php" class="btn-primary">
                Lihat Semua Tim
            </a>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- CTA Section -->
<section class="py-20 bg-gradient-to-r from-orange-600 to-orange-700 text-white relative overflow-hidden">
    <div class="absolute inset-0 opacity-10">
        <div class="absolute top-0 left-0 w-64 h-64 bg-white rounded-full -translate-x-1/2 -translate-y-1/2"></div>
        <div class="absolute bottom-0 right-0 w-96 h-96 bg-white rounded-full translate-x-1/2 translate-y-1/2"></div>
    </div>
    
    <div class="container mx-auto px-4 text-center relative z-10" data-aos="zoom-in">
        <h2 class="text-4xl md:text-5xl font-bold mb-4">
            Butuh Konsultasi atau Kerjasama?
        </h2>
        <p class="text-xl mb-8 text-orange-100 max-w-2xl mx-auto">
            Tim kami siap membantu Anda untuk penelitian, pengabdian, atau konsultasi teknologi
        </p>
        <div class="flex justify-center gap-4 flex-wrap">
            <a href="<?php echo SITE_URL; ?>/konsultatif.php" class="btn-secondary">
                <i class="fas fa-paper-plane mr-2"></i>Hubungi Kami
            </a>
            <a href="<?php echo SITE_URL; ?>/sarana.php" class="bg-white/20 backdrop-blur-sm text-white border-2 border-white px-8 py-4 rounded-xl font-semibold hover:bg-white/30 transition-all inline-block">
                <i class="fas fa-box mr-2"></i>Lihat Fasilitas
            </a>
        </div>
    </div>
</section>

<?php
// Include footer
require_once __DIR__ . '/../includes/footer.php';
?>
