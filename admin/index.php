<?php
/**
 * Admin Dashboard
 * File: admin/index.php
 */

// Set page title
$page_title = "Dashboard";

// Include admin header
require_once __DIR__ . '/../includes/admin_header.php';

// Get statistics from database
$stats = [
    'galeri' => 0,
    'pengelola' => 0,
    'arsip' => 0,
    'sarana' => 0,
    'konsultatif' => 0,
    'konsultatif_pending' => 0
];

// Query untuk statistik
$stats['galeri'] = countRows("SELECT COUNT(*) FROM galeri WHERE is_active = true");
$stats['pengelola'] = countRows("SELECT COUNT(*) FROM pengelola WHERE is_active = true");
$stats['arsip'] = countRows("SELECT COUNT(*) FROM arsip WHERE is_active = true");
$stats['sarana'] = countRows("SELECT COUNT(*) FROM sarana WHERE is_active = true");
$stats['konsultatif'] = countRows("SELECT COUNT(*) FROM konsultatif");
$stats['konsultatif_pending'] = countRows("SELECT COUNT(*) FROM konsultatif WHERE is_approved = false");

// Get recent activities (galeri terbaru)
$recent_galeri = executeQuery("SELECT * FROM galeri WHERE is_active = true ORDER BY created_at DESC LIMIT 5");

// Get recent arsip
$recent_arsip = executeQuery("SELECT * FROM arsip WHERE is_active = true ORDER BY created_at DESC LIMIT 5");

// Get agenda mendatang
$agenda_mendatang = executeQuery("SELECT * FROM galeri WHERE tipe = 'agenda' AND tanggal_kegiatan >= CURRENT_DATE AND is_active = true ORDER BY tanggal_kegiatan ASC LIMIT 5");
?>

