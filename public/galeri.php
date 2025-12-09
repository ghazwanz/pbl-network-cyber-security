<?php
/**
 * Public Galeri Page
 * File: public/galeri.php
 * Design Reference: Modern & Professional with Navy Blue Theme (Consistent with Arsip)
 */

// Set page title
$page_title = "Galeri Kegiatan - Laboratorium NCS";

// Include public header
require_once __DIR__ . '/../includes/header.php';

// Get filter
$filter_kategori = $_GET['filter'] ?? '';
$search = $_GET['search'] ?? '';

// Build query
$where = ["is_active = true"];
$params = [];

if ($filter_kategori && in_array($filter_kategori, ['agenda', 'kegiatan'])) {
    $where[] = "LOWER(tipe) = ?";
    $params[] = strtolower($filter_kategori);
}

if ($search) {
    $where[] = "(judul ILIKE ? OR deskripsi ILIKE ? OR lokasi ILIKE ?)";
    $search_param = '%' . $search . '%';
    $params[] = $search_param;
    $params[] = $search_param;
    $params[] = $search_param;
}

$where_clause = implode(' AND ', $where);

// Pagination settings
$records_per_page = 9; // 3x3 grid
$current_page = isset($_GET['page']) ? max(1, (int) $_GET['page']) : 1;
$offset = ($current_page - 1) * $records_per_page;

// Count total records for pagination
$count_query = "SELECT COUNT(*) FROM galeri WHERE " . $where_clause;
$total_kegiatan = countRows($count_query, $params);
$total_pages = max(1, ceil($total_kegiatan / $records_per_page));

// Adjust current page if exceeds total
if ($current_page > $total_pages)
    $current_page = $total_pages;

// Get data with pagination
$query = "SELECT * FROM galeri WHERE " . $where_clause . " ORDER BY tanggal_kegiatan DESC, created_at DESC LIMIT $records_per_page OFFSET $offset";
$kegiatan_halaman = executeQuery($query, $params);

// Get counts for filter
$count_agenda = countRows("SELECT COUNT(*) FROM galeri WHERE LOWER(tipe) = 'agenda' AND is_active = true");
$count_kegiatan = countRows("SELECT COUNT(*) FROM galeri WHERE LOWER(tipe) = 'kegiatan' AND is_active = true");
$total_galeri = countRows("SELECT COUNT(*) FROM galeri WHERE is_active = true");
?>

