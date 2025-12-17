<?php
/**
 * Admin Arsip Management
 * File: admin/arsip.php
 */

// Set page title
$page_title = "Kelola Arsip Penelitian & Pengabdian";

// Include admin header
require_once __DIR__ . '/../includes/admin_header.php';

// Handle actions
$action = $_GET['action'] ?? 'list';
$id = $_GET['id'] ?? null;

// Handle Delete
if ($action === 'delete' && $id) {
    $arsip = executeQuerySingle("SELECT * FROM arsip WHERE id = ?", [$id]);
    
    if ($arsip) {
        $pdo = beginTransaction();
        try {
            // Delete relations first
            executeNonQuery("DELETE FROM arsip_pengelola WHERE arsip_id = ?", [$id]);
            
            // Delete PDF file
            if ($arsip['file_pdf_path']) {
                deleteFile($arsip['file_pdf_path']);
            }
            
            // Delete from database
            executeNonQuery("DELETE FROM arsip WHERE id = ?", [$id]);
            
            commitTransaction($pdo);
            setFlashMessage('success', 'Arsip berhasil dihapus');
        } catch (Exception $e) {
            rollbackTransaction($pdo);
            setFlashMessage('error', 'Gagal menghapus arsip: ' . $e->getMessage());
        }
    } else {
        setFlashMessage('error', 'Data tidak ditemukan');
    }
    
    redirect(ADMIN_URL . '/arsip.php');
}

// Handle Form Submission (Add/Edit)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['form_action'])) {
    $form_action = $_POST['form_action']; // 'add' atau 'edit'
    $id_edit = $_POST['id'] ?? null;
    
    $judul = sanitize($_POST['judul'] ?? '');
    $kategori = sanitize($_POST['kategori'] ?? 'penelitian');
    $abstrak = sanitize($_POST['abstrak'] ?? '');
    $tahun_publikasi = isset($_POST['tahun_publikasi']) ? (int)$_POST['tahun_publikasi'] : date('Y');
    $penerbit = sanitize($_POST['penerbit'] ?? '');
    $keywords = sanitize($_POST['keywords'] ?? '');
    $is_featured = isset($_POST['is_featured']) ? 1 : 0;
    $is_active = isset($_POST['is_active']) ? 1 : 0;
    $pengelola_ids = $_POST['pengelola_ids'] ?? [];
    
    $errors = [];
    
    // Validasi
    if (empty($judul)) $errors[] = "Judul harus diisi";
    if (!in_array($kategori, ['penelitian', 'pengabdian'])) $errors[] = "Kategori tidak valid";
    if ($tahun_publikasi < 1900 || $tahun_publikasi > date('Y')) $errors[] = "Tahun publikasi tidak valid";
    if (empty($pengelola_ids)) $errors[] = "Pilih minimal satu pengelola/penulis";
    
    // Handle PDF upload
    $upload_required = ($form_action === 'add');
    $file_pdf_path = null;
    
    // Jika edit, ambil file PDF lama terlebih dahulu
    if ($form_action === 'edit' && $id_edit) {
        $old_arsip = executeQuerySingle("SELECT file_pdf_path FROM arsip WHERE id = ?", [$id_edit]);
        $file_pdf_path = $old_arsip['file_pdf_path'] ?? null;
    }
    
    if (isset($_FILES['file_pdf']) && $_FILES['file_pdf']['error'] === UPLOAD_ERR_OK) {
        $upload_result = uploadPDF($_FILES['file_pdf'], 'arsip', 'arsip');
        
        if ($upload_result['success']) {
            // Delete old PDF if editing and new file uploaded
            if ($form_action === 'edit' && $file_pdf_path) {
                deleteFile($file_pdf_path);
            }
            $file_pdf_path = '/arsip/' . $upload_result['filename'];
        } else {
            $errors[] = $upload_result['message'];
        }
    } elseif ($upload_required) {
        $errors[] = "File PDF harus diupload";
    }
    
    // If no errors, save to database
    if (empty($errors)) {
        // Ambil ID Admin yang sedang login
        $admin = getCurrentUser();
        $id_admin = $admin['id'] ?? null;

        $pdo = beginTransaction();
        try {
            if ($form_action === 'add') {
                $query = "INSERT INTO arsip (judul, kategori, abstrak, tahun_publikasi, penerbit, 
                          file_pdf_path, keywords, is_featured, is_active, jumlah_download, id_admin) 
                          VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 0, ?)";
                $params = [$judul, $kategori, $abstrak, $tahun_publikasi, $penerbit, 
                          $file_pdf_path, $keywords, $is_featured, $is_active, $id_admin];
                
                executeInsert($query, $params);
                $getQuery = "SELECT id from arsip order by id desc limit 1";
                $arsip_id = executeQuerySingle($getQuery);
                
                // Insert pengelola relations
                foreach ($pengelola_ids as $pengelola_id) {
                    executeInsert(
                        "INSERT INTO arsip_pengelola (arsip_id, pengelola_id) VALUES (?, ?)",
                        [$arsip_id["id"], $pengelola_id]
                    );
                }
                
                commitTransaction($pdo);
                setFlashMessage('success', 'Arsip berhasil ditambahkan');
                redirect(ADMIN_URL . '/arsip.php');
                
            } elseif ($form_action === 'edit' && $id_edit) {
                $query = "UPDATE arsip SET judul = ?, kategori = ?, abstrak = ?, tahun_publikasi = ?, 
                          penerbit = ?, file_pdf_path = ?, keywords = ?, is_featured = ?, is_active = ?, id_admin = ? WHERE id = ?";
                $params = [$judul, $kategori, $abstrak, $tahun_publikasi, $penerbit, 
                          $file_pdf_path, $keywords, $is_featured, $is_active, $id_admin, $id_edit];
                
                executeNonQuery($query, $params);
                
                // Delete old relations
                executeNonQuery("DELETE FROM arsip_pengelola WHERE arsip_id = ?", [$id_edit]);
                
                // Insert new relations
                foreach ($pengelola_ids as $pengelola_id) {
                    executeInsert(
                        "INSERT INTO arsip_pengelola (arsip_id, pengelola_id) VALUES (?, ?)",
                        [$id_edit, $pengelola_id]
                    );
                }
                
                commitTransaction($pdo);
                setFlashMessage('success', 'Arsip berhasil diupdate');
                redirect(ADMIN_URL . '/arsip.php');
            }
        } catch (Exception $e) {
            rollbackTransaction($pdo);
            $errors[] = "Gagal menyimpan data: " . $e->getMessage();
        }
    }
    
    // Show errors
    if (!empty($errors)) {
        foreach ($errors as $error) {
            setFlashMessage('error', $error);
        }
    }
}

