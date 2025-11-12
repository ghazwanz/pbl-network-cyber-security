<?php
/**
 * About Page - Lab Profile
 * File: public/about.php
 * Design Reference: Active SaaS (Orange Accent)
 */

// Set page title
$page_title = "Tentang Kami - Laboratorium NCS";

// Include public header
require_once __DIR__ . '/../includes/header.php';

// Get lab profile
$profil = executeQuerySingle("SELECT * FROM profil_lab LIMIT 1");

// Default values if no profile exists
if (!$profil) {
    $profil = [
        'nama_lab' => 'Laboratorium Network & Computer System',
        'deskripsi' => 'Laboratorium yang fokus pada penelitian dan pengembangan di bidang jaringan komputer dan sistem informasi.',
        'visi' => 'Menjadi laboratorium terdepan dalam penelitian dan pengembangan teknologi jaringan komputer.',
        'misi' => "1. Melakukan penelitian berkualitas\n2. Memberikan layanan pengabdian kepada masyarakat\n3. Mendukung pembelajaran mahasiswa",
        'sejarah' => 'Laboratorium NCS didirikan untuk mendukung kegiatan penelitian dan praktikum mahasiswa.',
        'logo_path' => null,
        'alamat' => 'Gedung Teknik Informatika, Universitas XYZ',
        'email' => 'lab.ncs@example.com',
        'telepon' => '+62 21 1234 5678',
        'website' => 'https://lab-ncs.example.com',
        'facebook' => null,
        'twitter' => null,
        'instagram' => null,
        'linkedin' => null
    ];
}
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

.mission-card {
    background: white;
    border: 2px solid #E5E7EB;
    border-radius: 1rem;
    padding: 2rem;
    transition: all 0.3s ease;
}

.mission-card:hover {
    border-color: var(--orange-500);
    transform: translateY(-4px);
    box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
}

.mission-number {
    width: 48px;
    height: 48px;
    background: linear-gradient(135deg, var(--orange-500), var(--orange-600));
    border-radius: 0.75rem;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1.5rem;
    font-weight: 800;
    margin-bottom: 1rem;
}

.history-section {
    background: white;
    border-left: 4px solid var(--orange-500);
    border-radius: 0.5rem;
    padding: 2rem;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
}

.contact-card {
    background: white;
    border-radius: 1rem;
    padding: 2rem;
    text-align: center;
    border: 2px solid #E5E7EB;
    transition: all 0.3s ease;
}

.contact-card:hover {
    border-color: var(--orange-500);
    transform: translateY(-4px);
}

.contact-icon {
    width: 64px;
    height: 64px;
    background: linear-gradient(135deg, var(--orange-500), var(--orange-600));
    border-radius: 1rem;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 1rem;
}

.social-btn {
    width: 48px;
    height: 48px;
    background: #F3F4F6;
    border-radius: 0.75rem;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #6B7280;
    transition: all 0.3s ease;
}

.social-btn:hover {
    background: var(--orange-500);
    color: white;
    transform: translateY(-2px);
}

.visi-box {
    background: linear-gradient(135deg, var(--orange-50) 0%, var(--orange-100) 100%);
    border-left: 4px solid var(--orange-500);
    border-radius: 1rem;
    padding: 2rem;
}

.logo-container {
    width: 200px;
    height: 200px;
    background: white;
    border-radius: 1rem;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
    padding: 1.5rem;
}

.logo-container img {
    max-width: 100%;
    max-height: 100%;
    object-fit: contain;
}
</style>

<!-- Hero Section -->
<section class="hero-gradient text-white py-20 relative" data-aos="fade-down">
    <div class="container mx-auto px-4">
        <div class="max-w-4xl mx-auto text-center">
            <h1 class="text-5xl md:text-6xl font-bold mb-6" data-aos="fade-up" data-aos-delay="100">
                Tentang Kami
            </h1>
            <p class="text-xl md:text-2xl text-orange-100 mb-8" data-aos="fade-up" data-aos-delay="200">
                Mengenal lebih dekat <?php echo htmlspecialchars($profil['nama_lab']); ?>
            </p>
        </div>
    </div>
    
    <!-- Wave Divider -->
    <div class="absolute bottom-0 left-0 right-0">
        <svg viewBox="0 0 1440 120" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M0 120L60 105C120 90 240 60 360 45C480 30 600 30 720 37.5C840 45 960 60 1080 67.5C1200 75 1320 75 1380 75L1440 75V120H1380C1320 120 1200 120 1080 120C960 120 840 120 720 120C600 120 480 120 360 120C240 120 120 120 60 120H0Z" fill="white"/>
        </svg>
    </div>
</section>