<!-- Dashboard Content -->
<div class="space-y-6">
    
    <!-- Welcome Card -->
    <div class="bg-gradient-to-r from-blue-600 to-blue-800 rounded-lg shadow-lg p-6 text-white">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold mb-2">Selamat Datang, <?php echo htmlspecialchars($current_user['nama_lengkap']); ?>!</h2>
                <p class="text-blue-100">Kelola konten website laboratorium dari dashboard ini</p>
            </div>
            <div class="hidden md:block">
                <i class="fas fa-chart-line text-6xl opacity-30"></i>
            </div>
        </div>
    </div>
    
    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        
        <!-- Galeri & Agenda -->
        <div class="stat-card">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm font-semibold uppercase mb-1">Galeri & Agenda</p>
                    <p class="text-3xl font-bold text-gray-800"><?php echo $stats['galeri']; ?></p>
                </div>
                <div class="bg-blue-100 w-16 h-16 rounded-full flex items-center justify-center">
                    <i class="fas fa-images text-3xl text-blue-600"></i>
                </div>
            </div>
            <a href="galeri.php" class="text-blue-600 text-sm font-semibold mt-4 inline-block hover:text-blue-800">
                Kelola Galeri <i class="fas fa-arrow-right ml-1"></i>
            </a>
        </div>
        
        <!-- Pengelola -->
        <div class="stat-card">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm font-semibold uppercase mb-1">Pengelola</p>
                    <p class="text-3xl font-bold text-gray-800"><?php echo $stats['pengelola']; ?></p>
                </div>
                <div class="bg-green-100 w-16 h-16 rounded-full flex items-center justify-center">
                    <i class="fas fa-users text-3xl text-green-600"></i>
                </div>
            </div>
            <a href="pengelola.php" class="text-green-600 text-sm font-semibold mt-4 inline-block hover:text-green-800">
                Kelola Pengelola <i class="fas fa-arrow-right ml-1"></i>
            </a>
        </div>
        
        <!-- Arsip -->
        <div class="stat-card">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm font-semibold uppercase mb-1">Arsip Publikasi</p>
                    <p class="text-3xl font-bold text-gray-800"><?php echo $stats['arsip']; ?></p>
                </div>
                <div class="bg-red-100 w-16 h-16 rounded-full flex items-center justify-center">
                    <i class="fas fa-file-pdf text-3xl text-red-600"></i>
                </div>
            </div>
            <a href="arsip.php" class="text-red-600 text-sm font-semibold mt-4 inline-block hover:text-red-800">
                Kelola Arsip <i class="fas fa-arrow-right ml-1"></i>
            </a>
        </div>
        
        <!-- Sarana Prasarana -->
        <div class="stat-card">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm font-semibold uppercase mb-1">Sarana Prasarana</p>
                    <p class="text-3xl font-bold text-gray-800"><?php echo $stats['sarana']; ?></p>
                </div>
                <div class="bg-purple-100 w-16 h-16 rounded-full flex items-center justify-center">
                    <i class="fas fa-laptop text-3xl text-purple-600"></i>
                </div>
            </div>
            <a href="sarana.php" class="text-purple-600 text-sm font-semibold mt-4 inline-block hover:text-purple-800">
                Kelola Sarana <i class="fas fa-arrow-right ml-1"></i>
            </a>
        </div>
        
        <!-- Konsultatif -->
        <div class="stat-card">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm font-semibold uppercase mb-1">Konsultatif</p>
                    <p class="text-3xl font-bold text-gray-800"><?php echo $stats['konsultatif']; ?></p>
                    <?php if ($stats['konsultatif_pending'] > 0): ?>
                    <p class="text-xs text-orange-600 mt-1">
                        <i class="fas fa-clock mr-1"></i><?php echo $stats['konsultatif_pending']; ?> menunggu approval
                    </p>
                    <?php endif; ?>
                </div>
                <div class="bg-orange-100 w-16 h-16 rounded-full flex items-center justify-center">
                    <i class="fas fa-comments text-3xl text-orange-600"></i>
                </div>
            </div>
            <a href="konsultatif.php" class="text-orange-600 text-sm font-semibold mt-4 inline-block hover:text-orange-800">
                Kelola Konsultatif <i class="fas fa-arrow-right ml-1"></i>
            </a>
        </div>
        
        <!-- Profil Lab -->
        <div class="stat-card">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm font-semibold uppercase mb-1">Profil Lab</p>
                    <p class="text-xl font-bold text-gray-800">Konfigurasi</p>
                </div>
                <div class="bg-indigo-100 w-16 h-16 rounded-full flex items-center justify-center">
                    <i class="fas fa-building text-3xl text-indigo-600"></i>
                </div>
            </div>
            <a href="profil.php" class="text-indigo-600 text-sm font-semibold mt-4 inline-block hover:text-indigo-800">
                Edit Profil Lab <i class="fas fa-arrow-right ml-1"></i>
            </a>
        </div>
        
    </div>
    
    <!-- Two Column Layout -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        
        <!-- Recent Galeri -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-bold text-gray-800">
                    <i class="fas fa-images text-blue-600 mr-2"></i>Galeri Terbaru
                </h3>
                <a href="galeri.php" class="text-sm text-blue-600 hover:text-blue-800">Lihat Semua</a>
            </div>
            
            <?php if ($recent_galeri && count($recent_galeri) > 0): ?>
            <div class="space-y-3">
                <?php foreach ($recent_galeri as $item): ?>
                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition">
                    <div class="flex items-center space-x-3">
                        <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-image text-blue-600"></i>
                        </div>
                        <div>
                            <p class="font-semibold text-gray-800 text-sm"><?php echo truncateText($item['judul'], 40); ?></p>
                            <p class="text-xs text-gray-500">
                                <span class="<?php echo $item['tipe'] === 'agenda' ? 'text-green-600' : 'text-blue-600'; ?>">
                                    <?php echo ucfirst($item['tipe']); ?>
                                </span>
                                • <?php echo formatDateIndo($item['tanggal_kegiatan']); ?>
                            </p>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php else: ?>
            <p class="text-gray-500 text-center py-8">Belum ada galeri</p>
            <?php endif; ?>
        </div>
        
        <!-- Recent Arsip -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-bold text-gray-800">
                    <i class="fas fa-file-pdf text-red-600 mr-2"></i>Arsip Terbaru
                </h3>
                <a href="arsip.php" class="text-sm text-red-600 hover:text-red-800">Lihat Semua</a>
            </div>
            
            <?php if ($recent_arsip && count($recent_arsip) > 0): ?>
            <div class="space-y-3">
                <?php foreach ($recent_arsip as $item): ?>
                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition">
                    <div class="flex items-center space-x-3">
                        <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-file-pdf text-red-600"></i>
                        </div>
                        <div>
                            <p class="font-semibold text-gray-800 text-sm"><?php echo truncateText($item['judul'], 40); ?></p>
                            <p class="text-xs text-gray-500">
                                <span class="<?php echo $item['kategori'] === 'penelitian' ? 'text-blue-600' : 'text-green-600'; ?>">
                                    <?php echo ucfirst($item['kategori']); ?>
                                </span>
                                • <?php echo $item['tahun_publikasi']; ?>
                            </p>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php else: ?>
            <p class="text-gray-500 text-center py-8">Belum ada arsip</p>
            <?php endif; ?>
        </div>
        
    </div>
    
    <!-- Agenda Mendatang -->
    <?php if ($agenda_mendatang && count($agenda_mendatang) > 0): ?>
    <div class="bg-white rounded-lg shadow-md p-6">
        <h3 class="text-lg font-bold text-gray-800 mb-4">
            <i class="fas fa-calendar-alt text-green-600 mr-2"></i>Agenda Mendatang
        </h3>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            <?php foreach ($agenda_mendatang as $agenda): ?>
            <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition">
                <div class="flex items-start space-x-3">
                    <div class="bg-green-100 px-3 py-2 rounded-lg text-center flex-shrink-0">
                        <p class="text-2xl font-bold text-green-600"><?php echo date('d', strtotime($agenda['tanggal_kegiatan'])); ?></p>
                        <p class="text-xs text-green-600"><?php echo date('M', strtotime($agenda['tanggal_kegiatan'])); ?></p>
                    </div>
                    <div class="flex-1">
                        <p class="font-semibold text-gray-800 text-sm mb-1"><?php echo $agenda['judul']; ?></p>
                        <?php if ($agenda['lokasi']): ?>
                        <p class="text-xs text-gray-500">
                            <i class="fas fa-map-marker-alt mr-1"></i><?php echo $agenda['lokasi']; ?>
                        </p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>
    
</div>

<?php
// Include admin footer
require_once __DIR__ . '/../includes/admin_footer.php';
?>