<!-- Hero Section -->
<section class="relative lg:py-44 py-32 px-4 overflow-hidden">
    <div class="absolute inset-0 opacity-[0.5]"
        style="background-image: url('data:image/svg+xml,%3Csvg width=%2260%22 height=%2260%22 viewBox=%220 0 60 60%22 xmlns=%22http://www.w3.org/2000/svg%22%3E%3Cg fill=%22none%22 fill-rule=%22evenodd%22%3E%3Cg fill=%22%23000%22 fill-opacity=%221%22%3E%3Cpath d=%22M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z%22/%3E%3C/g%3E%3C/g%3E%3C/svg%3E');">
    </div>

    <!-- Gradient Mesh Background - Inspired by Prismo style -->
    <div class="absolute inset-0 overflow-hidden pointer-events-none">
        <!-- Base gradient layer -->
        <div class="absolute inset-0 bg-gradient-to-b from-orange-50/80 via-white to-blue-50/60"></div>

        <!-- Left gradient blob - Orange/Peach -->
        <div
            class="absolute -left-20 top-0 w-[60%] h-full bg-gradient-to-br from-orange-100/70 via-orange-50/50 to-transparent blur-3xl">
        </div>

        <!-- Right gradient blob - Blue/Purple -->
        <div
            class="absolute -right-20 top-0 w-[60%] h-full bg-gradient-to-bl from-blue-100/60 via-indigo-50/40 to-transparent blur-3xl">
        </div>

        <!-- Center fade overlay -->
        <div class="absolute inset-0 bg-gradient-to-b from-transparent via-white/30 to-white/60"></div>

        <!-- Subtle noise texture overlay for depth -->
        <div class="absolute inset-0 opacity-[0.015]"
            style="background-image: url('data:image/svg+xml,%3Csvg viewBox=%220 0 256 256%22 xmlns=%22http://www.w3.org/2000/svg%22%3E%3Cfilter id=%22noise%22%3E%3CfeTurbulence type=%22fractalNoise%22 baseFrequency=%220.9%22 numOctaves=%224%22 stitchTiles=%22stitch%22/%3E%3C/filter%3E%3Crect width=%22100%25%22 height=%22100%25%22 filter=%22url(%23noise)%22/%3E%3C/svg%3E');">
        </div>
    </div>

    <!-- Floating Widget Icons Container -->
    <div class="absolute inset-0 max-w-7xl mx-auto">
        <!-- Floating Widget Icons - Left Side -->
        <div class="absolute left-4 md:left-10 lg:left-20 top-24 md:top-32 w-10 h-10 md:w-14 md:h-14 bg-white rounded-2xl shadow-xl flex items-center justify-center animate-bounce opacity-80"
            style="animation-duration: 3s;">
            <i class="fas fa-camera text-orange-500 text-lg md:text-xl"></i>
        </div>

        <div class="absolute left-8 md:left-24 lg:left-40 top-48 md:top-56 w-12 h-12 md:w-16 md:h-16 bg-gradient-to-br from-blue-500 to-blue-600 rounded-2xl shadow-xl flex items-center justify-center animate-bounce opacity-90"
            style="animation-duration: 4s;">
            <i class="fas fa-image text-white text-xl md:text-2xl"></i>
        </div>

        <div class="absolute left-2 md:left-16 lg:left-32 bottom-32 md:bottom-40 w-9 h-9 md:w-12 md:h-12 bg-white rounded-xl shadow-lg flex items-center justify-center animate-bounce opacity-70"
            style="animation-duration: 3.5s;">
            <i class="fas fa-video text-[#1B2D62] text-sm md:text-lg"></i>
        </div>

        <div class="hidden md:flex absolute left-6 lg:left-12 bottom-56 lg:bottom-64 w-10 h-10 lg:w-14 lg:h-14 bg-gradient-to-br from-orange-400 to-orange-500 rounded-xl shadow-xl items-center justify-center animate-bounce opacity-80"
            style="animation-duration: 4.5s;">
            <i class="fas fa-calendar-check text-white text-lg lg:text-xl"></i>
        </div>

        <!-- Floating Widget Icons - Right Side -->
        <div class="absolute right-4 md:right-10 lg:right-20 top-28 md:top-36 w-11 h-11 md:w-14 md:h-14 bg-gradient-to-br from-green-500 to-green-600 rounded-2xl shadow-xl flex items-center justify-center animate-bounce opacity-85"
            style="animation-duration: 3.5s;">
            <i class="fas fa-photo-video text-white text-lg md:text-xl"></i>
        </div>

        <div class="absolute right-8 md:right-20 lg:right-36 top-52 md:top-60 w-10 h-10 md:w-12 md:h-12 bg-white rounded-xl shadow-lg flex items-center justify-center animate-bounce opacity-75"
            style="animation-duration: 4s;">
            <i class="fas fa-users text-blue-500 text-base md:text-lg"></i>
        </div>

        <div class="absolute right-3 md:right-14 lg:right-28 bottom-36 md:bottom-44 w-12 h-12 md:w-16 md:h-16 bg-gradient-to-br from-purple-500 to-purple-600 rounded-2xl shadow-xl flex items-center justify-center animate-bounce opacity-80"
            style="animation-duration: 3s;">
            <i class="fas fa-award text-white text-xl md:text-2xl"></i>
        </div>

        <div class="hidden md:flex absolute right-6 lg:right-10 bottom-60 lg:bottom-72 w-9 h-9 lg:w-11 lg:h-11 bg-white rounded-lg shadow-lg items-center justify-center animate-bounce opacity-70"
            style="animation-duration: 4.2s;">
            <i class="fas fa-shield-alt text-orange-500 text-sm lg:text-base"></i>
        </div>
    </div>

    <div class="mx-auto px-4 relative z-[5]">
        <div class="max-w-5xl mx-auto text-center">
            <!-- Badge -->
            <div class="inline-flex items-center gap-2 px-5 py-2.5 bg-white border-2 border-orange-200 rounded-full text-orange-600 font-medium mb-6 shadow-lg"
                data-aos="fade-up">
                <i class="fas fa-images text-orange-500"></i>
                <span class="text-sm tracking-wide">INOVASI TEKNOLOGI KEKINIAN</span>
            </div>

            <!-- Heading -->
            <h1 class="text-5xl md:text-6xl lg:text-7xl font-medium text-[#1B2D62] leading-[1.1] tracking-tight mb-8"
                data-aos="fade-up" data-aos-delay="100">
                Galeri <br class="hidden sm:block">
                <span
                    class="bg-gradient-to-r from-[#1B2D62] via-[#2C4AA4] to-orange-500 bg-clip-text text-transparent">Kegiatan</span>
            </h1>

            <!-- Subtitle -->
            <p class="text-xl md:text-2xl text-gray-600 leading-relaxed mb-10" data-aos="fade-up" data-aos-delay="200">
                Dokumentasi kegiatan dan aktivitas<br class="hidden md:block">
                Laboratorium Network & Cyber Security
            </p>

            <!-- Stats Bar -->
            <div class="flex flex-wrap justify-center gap-6 md:gap-10" data-aos="fade-up" data-aos-delay="300">
                <div class="flex items-center gap-3 bg-white px-6 py-4 rounded-xl shadow-lg border border-gray-100">
                    <div
                        class="w-12 h-12 bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg flex items-center justify-center">
                        <i class="fas fa-calendar-alt text-white text-xl"></i>
                    </div>
                    <div class="text-left">
                        <p class="text-sm text-gray-600 font-semibold">Agenda</p>
                        <p class="text-2xl font-medium text-[#1B2D62]"><?php echo number_format($count_agenda); ?></p>
                    </div>
                </div>

                <div class="flex items-center gap-3 bg-white px-6 py-4 rounded-xl shadow-lg border border-gray-100">
                    <div
                        class="w-12 h-12 bg-gradient-to-br from-orange-500 to-orange-600 rounded-lg flex items-center justify-center">
                        <i class="fas fa-camera text-white text-xl"></i>
                    </div>
                    <div class="text-left">
                        <p class="text-sm text-gray-600 font-semibold">Kegiatan</p>
                        <p class="text-2xl font-medium text-[#1B2D62]"><?php echo number_format($count_kegiatan); ?></p>
                    </div>
                </div>

                <div class="flex items-center gap-3 bg-white px-6 py-4 rounded-xl shadow-lg border border-gray-100">
                    <div
                        class="w-12 h-12 bg-gradient-to-br from-green-500 to-green-600 rounded-lg flex items-center justify-center">
                        <i class="fas fa-photo-video text-white text-xl"></i>
                    </div>
                    <div class="text-left">
                        <p class="text-sm text-gray-600 font-semibold">Total Galeri</p>
                        <p class="text-2xl font-medium text-[#1B2D62]"><?php echo number_format($total_galeri); ?></p>
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
            <h2 class="text-2xl md:text-3xl font-semibold text-[#1B2D62] mb-2">Cari Kegiatan</h2>
            <p class="text-gray-600">Filter berdasarkan kategori atau gunakan pencarian</p>
        </div>

        <!-- Search Bar -->
        <div class="max-w-3xl mx-auto mb-12" data-aos="fade-up" data-aos-delay="100">
            <form method="GET" action="">
                <div class="relative">
                    <i class="fas fa-search absolute left-5 top-1/2 -translate-y-1/2 text-gray-400 text-lg"></i>
                    <input type="text" name="search" id="search-input" value="<?php echo htmlspecialchars($search); ?>"
                        placeholder="Cari berdasarkan judul atau deskripsi..."
                        class="w-full pl-14 pr-32 py-4 text-base border-2 border-gray-300 rounded-xl focus:border-orange-500 focus:outline-none focus:ring-4 focus:ring-orange-100 transition-all">
                    <?php if ($filter_kategori): ?>
                        <!-- Hidden input untuk preserve filter -->
                        <input type="hidden" name="filter" value="<?php echo htmlspecialchars($filter_kategori); ?>">
                    <?php endif; ?>
                    <button type="submit"
                        class="absolute right-2 top-1/2 -translate-y-1/2 px-6 py-2.5 bg-gradient-to-r from-orange-500 to-orange-600 text-white font-semibold rounded-lg hover:shadow-lg transition-all duration-300">
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
                    <span
                        class="inline-flex items-center gap-2 px-4 py-2 bg-orange-50 border border-orange-200 rounded-lg text-orange-700 text-sm font-semibold">
                        <i class="fas fa-search"></i>
                        "<?php echo htmlspecialchars($search); ?>"
                        <a href="?<?php echo $filter_kategori ? 'filter=' . urlencode($filter_kategori) : ''; ?>"
                            class="ml-1 hover:text-orange-900">
                            <i class="fas fa-times"></i>
                        </a>
                    </span>
                <?php endif; ?>

                <?php if ($filter_kategori): ?>
                    <span
                        class="inline-flex items-center gap-2 px-4 py-2 bg-blue-50 border border-blue-200 rounded-lg text-blue-700 text-sm font-semibold">
                        <i class="fas fa-filter"></i>
                        <?php echo ucfirst($filter_kategori); ?>
                        <a href="?<?php echo $search ? 'search=' . urlencode($search) : ''; ?>"
                            class="ml-1 hover:text-blue-900">
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

