<?php
/**
 * Public Pengelola Page
 * File: public/pengelola.php
 * Design Reference: Active SaaS (Orange Accent)
 */

// Set page title
$page_title = "Tim Pengelola - Laboratorium NCS";

// Include public header
require_once __DIR__ . '/../includes/header.php';

// Get active pengelola ordered by urutan_tampil
$query = "SELECT * FROM pengelola WHERE is_active = true ORDER BY urutan_tampil ASC, nama_lengkap ASC";
$pengelola_list = executeQuery($query);

// Group by position hierarchy (Kepala Lab first)
$kepala_lab = [];
$teknisi = [];
$peneliti = [];
$lainnya = [];

if ($pengelola_list) {
    foreach ($pengelola_list as $p) {
        if (stripos($p['jabatan'], 'kepala') !== false) {
            $kepala_lab[] = $p;
        } elseif (stripos($p['jabatan'], 'teknisi') !== false) {
            $teknisi[] = $p;
        } elseif (stripos($p['jabatan'], 'peneliti') !== false) {
            $peneliti[] = $p;
        } else {
            $lainnya[] = $p;
        }
    }
}

$total_pengelola = count($pengelola_list);
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

.team-card {
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    border: 1px solid #E5E7EB;
    background: white;
}

.team-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
    border-color: var(--orange-500);
}

.team-card:hover .team-photo {
    transform: scale(1.05);
    box-shadow: 0 10px 15px -3px rgba(249, 115, 22, 0.3);
}

.team-photo {
    transition: all 0.3s ease;
    border: 4px solid white;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
}

.section-badge {
    background: linear-gradient(135deg, var(--orange-500) 0%, var(--orange-600) 100%);
    color: white;
    padding: 0.5rem 1.5rem;
    border-radius: 9999px;
    font-weight: 600;
    display: inline-block;
    box-shadow: 0 4px 6px -1px rgba(249, 115, 22, 0.3);
}

.contact-btn {
    background: linear-gradient(135deg, var(--orange-500) 0%, var(--orange-600) 100%);
    color: white;
    padding: 0.5rem 1rem;
    border-radius: 0.5rem;
    font-weight: 600;
    transition: all 0.3s ease;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
}

.contact-btn:hover {
    box-shadow: 0 10px 15px -3px rgba(249, 115, 22, 0.4);
    transform: translateY(-2px);
}

.badge-position {
    background: var(--orange-50);
    color: var(--orange-700);
    padding: 0.375rem 1rem;
    border-radius: 9999px;
    font-size: 0.875rem;
    font-weight: 600;
    display: inline-block;
}

.expertise-tag {
    background: #F3F4F6;
    color: #4B5563;
    padding: 0.25rem 0.75rem;
    border-radius: 0.375rem;
    font-size: 0.813rem;
}

/* Wave divider */
.wave-divider {
    position: absolute;
    bottom: 0;
    left: 0;
    width: 100%;
    overflow: hidden;
    line-height: 0;
}
</style>

<!-- Hero Section -->
<section class="hero-gradient text-white py-20 relative" data-aos="fade-down">
    <div class="container mx-auto px-4">
        <div class="max-w-4xl mx-auto text-center">
            <h1 class="text-5xl md:text-6xl font-bold mb-6" data-aos="fade-up" data-aos-delay="100">
                Tim Pengelola
            </h1>
            <p class="text-xl md:text-2xl text-orange-100 mb-8" data-aos="fade-up" data-aos-delay="200">
                Dedikasi dan keahlian untuk kemajuan Laboratorium Network & Cloud System
            </p>
            
            <!-- Stats -->
            <div class="inline-flex items-center bg-white/10 backdrop-blur-sm rounded-xl px-6 py-4" data-aos="fade-up" data-aos-delay="300">
                <div class="text-center">
                    <div class="text-4xl font-bold mb-1"><?php echo $total_pengelola; ?>+</div>
                    <div class="text-orange-100">Tim Profesional</div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Wave Divider -->
    <div class="wave-divider">
        <svg viewBox="0 0 1440 120" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M0 120L60 105C120 90 240 60 360 45C480 30 600 30 720 37.5C840 45 960 60 1080 67.5C1200 75 1320 75 1380 75L1440 75V120H1380C1320 120 1200 120 1080 120C960 120 840 120 720 120C600 120 480 120 360 120C240 120 120 120 60 120H0Z" fill="white"/>
        </svg>
    </div>