<!-- Profile Overview Section -->
<section class="py-20 bg-white">
    <div class="container mx-auto px-4">
        <div class="max-w-6xl mx-auto">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-12 items-center">
                <!-- Logo -->
                <div class="lg:col-span-1" data-aos="fade-right">
                    <div class="logo-container mx-auto">
                        <?php if ($profil['logo_path']): ?>
                        <img src="<?php echo SITE_URL . htmlspecialchars($profil['logo_path']); ?>" 
                             alt="<?php echo htmlspecialchars($profil['nama_lab']); ?>">
                        <?php else: ?>
                        <i class="fas fa-building text-gray-300 text-8xl"></i>
                        <?php endif; ?>
                    </div>
                </div>
                
                <!-- Description -->
                <div class="lg:col-span-2" data-aos="fade-left">
                    <h2 class="text-4xl font-bold text-gray-900 mb-4">
                        <?php echo htmlspecialchars($profil['nama_lab']); ?>
                    </h2>
                    <p class="text-xl text-gray-600 leading-relaxed mb-6">
                        <?php echo nl2br(htmlspecialchars($profil['deskripsi'])); ?>
                    </p>
                    
                    <div class="flex flex-wrap gap-4">
                        <?php if ($profil['email']): ?>
                        <a href="mailto:<?php echo htmlspecialchars($profil['email']); ?>" 
                           class="inline-flex items-center gap-2 text-orange-600 hover:text-orange-700 font-semibold">
                            <i class="fas fa-envelope"></i>
                            <?php echo htmlspecialchars($profil['email']); ?>
                        </a>
                        <?php endif; ?>
                        
                        <?php if ($profil['telepon']): ?>
                        <a href="tel:<?php echo htmlspecialchars($profil['telepon']); ?>" 
                           class="inline-flex items-center gap-2 text-orange-600 hover:text-orange-700 font-semibold">
                            <i class="fas fa-phone"></i>
                            <?php echo htmlspecialchars($profil['telepon']); ?>
                        </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Visi Section -->
<section class="py-20 bg-gray-50">
    <div class="container mx-auto px-4">
        <div class="max-w-4xl mx-auto" data-aos="fade-up">
            <div class="text-center mb-12">
                <h2 class="text-4xl font-bold text-gray-900 mb-4">
                    <i class="fas fa-eye text-orange-600 mr-3"></i>Visi
                </h2>
            </div>
            
            <div class="visi-box">
                <p class="text-xl text-gray-800 leading-relaxed text-center">
                    <?php echo nl2br(htmlspecialchars($profil['visi'])); ?>
                </p>
            </div>
        </div>
    </div>
</section>

<!-- Misi Section -->
<section class="py-20 bg-white">
    <div class="container mx-auto px-4">
        <div class="max-w-6xl mx-auto">
            <div class="text-center mb-12" data-aos="fade-up">
                <h2 class="text-4xl font-bold text-gray-900 mb-4">
                    <i class="fas fa-bullseye text-orange-600 mr-3"></i>Misi
                </h2>
                <p class="text-xl text-gray-600">
                    Langkah strategis untuk mencapai visi kami
                </p>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <?php 
                $misi_items = explode("\n", $profil['misi']);
                $misi_count = 0;
                foreach ($misi_items as $index => $misi_item): 
                    $misi_item = trim($misi_item);
                    if (empty($misi_item)) continue;
                    $misi_count++;
                    // Remove number prefix if exists (1. 2. etc)
                    $misi_text = preg_replace('/^\d+\.\s*/', '', $misi_item);
                ?>
                <div class="mission-card" data-aos="fade-up" data-aos-delay="<?php echo ($misi_count * 100); ?>">
                    <div class="mission-number">
                        <?php echo $misi_count; ?>
                    </div>
                    <p class="text-gray-700 leading-relaxed">
                        <?php echo htmlspecialchars($misi_text); ?>
                    </p>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</section>

<!-- History Section -->
<?php if ($profil['sejarah']): ?>
<section class="py-20 bg-gray-50">
    <div class="container mx-auto px-4">
        <div class="max-w-4xl mx-auto" data-aos="fade-up">
            <div class="text-center mb-12">
                <h2 class="text-4xl font-bold text-gray-900 mb-4">
                    <i class="fas fa-history text-orange-600 mr-3"></i>Sejarah
                </h2>
                <p class="text-xl text-gray-600">
                    Perjalanan dan perkembangan laboratorium
                </p>
            </div>
            
            <div class="history-section">
                <p class="text-lg text-gray-700 leading-relaxed">
                    <?php echo nl2br(htmlspecialchars($profil['sejarah'])); ?>
                </p>
            </div>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- Contact Info Section -->