<!-- Gallery List Section -->
<section class="py-16 pb-32 bg-[#F8FCFF]">
    <div class="container mx-auto px-4">

        <?php if ($kegiatan_halaman && count($kegiatan_halaman) > 0): ?>

            <!-- Results Info -->
            <div class="max-w-7xl mx-auto mb-8" data-aos="fade-up">
                <p class="text-gray-600 font-semibold">
                    Menampilkan <span class="text-[#1B2D62] font-medium"><?php echo count($kegiatan_halaman); ?></span> dari
                    <span class="text-[#1B2D62] font-medium"><?php echo number_format($total_kegiatan); ?></span> kegiatan
                    <?php if ($search || $filter_kategori): ?>
                        <span class="text-orange-600">
                            (<?php echo $filter_kategori ? ucfirst($filter_kategori) : 'Semua'; ?>)
                        </span>
                    <?php endif; ?>
                </p>
            </div>

            <!-- Card Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 max-w-7xl mx-auto">
                <?php foreach ($kegiatan_halaman as $index => $item): ?>

                    <?php
                    // Prepare item data for modal
                    $gambar_url = !empty($item['gambar_path']) && file_exists("../uploads" . $item['gambar_path'])
                        ? UPLOAD_URL . $item['gambar_path']
                        : ASSETS_URL . '/img/no-image.png';

                    $item_data = [
                        'judul' => htmlspecialchars($item['judul']),
                        'deskripsi' => htmlspecialchars($item['deskripsi'] ?: 'Dokumentasi kegiatan Laboratorium Network & Cyber Security.'),
                        'tipe' => htmlspecialchars($item['tipe']),
                        'lokasi' => htmlspecialchars($item['lokasi'] ?? ''),
                        'tanggal' => !empty($item['tanggal_kegiatan']) ? date('d F Y', strtotime($item['tanggal_kegiatan'])) : '-',
                        'gambar' => $gambar_url,
                        'is_featured' => !empty($item['is_featured'])
                    ];
                    ?>
                    <div class="group bg-white border-2 border-gray-200 rounded-2xl overflow-hidden hover:border-orange-500 hover:shadow-2xl transition-all duration-300 hover:-translate-y-2 cursor-pointer"
                        data-aos="fade-up" data-aos-delay="<?php echo ($index * 50); ?>"
                        onclick='showGaleriDetail(<?php echo json_encode($item_data); ?>)' data-toggle="modal"
                        data-target="#modalDetailGaleri">

                        <!-- Card Image -->
                        <div class="relative h-48 bg-gradient-to-br from-gray-100 to-gray-200 overflow-hidden">
                            <img src="<?php echo $gambar_url; ?>" alt="<?php echo htmlspecialchars($item['judul']); ?>"
                                class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">

                            <!-- Overlay on Hover -->
                            <div
                                class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                            </div>

                            <!-- View Button on Hover -->
                            <div
                                class="absolute inset-0 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                                <span
                                    class="w-14 h-14 bg-white/90 rounded-full flex items-center justify-center text-orange-600 group-hover:bg-orange-500 group-hover:text-white transition-all duration-300 shadow-xl">
                                    <i class="fas fa-eye text-xl"></i>
                                </span>
                            </div>

                            <!-- Category Badge -->
                            <span class="absolute top-4 left-4 px-3 py-1.5 text-xs font-bold rounded-lg uppercase tracking-wide shadow-lg
                        <?php echo strtolower($item['tipe']) === 'agenda'
                            ? 'bg-blue-500 text-white'
                            : 'bg-orange-500 text-white'; ?>">
                                <?php echo htmlspecialchars($item['tipe']); ?>
                            </span>

                            <!-- Featured Badge -->
                            <?php if (!empty($item['is_featured'])): ?>
                                <span
                                    class="absolute top-4 right-4 px-3 py-1.5 text-xs font-bold rounded-lg uppercase tracking-wide shadow-lg bg-yellow-500 text-white">
                                    <i class="fas fa-star mr-1"></i>Featured
                                </span>
                            <?php endif; ?>
                        </div>

                        <!-- Card Content -->
                        <div class="p-6">
                            <!-- Title -->
                            <h3
                                class="text-xl font-medium text-[#1B2D62] mb-3 group-hover:text-[#2C4AA4] transition-colors duration-300 line-clamp-2">
                                <?php echo htmlspecialchars($item['judul']); ?>
                            </h3>

                            <!-- Location if available -->
                            <?php if (!empty($item['lokasi'])): ?>
                                <div class="flex items-center gap-2 text-sm text-gray-500 mb-3">
                                    <i class="fas fa-map-marker-alt text-orange-500"></i>
                                    <span><?php echo htmlspecialchars($item['lokasi']); ?></span>
                                </div>
                            <?php endif; ?>

                            <!-- Description -->
                            <p class="text-sm text-gray-600 leading-relaxed line-clamp-3">
                                <?php echo htmlspecialchars($item['deskripsi'] ?: 'Dokumentasi kegiatan Laboratorium Network & Cyber Security.'); ?>
                            </p>

                            <!-- Footer -->
                            <div class="flex items-center justify-between pt-5 mt-5 border-t-2 border-gray-100">
                                <span
                                    class="inline-flex items-center gap-2 text-orange-600 font-medium hover:text-orange-700 transition-colors group/link">
                                    <span>Lihat Detail</span>
                                    <i class="fas fa-arrow-right group-hover/link:translate-x-1 transition-transform"></i>
                                </span>

                                <div class="flex items-center gap-2 text-gray-500">
                                    <div class="flex items-center gap-1.5 px-3 py-1.5 bg-gray-50 rounded-lg">
                                        <i class="fas fa-calendar text-gray-400"></i>
                                        <span class="font-medium text-sm">
                                            <?php echo !empty($item['tanggal_kegiatan']) ? date('d M Y', strtotime($item['tanggal_kegiatan'])) : '-'; ?>
                                        </span>
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
                if ($filter_kategori)
                    $url_params[] = 'filter=' . urlencode($filter_kategori);
                if ($search)
                    $url_params[] = 'search=' . urlencode($search);
                if (!empty($url_params))
                    $base_url .= implode('&', $url_params) . '&';
                ?>

                <div class="flex justify-center items-center gap-2 mt-16" data-aos="fade-up">
                    <!-- Previous Button -->
                    <?php if ($current_page > 1): ?>
                        <a href="<?php echo $base_url; ?>page=<?php echo $current_page - 1; ?>"
                            class="w-11 h-11 flex items-center justify-center border-2 border-gray-300 rounded-xl hover:border-orange-500 hover:bg-orange-50 hover:text-orange-600 transition-all bg-white font-medium">
                            <i class="fas fa-chevron-left"></i>
                        </a>
                    <?php else: ?>
                        <span
                            class="w-11 h-11 flex items-center justify-center border-2 border-gray-200 rounded-xl bg-gray-50 text-gray-400 cursor-not-allowed">
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
                        echo '<a href="' . $base_url . 'page=1" class="w-11 h-11 flex items-center justify-center border-2 border-gray-300 rounded-xl hover:border-orange-500 hover:bg-orange-50 hover:text-orange-600 transition-all bg-white font-medium">1</a>';
                        if ($start_page > 2) {
                            echo '<span class="w-11 h-11 flex items-center justify-center text-gray-400 font-medium">...</span>';
                        }
                    }

                    for ($i = $start_page; $i <= $end_page; $i++):
                        ?>
                        <?php if ($i == $current_page): ?>
                            <span
                                class="w-11 h-11 flex items-center justify-center border-2 border-orange-500 rounded-xl bg-gradient-to-r from-orange-500 to-orange-600 text-white font-medium shadow-lg scale-110">
                                <?php echo $i; ?>
                            </span>
                        <?php else: ?>
                            <a href="<?php echo $base_url; ?>page=<?php echo $i; ?>"
                                class="w-11 h-11 flex items-center justify-center border-2 border-gray-300 rounded-xl hover:border-orange-500 hover:bg-orange-50 hover:text-orange-600 transition-all bg-white font-medium">
                                <?php echo $i; ?>
                            </a>
                        <?php endif; ?>
                    <?php endfor; ?>

                    <?php
                    if ($end_page < $total_pages) {
                        if ($end_page < $total_pages - 1) {
                            echo '<span class="w-11 h-11 flex items-center justify-center text-gray-400 font-medium">...</span>';
                        }
                        echo '<a href="' . $base_url . 'page=' . $total_pages . '" class="w-11 h-11 flex items-center justify-center border-2 border-gray-300 rounded-xl hover:border-orange-500 hover:bg-orange-50 hover:text-orange-600 transition-all bg-white font-medium">' . $total_pages . '</a>';
                    }
                    ?>

                    <!-- Next Button -->
                    <?php if ($current_page < $total_pages): ?>
                        <a href="<?php echo $base_url; ?>page=<?php echo $current_page + 1; ?>"
                            class="w-11 h-11 flex items-center justify-center border-2 border-gray-300 rounded-xl hover:border-orange-500 hover:bg-orange-50 hover:text-orange-600 transition-all bg-white font-medium">
                            <i class="fas fa-chevron-right"></i>
                        </a>
                    <?php else: ?>
                        <span
                            class="w-11 h-11 flex items-center justify-center border-2 border-gray-200 rounded-xl bg-gray-50 text-gray-400 cursor-not-allowed">
                            <i class="fas fa-chevron-right"></i>
                        </span>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

        <?php else: ?>

            <!-- Empty State -->
            <div class="text-center py-20 max-w-2xl mx-auto" data-aos="fade-up">
                <div
                    class="w-32 h-32 bg-gradient-to-br from-orange-100 to-orange-50 rounded-full flex items-center justify-center mx-auto mb-8 shadow-lg">
                    <i class="fas fa-images text-orange-400 text-6xl"></i>
                </div>

                <h3 class="text-3xl font-medium text-[#1B2D62] mb-4">
                    <?php if ($search): ?>
                        Hasil Pencarian Tidak Ditemukan
                    <?php elseif ($filter_kategori): ?>
                        Belum Ada <?php echo ucfirst($filter_kategori); ?>
                    <?php else: ?>
                        Galeri Belum Tersedia
                    <?php endif; ?>
                </h3>

                <p class="text-lg text-gray-600 mb-8 leading-relaxed">
                    <?php if ($search): ?>
                        Tidak ada hasil untuk pencarian "<strong
                            class="text-orange-600"><?php echo htmlspecialchars($search); ?></strong>".<br>
                        Coba gunakan kata kunci yang berbeda atau lihat semua galeri.
                    <?php elseif ($filter_kategori): ?>
                        Belum ada <strong class="text-orange-600"><?php echo ucfirst($filter_kategori); ?></strong> yang
                        dipublikasikan.<br>
                        Silakan coba kategori lain atau lihat semua galeri.
                    <?php else: ?>
                        Saat ini belum ada galeri yang dipublikasikan.<br>
                        Silakan kembali lagi nanti untuk melihat update terbaru.
                    <?php endif; ?>
                </p>

                <?php if ($search || $filter_kategori): ?>
                    <div class="flex flex-col sm:flex-row gap-4 justify-center">
                        <a href="?"
                            class="inline-flex items-center justify-center gap-2 bg-gradient-to-r from-orange-500 to-orange-600 text-white font-medium px-8 py-4 rounded-xl hover:shadow-xl hover:scale-105 transition-all duration-300">
                            <i class="fas fa-arrow-left"></i>
                            Lihat Semua Galeri
                        </a>
                        <button onclick="document.querySelector('input[name=search]').focus()"
                            class="inline-flex items-center justify-center gap-2 bg-white border-2 border-gray-300 text-gray-700 font-medium px-8 py-4 rounded-xl hover:border-orange-500 hover:text-orange-600 transition-all duration-300">
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
                <div class="absolute inset-0"
                    style="background-image: url('data:image/svg+xml,%3Csvg width=%2260%22 height=%2260%22 viewBox=%220 0 60 60%22 xmlns=%22http://www.w3.org/2000/svg%22%3E%3Cg fill=%22none%22 fill-rule=%22evenodd%22%3E%3Cg fill=%22%23ffffff%22 fill-opacity=%221%22%3E%3Cpath d=%22M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z%22/%3E%3C/g%3E%3C/g%3E%3C/svg%3E');">
                </div>
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
                    <div class="inline-flex items-center gap-2.5 px-5 py-2.5 bg-white/10 backdrop-blur-sm border border-white/20 rounded-full text-white font-semibold text-sm mb-8"
                        data-aos="fade-up">
                        <span class="w-2 h-2 bg-orange-400 rounded-full animate-pulse"></span>
                        <span class="tracking-wide">HUBUNGI KAMI</span>
                    </div>

                    <!-- Heading -->
                    <h2 class="text-4xl md:text-5xl lg:text-6xl font-medium text-white mb-6 leading-tight"
                        data-aos="fade-up">
                        Butuh Bantuan<br class="hidden sm:block">
                        <span
                            class="bg-gradient-to-r from-orange-400 via-orange-300 to-yellow-300 bg-clip-text text-transparent">Lebih
                            Lanjut?</span>
                    </h2>

                    <!-- Description -->
                    <p class="text-lg md:text-xl text-white/80 mb-12 leading-relaxed max-w-2xl mx-auto"
                        data-aos="fade-up">
                        Jika Anda memiliki pertanyaan atau membutuhkan informasi lebih lanjut tentang layanan kami,
                        jangan ragu untuk menghubungi tim kami.
                    </p>

                    <!-- CTA Buttons -->
                    <div class="flex flex-col sm:flex-row gap-4 justify-center" data-aos="fade-up">
                        <a href="./kontak.php"
                            class="group inline-flex items-center justify-center gap-3 bg-gradient-to-r from-orange-500 to-orange-600 text-white font-medium text-base px-8 py-4 rounded-xl shadow-lg shadow-orange-500/30 transition-all duration-300 hover:shadow-xl hover:shadow-orange-500/40 hover:scale-[1.02] active:scale-[0.98]">
                            <i
                                class="fas fa-paper-plane transition-transform duration-300 group-hover:-translate-y-0.5 group-hover:translate-x-0.5"></i>
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