</section>

<!-- Main Content -->
<section class="py-16 bg-white">
    <div class="container mx-auto px-4">
        
        <?php if ($pengelola_list && count($pengelola_list) > 0): ?>
        
        <!-- Kepala Laboratorium -->
        <?php if (!empty($kepala_lab)): ?>
        <div class="mb-16" data-aos="fade-up">
            <div class="text-center mb-12">
                <span class="section-badge">Kepala Laboratorium</span>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 max-w-5xl mx-auto">
                <?php foreach ($kepala_lab as $member): ?>
                <div class="team-card rounded-xl overflow-hidden" data-aos="fade-up" data-aos-delay="100">
                    <!-- Photo -->
                    <div class="relative p-6 pb-0">
                        <img 
                            src="<?php echo SITE_URL . '/..' . htmlspecialchars($member['foto_path']); ?>" 
                            alt="<?php echo htmlspecialchars($member['nama_lengkap']); ?>"
                            class="team-photo w-40 h-40 mx-auto object-cover rounded-full"
                            onerror="this.src='<?php echo ASSETS_URL; ?>/img/default-avatar.png'"
                        >
                    </div>
                    
                    <!-- Content -->
                    <div class="p-6 text-center">
                        <!-- Name -->
                        <h3 class="text-xl font-bold text-gray-900 mb-2">
                            <?php echo htmlspecialchars($member['nama_lengkap']); ?>
                        </h3>
                        
                        <!-- Position -->
                        <span class="badge-position mb-3">
                            <?php echo htmlspecialchars($member['jabatan']); ?>
                        </span>
                        
                        <!-- Education -->
                        <?php if ($member['pendidikan_terakhir']): ?>
                        <p class="text-gray-600 text-sm mb-2 mt-3">
                            <i class="fas fa-graduation-cap text-orange-500 mr-1"></i>
                            <?php echo htmlspecialchars($member['pendidikan_terakhir']); ?>
                        </p>
                        <?php endif; ?>
                        
                        <!-- Expertise -->
                        <?php if ($member['bidang_keahlian']): ?>
                        <div class="mb-4">
                            <p class="text-gray-700 text-sm leading-relaxed">
                                <?php echo htmlspecialchars($member['bidang_keahlian']); ?>
                            </p>
                        </div>
                        <?php endif; ?>
                        
                        <!-- Contact -->
                        <div class="pt-4 border-t space-y-2">
                            <a href="mailto:<?php echo htmlspecialchars($member['email']); ?>" 
                               class="text-sm text-gray-600 hover:text-orange-600 flex items-center justify-center gap-2">
                                <i class="fas fa-envelope text-orange-500"></i>
                                <span class="truncate"><?php echo htmlspecialchars($member['email']); ?></span>
                            </a>
                            
                            <?php if ($member['no_telepon']): ?>
                            <a href="tel:<?php echo htmlspecialchars($member['no_telepon']); ?>" 
                               class="text-sm text-gray-600 hover:text-orange-600 flex items-center justify-center gap-2">
                                <i class="fas fa-phone text-orange-500"></i>
                                <span><?php echo htmlspecialchars($member['no_telepon']); ?></span>
                            </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>
        
        <!-- Teknisi -->
        <?php if (!empty($teknisi)): ?>
        <div class="mb-16" data-aos="fade-up">
            <div class="text-center mb-12">
                <span class="section-badge">Tim Teknisi</span>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <?php foreach ($teknisi as $index => $member): ?>
                <div class="team-card rounded-xl overflow-hidden" data-aos="fade-up" data-aos-delay="<?php echo ($index * 50); ?>">
                    <!-- Photo -->
                    <div class="relative p-6 pb-0">
                        <img 
                            src="<?php echo SITE_URL . '/..' . htmlspecialchars($member['foto_path']); ?>" 
                            alt="<?php echo htmlspecialchars($member['nama_lengkap']); ?>"
                            class="team-photo w-32 h-32 mx-auto object-cover rounded-full"
                            onerror="this.src='<?php echo ASSETS_URL; ?>/img/default-avatar.png'"
                        >
                    </div>
                    
                    <!-- Content -->
                    <div class="p-6 text-center">
                        <h3 class="text-lg font-bold text-gray-900 mb-2">
                            <?php echo htmlspecialchars($member['nama_lengkap']); ?>
                        </h3>
                        
                        <span class="badge-position mb-3">
                            <?php echo htmlspecialchars($member['jabatan']); ?>
                        </span>
                        
                        <?php if ($member['pendidikan_terakhir']): ?>
                        <p class="text-gray-600 text-sm mb-2 mt-3">
                            <i class="fas fa-graduation-cap text-orange-500 mr-1"></i>
                            <?php echo htmlspecialchars($member['pendidikan_terakhir']); ?>
                        </p>
                        <?php endif; ?>
                        
                        <?php if ($member['bidang_keahlian']): ?>
                        <div class="mb-4">
                            <p class="text-gray-700 text-sm leading-relaxed">
                                <?php echo htmlspecialchars($member['bidang_keahlian']); ?>
                            </p>
                        </div>
                        <?php endif; ?>
                        
                        <div class="pt-4 border-t space-y-2">
                            <a href="mailto:<?php echo htmlspecialchars($member['email']); ?>" 
                               class="text-sm text-gray-600 hover:text-orange-600 flex items-center justify-center gap-2">
                                <i class="fas fa-envelope text-orange-500"></i>
                                <span class="truncate"><?php echo htmlspecialchars($member['email']); ?></span>
                            </a>
                            
                            <?php if ($member['no_telepon']): ?>
                            <a href="tel:<?php echo htmlspecialchars($member['no_telepon']); ?>" 
                               class="text-sm text-gray-600 hover:text-orange-600 flex items-center justify-center gap-2">
                                <i class="fas fa-phone text-orange-500"></i>
                                <span><?php echo htmlspecialchars($member['no_telepon']); ?></span>
                            </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>
        
        <!-- Peneliti -->
        <?php if (!empty($peneliti)): ?>
        <div class="mb-16" data-aos="fade-up">
            <div class="text-center mb-12">
                <span class="section-badge">Tim Peneliti</span>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <?php foreach ($peneliti as $index => $member): ?>
                <div class="team-card rounded-xl overflow-hidden" data-aos="fade-up" data-aos-delay="<?php echo ($index * 50); ?>">
                    <div class="relative p-6 pb-0">
                        <img 
                            src="<?php echo SITE_URL . '/..' . htmlspecialchars($member['foto_path']); ?>" 
                            alt="<?php echo htmlspecialchars($member['nama_lengkap']); ?>"
                            class="team-photo w-32 h-32 mx-auto object-cover rounded-full"
                            onerror="this.src='<?php echo ASSETS_URL; ?>/img/default-avatar.png'"
                        >
                    </div>
                    
                    <div class="p-6 text-center">
                        <h3 class="text-lg font-bold text-gray-900 mb-2">
                            <?php echo htmlspecialchars($member['nama_lengkap']); ?>
                        </h3>
                        
                        <span class="badge-position mb-3">
                            <?php echo htmlspecialchars($member['jabatan']); ?>
                        </span>
                        
                        <?php if ($member['pendidikan_terakhir']): ?>
                        <p class="text-gray-600 text-sm mb-2 mt-3">
                            <i class="fas fa-graduation-cap text-orange-500 mr-1"></i>
                            <?php echo htmlspecialchars($member['pendidikan_terakhir']); ?>
                        </p>
                        <?php endif; ?>
                        
                        <?php if ($member['bidang_keahlian']): ?>
                        <div class="mb-4">
                            <p class="text-gray-700 text-sm leading-relaxed">
                                <?php echo htmlspecialchars($member['bidang_keahlian']); ?>
                            </p>
                        </div>
                        <?php endif; ?>
                        
                        <div class="pt-4 border-t space-y-2">
                            <a href="mailto:<?php echo htmlspecialchars($member['email']); ?>" 
                               class="text-sm text-gray-600 hover:text-orange-600 flex items-center justify-center gap-2">
                                <i class="fas fa-envelope text-orange-500"></i>
                                <span class="truncate"><?php echo htmlspecialchars($member['email']); ?></span>
                            </a>
                            
                            <?php if ($member['no_telepon']): ?>
                            <a href="tel:<?php echo htmlspecialchars($member['no_telepon']); ?>" 
                               class="text-sm text-gray-600 hover:text-orange-600 flex items-center justify-center gap-2">
                                <i class="fas fa-phone text-orange-500"></i>
                                <span><?php echo htmlspecialchars($member['no_telepon']); ?></span>
                            </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>
        
        <!-- Lainnya -->
        <?php if (!empty($lainnya)): ?>
        <div data-aos="fade-up">
            <div class="text-center mb-12">
                <span class="section-badge">Tim Pendukung</span>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <?php foreach ($lainnya as $index => $member): ?>
                <div class="team-card rounded-xl overflow-hidden" data-aos="fade-up" data-aos-delay="<?php echo ($index * 50); ?>">
                    <div class="relative p-6 pb-0">
                        <img 
                            src="<?php echo SITE_URL . '/..' . htmlspecialchars($member['foto_path']); ?>" 
                            alt="<?php echo htmlspecialchars($member['nama_lengkap']); ?>"
                            class="team-photo w-32 h-32 mx-auto object-cover rounded-full"
                            onerror="this.src='<?php echo ASSETS_URL; ?>/img/default-avatar.png'"
                        >
                    </div>
                    
                    <div class="p-6 text-center">
                        <h3 class="text-lg font-bold text-gray-900 mb-2">
                            <?php echo htmlspecialchars($member['nama_lengkap']); ?>
                        </h3>
                        
                        <span class="badge-position mb-3">
                            <?php echo htmlspecialchars($member['jabatan']); ?>
                        </span>
                        
                        <?php if ($member['pendidikan_terakhir']): ?>
                        <p class="text-gray-600 text-sm mb-2 mt-3">
                            <i class="fas fa-graduation-cap text-orange-500 mr-1"></i>
                            <?php echo htmlspecialchars($member['pendidikan_terakhir']); ?>
                        </p>
                        <?php endif; ?>
                        
                        <?php if ($member['bidang_keahlian']): ?>
                        <div class="mb-4">
                            <p class="text-gray-700 text-sm leading-relaxed">
                                <?php echo htmlspecialchars($member['bidang_keahlian']); ?>
                            </p>
                        </div>
                        <?php endif; ?>
                        
                        <div class="pt-4 border-t space-y-2">
                            <a href="mailto:<?php echo htmlspecialchars($member['email']); ?>" 
                               class="text-sm text-gray-600 hover:text-orange-600 flex items-center justify-center gap-2">
                                <i class="fas fa-envelope text-orange-500"></i>
                                <span class="truncate"><?php echo htmlspecialchars($member['email']); ?></span>
                            </a>
                            
                            <?php if ($member['no_telepon']): ?>
                            <a href="tel:<?php echo htmlspecialchars($member['no_telepon']); ?>" 
                               class="text-sm text-gray-600 hover:text-orange-600 flex items-center justify-center gap-2">
                                <i class="fas fa-phone text-orange-500"></i>
                                <span><?php echo htmlspecialchars($member['no_telepon']); ?></span>
                            </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>
        
        <?php else: ?>
        
        <!-- Empty State -->
        <div class="text-center py-16" data-aos="fade-up">
            <i class="fas fa-users text-gray-300 text-8xl mb-6"></i>
            <h3 class="text-2xl font-bold text-gray-700 mb-3">
                Data Tim Belum Tersedia
            </h3>
            <p class="text-gray-500 text-lg">
                Informasi tim pengelola akan segera ditambahkan.
            </p>
        </div>
        
        <?php endif; ?>
        
    </div>
</section>

<!-- CTA Section -->
<section class="py-16 bg-gradient-to-r from-orange-500 to-orange-600 text-white" data-aos="fade-up">
    <div class="container mx-auto px-4 text-center">
        <h2 class="text-3xl md:text-4xl font-bold mb-4">Ingin Bergabung dengan Tim Kami?</h2>
        <p class="text-xl text-orange-100 mb-8 max-w-2xl mx-auto">
            Kami selalu mencari talenta terbaik untuk bergabung dengan tim Laboratorium NCS
        </p>
        <a href="<?php echo SITE_URL; ?>/index.php#kontak" 
           class="inline-block bg-white text-orange-600 font-semibold px-8 py-4 rounded-lg hover:bg-gray-100 transition-all shadow-lg hover:shadow-xl">
            <i class="fas fa-paper-plane mr-2"></i>Hubungi Kami
        </a>
    </div>
</section>

<?php
// Include public footer
require_once __DIR__ . '/../includes/footer.php';
?>
