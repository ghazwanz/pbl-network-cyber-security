<?php
/**
 * Public Gallery Page
 * File: public/galeri.php
 * Design Reference: Active SaaS (Orange Accent)
 */

// Set page title
$page_title = "Galeri & Agenda - Laboratorium NCS";

// Include public header
require_once __DIR__ . '/../includes/header.php';

// Get filter
$filter = $_GET['filter'] ?? 'kegiatan';
if (!in_array($filter, ['kegiatan', 'agenda'])) {
    $filter = 'kegiatan';
}

// Get data based on filter
$query = "SELECT * FROM galeri WHERE tipe = ? AND is_active = true ORDER BY tanggal_kegiatan DESC, created_at DESC LIMIT 50";
$galeri_items = executeQuery($query, [$filter]);

// Get counts
$count_kegiatan = countRows("SELECT COUNT(*) FROM galeri WHERE tipe = 'kegiatan' AND is_active = true");
$count_agenda = countRows("SELECT COUNT(*) FROM galeri WHERE tipe = 'agenda' AND is_active = true");
?>

<style>
/* Active SaaS Inspired Color Palette */
:root {
    --orange-50: #FFF7ED;
    --orange-100: #FFEDD5;
    --orange-400: #FB923C;
    --orange-500: #F97316;
    --orange-600: #EA580C;
    --orange-700: #C2410C;
    --gray-50: #F9FAFB;
    --gray-100: #F3F4F6;
    --gray-700: #374151;
    --gray-800: #1F2937;
    --gray-900: #111827;
}

.hero-gradient {
    background: linear-gradient(135deg, var(--orange-600) 0%, var(--orange-700) 100%);
}

.card-galeri {
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    border: 1px solid #E5E7EB;
}

.card-galeri:hover {
    transform: translateY(-8px);
    box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
    border-color: var(--orange-500);
}

.card-galeri img {
    transition: transform 0.3s ease;
}

.card-galeri:hover img {
    transform: scale(1.05);
}

.filter-btn {
    position: relative;
    padding: 0.75rem 1.5rem;
    font-weight: 600;
    border-radius: 9999px;
    transition: all 0.3s ease;
    border: 2px solid transparent;
}

