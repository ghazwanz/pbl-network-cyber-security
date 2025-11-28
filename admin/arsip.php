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
if ($_SERVER['REQUEST_METHOD'] === 'POST' && in_array($action, ['add', 'edit'])) {
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
    $file_pdf_path = '';
    $upload_required = ($action === 'add');
    
    if (isset($_FILES['file_pdf']) && $_FILES['file_pdf']['error'] === UPLOAD_ERR_OK) {
        $upload_result = uploadPDF($_FILES['file_pdf'], 'arsip', 'arsip');
        
        if ($upload_result['success']) {
            $file_pdf_path = '/arsip/' . $upload_result['filename'];
            
            // Delete old PDF if editing
            if ($action === 'edit' && $id) {
                $old_arsip = executeQuerySingle("SELECT file_pdf_path FROM arsip WHERE id = ?", [$id]);
                if ($old_arsip && $old_arsip['file_pdf_path']) {
                    deleteFile($old_arsip['file_pdf_path']);
                }
            }
        } else {
            $errors[] = $upload_result['message'];
        }
    } elseif ($upload_required) {
        $errors[] = "File PDF harus diupload";
    }
    
    // If no errors, save to database
    if (empty($errors)) {
        $pdo = beginTransaction();
        try {
            if ($action === 'add') {
                $query = "INSERT INTO arsip (judul, kategori, abstrak, tahun_publikasi, penerbit, 
                          file_pdf_path, keywords, is_featured, is_active, jumlah_download) 
                          VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 0)";
                $params = [$judul, $kategori, $abstrak, $tahun_publikasi, $penerbit, 
                          $file_pdf_path, $keywords, $is_featured, $is_active];
                
                executeInsert($query, $params);
                $getQuery = "SELECT id from arsip order by id desc limit 1";
                $arsip_id = executeQuerySingle($getQuery);
                
                // Insert pengelola relations
                foreach ($pengelola_ids as $index => $pengelola_id) {
                    $urutan = $index + 1;
                    $peran = ($urutan === 1) ? 'Penulis Utama' : 'Penulis Pendamping';
                    executeInsert(
                        "INSERT INTO arsip_pengelola (arsip_id, pengelola_id, urutan_penulis, peran) VALUES (?, ?, ?, ?)",
                        [$arsip_id["id"], $pengelola_id, $urutan, $peran]
                    );
                }
                
                commitTransaction($pdo);
                setFlashMessage('success', 'Arsip berhasil ditambahkan');
                redirect(ADMIN_URL . '/arsip.php');
                
            } elseif ($action === 'edit' && $id) {
                if ($file_pdf_path) {
                    $query = "UPDATE arsip SET judul = ?, kategori = ?, abstrak = ?, tahun_publikasi = ?, 
                              penerbit = ?, file_pdf_path = ?, keywords = ?, is_featured = ?, is_active = ? WHERE id = ?";
                    $params = [$judul, $kategori, $abstrak, $tahun_publikasi, $penerbit, 
                              $file_pdf_path, $keywords, $is_featured, $is_active, $id];
                } else {
                    $query = "UPDATE arsip SET judul = ?, kategori = ?, abstrak = ?, tahun_publikasi = ?, 
                              penerbit = ?, keywords = ?, is_featured = ?, is_active = ? WHERE id = ?";
                    $params = [$judul, $kategori, $abstrak, $tahun_publikasi, $penerbit, 
                              $keywords, $is_featured, $is_active, $id];
                }
                
                executeNonQuery($query, $params);
                
                // Delete old relations
                executeNonQuery("DELETE FROM arsip_pengelola WHERE arsip_id = ?", [$id]);
                
                // Insert new relations
                foreach ($pengelola_ids as $index => $pengelola_id) {
                    $urutan = $index + 1;
                    $peran = ($urutan === 1) ? 'Penulis Utama' : 'Penulis Pendamping';
                    executeInsert(
                        "INSERT INTO arsip_pengelola (arsip_id, pengelola_id, urutan_penulis, peran) VALUES (?, ?, ?, ?)",
                        [$id, $pengelola_id, $urutan, $peran]
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

// Get data for edit
$edit_data = null;
$edit_pengelola_ids = [];
if ($action === 'edit' && $id) {
    $edit_data = executeQuerySingle("SELECT * FROM arsip WHERE id = ?", [$id]);
    if (!$edit_data) {
        setFlashMessage('error', 'Data tidak ditemukan');
        redirect(ADMIN_URL . '/arsip.php');
    }
    
    // Get pengelola relations
    $relations = executeQuery(
        "SELECT pengelola_id FROM arsip_pengelola WHERE arsip_id = ? ORDER BY urutan_penulis",
        [$id]
    );
    foreach ($relations as $rel) {
        $edit_pengelola_ids[] = $rel['pengelola_id'];
    }
}

// Get list data with pagination and filter
if ($action === 'list') {
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
    $query = "SELECT * FROM arsip WHERE " . $where_clause . " ORDER BY tahun_publikasi DESC, created_at DESC LIMIT ? OFFSET ?";
    $params[] = $limit;
    $params[] = $offset;
    $arsip_list = executeQuery($query, $params);
    
    // Get years for filter
    $years = executeQuery("SELECT DISTINCT tahun_publikasi FROM arsip ORDER BY tahun_publikasi DESC");
}

// Get all active pengelola for form
$pengelola_list = executeQuery("SELECT id, nama_lengkap, jabatan FROM pengelola WHERE is_active = true ORDER BY nama_lengkap");
?>

<?php if ($action === 'list'): ?>

<!-- List View -->
<div class="space-y-6">
    
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Arsip Penelitian & Pengabdian</h2>
            <p class="text-gray-600 mt-1">Kelola dokumen penelitian dan pengabdian masyarakat</p>
        </div>
        <a href="?action=add" class="btn btn-primary">
            <i class="fas fa-plus mr-2"></i>Tambah Arsip
        </a>
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
            <select name="filter_tahun" class="form-input md:w-32">
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
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <?php if ($arsip_list && count($arsip_list) > 0): ?>
        <div class="overflow-x-auto">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Judul & Penulis</th>
                        <th class="w-32">Kategori</th>
                        <th class="w-24 text-center">Tahun</th>
                        <th class="w-40">Penerbit</th>
                        <th class="w-24 text-center">Download</th>
                        <th class="w-24 text-center">Status</th>
                        <th class="w-32 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($arsip_list as $item): ?>
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
                    <tr>
                        <td>
                            <p class="font-semibold text-gray-800"><?php echo htmlspecialchars($item['judul']); ?></p>
                            <?php if ($authors): ?>
                            <p class="text-sm text-blue-600 mt-1">
                                <i class="fas fa-user mr-1"></i>
                                <?php 
                                $author_names = array_map(function($a) { return $a['nama_lengkap']; }, $authors);
                                echo htmlspecialchars(implode(', ', $author_names));
                                ?>
                            </p>
                            <?php endif; ?>
                            <?php if ($item['keywords']): ?>
                            <p class="text-xs text-gray-500 mt-1">
                                <i class="fas fa-tags mr-1"></i><?php echo htmlspecialchars($item['keywords']); ?>
                            </p>
                            <?php endif; ?>
                            <?php if ($item['is_featured']): ?>
                            <span class="inline-block bg-yellow-100 text-yellow-800 text-xs px-2 py-1 rounded mt-1">
                                <i class="fas fa-star mr-1"></i>Featured
                            </span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <span class="inline-block px-3 py-1 rounded-full text-sm font-semibold <?php echo $item['kategori'] === 'penelitian' ? 'bg-green-100 text-green-800' : 'bg-blue-100 text-blue-800'; ?>">
                                <?php echo ucfirst($item['kategori']); ?>
                            </span>
                        </td>
                        <td class="text-center font-semibold text-gray-700">
                            <?php echo $item['tahun_publikasi']; ?>
                        </td>
                        <td class="text-sm text-gray-600">
                            <?php echo htmlspecialchars($item['penerbit'] ?: '-'); ?>
                        </td>
                        <td class="text-center">
                            <span class="inline-flex items-center gap-1 text-purple-600 font-semibold">
                                <i class="fas fa-download"></i>
                                <?php echo number_format($item['jumlah_download']); ?>
                            </span>
                        </td>
                        <td class="text-center">
                            <span class="inline-block px-3 py-1 rounded-full text-sm font-semibold <?php echo $item['is_active'] ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'; ?>">
                                <?php echo $item['is_active'] ? 'Aktif' : 'Nonaktif'; ?>
                            </span>
                        </td>
                        <td class="text-center">
                            <div class="flex items-center justify-center gap-2">
                                <a href="<?php echo SITE_URL . '/..' . htmlspecialchars($item['file_pdf_path']); ?>" 
                                   target="_blank" 
                                   class="text-green-600 hover:text-green-800" 
                                   title="Lihat PDF">
                                    <i class="fas fa-file-pdf"></i>
                                </a>
                                <a href="?action=edit&id=<?php echo $item['id']; ?>" 
                                   class="text-blue-600 hover:text-blue-800" 
                                   title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <a href="?action=delete&id=<?php echo $item['id']; ?>" 
                                   class="text-red-600 hover:text-red-800 btn-delete" 
                                   title="Hapus">
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
            <a href="?action=add" class="btn btn-primary mt-4">
                <i class="fas fa-plus mr-2"></i>Tambah Arsip Pertama
            </a>
        </div>
        <?php endif; ?>
    </div>
    
</div>

<?php elseif (in_array($action, ['add', 'edit'])): ?>

<!-- Form View -->
<div class="max-w-4xl">
    
    <!-- Header -->
    <div class="mb-6">
        <a href="?action=list" class="text-blue-600 hover:text-blue-800 mb-2 inline-block">
            <i class="fas fa-arrow-left mr-2"></i>Kembali ke List
        </a>
        <h2 class="text-2xl font-bold text-gray-800">
            <?php echo $action === 'add' ? 'Tambah' : 'Edit'; ?> Arsip
        </h2>
    </div>
    
    <!-- Form -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <form method="POST" enctype="multipart/form-data" class="needs-validation">
            
            <!-- Judul -->
            <div class="form-group">
                <label class="form-label" for="judul">Judul <span class="text-red-500">*</span></label>
                <input 
                    type="text" 
                    id="judul" 
                    name="judul" 
                    class="form-input" 
                    value="<?php echo $edit_data ? htmlspecialchars($edit_data['judul']) : ''; ?>"
                    required
                    maxlength="200"
                    placeholder="Judul penelitian/pengabdian"
                >
            </div>
            
            <!-- Kategori & Tahun -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="form-group">
                    <label class="form-label" for="kategori">Kategori <span class="text-red-500">*</span></label>
                    <select id="kategori" name="kategori" class="form-input" required>
                        <option value="penelitian" <?php echo ($edit_data && $edit_data['kategori'] === 'penelitian') ? 'selected' : ''; ?>>Penelitian</option>
                        <option value="pengabdian" <?php echo ($edit_data && $edit_data['kategori'] === 'pengabdian') ? 'selected' : ''; ?>>Pengabdian Masyarakat</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label class="form-label" for="tahun_publikasi">Tahun Publikasi <span class="text-red-500">*</span></label>
                    <input 
                        type="number" 
                        id="tahun_publikasi" 
                        name="tahun_publikasi" 
                        class="form-input"
                        value="<?php echo $edit_data ? $edit_data['tahun_publikasi'] : date('Y'); ?>"
                        required
                        min="1900"
                        max="<?php echo date('Y'); ?>"
                    >
                </div>
            </div>
            
            <!-- Abstrak -->
            <div class="form-group">
                <label class="form-label" for="abstrak">Abstrak</label>
                <textarea 
                    id="abstrak" 
                    name="abstrak" 
                    class="form-input" 
                    rows="5"
                    maxlength="1000"
                    placeholder="Ringkasan singkat penelitian/pengabdian"
                ><?php echo $edit_data ? htmlspecialchars($edit_data['abstrak']) : ''; ?></textarea>
                <p class="text-sm text-gray-500 mt-1">Maksimal 1000 karakter</p>
            </div>
            
            <!-- Penerbit -->
            <div class="form-group">
                <label class="form-label" for="penerbit">Penerbit</label>
                <input 
                    type="text" 
                    id="penerbit" 
                    name="penerbit" 
                    class="form-input" 
                    value="<?php echo $edit_data ? htmlspecialchars($edit_data['penerbit']) : ''; ?>"
                    maxlength="200"
                    placeholder="Contoh: Jurnal Teknik Informatika Vol. 12"
                >
            </div>
            
            <!-- Keywords -->
            <div class="form-group">
                <label class="form-label" for="keywords">Keywords</label>
                <input 
                    type="text" 
                    id="keywords" 
                    name="keywords" 
                    class="form-input" 
                    value="<?php echo $edit_data ? htmlspecialchars($edit_data['keywords']) : ''; ?>"
                    maxlength="200"
                    placeholder="Contoh: Machine Learning, Cybersecurity, Network"
                >
                <p class="text-sm text-gray-500 mt-1">Pisahkan dengan koma</p>
            </div>
            
            <!-- Pengelola/Penulis -->
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
                                <?php echo in_array($pengelola['id'], $edit_pengelola_ids) ? 'checked' : ''; ?>
                            >
                            <div class="ml-3">
                                <p class="font-semibold text-gray-800"><?php echo htmlspecialchars($pengelola['nama_lengkap']); ?></p>
                                <p class="text-sm text-gray-500"><?php echo htmlspecialchars($pengelola['jabatan']); ?></p>
                            </div>
                        </label>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p class="text-gray-500">Tidak ada pengelola aktif</p>
                    <?php endif; ?>
                </div>
                <p class="text-sm text-gray-500 mt-1">Pilih satu atau lebih penulis. Urutan pertama akan menjadi penulis utama.</p>
            </div>
            
            <!-- File PDF -->
            <div class="form-group">
                <label class="form-label" for="file_pdf">
                    File PDF <span class="text-red-500"><?php echo $action === 'add' ? '*' : ''; ?></span>
                </label>
                <input 
                    type="file" 
                    id="file_pdf" 
                    name="file_pdf" 
                    class="form-input" 
                    accept=".pdf"
                    <?php echo $action === 'add' ? 'required' : ''; ?>
                >
                <p class="text-sm text-gray-500 mt-1">Format: PDF. Maksimal 5MB</p>
                
                <?php if ($edit_data && $edit_data['file_pdf_path']): ?>
                <div class="mt-3 p-3 bg-blue-50 rounded-lg">
                    <p class="text-sm text-blue-800">
                        <i class="fas fa-file-pdf mr-2"></i>
                        File saat ini: 
                        <a href="<?php echo SITE_URL . '/..' . htmlspecialchars($edit_data['file_pdf_path']); ?>" 
                           target="_blank" 
                           class="underline hover:text-blue-600">
                            Lihat PDF
                        </a>
                    </p>
                </div>
                <?php endif; ?>
            </div>
            
            <!-- Featured & Active -->
            <div class="form-group space-y-2">
                <label class="flex items-center">
                    <input 
                        type="checkbox" 
                        name="is_featured" 
                        class="w-4 h-4 text-blue-600 mr-2"
                        <?php echo ($edit_data && $edit_data['is_featured']) ? 'checked' : ''; ?>
                    >
                    <span class="text-gray-700">Featured (tampilkan di homepage)</span>
                </label>
                
                <label class="flex items-center">
                    <input 
                        type="checkbox" 
                        name="is_active" 
                        class="w-4 h-4 text-blue-600 mr-2"
                        <?php echo (!$edit_data || $edit_data['is_active']) ? 'checked' : ''; ?>
                    >
                    <span class="text-gray-700">Aktif (tampilkan di halaman publik)</span>
                </label>
            </div>
            
            <!-- Buttons -->
            <div class="flex items-center gap-4 pt-4">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save mr-2"></i>Simpan
                </button>
                <a href="?action=list" class="btn bg-gray-500 text-white hover:bg-gray-600">
                    <i class="fas fa-times mr-2"></i>Batal
                </a>
            </div>
            
        </form>
    </div>
    
</div>

<?php endif; ?>

<?php
// Include admin footer
require_once __DIR__ . '/../includes/admin_footer.php';
?>