<!-- Modal Detail Galeri -->
<div id="modalDetailGaleri" aria-hidden="true"
    class="fixed inset-0 bg-slate-950/60 backdrop-blur-sm flex justify-center items-center opacity-0 pointer-events-none transition-all duration-300 ease-out z-[9999]">
    <div
        class="bg-white rounded-2xl shadow-2xl border border-gray-200 w-full max-w-3xl scale-95 transition-transform duration-300 max-h-[90vh] overflow-y-auto mx-4">

        <!-- Modal Header -->
        <div
            class="p-5 pb-3 flex justify-between items-center border-b border-gray-200 sticky top-0 bg-white z-10 rounded-t-2xl">
            <h1 class="text-lg text-[#1B2D62] font-semibold flex items-center gap-2">
                <div
                    class="w-8 h-8 bg-gradient-to-br from-orange-500 to-orange-600 rounded-lg flex items-center justify-center">
                    <i class="fas fa-images text-white text-sm"></i>
                </div>
                <span>Detail Galeri</span>
            </h1>
            <button type="button" data-dismiss="modal"
                class="inline-grid place-items-center text-gray-500 hover:bg-gray-100 rounded-lg min-w-[40px] min-h-[40px] transition-all">
                <svg width="1.5em" height="1.5em" stroke-width="1.5" viewBox="0 0 24 24" fill="none"
                    xmlns="http://www.w3.org/2000/svg" color="currentColor" class="h-5 w-5">
                    <path
                        d="M6.75827 17.2426L12.0009 12M17.2435 6.75736L12.0009 12M12.0009 12L6.75827 6.75736M12.0009 12L17.2435 17.2426"
                        stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"></path>
                </svg>
            </button>
        </div>

        <!-- Modal Body -->
        <div class="p-6">
            <div class="flex flex-col gap-6">

                <!-- Image Section -->
                <div class="w-full">
                    <div
                        class="rounded-xl overflow-hidden shadow-lg border border-gray-200 bg-gradient-to-br from-gray-100 to-gray-200">
                        <img id="detail-gambar" src="" alt="Detail Gambar" class="w-full max-h-[400px] object-cover">
                    </div>
                </div>

                <!-- Content Section -->
                <div class="w-full space-y-5">

                    <!-- Title -->
                    <div>
                        <label class="text-xs font-bold text-gray-500 uppercase tracking-wide">Judul Kegiatan</label>
                        <h4 id="detail-judul" class="text-2xl font-semibold text-[#1B2D62] mt-1"></h4>
                    </div>

                    <!-- Info Grid -->
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div class="bg-gradient-to-br from-blue-50 to-blue-100 p-4 rounded-xl border border-blue-200">
                            <label
                                class="text-xs font-bold text-blue-600 uppercase tracking-wide flex items-center gap-1">
                                <i class="fas fa-tag"></i> Kategori
                            </label>
                            <div id="detail-tipe" class="mt-2"></div>
                        </div>
                        <div
                            class="bg-gradient-to-br from-orange-50 to-orange-100 p-4 rounded-xl border border-orange-200">
                            <label
                                class="text-xs font-bold text-orange-600 uppercase tracking-wide flex items-center gap-1">
                                <i class="fas fa-calendar-alt"></i> Tanggal
                            </label>
                            <div id="detail-tanggal" class="mt-2 text-sm font-semibold text-gray-800"></div>
                        </div>
                        <div
                            class="bg-gradient-to-br from-green-50 to-green-100 p-4 rounded-xl border border-green-200">
                            <label
                                class="text-xs font-bold text-green-600 uppercase tracking-wide flex items-center gap-1">
                                <i class="fas fa-map-marker-alt"></i> Lokasi
                            </label>
                            <div id="detail-lokasi" class="mt-2 text-sm font-semibold text-gray-800"></div>
                        </div>
                    </div>

                    <!-- Description -->
                    <div class="bg-gray-50 p-5 rounded-xl border border-gray-200">
                        <label class="text-xs font-bold text-gray-500 uppercase tracking-wide flex items-center gap-1">
                            <i class="fas fa-align-left"></i> Deskripsi
                        </label>
                        <p id="detail-deskripsi" class="text-gray-700 text-sm mt-2 leading-relaxed break-words"></p>
                    </div>

                    <!-- Featured Badge -->
                    <div id="detail-featured-container" class="hidden">
                        <span
                            class="inline-flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-yellow-400 to-yellow-500 text-white font-semibold rounded-lg shadow-md">
                            <i class="fas fa-star"></i>
                            <span>Konten Unggulan</span>
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Footer -->
        <div class="p-5 pt-3 flex justify-end gap-3 border-t border-gray-200 sticky bottom-0 bg-white rounded-b-2xl">
            <button type="button" data-dismiss="modal"
                class="inline-flex items-center justify-center px-6 py-2.5 rounded-xl text-sm font-medium bg-gradient-to-r from-orange-500 to-orange-600 text-white hover:shadow-lg hover:scale-105 transition-all duration-300">
                <i class="fas fa-times mr-2"></i>Tutup
            </button>
        </div>
    </div>
</div>

<script>
    // Function to show galeri detail modal
    function showGaleriDetail(data) {
        // Set image
        $('#detail-gambar').attr('src', data.gambar);

        // Set title
        $('#detail-judul').text(data.judul);

        // Set category with badge
        const tipeClass = data.tipe.toLowerCase() === 'agenda'
            ? 'bg-blue-500 text-white'
            : 'bg-orange-500 text-white';
        $('#detail-tipe').html(`<span class="px-3 py-1.5 rounded-lg text-xs font-bold uppercase tracking-wide ${tipeClass}">${data.tipe}</span>`);

        // Set date
        $('#detail-tanggal').text(data.tanggal);

        // Set location
        $('#detail-lokasi').text(data.lokasi || 'Tidak tersedia');

        // Set description
        $('#detail-deskripsi').text(data.deskripsi);

        // Show/hide featured badge
        if (data.is_featured) {
            $('#detail-featured-container').removeClass('hidden');
        } else {
            $('#detail-featured-container').addClass('hidden');
        }
    }
</script>

<?php
// Include public footer
require_once __DIR__ . '/../includes/footer.php';
?>