// Get list data with pagination and filter
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$filter_kategori = $_GET['filter_kategori'] ?? '';
$filter_tahun = $_GET['filter_tahun'] ?? '';
$search = $_GET['search'] ?? '';

$limit = ITEMS_PER_PAGE;
$offset = ($page - 1) * $limit;

// Build query
$where = ["1=1"];
$params = [];

if ($filter_kategori && in_array($filter_kategori, ['penelitian', 'pengabdian'])) {
    $where[] = "kategori = ?";
    $params[] = $filter_kategori;
}
    
    if ($filter_tahun) {
        $where[] = "tahun_publikasi = ?";
        $params[] = (int)$filter_tahun;
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

// Count total
$total = countRows("SELECT COUNT(*) FROM arsip WHERE " . $where_clause, $params);
$total_pages = ceil($total / $limit);

// Get data
$query = "SELECT a.*, u.nama_lengkap as created_by_name 
          FROM arsip a 
          LEFT JOIN users u ON a.id_admin = u.id 
          WHERE " . $where_clause . " 
          ORDER BY a.tahun_publikasi DESC, a.created_at DESC 
          LIMIT ? OFFSET ?";
$params[] = $limit;
$params[] = $offset;
$arsip_list = executeQuery($query, $params);

// Get years for filter
$years = executeQuery("SELECT DISTINCT tahun_publikasi FROM arsip ORDER BY tahun_publikasi DESC");

// Get all active pengelola for form
$pengelola_list = executeQuery("SELECT id, nama_lengkap, jabatan FROM pengelola WHERE is_active = true ORDER BY nama_lengkap");
?>

<!-- List View -->
<div class="space-y-6">
    
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h2 class="text-lg font-semibold text-blue-900"><i class="fas fa-file-pdf mr-2"></i>Arsip Penelitian & Pengabdian</h2>
            <p class="text-gray-600 mt-1">Kelola dokumen penelitian dan pengabdian masyarakat</p>
        </div>
        <button type="button" data-toggle="modal" data-target="#modalArsip" onclick="openModalAdd()" class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded-lg shadow transition flex items-center gap-2">
            <i class="fas fa-plus"></i>Tambah Arsip
        </button>
    </div>
    
    <!-- Filter & Search -->
    <div class="bg-white rounded-lg shadow-md p-4">
        <form method="GET" class="flex flex-col md:flex-row gap-4">
            <input type="hidden" name="action" value="list">
            
            <!-- Search -->
            <div class="flex-1">
                <input 
                    type="text" 
                    name="search" 
                    placeholder="Cari judul, abstrak, penerbit, atau keywords..." 
                    value="<?php echo htmlspecialchars($search); ?>"
                    class="form-input"
                >
            </div>
            
            <!-- Filter Kategori -->
            <select name="filter_kategori" class="form-input md:w-48">
                <option value="">Semua Kategori</option>
                <option value="penelitian" <?php echo $filter_kategori === 'penelitian' ? 'selected' : ''; ?>>Penelitian</option>
                <option value="pengabdian" <?php echo $filter_kategori === 'pengabdian' ? 'selected' : ''; ?>>Pengabdian</option>
            </select>
            
            <!-- Filter Tahun -->
            <select name="filter_tahun" class="form-input md:w-48">
                <option value="">Semua Tahun</option>
                <?php if ($years): ?>
                    <?php foreach ($years as $year): ?>
                    <option value="<?php echo $year['tahun_publikasi']; ?>" 
                            <?php echo $filter_tahun == $year['tahun_publikasi'] ? 'selected' : ''; ?>>
                        <?php echo $year['tahun_publikasi']; ?>
                    </option>
                    <?php endforeach; ?>
                <?php endif; ?>
            </select>
            
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-search mr-2"></i>Cari
            </button>
            
            <?php if ($search || $filter_kategori || $filter_tahun): ?>
            <a href="?action=list" class="btn bg-gray-500 text-white hover:bg-gray-600">
                <i class="fas fa-times mr-2"></i>Reset
            </a>
            <?php endif; ?>
        </form>
    </div>
    
    <!-- Stats -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="bg-white rounded-lg shadow p-4 border-l-4 border-blue-500">
            <p class="text-gray-500 text-sm mb-1 uppercase font-bold tracking-wider">Total Arsip</p>
            <p class="text-2xl font-bold text-gray-800">
                <?php echo countRows("SELECT COUNT(*) FROM arsip WHERE is_active = true"); ?>
            </p>
        </div>
        <div class="bg-white rounded-lg shadow p-4 border-l-4 border-green-500">
            <p class="text-gray-500 text-sm mb-1 uppercase font-bold tracking-wider">Penelitian</p>
            <p class="text-2xl font-bold text-green-600">
                <?php echo countRows("SELECT COUNT(*) FROM arsip WHERE kategori = 'penelitian' AND is_active = true"); ?>
            </p>
        </div>
        <div class="bg-white rounded-lg shadow p-4 border-l-4 border-orange-500">
            <p class="text-gray-500 text-sm mb-1 uppercase font-bold tracking-wider">Pengabdian</p>
            <p class="text-2xl font-bold text-orange-600">
                <?php echo countRows("SELECT COUNT(*) FROM arsip WHERE kategori = 'pengabdian' AND is_active = true"); ?>
            </p>
        </div>
        <div class="bg-white rounded-lg shadow p-4 border-l-4 border-purple-500">
            <p class="text-gray-500 text-sm mb-1 uppercase font-bold tracking-wider">Total Download</p>
            <p class="text-2xl font-bold text-purple-600">
                <?php 
                $total_downloads = executeQuerySingle("SELECT SUM(jumlah_download) as total FROM arsip");
                echo number_format($total_downloads['total'] ?? 0);
                ?>
            </p>
        </div>
    </div>
    
    <!-- Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <?php if ($arsip_list && count($arsip_list) > 0): ?>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50 text-gray-600 text-xs uppercase font-bold border-b border-gray-200">
                        <th class="px-6 py-4">Judul & Penulis</th>
                        <th class="px-6 py-4 w-32">Kategori</th>
                        <th class="px-6 py-4 w-24 text-center">Tahun</th>
                        <th class="px-6 py-4 w-40">Penerbit</th>
                        <th class="px-6 py-4 w-24 text-center">Download</th>
                        <th class="px-6 py-4 w-24 text-center">Status</th>
                        <th class="px-6 py-4 w-40">Dibuat Oleh</th>
                        <th class="px-6 py-4 w-32 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <?php foreach ($arsip_list as $item): ?>
                    <?php
                    // Get authors
                    $authors = executeQuery(
                        "SELECT p.nama_lengkap 
                         FROM arsip_pengelola ap 
                         JOIN pengelola p ON ap.pengelola_id = p.id 
                         WHERE ap.arsip_id = ? 
                         ORDER BY p.nama_lengkap",
                        [$item['id']]
                    );
                    ?>
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-6 py-4">
                            <p class="font-semibold text-gray-800 text-sm"><?php echo $item['judul']; ?></p>
                            <?php if ($authors): ?>
                            <p class="text-sm text-blue-600 mt-1 line-clamp-2">
                                <i class="fas fa-user mr-1"></i>
                                <?php 
                                $author_names = array_map(function($a) { return $a['nama_lengkap']; }, $authors);
                                echo implode(', ', $author_names);
                                ?>
                            </p>
                            <?php endif; ?>
                            <?php if ($item['keywords']): ?>
                            <p class="text-xs text-gray-500 mt-1 line-clamp-2">
                                <i class="fas fa-tags mr-1"></i><?php echo $item['keywords']; ?>
                            </p>
                            <?php endif; ?>
                            <?php if ($item['is_featured']): ?>
                            <span class="inline-block bg-yellow-100 text-yellow-800 text-xs px-2 py-1 rounded mt-1">
                                <i class="fas fa-star mr-1"></i>Featured
                            </span>
                            <?php endif; ?>
                        </td>
                        <td class="px-6 py-4">
                            <span class="inline-block px-3 py-1 rounded-full text-xs font-semibold <?php echo $item['kategori'] === 'penelitian' ? 'bg-green-100 text-green-800' : 'bg-blue-100 text-blue-800'; ?>">
                                <?php echo ucfirst($item['kategori']); ?>
                            </span>
                        </td>
                        <td class="px-6 py-4 text-center font-semibold text-gray-700">
                            <?php echo $item['tahun_publikasi']; ?>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600">
                            <?php echo $item['penerbit'] ?: '-'; ?>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span class="inline-flex items-center gap-1 text-purple-600 font-semibold">
                                <i class="fas fa-download"></i>
                                <?php echo number_format($item['jumlah_download']); ?>
                            </span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span class="inline-block px-3 py-1 rounded-full text-xs font-semibold <?php echo $item['is_active'] ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'; ?>">
                                <?php echo $item['is_active'] ? 'Aktif' : 'Nonaktif'; ?>
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-2">
                                <div class="w-8 h-8 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 font-semibold text-sm">
                                    <?php 
                                    if ($item['created_by_name']) {
                                        $name_parts = explode(' ', $item['created_by_name']);
                                        $initials = '';
                                        foreach ($name_parts as $part) {
                                            $initials .= strtoupper(substr($part, 0, 1));
                                            if (strlen($initials) >= 2) break;
                                        }
                                        echo $initials;
                                    } else {
                                        echo '<i class="fas fa-user text-xs"></i>';
                                    }
                                    ?>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-800">
                                        <?php echo $item['created_by_name'] ?? 'Unknown'; ?>
                                    </p>
                                    <?php if (!empty($item['created_at'])): ?>
                                    <p class="text-xs text-gray-500">
                                        <?php 
                                        $date = new DateTime($item['created_at']);
                                        echo $date->format('d M Y');
                                        ?>
                                    </p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <div class="flex items-center justify-center gap-2">
                                <button type="button" data-toggle="modal" data-target="#modalDetailArsip" 
                                   onclick='viewDetailArsip(<?php echo json_encode($item); ?>, <?php echo json_encode($author_names ?? []); ?>)'
                                   class="text-gray-500 hover:bg-gray-50 p-2 rounded-lg transition" 
                                   title="Lihat Detail">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <a href="<?php echo UPLOAD_URL . $item['file_pdf_path']; ?>" 
                                   target="_blank" 
                                   class="text-green-500 hover:bg-green-50 p-2 rounded-lg transition" 
                                   title="Lihat PDF">
                                    <i class="fas fa-file-pdf"></i>
                                </a>
                                <button type="button" data-toggle="modal" data-target="#modalArsip" 
                                   onclick='openModalEdit(<?php echo json_encode($item); ?>, <?php echo json_encode(array_map(function($a) { return $a["pengelola_id"]; }, executeQuery("SELECT pengelola_id FROM arsip_pengelola WHERE arsip_id = ?", [$item["id"]]))); ?>)'
                                   class="text-blue-500 hover:bg-blue-50 p-2 rounded-lg transition" 
                                   title="Edit">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <a href="?action=delete&id=<?php echo $item['id']; ?>" 
                                   class="text-red-500 hover:bg-red-50 p-2 rounded-lg transition" 
                                   title="Hapus"
                                   onclick="return confirm('Yakin ingin menghapus data ini?');">
                                    <i class="fas fa-trash"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        <?php if ($total_pages > 1): ?>
        <div class="px-6 py-4 border-t">
            <?php 
            $base_url = '?action=list';
            if ($filter_kategori) $base_url .= '&filter_kategori=' . $filter_kategori;
            if ($filter_tahun) $base_url .= '&filter_tahun=' . $filter_tahun;
            if ($search) $base_url .= '&search=' . urlencode($search);
            echo createPagination($page, $total_pages, $base_url); 
            ?>
        </div>
        <?php endif; ?>
        
        <?php else: ?>
        <div class="text-center py-12">
            <i class="fas fa-file-pdf text-6xl text-gray-300 mb-4"></i>
            <p class="text-gray-500 text-lg">Belum ada arsip</p>
            <button type="button" data-toggle="modal" data-target="#modalArsip" onclick="openModalAdd()" class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded-lg shadow transition inline-flex items-center gap-2 mt-4">
                <i class="fas fa-plus"></i>Tambah Arsip Pertama
            </button>
        </div>
        <?php endif; ?>
    </div> 
</div>

<!-- Modal Detail Arsip -->
<div id="modalDetailArsip" aria-hidden="true"
    class="fixed inset-0 bg-slate-950/50 flex justify-center items-center opacity-0 pointer-events-none transition-opacity duration-300 ease-out z-[9999]">
    <div class="bg-white rounded-xl shadow-2xl border border-slate-200 w-full max-w-2xl scale-95 transition-transform duration-300 max-h-[90vh] overflow-y-auto mx-4">

        <div class="bg-gradient-to-r from-blue-600 to-blue-800 p-6 relative">
            <button type="button" data-dismiss="modal"
                class="absolute top-4 right-4 text-white/70 hover:text-white text-2xl transition"><i
                    class="fas fa-times"></i></button>

            <div class="flex items-start gap-4">
                <div class="w-16 h-16 rounded-lg bg-white/20 flex items-center justify-center text-white text-2xl">
                    <i class="fas fa-file-pdf"></i>
                </div>
                <div class="flex-1">
                    <h3 id="detailJudul" class="text-xl font-bold text-white leading-tight"></h3>
                    <div class="flex flex-wrap gap-2 mt-2">
                        <span id="detailKategoriBadge" class="inline-block px-3 py-1 bg-white/20 backdrop-blur-sm rounded-full text-white text-xs font-semibold"></span>
                        <span id="detailTahunBadge" class="inline-block px-3 py-1 bg-white/20 backdrop-blur-sm rounded-full text-white text-xs font-semibold"></span>
                        <span id="detailStatusBadge" class="inline-block px-3 py-1 rounded-full text-xs font-semibold"></span>
                    </div>
                </div>
            </div>
        </div>

        <div class="p-6 space-y-4">
            <!-- Penulis -->
            <div>
                <p class="text-gray-500 text-xs uppercase tracking-wide mb-1"><i class="fas fa-users mr-1"></i>Penulis</p>
                <p id="detailPenulis" class="font-medium text-gray-800"></p>
            </div>

            <hr class="border-gray-100">

            <!-- Penerbit -->
            <div class="grid grid-cols-2 gap-4 text-sm">
                <div>
                    <p class="text-gray-500 text-xs uppercase tracking-wide">Penerbit</p>
                    <p id="detailPenerbit" class="font-medium text-gray-800"></p>
                </div>
                <div>
                    <p class="text-gray-500 text-xs uppercase tracking-wide">Jumlah Download</p>
                    <p id="detailDownload" class="font-medium text-purple-600"></p>
                </div>
            </div>

            <hr class="border-gray-100">

            <!-- Abstrak -->
            <div>
                <p class="text-gray-500 text-xs uppercase tracking-wide mb-1"><i class="fas fa-align-left mr-1"></i>Abstrak</p>
                <p id="detailAbstrak" class="text-sm text-gray-700 leading-relaxed"></p>
            </div>

            <!-- Keywords -->
            <div id="detailKeywordsContainer">
                <p class="text-gray-500 text-xs uppercase tracking-wide mb-2"><i class="fas fa-tags mr-1"></i>Keywords</p>
                <div id="detailKeywords" class="flex flex-wrap gap-2"></div>
            </div>

            <!-- Featured Badge -->
            <div id="detailFeaturedContainer" class="hidden">
                <span class="inline-block bg-yellow-100 text-yellow-800 text-sm px-3 py-1 rounded-full">
                    <i class="fas fa-star mr-1"></i>Featured - Ditampilkan di Homepage
                </span>
            </div>
        </div>

        <div class="p-5 pt-3 flex justify-between gap-3 border-t border-slate-200 sticky bottom-0 bg-white">
            <a id="detailPdfLink" href="#" target="_blank"
                class="inline-flex items-center justify-center px-4 py-2 rounded-md text-sm font-medium bg-green-600 text-white hover:bg-green-700 transition-all">
                <i class="fas fa-file-pdf mr-2"></i>Lihat PDF
            </a>
            <button type="button" data-dismiss="modal"
                class="inline-flex items-center justify-center px-4 py-2 rounded-md text-sm font-medium text-slate-600 hover:bg-slate-100 transition-all">
                <i class="fas fa-times mr-2"></i>Tutup
            </button>
        </div>
    </div>
</div>

<div id="modalArsip" aria-hidden="true" class="fixed inset-0 bg-slate-950/50 flex justify-center items-center opacity-0 pointer-events-none transition-opacity duration-300 ease-out z-[9999]">
    <div class="bg-white rounded-xl shadow-2xl border border-slate-200 w-full max-w-4xl scale-95 transition-transform duration-300 max-h-[90vh] overflow-y-auto mx-4">
        
        <div class="p-5 pb-3 flex justify-between items-center border-b border-slate-200 sticky top-0 bg-white z-10">
            <h1 id="modalArsipTitle" class="text-lg text-slate-800 font-semibold">
                <i class="fas fa-file-alt mr-2 text-blue-600"></i>Tambah Arsip
            </h1>
            <button type="button" data-dismiss="modal" 
                    class="inline-grid place-items-center text-slate-600 hover:bg-slate-200/30 rounded-md min-w-[34px] min-h-[34px] transition-all"> 
                <svg width="1.5em" height="1.5em" stroke-width="1.5" viewBox="0 0 24 24" fill="none"
                     xmlns="http://www.w3.org/2000/svg" color="currentColor" class="h-5 w-5">
                    <path
                        d="M6.75827 17.2426L12.0009 12M17.2435 6.75736L12.0009 12M12.0009 12L6.75827 6.75736M12.0009 12L17.2435 17.2426"
                        stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"></path>
                </svg>
            </button>
        </div>

        <form method="POST" enctype="multipart/form-data" id="formArsip">
            <input type="hidden" name="form_action" id="formAction" value="add">
            <input type="hidden" name="id" id="formId">
            
            <div class="p-6 pt-4 space-y-6">
                
                <div class="form-group">
                    <label class="form-label" for="judul">Judul <span class="text-red-500">*</span></label>
                    <input 
                        type="text" 
                        id="judul" 
                        name="judul" 
                        class="form-input" 
                        required
                        maxlength="200"
                        placeholder="Judul penelitian/pengabdian"
                    >
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="form-group">
                        <label class="form-label" for="kategori">Kategori <span class="text-red-500">*</span></label>
                        <select id="kategori" name="kategori" class="form-input" required>
                            <option value="">-- Pilih --</option>
                            <option value="penelitian">Penelitian</option>
                            <option value="pengabdian">Pengabdian Masyarakat</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label" for="tahun_publikasi">Tahun Publikasi <span class="text-red-500">*</span></label>
                        <input 
                            type="number" 
                            id="tahun_publikasi" 
                            name="tahun_publikasi" 
                            class="form-input"
                            value="<?php echo date('Y'); ?>"
                            required
                            min="1900"
                            max="<?php echo date('Y'); ?>"
                        >
                    </div>
                </div>
                
                <div class="form-group">
                    <label class="form-label" for="abstrak">Abstrak</label>
                    <textarea 
                        id="abstrak" 
                        name="abstrak" 
                        class="form-input" 
                        rows="5"
                        maxlength="1000"
                        placeholder="Ringkasan singkat penelitian/pengabdian"
                    ></textarea>
                    <p class="text-sm text-gray-500 mt-1">Maksimal 1000 karakter</p>
                </div>
                
                <div class="form-group">
                    <label class="form-label" for="penerbit">Penerbit</label>
                    <input 
                        type="text" 
                        id="penerbit" 
                        name="penerbit" 
                        class="form-input" 
                        maxlength="200"
                        placeholder="Contoh: Jurnal Teknik Informatika Vol. 12"
                    >
                </div>
                
                <div class="form-group">
                    <label class="form-label" for="keywords">Keywords</label>
                    <input 
                        type="text" 
                        id="keywords" 
                        name="keywords" 
                        class="form-input" 
                        maxlength="200"
                        placeholder="Contoh: Machine Learning, Cybersecurity, Network"
                    >
                    <p class="text-sm text-gray-500 mt-1">Pisahkan dengan koma</p>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Pengelola/Penulis <span class="text-red-500">*</span></label>
                    <div class="border rounded-lg p-4 space-y-2 max-h-64 overflow-y-auto">
                        <?php if ($pengelola_list): ?>
                            <?php foreach ($pengelola_list as $pengelola): ?>
                            <label class="flex items-start p-2 hover:bg-gray-50 rounded cursor-pointer">
                                <input 
                                    type="checkbox" 
                                    name="pengelola_ids[]" 
                                    value="<?php echo $pengelola['id']; ?>"
                                    class="mt-1 w-4 h-4 text-blue-600"
                                >
                                <div class="ml-3">
                                    <p class="font-semibold text-gray-800"><?php echo $pengelola['nama_lengkap']; ?></p>
                                    <p class="text-sm text-gray-500"><?php echo $pengelola['jabatan']; ?></p>
                                </div>
                            </label>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p class="text-gray-500">Tidak ada pengelola aktif</p>
                        <?php endif; ?>
                    </div>
                    <p class="text-sm text-gray-500 mt-1">Pilih satu atau lebih penulis.</p>
                </div>
                
                <div class="form-group">
                    <label class="form-label" for="file_pdf">
                        File PDF <span class="text-red-500" id="filePdfRequiredSpan">*</span>
                    </label>
                    <input 
                        type="file" 
                        id="formFilePdf" 
                        name="file_pdf" 
                        class="form-input" 
                        accept=".pdf"
                    >
                    <p class="text-sm text-gray-500 mt-1" id="formPdfHelp">
                        <i class="fas fa-info-circle mr-1"></i>Format: PDF. Maksimal 5MB.
                    </p>
                    
                    <!-- Preview PDF Container -->
                    <div class="mt-3 p-3 bg-blue-50 rounded-lg hidden" id="pdfPreviewContainer">
                        <p class="text-sm text-blue-800">
                            <i class="fas fa-file-pdf text-red-500 mr-2"></i>
                            <span id="pdfFileName"></span>
                            <span id="pdfFileSize" class="text-gray-500 ml-2"></span>
                        </p>
                    </div>
                    
                    <!-- Current PDF (for edit mode) -->
                    <div class="mt-3 p-3 bg-green-50 rounded-lg hidden" id="currentPdfContainer">
                        <p class="text-sm text-green-800">
                            <i class="fas fa-file-pdf text-red-500 mr-2"></i>
                            File saat ini: 
                            <a href="#" id="currentPdfLink" target="_blank" class="underline hover:text-green-600 font-medium">
                                Lihat PDF
                            </a>
                        </p>
                    </div>
                </div>
                
                <div class="form-group space-y-2">
                    <label class="flex items-center">
                        <input 
                            type="checkbox" 
                            name="is_featured" 
                            id="is_featured_input"
                            class="w-4 h-4 text-blue-600 mr-2"
                        >
                        <span class="text-gray-700">Featured (tampilkan di homepage)</span>
                    </label>
                    
                    <label class="flex items-center">
                        <input 
                            type="checkbox" 
                            name="is_active" 
                            id="is_active_input"
                            class="w-4 h-4 text-blue-600 mr-2"
                            checked
                        >
                        <span class="text-gray-700">Aktif (tampilkan di halaman publik)</span>
                    </label>
                </div>
            </div>

            <div class="p-5 pt-3 flex justify-end gap-3 border-t border-slate-200 sticky bottom-0 bg-white">
                <button type="button" data-dismiss="modal"
                        class="inline-flex items-center justify-center px-4 py-2 rounded-md text-sm font-medium text-slate-600 hover:bg-slate-100 transition-all">
                    <i class="fas fa-times mr-2"></i>Batal
                </button>
                <button type="submit"
                    class="inline-flex items-center justify-center px-4 py-2 rounded-md text-sm font-medium shadow-sm bg-blue-600 text-white hover:bg-blue-700 transition-all">
                    <i class="fas fa-save mr-2"></i>Simpan
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    // --- VIEW DETAIL ARSIP ---
    function viewDetailArsip(data, authorNames) {
        // Judul
        $('#detailJudul').text(data.judul || '-');
        
        // Kategori badge
        const kategori = data.kategori || 'penelitian';
        $('#detailKategoriBadge').text(kategori.charAt(0).toUpperCase() + kategori.slice(1));
        
        // Tahun badge
        $('#detailTahunBadge').text('Tahun ' + (data.tahun_publikasi || '-'));
        
        // Status badge
        if (data.is_active == 1) {
            $('#detailStatusBadge').attr('class', 'inline-block px-3 py-1 rounded-full text-xs font-semibold bg-green-400 text-white').text('Aktif');
        } else {
            $('#detailStatusBadge').attr('class', 'inline-block px-3 py-1 rounded-full text-xs font-semibold bg-red-400 text-white').text('Nonaktif');
        }
        
        // Penulis
        if (authorNames && authorNames.length > 0) {
            $('#detailPenulis').text(authorNames.join(', '));
        } else {
            $('#detailPenulis').text('-');
        }
        
        // Penerbit
        $('#detailPenerbit').text(data.penerbit || '-');
        
        // Download count
        $('#detailDownload').html('<i class="fas fa-download mr-1"></i>' + (data.jumlah_download || 0).toLocaleString());
        
        // Abstrak
        $('#detailAbstrak').text(data.abstrak || 'Tidak ada abstrak');
        
        // Keywords
        if (data.keywords) {
            const keywords = data.keywords.split(',').map(k => k.trim());
            let keywordsHtml = '';
            keywords.forEach(function(keyword) {
                keywordsHtml += '<span class="inline-block bg-gray-100 text-gray-700 text-xs px-2 py-1 rounded">' + keyword + '</span>';
            });
            $('#detailKeywords').html(keywordsHtml);
            $('#detailKeywordsContainer').removeClass('hidden');
        } else {
            $('#detailKeywordsContainer').addClass('hidden');
        }
        
        // Featured
        if (data.is_featured == 1) {
            $('#detailFeaturedContainer').removeClass('hidden');
        } else {
            $('#detailFeaturedContainer').addClass('hidden');
        }
        
        // PDF Link
        if (data.file_pdf_path) {
            $('#detailPdfLink').attr('href', '<?php echo UPLOAD_URL; ?>' + data.file_pdf_path).removeClass('hidden');
        } else {
            $('#detailPdfLink').addClass('hidden');
        }
    }

    // --- FORM MODAL LOGIC (ADD/EDIT) menggunakan jQuery ---
    function openModalAdd() {
        // Reset Form
        $('#modalArsipTitle').html('<i class="fas fa-file-alt mr-2 text-blue-600"></i>Tambah Arsip');
        $('#formAction').val('add');
        $('#formId').val('');
        $('#formArsip')[0].reset();
        
        // Reset input fields
        $('#judul').val('');
        $('#kategori').val('');
        $('#tahun_publikasi').val(new Date().getFullYear());
        $('#abstrak').val('');
        $('#penerbit').val('');
        $('#keywords').val('');
        
        // Reset file input
        $('#formFilePdf').val('');
        $('#filePdfRequiredSpan').text('*');
        $('#formPdfHelp').html('<i class="fas fa-info-circle mr-1"></i>Format: PDF. Maksimal 5MB.');
        
        // Hide preview containers
        $('#pdfPreviewContainer').addClass('hidden');
        $('#currentPdfContainer').addClass('hidden');
        
        // Reset checkboxes
        $('#is_featured_input').prop('checked', false);
        $('#is_active_input').prop('checked', true);
        
        // Uncheck all pengelola
        $('input[name="pengelola_ids[]"]').prop('checked', false);
    }

    function openModalEdit(data, pengelolaIds) {
        // Set form action dan id
        $('#modalArsipTitle').html('<i class="fas fa-edit mr-2 text-blue-600"></i>Edit Arsip');
        $('#formAction').val('edit');
        $('#formId').val(data.id);
        
        // Populate form fields
        $('#judul').val(data.judul || '');
        $('#kategori').val(data.kategori || '');
        $('#tahun_publikasi').val(data.tahun_publikasi || new Date().getFullYear());
        $('#abstrak').val(data.abstrak || '');
        $('#penerbit').val(data.penerbit || '');
        $('#keywords').val(data.keywords || '');
        
        // File PDF tidak required saat edit
        $('#formFilePdf').val('');
        $('#filePdfRequiredSpan').text('');
        $('#formPdfHelp').html('<i class="fas fa-info-circle mr-1"></i>Biarkan kosong jika tidak ingin mengganti file.');
        
        // Hide new preview, show current PDF if exists
        $('#pdfPreviewContainer').addClass('hidden');
        if (data.file_pdf_path) {
            $('#currentPdfLink').attr('href', '../uploads' + data.file_pdf_path);
            $('#currentPdfContainer').removeClass('hidden');
        } else {
            $('#currentPdfContainer').addClass('hidden');
        }
        
        // Checkboxes
        $('#is_featured_input').prop('checked', data.is_featured == 1);
        $('#is_active_input').prop('checked', data.is_active == 1);
        
        // Set pengelola checkboxes
        $('input[name="pengelola_ids[]"]').each(function() {
            $(this).prop('checked', pengelolaIds.includes(parseInt($(this).val())));
        });
    }
</script>

<?php require_once __DIR__ . '/../includes/admin_footer.php'; ?>