<section class="py-20 bg-white">
    <div class="container mx-auto px-4">
        <div class="max-w-6xl mx-auto">
            <div class="text-center mb-12" data-aos="fade-up">
                <h2 class="text-4xl font-bold text-gray-900 mb-4">
                    Hubungi Kami
                </h2>
                <p class="text-xl text-gray-600">
                    Informasi kontak laboratorium
                </p>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <!-- Address -->
                <?php if ($profil['alamat']): ?>
                <div class="contact-card" data-aos="fade-up" data-aos-delay="100">
                    <div class="contact-icon">
                        <i class="fas fa-map-marker-alt text-white text-2xl"></i>
                    </div>
                    <h3 class="font-bold text-gray-900 mb-2">Alamat</h3>
                    <p class="text-gray-600 text-sm">
                        <?php echo nl2br(htmlspecialchars($profil['alamat'])); ?>
                    </p>
                </div>
                <?php endif; ?>
                
                <!-- Email -->
                <?php if ($profil['email']): ?>
                <div class="contact-card" data-aos="fade-up" data-aos-delay="200">
                    <div class="contact-icon">
                        <i class="fas fa-envelope text-white text-2xl"></i>
                    </div>
                    <h3 class="font-bold text-gray-900 mb-2">Email</h3>
                    <a href="mailto:<?php echo htmlspecialchars($profil['email']); ?>" 
                       class="text-orange-600 hover:text-orange-700 text-sm break-all">
                        <?php echo htmlspecialchars($profil['email']); ?>
                    </a>
                </div>
                <?php endif; ?>
                
                <!-- Phone -->
                <?php if ($profil['telepon']): ?>
                <div class="contact-card" data-aos="fade-up" data-aos-delay="300">
                    <div class="contact-icon">
                        <i class="fas fa-phone text-white text-2xl"></i>
                    </div>
                    <h3 class="font-bold text-gray-900 mb-2">Telepon</h3>
                    <a href="tel:<?php echo htmlspecialchars($profil['telepon']); ?>" 
                       class="text-orange-600 hover:text-orange-700 text-sm">
                        <?php echo htmlspecialchars($profil['telepon']); ?>
                    </a>
                </div>
                <?php endif; ?>
                
                <!-- Website -->
                <?php if ($profil['website']): ?>
                <div class="contact-card" data-aos="fade-up" data-aos-delay="400">
                    <div class="contact-icon">
                        <i class="fas fa-globe text-white text-2xl"></i>
                    </div>
                    <h3 class="font-bold text-gray-900 mb-2">Website</h3>
                    <a href="<?php echo htmlspecialchars($profil['website']); ?>" 
                       target="_blank" rel="noopener"
                       class="text-orange-600 hover:text-orange-700 text-sm break-all">
                        Website Lab
                    </a>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<!-- Social Media Section -->
<?php if ($profil['facebook'] || $profil['twitter'] || $profil['instagram'] || $profil['linkedin']): ?>
<section class="py-16 bg-gray-50">
    <div class="container mx-auto px-4">
        <div class="max-w-4xl mx-auto text-center" data-aos="fade-up">
            <h3 class="text-2xl font-bold text-gray-900 mb-6">
                Ikuti Kami di Media Sosial
            </h3>
            <div class="flex justify-center gap-4 flex-wrap">
                <?php if ($profil['facebook']): ?>
                <a href="<?php echo htmlspecialchars($profil['facebook']); ?>" 
                   target="_blank" rel="noopener" class="social-btn" title="Facebook">
                    <i class="fab fa-facebook text-xl"></i>
                </a>
                <?php endif; ?>
                
                <?php if ($profil['twitter']): ?>
                <a href="<?php echo htmlspecialchars($profil['twitter']); ?>" 
                   target="_blank" rel="noopener" class="social-btn" title="Twitter">
                    <i class="fab fa-twitter text-xl"></i>
                </a>
                <?php endif; ?>
                
                <?php if ($profil['instagram']): ?>
                <a href="<?php echo htmlspecialchars($profil['instagram']); ?>" 
                   target="_blank" rel="noopener" class="social-btn" title="Instagram">
                    <i class="fab fa-instagram text-xl"></i>
                </a>
                <?php endif; ?>
                
                <?php if ($profil['linkedin']): ?>
                <a href="<?php echo htmlspecialchars($profil['linkedin']); ?>" 
                   target="_blank" rel="noopener" class="social-btn" title="LinkedIn">
                    <i class="fab fa-linkedin text-xl"></i>
                </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- CTA Section -->
<section class="py-16 bg-gradient-to-r from-orange-600 to-orange-700 text-white">
    <div class="container mx-auto px-4 text-center" data-aos="zoom-in">
        <h2 class="text-3xl md:text-4xl font-bold mb-4">
            Tertarik Berkolaborasi?
        </h2>
        <p class="text-xl mb-8 text-orange-100 max-w-2xl mx-auto">
            Kami terbuka untuk kerjasama penelitian, konsultasi, dan pengabdian masyarakat
        </p>
        <a href="<?php echo SITE_URL; ?>/konsultatif.php" 
           class="inline-block bg-white text-orange-600 px-8 py-4 rounded-xl font-semibold hover:bg-orange-50 transition-all text-lg">
            <i class="fas fa-paper-plane mr-2"></i>Hubungi Kami Sekarang
        </a>
    </div>
</section>

<?php
// Include public footer
require_once __DIR__ . '/../includes/footer.php';
?>
