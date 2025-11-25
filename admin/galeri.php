<?php
/**
 * Admin Galeri Management
 * File: admin/galeri.php
 */

// Set page title
$page_title = "Kelola Galeri & Agenda";

// Include admin header
require_once __DIR__ . '/../includes/admin_header.php';

// Handle actions
$action = $_GET['action'] ?? 'list';
$id = $_GET['id'] ?? null;

// Handle Delete
if ($action === 'delete' && $id) {
    $galeri = executeQuerySingle("SELECT * FROM galeri WHERE id = ?", [$id]);
    
    if ($galeri) {
        // Delete image file
        if ($galeri['gambar_path']) {
            $file_path = $galeri['gambar_path'];
            deleteFile($file_path);
        }
        
        // Delete from database
        $result = executeNonQuery("DELETE FROM galeri WHERE id = ?", [$id]);
        
        if ($result) {
            setFlashMessage('success', 'Galeri berhasil dihapus');
        } else {
            setFlashMessage('error', 'Gagal menghapus galeri');
        }
    } else {
        setFlashMessage('error', 'Data tidak ditemukan');
    }
    
    redirect(ADMIN_URL . '/galeri.php');
}

// Handle Form Submission (Add/Edit)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && in_array($action, ['add', 'edit'])) {
    $judul = sanitize($_POST['judul'] ?? '');
    $deskripsi = sanitize($_POST['deskripsi'] ?? '');
    $tipe = sanitize($_POST['tipe'] ?? 'kegiatan');
    $tanggal_kegiatan = $_POST['tanggal_kegiatan'] ?? '';
    $lokasi = sanitize($_POST['lokasi'] ?? '');
    $is_featured = isset($_POST['is_featured']) ? 1 : 0;
    
    $errors = [];
    
    // Validasi
    if (empty($judul)) $errors[] = "Judul harus diisi";
    if (empty($tanggal_kegiatan)) $errors[] = "Tanggal kegiatan harus diisi";
    if (!in_array($tipe, ['agenda', 'kegiatan'])) $errors[] = "Tipe tidak valid";
    
    // Handle image upload
    $gambar_path = '';
    $upload_required = ($action === 'add');
    
    if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] === UPLOAD_ERR_OK) {
        $upload_result = uploadImage($_FILES['gambar'], 'galeri', 'galeri');
        
        if ($upload_result['success']) {
            $gambar_path = '/galeri/' . $upload_result['filename'];
            
            // Delete old image if editing
            if ($action === 'edit' && $id) {
                $old_galeri = executeQuerySingle("SELECT gambar_path FROM galeri WHERE id = ?", [$id]);
                if ($old_galeri && $old_galeri['gambar_path']) {
                    deleteFile($old_galeri['gambar_path']);
                }
            }
        } else {
            $errors[] = $upload_result['message'];
        }
    }
    
    // If no errors, save to database
    if (empty($errors)) {
        if ($action === 'add') {
            $query = "INSERT INTO \"galeri\" (judul, deskripsi, gambar_path, tipe, tanggal_kegiatan, lokasi, is_featured) 
                      VALUES (?, ?, ?, ?, ?, ?, ?)";
            $params = [$judul, $deskripsi, $gambar_path, $tipe, $tanggal_kegiatan, $lokasi, $is_featured];
            
            $result = executeInsert($query, $params);
            
            if ($result) {
                setFlashMessage('success', 'Galeri berhasil ditambahkan');
                redirect(ADMIN_URL . '/galeri.php');
            } else {
                $errors[] = "Gagal menyimpan data";
            }
        } elseif ($action === 'edit' && $id) {
            if ($gambar_path) {
                $query = "UPDATE \"galeri\" SET judul = ?, deskripsi = ?, gambar_path = ?, tipe = ?, 
                          tanggal_kegiatan = ?, lokasi = ?, is_featured = ? WHERE id = ?";
                $params = [$judul, $deskripsi, $gambar_path, $tipe, $tanggal_kegiatan, $lokasi, $is_featured, $id];
            } else {
                $query = "UPDATE \"galeri\" SET judul = ?, deskripsi = ?, tipe = ?, 
                          tanggal_kegiatan = ?, lokasi = ?, is_featured = ? WHERE id = ?";
                $params = [$judul, $deskripsi, $tipe, $tanggal_kegiatan, $lokasi, $is_featured, $id];
            }
            
            $result = executeNonQuery($query, $params);
            
            if ($result !== false) {
                setFlashMessage('success', 'Galeri berhasil diupdate');
                redirect(ADMIN_URL . '/galeri.php');
            } else {
                $errors[] = "Gagal update data";
            }
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
if ($action === 'edit' && $id) {
    $edit_data = executeQuerySingle("SELECT * FROM galeri WHERE id = ?", [$id]);
    if (!$edit_data) {
        setFlashMessage('error', 'Data tidak ditemukan');
        redirect(ADMIN_URL . '/galeri.php');
    }
}

// Get list data with pagination and filter
if ($action === 'list') {
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $filter_tipe = $_GET['filter_tipe'] ?? '';
    $search = $_GET['search'] ?? '';
    
    $limit = ITEMS_PER_PAGE;
    $offset = ($page - 1) * $limit;
    
    // Build query
    $where = ["is_active = true"];
    $params = [];
    
    if ($filter_tipe && in_array($filter_tipe, ['agenda', 'kegiatan'])) {
        $where[] = "tipe = ?";
        $params[] = $filter_tipe;
    }
    
    if ($search) {
        $where[] = "(judul ILIKE ? OR deskripsi ILIKE ? OR lokasi ILIKE ?)";
        $search_param = '%' . $search . '%';
        $params[] = $search_param;
        $params[] = $search_param;
        $params[] = $search_param;
    }
    
    $where_clause = implode(' AND ', $where);
    
    // Count total
    $total = countRows("SELECT COUNT(*) FROM galeri WHERE " . $where_clause, $params);
    $total_pages = ceil($total / $limit);
    
    // Get data
    $query = "SELECT * FROM galeri WHERE " . $where_clause . " ORDER BY tanggal_kegiatan DESC, created_at DESC LIMIT ? OFFSET ?";
    $params[] = $limit;
    $params[] = $offset;
    $galeri_list = executeQuery($query, $params);
}
?>

<?php if ($action === 'list'): ?>

<!-- List View -->
<div class="space-y-6">
    
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Galeri & Agenda</h2>
            <p class="text-gray-600 mt-1">Kelola foto kegiatan dan agenda laboratorium</p>
        </div>
        <a href="?action=add" class="btn btn-primary">
            <i class="fas fa-plus mr-2"></i>Tambah Galeri
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
                    placeholder="Cari judul, deskripsi, atau lokasi..." 
                    value="<?php echo htmlspecialchars($search); ?>"
                    class="form-input"
                >
            </div>
            
            <!-- Filter Tipe -->
            <select name="filter_tipe" class="form-input md:w-48" onchange="this.form.submit()">
                <option value="">Semua Tipe</option>
                <option value="agenda" <?php echo $filter_tipe === 'agenda' ? 'selected' : ''; ?>>Agenda</option>
                <option value="kegiatan" <?php echo $filter_tipe === 'kegiatan' ? 'selected' : ''; ?>>Kegiatan</option>
            </select>
            
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-search mr-2"></i>Cari
            </button>
            
            <?php if ($search || $filter_tipe): ?>
            <a href="?action=list" class="btn bg-gray-500 text-white hover:bg-gray-600">
                <i class="fas fa-times mr-2"></i>Reset
            </a>
            <?php endif; ?>
        </form>
    </div>
    
    <!-- Stats -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="bg-white rounded-lg shadow p-4">
            <p class="text-gray-500 text-sm mb-1">Total Galeri</p>
            <p class="text-2xl font-bold text-blue-600"><?php echo $total; ?></p>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <p class="text-gray-500 text-sm mb-1">Agenda</p>
            <p class="text-2xl font-bold text-green-600">
                <?php echo countRows("SELECT COUNT(*) FROM galeri WHERE tipe = 'agenda' AND is_active = true"); ?>
            </p>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <p class="text-gray-500 text-sm mb-1">Kegiatan</p>
            <p class="text-2xl font-bold text-orange-600">
                <?php echo countRows("SELECT COUNT(*) FROM galeri WHERE tipe = 'kegiatan' AND is_active = true"); ?>
            </p>
        </div>
    </div>
    
    <!-- Table -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <?php if ($galeri_list && count($galeri_list) > 0): ?>
        <div class="overflow-x-auto">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th class="w-20">Gambar</th>
                        <th>Judul</th>
                        <th class="w-32">Tipe</th>
                        <th class="w-40">Tanggal</th>
                        <th class="w-48">Lokasi</th>
                        <th class="w-32 text-center">Status</th>
                        <th class="w-40 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($galeri_list as $item): ?>
                    <tr>
                        <td>
                            <img 
                                src="<?php echo UPLOAD_URL . htmlspecialchars($item['gambar_path']); ?>" 
                                alt="<?php echo htmlspecialchars($item['judul']); ?>"
                                class="w-16 h-16 object-cover rounded"
                                onerror="this.src='<?php echo ASSETS_URL; ?>/img/no-image.png'"
                            >
                        </td>
                        <td>
                            <p class="font-semibold text-gray-800"><?php echo htmlspecialchars($item['judul']); ?></p>
                            <?php if ($item['is_featured']): ?>
                            <span class="inline-block bg-yellow-100 text-yellow-800 text-xs px-2 py-1 rounded mt-1">
                                <i class="fas fa-star mr-1"></i>Featured
                            </span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <span class="inline-block px-3 py-1 rounded-full text-sm font-semibold <?php echo $item['tipe'] === 'agenda' ? 'bg-green-100 text-green-800' : 'bg-blue-100 text-blue-800'; ?>">
                                <?php echo ucfirst($item['tipe']); ?>
                            </span>
                        </td>
                        <td class="text-sm text-gray-600">
                            <?php echo formatDateIndo($item['tanggal_kegiatan']); ?>
                        </td>
                        <td class="text-sm text-gray-600">
                            <?php echo htmlspecialchars($item['lokasi'] ?: '-'); ?>
                        </td>
                        <td class="text-center">
                            <span class="inline-block px-3 py-1 rounded-full text-sm font-semibold <?php echo $item['is_active'] ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'; ?>">
                                <?php echo $item['is_active'] ? 'Aktif' : 'Nonaktif'; ?>
                            </span>
                        </td>
                        <td class="text-center">
                            <div class="flex items-center justify-center gap-2">
                                <a href="?action=edit&id=<?php echo $item['id']; ?>" class="text-blue-600 hover:text-blue-800" title="Edit">
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
            if ($filter_tipe) $base_url .= '&filter_tipe=' . $filter_tipe;
            if ($search) $base_url .= '&search=' . urlencode($search);
            echo createPagination($page, $total_pages, $base_url); 
            ?>
        </div>
        <?php endif; ?>
        
        <?php else: ?>
        <div class="text-center py-12">
            <i class="fas fa-images text-6xl text-gray-300 mb-4"></i>
            <p class="text-gray-500 text-lg">Belum ada galeri</p>
            <a href="?action=add" class="btn btn-primary mt-4">
                <i class="fas fa-plus mr-2"></i>Tambah Galeri Pertama
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
            <?php echo $action === 'add' ? 'Tambah' : 'Edit'; ?> Galeri
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
                >
            </div>
            
            <!-- Deskripsi -->
            <div class="form-group">
                <label class="form-label" for="deskripsi">Deskripsi</label>
                <textarea 
                    id="deskripsi" 
                    name="deskripsi" 
                    class="form-input" 
                    rows="4"
                    maxlength="500"
                ><?php echo $edit_data ? htmlspecialchars($edit_data['deskripsi']) : ''; ?></textarea>
                <p class="text-sm text-gray-500 mt-1">Maksimal 500 karakter</p>
            </div>
            
            <!-- Tipe & Tanggal -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="form-group">
                    <label class="form-label" for="tipe">Tipe <span class="text-red-500">*</span></label>
                    <select id="tipe" name="tipe" class="form-input" required>
                        <option value="kegiatan" <?php echo ($edit_data && $edit_data['tipe'] === 'kegiatan') ? 'selected' : ''; ?>>Kegiatan (Sudah Lewat)</option>
                        <option value="agenda" <?php echo ($edit_data && $edit_data['tipe'] === 'agenda') ? 'selected' : ''; ?>>Agenda (Akan Datang)</option>
                    </select>
                    <p class="text-sm text-gray-500 mt-1">Pilih agenda untuk event yang akan datang</p>
                </div>
                
                <div class="form-group">
                    <label class="form-label" for="tanggal_kegiatan">Tanggal Kegiatan <span class="text-red-500">*</span></label>
                    <input 
                        type="date" 
                        id="tanggal_kegiatan" 
                        name="tanggal_kegiatan" 
                        class="form-input"
                        value="<?php echo $edit_data ? $edit_data['tanggal_kegiatan'] : ''; ?>"
                        required
                    >
                </div>
            </div>
            
            <!-- Lokasi -->
            <div class="form-group">
                <label class="form-label" for="lokasi">Lokasi</label>
                <input 
                    type="text" 
                    id="lokasi" 
                    name="lokasi" 
                    class="form-input" 
                    value="<?php echo $edit_data ? htmlspecialchars($edit_data['lokasi']) : ''; ?>"
                    maxlength="200"
                    placeholder="Contoh: Lab NCS Lantai 3"
                >
            </div>
            
            <!-- Gambar -->
            <div class="form-group">
                <label class="form-label" for="gambar">
                    Gambar <span class="text-red-500"><?php echo $action === 'add' ? '*' : ''; ?></span>
                </label>
                <input 
                    type="file" 
                    id="gambar" 
                    name="gambar" 
                    class="form-input" 
                    accept="image/*"
                    <?php echo $action === 'add' ? 'required' : ''; ?>
                    data-preview="#preview-image"
                >
                <p class="text-sm text-gray-500 mt-1">Format: JPG, PNG, GIF. Maksimal 2MB</p>
                
                <!-- Image Preview -->
                <div class="mt-3">
                    <img 
                        id="preview-image" 
                        src="<?php echo $edit_data ? UPLOAD_URL . htmlspecialchars($edit_data['gambar_path']) : ''; ?>" 
                        alt="Preview" 
                        class="image-preview <?php echo $edit_data ? '' : 'hidden'; ?>"
                    >
                </div>
            </div>
            
            <!-- Featured -->
            <div class="form-group">
                <label class="flex items-center">
                    <input 
                        type="checkbox" 
                        name="is_featured" 
                        class="w-4 h-4 text-blue-600 mr-2"
                        <?php echo ($edit_data && $edit_data['is_featured']) ? 'checked' : ''; ?>
                    >
                    <span class="text-gray-700">Tampilkan di homepage (featured)</span>
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
