<?php
/**
 * Admin Sarana Management
 * File: admin/sarana.php
 */

// Set page title
$page_title = "Kelola Sarana & Prasarana";

// Include admin header
require_once __DIR__ . '/../includes/admin_header.php';

// Handle actions
$action = $_GET['action'] ?? 'list';
$id = $_GET['id'] ?? null;

// Handle Delete
if ($action === 'delete' && $id) {
    $sarana = executeQuerySingle("SELECT * FROM sarana WHERE id = ?", [$id]);
    
    if ($sarana) {
        // Delete from database
        $result = executeNonQuery("DELETE FROM sarana WHERE id = ?", [$id]);
        
        if ($result) {
            setFlashMessage('success', 'Sarana berhasil dihapus');
        } else {
            setFlashMessage('error', 'Gagal menghapus sarana');
        }
    } else {
        setFlashMessage('error', 'Data tidak ditemukan');
    }
    
    redirect(ADMIN_URL . '/sarana.php');
}

// Handle Form Submission (Add/Edit)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && in_array($action, ['add', 'edit'])) {
    $nama_sarana = sanitize($_POST['nama_sarana'] ?? '');
    $deskripsi = sanitize($_POST['deskripsi'] ?? '');
    
    // Handle image upload
    $gambar_url = '';
    if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] === UPLOAD_ERR_OK) {
        $allowed_types = ['image/jpeg', 'image/png', 'image/jpg', 'image/gif'];
        $max_size = 2 * 1024 * 1024; // 2MB
        
        if (in_array($_FILES['gambar']['type'], $allowed_types) && $_FILES['gambar']['size'] <= $max_size) {
            $upload_dir = __DIR__ . '/../uploads/sarana/';
            if (!file_exists($upload_dir)) {
                mkdir($upload_dir, 0755, true);
            }
            
            $file_extension = pathinfo($_FILES['gambar']['name'], PATHINFO_EXTENSION);
            $file_name = uniqid('sarana_') . '.' . $file_extension;
            $upload_path = $upload_dir . $file_name;
            
            if (move_uploaded_file($_FILES['gambar']['tmp_name'], $upload_path)) {
                $gambar_url = '/uploads/sarana/' . $file_name;
            }
        } else {
            $errors[] = "Format gambar tidak valid atau ukuran terlalu besar (max 2MB)";
        }
    } elseif ($action === 'edit' && $edit_data) {
        // Keep existing image if no new upload
        $gambar_url = $edit_data['gambar'];
    }

    $spesifikasi = sanitize($_POST['spesifikasi'] ?? '');
    $jumlah = isset($_POST['jumlah']) ? (int)$_POST['jumlah'] : 1;
    $kondisi = sanitize($_POST['kondisi'] ?? 'Baik');
    $is_active = isset($_POST['is_active']) ? 1 : 0;
    
    $errors = [];
    
    // Validasi
    if (empty($nama_sarana)) $errors[] = "Nama sarana harus diisi";
    if ($jumlah < 1) $errors[] = "Jumlah minimal 1";
    if (!in_array($kondisi, ['Baik', 'Rusak Ringan', 'Rusak Berat'])) $errors[] = "Kondisi tidak valid";
    
    // If no errors, save to database
    if (empty($errors)) {
        if ($action === 'add') {
            $query = "INSERT INTO sarana (nama_sarana, gambar, deskripsi, spesifikasi, jumlah, kondisi, is_active) 
                      VALUES (?, ?, ?, ?, ?, ?, ?)";
            $params = [$nama_sarana, $gambar_url, $deskripsi, $spesifikasi, $jumlah, $kondisi, 
                    $is_active];
            
            $result = executeInsert($query, $params);
            
            if ($result) {
                setFlashMessage('success', 'Sarana berhasil ditambahkan');
                redirect(ADMIN_URL . '/sarana.php');
            } else {
                $errors[] = "Gagal menyimpan data";
            }
        } elseif ($action === 'edit' && $id) {
            $query = "UPDATE sarana SET nama_sarana = ?, gambar = ?, deskripsi = ?, spesifikasi = ?, 
                      jumlah = ?, kondisi = ?, is_active = ? WHERE id = ?";
            $params = [$nama_sarana, $gambar_url, $deskripsi, $spesifikasi, $jumlah, $kondisi, 
                    $is_active, $id];
            
            $result = executeNonQuery($query, $params);
            
            if ($result !== false) {
                setFlashMessage('success', 'Sarana berhasil diupdate');
                redirect(ADMIN_URL . '/sarana.php');
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
    $edit_data = executeQuerySingle("SELECT * FROM sarana WHERE id = ?", [$id]);
    if (!$edit_data) {
        setFlashMessage('error', 'Data tidak ditemukan');
        redirect(ADMIN_URL . '/sarana.php');
    }
}

// Get list data with pagination and filter
if ($action === 'list') {
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $filter_kondisi = $_GET['filter_kondisi'] ?? '';
    $search = $_GET['search'] ?? '';
    
    $limit = ITEMS_PER_PAGE;
    $offset = ($page - 1) * $limit;
    
    // Build query
    $where = ["1=1"];
    $params = [];
    
    if ($filter_kondisi && in_array($filter_kondisi, ['Baik', 'Rusak Ringan', 'Rusak Berat'])) {
        $where[] = "kondisi = ?";
        $params[] = $filter_kondisi;
    }
    
    if ($search) {
        $where[] = "(nama_sarana ILIKE ? OR deskripsi ILIKE ? OR spesifikasi ILIKE ? OR lokasi ILIKE ?)";
        $search_param = '%' . $search . '%';
        $params[] = $search_param;
        $params[] = $search_param;
        $params[] = $search_param;
        $params[] = $search_param;
    }
    
    $where_clause = implode(' AND ', $where);
    
    // Count total
    $total = countRows("SELECT COUNT(*) FROM sarana WHERE " . $where_clause, $params);
    $total_pages = ceil($total / $limit);
    
    // Get data
    $query = "SELECT * FROM sarana WHERE " . $where_clause . " ORDER BY nama_sarana ASC LIMIT ? OFFSET ?";
    $params[] = $limit;
    $params[] = $offset;
    $sarana_list = executeQuery($query, $params);
}
?>

<?php if ($action === 'list'): ?>

<!-- List View -->
<div class="space-y-6">
    
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Sarana & Prasarana</h2>
            <p class="text-gray-600 mt-1">Kelola inventaris sarana dan prasarana laboratorium</p>
        </div>
        <a href="?action=add" class="btn btn-primary">
            <i class="fas fa-plus mr-2"></i>Tambah Sarana
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
                    placeholder="Cari nama, deskripsi, atau spesifikasi..." 
                    value="<?php echo htmlspecialchars($search); ?>"
                    class="form-input"
                >
            </div>
            
            <!-- Filter Kondisi -->
            <select name="filter_kondisi" class="form-input md:w-48">
                <option value="">Semua Kondisi</option>
                <option value="Baik" <?php echo $filter_kondisi === 'Baik' ? 'selected' : ''; ?>>Baik</option>
                <option value="Rusak Ringan" <?php echo $filter_kondisi === 'Rusak Ringan' ? 'selected' : ''; ?>>Rusak Ringan</option>
                <option value="Rusak Berat" <?php echo $filter_kondisi === 'Rusak Berat' ? 'selected' : ''; ?>>Rusak Berat</option>
            </select>
            
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-search mr-2"></i>Cari
            </button>
            
            <?php if ($search || $filter_kondisi): ?>
            <a href="?action=list" class="btn bg-gray-500 text-white hover:bg-gray-600">
                <i class="fas fa-times mr-2"></i>Reset
            </a>
            <?php endif; ?>
        </form>
    </div>
    
    <!-- Stats -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="bg-white rounded-lg shadow p-4">
            <p class="text-gray-500 text-sm mb-1">Total Item</p>
            <p class="text-2xl font-bold text-blue-600">
                <?php echo countRows("SELECT COUNT(*) FROM sarana WHERE is_active = true"); ?>
            </p>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <p class="text-gray-500 text-sm mb-1">Kondisi Baik</p>
            <p class="text-2xl font-bold text-green-600">
                <?php echo countRows("SELECT COUNT(*) FROM sarana WHERE kondisi = 'Baik' AND is_active = true"); ?>
            </p>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <p class="text-gray-500 text-sm mb-1">Rusak Ringan</p>
            <p class="text-2xl font-bold text-yellow-600">
                <?php echo countRows("SELECT COUNT(*) FROM sarana WHERE kondisi = 'Rusak Ringan' AND is_active = true"); ?>
            </p>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <p class="text-gray-500 text-sm mb-1">Rusak Berat</p>
            <p class="text-2xl font-bold text-red-600">
                <?php echo countRows("SELECT COUNT(*) FROM sarana WHERE kondisi = 'Rusak Berat' AND is_active = true"); ?>
            </p>
        </div>
    </div>
    
    <!-- Table -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <?php if ($sarana_list && count($sarana_list) > 0): ?>
        <div class="overflow-x-auto">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Nama Sarana</th>
                        <th class="w-32 text-center">Gambar</th>
                        <th class="w-64">Spesifikasi</th>
                        <th class="w-24 text-center">Jumlah</th>
                        <th class="w-32">Kondisi</th>
                        <th class="w-32 text-center">Status</th>
                        <th class="w-32 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($sarana_list as $item): ?>
                    <tr>
                        <td>
                            <p class="font-semibold text-gray-800"><?php echo htmlspecialchars($item['nama_sarana']); ?></p>
                            <?php if ($item['deskripsi']): ?>
                            <p class="text-sm text-gray-500 mt-1 line-clamp-2"><?php echo htmlspecialchars($item['deskripsi']); ?></p>
                            <?php endif; ?>
                        </td>
                       <td class="text-center">
                            <?php if (!empty($item['gambar'])): ?>
                                <?php
                                // Buat full URL untuk gambar
                                $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http";
                                $host = $_SERVER['HTTP_HOST'];
                                $script_path = dirname($_SERVER['SCRIPT_NAME']); // /labkom/admin
                                $base_path = dirname($script_path); // /labkom
                                $image_url = $protocol . '://' . $host . $base_path . $item['gambar'];
                                ?>
                                <img src="<?php echo htmlspecialchars($image_url); ?>" 
                                    alt="<?php echo htmlspecialchars($item['nama_sarana']); ?>" 
                                    class="w-16 h-16 object-cover rounded mx-auto"
                                    onerror="this.parentElement.innerHTML='<div class=\'w-16 h-16 bg-gray-200 rounded mx-auto flex items-center justify-center\'><i class=\'fas fa-image text-gray-400\'></i></div>';">
                            <?php else: ?>
                                <div class="w-16 h-16 bg-gray-200 rounded mx-auto flex items-center justify-center">
                                    <i class="fas fa-image text-gray-400 text-xl"></i>
                                </div>
                            <?php endif; ?>
                        </td>
                        <td class="text-sm text-gray-600">
                            <?php echo $item['spesifikasi'] ? htmlspecialchars($item['spesifikasi']) : '-'; ?>
                        </td>
                        <td class="text-center">
                            <span class="inline-block px-3 py-1 rounded-full bg-blue-100 text-blue-800 font-semibold">
                                <?php echo $item['jumlah']; ?>
                            </span>
                        </td>
                        <td>
                            <span class="inline-block px-3 py-1 rounded-full text-sm font-semibold 
                                <?php 
                                if ($item['kondisi'] === 'Baik') echo 'bg-green-100 text-green-800';
                                elseif ($item['kondisi'] === 'Rusak Ringan') echo 'bg-yellow-100 text-yellow-800';
                                else echo 'bg-red-100 text-red-800';
                                ?>">
                                <?php echo htmlspecialchars($item['kondisi']); ?>
                            </span>
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
            if ($filter_kondisi) $base_url .= '&filter_kondisi=' . urlencode($filter_kondisi);
            if ($search) $base_url .= '&search=' . urlencode($search);
            echo createPagination($page, $total_pages, $base_url); 
            ?>
        </div>
        <?php endif; ?>
        
        <?php else: ?>
        <div class="text-center py-12">
            <i class="fas fa-box text-6xl text-gray-300 mb-4"></i>
            <p class="text-gray-500 text-lg">Belum ada sarana</p>
            <a href="?action=add" class="btn btn-primary mt-4">
                <i class="fas fa-plus mr-2"></i>Tambah Sarana Pertama
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
            <?php echo $action === 'add' ? 'Tambah' : 'Edit'; ?> Sarana
        </h2>
    </div>
    
    <!-- Form -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <form method="POST" enctype="multipart/form-data" class="needs-validation">
    
                <!-- Nama Sarana -->
                <div class="form-group">
                <label class="form-label" for="nama_sarana">Nama Sarana <span class="text-red-500">*</span></label>
                <input 
                    type="text" 
                    id="nama_sarana" 
                    name="nama_sarana" 
                    class="form-input" 
                    value="<?php echo $edit_data ? htmlspecialchars($edit_data['nama_sarana']) : ''; ?>"
                    required
                    maxlength="100"
                    placeholder="Contoh: Server Rack 42U"
                >
                </div>
    
                <!-- Gambar -->
                <div class="form-group">
                    <label class="form-label" for="gambar">Gambar Sarana</label>
                    <?php if ($edit_data && !empty($edit_data['gambar'])): ?>
                    <div class="mb-2">
                        <?php
                        // Buat full URL untuk gambar
                        $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http";
                        $host = $_SERVER['HTTP_HOST'];
                        
                        // Ambil base path dari URL saat ini
                        $script_path = dirname($_SERVER['SCRIPT_NAME']); // Misal: /labkom/admin
                        $base_path = dirname($script_path); // Misal: /labkom
                        
                        $image_url = $protocol . '://' . $host . $base_path . $edit_data['gambar'];
                        ?>
                        <img src="<?php echo htmlspecialchars($image_url); ?>" 
                            alt="Preview" 
                            class="w-32 h-32 object-cover rounded border"
                            onerror="this.src='data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 width=%22100%22 height=%22100%22><rect fill=%22%23ddd%22 width=%22100%22 height=%22100%22/><text x=%2250%25%22 y=%2250%25%22 text-anchor=%22middle%22 dy=%22.3em%22 fill=%22%23999%22>No Image</text></svg>';">
                    </div>
                    <?php endif; ?>
                    <input 
                        type="file" 
                        id="gambar" 
                        name="gambar" 
                        class="form-input" 
                        accept="image/jpeg,image/png,image/jpg,image/gif"
                        onchange="previewImage(this)"
                    >
                    <p class="text-sm text-gray-500 mt-1">Format: JPG, PNG, GIF. Maksimal 2MB</p>
                    <div id="preview-container" class="mt-2"></div>
                </div>
                
                <!-- Deskripsi -->
                <div class="form-group">
                <label class="form-label" for="deskripsi">Deskripsi</label>
                <textarea 
                    id="deskripsi" 
                    name="deskripsi" 
                    class="form-input" 
                    rows="3"
                    maxlength="500"
                    placeholder="Deskripsi singkat tentang sarana"
                ><?php echo $edit_data ? htmlspecialchars($edit_data['deskripsi']) : ''; ?></textarea>
            </div>
            
            <!-- Spesifikasi -->
            <div class="form-group">
                <label class="form-label" for="spesifikasi">Spesifikasi</label>
                <textarea 
                    id="spesifikasi" 
                    name="spesifikasi" 
                    class="form-input" 
                    rows="3"
                    maxlength="500"
                    placeholder="Spesifikasi teknis sarana"
                ><?php echo $edit_data ? htmlspecialchars($edit_data['spesifikasi']) : ''; ?></textarea>
            </div>
            
            <!-- Jumlah & Kondisi -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="form-group">
                    <label class="form-label" for="jumlah">Jumlah <span class="text-red-500">*</span></label>
                    <input 
                        type="number" 
                        id="jumlah" 
                        name="jumlah" 
                        class="form-input" 
                        value="<?php echo $edit_data ? $edit_data['jumlah'] : 1; ?>"
                        required
                        min="1"
                        max="9999"
                    >
                </div>
                
                <div class="form-group">
                    <label class="form-label" for="kondisi">Kondisi <span class="text-red-500">*</span></label>
                    <select id="kondisi" name="kondisi" class="form-input" required>
                        <option value="Baik" <?php echo ($edit_data && $edit_data['kondisi'] === 'Baik') ? 'selected' : ''; ?>>Baik</option>
                        <option value="Rusak Ringan" <?php echo ($edit_data && $edit_data['kondisi'] === 'Rusak Ringan') ? 'selected' : ''; ?>>Rusak Ringan</option>
                        <option value="Rusak Berat" <?php echo ($edit_data && $edit_data['kondisi'] === 'Rusak Berat') ? 'selected' : ''; ?>>Rusak Berat</option>
                    </select>
                </div>
            </div>
            
            <!-- Status Aktif -->
            <div class="form-group">
                <label class="flex items-center">
                    <input 
                        type="checkbox" 
                        name="is_active" 
                        class="w-4 h-4 text-blue-600 mr-2"
                        <?php echo (!$edit_data || $edit_data['is_active']) ? 'checked' : ''; ?>
                    >
                    <span class="text-gray-700">Aktif (tampilkan dalam sistem)</span>
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

<script>
function previewImage(input) {
    const preview = document.getElementById('preview-container');
    preview.innerHTML = '';
    
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            preview.innerHTML = `
                <img src="${e.target.result}" 
                     class="w-32 h-32 object-cover rounded border" 
                     alt="Preview">
            `;
        };
        reader.readAsDataURL(input.files[0]);
    }
}
function formatCurrency(input) {
    // Remove all non-numeric characters
    let value = input.value.replace(/\D/g, '');
    
    // Format with thousand separators
    if (value) {
        value = parseInt(value).toLocaleString('id-ID');
    }
    
    input.value = value;
}
</script>

<?php endif; ?>

<?php
// Include admin footer
require_once __DIR__ . '/../includes/admin_footer.php';
?>