.filter-btn:not(.active) {
    background: white;
    color: var(--gray-700);
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

.badge-orange {
    background: var(--orange-50);
    color: var(--orange-700);
    padding: 0.25rem 0.75rem;
    border-radius: 9999px;
    font-size: 0.875rem;
    font-weight: 600;
}

.date-badge {
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(8px);
    padding: 0.5rem 1rem;
    border-radius: 0.5rem;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
}

.empty-state {
    background: var(--gray-50);
    border: 2px dashed #E5E7EB;
    border-radius: 1rem;
    padding: 4rem 2rem;
}

/* Floating Animation */
@keyframes float {
    0%, 100% { transform: translateY(0px); }
    50% { transform: translateY(-10px); }
}

.float-animation {
    animation: float 3s ease-in-out infinite;
}
</style>

<!-- Hero Section -->
<section class="hero-gradient text-white py-20" data-aos="fade-down">
    <div class="container mx-auto px-4">
        <div class="max-w-4xl mx-auto text-center">
            <h1 class="text-5xl md:text-6xl font-bold mb-6" data-aos="fade-up" data-aos-delay="100">
                Galeri & Agenda
            </h1>
            <p class="text-xl md:text-2xl text-orange-100 mb-8" data-aos="fade-up" data-aos-delay="200">
                Dokumentasi kegiatan dan agenda mendatang Laboratorium Network & Cloud System
            </p>
            
            <!-- Stats -->
            <div class="grid grid-cols-2 gap-6 max-w-md mx-auto" data-aos="fade-up" data-aos-delay="300">
                <div class="bg-white/10 backdrop-blur-sm rounded-xl p-4">
                    <div class="text-3xl font-bold mb-1"><?php echo $count_kegiatan; ?>+</div>
                    <div class="text-orange-100">Kegiatan</div>
                </div>
                <div class="bg-white/10 backdrop-blur-sm rounded-xl p-4">
                    <div class="text-3xl font-bold mb-1"><?php echo $count_agenda; ?></div>
                    <div class="text-orange-100">Agenda</div>
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

<!-- Filter Section -->
<section class="py-12 bg-white">
    <div class="container mx-auto px-4">
        <div class="flex justify-center items-center gap-4 flex-wrap" data-aos="fade-up">
            <a href="?filter=kegiatan" class="filter-btn <?php echo $filter === 'kegiatan' ? 'active' : ''; ?>">
                <i class="fas fa-images mr-2"></i>
                Kegiatan
                <span class="ml-2 inline-flex items-center justify-center w-6 h-6 text-xs rounded-full <?php echo $filter === 'kegiatan' ? 'bg-white/20' : 'bg-gray-200'; ?>">
                    <?php echo $count_kegiatan; ?>
                </span>
            </a>
            
            <a href="?filter=agenda" class="filter-btn <?php echo $filter === 'agenda' ? 'active' : ''; ?>">
                <i class="fas fa-calendar-alt mr-2"></i>
                Agenda
                <span class="ml-2 inline-flex items-center justify-center w-6 h-6 text-xs rounded-full <?php echo $filter === 'agenda' ? 'bg-white/20' : 'bg-gray-200'; ?>">
                    <?php echo $count_agenda; ?>
                </span>
            </a>
        </div>
    </div>
</section>

<!-- Gallery Grid -->
<section class="py-16 bg-gray-50">
    <div class="container mx-auto px-4">
        
        <?php if ($galeri_items && count($galeri_items) > 0): ?>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            <?php foreach ($galeri_items as $index => $item): ?>
            <div class="card-galeri bg-white rounded-xl overflow-hidden" data-aos="fade-up" data-aos-delay="<?php echo $index * 50; ?>">
                <!-- Image -->
                <div class="relative overflow-hidden h-64">
                    <img 
                        src="<?php echo htmlspecialchars($item['gambar_path']); ?>" 
                        alt="<?php echo htmlspecialchars($item['judul']); ?>"
                        class="w-full h-full object-cover"
                        loading="lazy"
                        onerror="this.src='<?php echo ASSETS_URL; ?>/img/no-image.png'"
                    >
                    
                    <!-- Date Badge -->
                    <div class="absolute top-4 left-4 date-badge">
                        <div class="flex items-center gap-2">
                            <i class="fas fa-calendar text-orange-600"></i>
                            <span class="font-semibold text-gray-800">
                                <?php 
                                $date = new DateTime($item['tanggal_kegiatan']);
                                echo $date->format('d M Y'); 
                                ?>
                            </span>
                        </div>
                    </div>
                    
                    <?php if ($item['is_featured']): ?>
                    <div class="absolute top-4 right-4 bg-yellow-400 text-yellow-900 px-3 py-1 rounded-full text-sm font-bold shadow-lg">
                        <i class="fas fa-star mr-1"></i>Featured
                    </div>
                    <?php endif; ?>
                </div>
                
                <!-- Content -->
                <div class="p-6">
                    <!-- Type Badge -->
                    <span class="badge-orange inline-block mb-3">
                        <i class="fas <?php echo $item['tipe'] === 'agenda' ? 'fa-calendar-check' : 'fa-camera'; ?> mr-1"></i>
                        <?php echo ucfirst($item['tipe']); ?>
                    </span>
                    
                    <!-- Title -->
                    <h3 class="text-xl font-bold text-gray-900 mb-2 hover:text-orange-600 transition-colors">
                        <?php echo htmlspecialchars($item['judul']); ?>
                    </h3>
                    
                    <!-- Description -->
                    <?php if ($item['deskripsi']): ?>
                    <p class="text-gray-600 mb-4 line-clamp-3">
                        <?php echo htmlspecialchars($item['deskripsi']); ?>
                    </p>
                    <?php endif; ?>
                    
                    <!-- Location -->
                    <?php if ($item['lokasi']): ?>
                    <div class="flex items-center text-gray-500 text-sm">
                        <i class="fas fa-map-marker-alt text-orange-500 mr-2"></i>
                        <?php echo htmlspecialchars($item['lokasi']); ?>
                    </div>
                    <?php endif; ?>
                </div>
                
                <!-- Footer -->
                <div class="px-6 pb-6">
                    <button onclick="openModal(<?php echo htmlspecialchars(json_encode($item)); ?>)" 
                            class="w-full bg-gradient-to-r from-orange-500 to-orange-600 text-white font-semibold py-3 rounded-lg hover:from-orange-600 hover:to-orange-700 transition-all shadow-md hover:shadow-lg">
                        <i class="fas fa-expand-alt mr-2"></i>Lihat Detail
                    </button>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        
        <?php else: ?>
        
        <!-- Empty State -->
        <div class="empty-state text-center max-w-lg mx-auto" data-aos="fade-up">
            <div class="float-animation inline-block mb-6">
                <i class="fas <?php echo $filter === 'agenda' ? 'fa-calendar-times' : 'fa-image'; ?> text-gray-300 text-8xl"></i>
            </div>
            <h3 class="text-2xl font-bold text-gray-700 mb-3">
                Belum Ada <?php echo ucfirst($filter); ?>
            </h3>
            <p class="text-gray-500 text-lg mb-6">
                <?php if ($filter === 'agenda'): ?>
                Saat ini tidak ada agenda yang dijadwalkan. Pantau terus halaman ini untuk update terbaru!
                <?php else: ?>
                Belum ada dokumentasi kegiatan yang dipublikasikan. Nantikan dokumentasi kegiatan menarik kami!
                <?php endif; ?>
            </p>
            <a href="?filter=<?php echo $filter === 'agenda' ? 'kegiatan' : 'agenda'; ?>" 
               class="inline-block bg-gradient-to-r from-orange-500 to-orange-600 text-white font-semibold px-6 py-3 rounded-lg hover:from-orange-600 hover:to-orange-700 transition-all shadow-md hover:shadow-lg">
                <i class="fas fa-arrow-right mr-2"></i>
                Lihat <?php echo $filter === 'agenda' ? 'Kegiatan' : 'Agenda'; ?>
            </a>
        </div>
        
        <?php endif; ?>
        
    </div>
</section>

<!-- Modal -->
<div id="imageModal" class="fixed inset-0 bg-black/80 backdrop-blur-sm z-50 hidden items-center justify-center p-4" onclick="closeModal(event)">
    <div class="bg-white rounded-2xl max-w-4xl w-full max-h-[90vh] overflow-y-auto relative" onclick="event.stopPropagation()">
        <!-- Close Button -->
        <button onclick="closeModal()" class="absolute top-4 right-4 z-10 bg-white/90 hover:bg-white text-gray-800 w-10 h-10 rounded-full shadow-lg transition-all">
            <i class="fas fa-times"></i>
        </button>
        
        <!-- Modal Content (will be filled by JavaScript) -->
        <div id="modalContent"></div>
    </div>
</div>

<script>
function openModal(item) {
    const modal = document.getElementById('imageModal');
    const modalContent = document.getElementById('modalContent');
    
    const date = new Date(item.tanggal_kegiatan);
    const formattedDate = date.toLocaleDateString('id-ID', { 
        weekday: 'long', 
        year: 'numeric', 
        month: 'long', 
        day: 'numeric' 
    });
    
    modalContent.innerHTML = `
        <!-- Image -->
        <div class="relative">
            <img 
                src="../${item.gambar_path}" 
                alt="${item.judul}"
                class="w-full h-96 object-cover rounded-t-2xl"
            >
            ${item.is_featured ? '<div class="absolute top-4 right-4 bg-yellow-400 text-yellow-900 px-4 py-2 rounded-full text-sm font-bold shadow-lg"><i class="fas fa-star mr-2"></i>Featured</div>' : ''}
        </div>
        
        <!-- Content -->
        <div class="p-8">
            <!-- Badge -->
            <span class="inline-block bg-orange-50 text-orange-700 px-4 py-2 rounded-full text-sm font-semibold mb-4">
                <i class="fas ${item.tipe === 'agenda' ? 'fa-calendar-check' : 'fa-camera'} mr-2"></i>
                ${item.tipe.charAt(0).toUpperCase() + item.tipe.slice(1)}
            </span>
            
            <!-- Title -->
            <h2 class="text-3xl font-bold text-gray-900 mb-4">${item.judul}</h2>
            
            <!-- Meta Info -->
            <div class="flex flex-wrap gap-4 mb-6 text-gray-600">
                <div class="flex items-center">
                    <i class="fas fa-calendar text-orange-500 mr-2"></i>
                    <span>${formattedDate}</span>
                </div>
                ${item.lokasi ? `
                <div class="flex items-center">
                    <i class="fas fa-map-marker-alt text-orange-500 mr-2"></i>
                    <span>${item.lokasi}</span>
                </div>
                ` : ''}
            </div>
            
            <!-- Description -->
            ${item.deskripsi ? `
            <div class="prose max-w-none">
                <p class="text-gray-700 text-lg leading-relaxed">${item.deskripsi}</p>
            </div>
            ` : ''}
        </div>
    `;
    
    modal.classList.remove('hidden');
    modal.classList.add('flex');
    document.body.style.overflow = 'hidden';
}

function closeModal(event) {
    if (event && event.target !== event.currentTarget) return;
    
    const modal = document.getElementById('imageModal');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
    document.body.style.overflow = 'auto';
}

// Close modal on Escape key
document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        closeModal();
    }
});
</script>

<?php
// Include public footer
require_once __DIR__ . '/../includes/footer.php';
?>
