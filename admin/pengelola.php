<?php
/**
 * Admin Pengelola Management
 * File: admin/pengelola.php
 */

// Set page title
$page_title = "Kelola Pengelola";

// Include admin header
require_once __DIR__ . '/../includes/admin_header.php';

// Handle actions
$action = $_GET['action'] ?? 'list';
$id = $_GET['id'] ?? null;

// Handle Delete
if ($action === 'delete' && $id) {
    $pengelola = executeQuerySingle("SELECT * FROM pengelola WHERE id = ?", [$id]);
    
    if ($pengelola) {
        // Check if has arsip relations
        $arsip_count = countRows("SELECT COUNT(*) FROM arsip_pengelola WHERE pengelola_id = ?", [$id]);
        
        if ($arsip_count > 0) {
            setFlashMessage('error', 'Tidak dapat menghapus pengelola yang memiliki arsip. Hapus relasi arsip terlebih dahulu.');
        } else {
            // Delete photo file
            if ($pengelola['foto_path']) {
                deleteFile($pengelola['foto_path']);
            }
            
            // Delete from database
            $result = executeNonQuery("DELETE FROM pengelola WHERE id = ?", [$id]);
            
            if ($result) {
                setFlashMessage('success', 'Pengelola berhasil dihapus');
            } else {
                setFlashMessage('error', 'Gagal menghapus pengelola');
            }
        }
    } else {
        setFlashMessage('error', 'Data tidak ditemukan');
    }
    
    redirect(ADMIN_URL . '/pengelola.php');
}

// Handle Form Submission (Add/Edit)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && in_array($action, ['add', 'edit'])) {
    $nama_lengkap = sanitize($_POST['nama_lengkap'] ?? '');
    $nip_nidn = sanitize($_POST['nip_nidn'] ?? '');
    $jabatan = sanitize($_POST['jabatan'] ?? '');
    $pendidikan_terakhir = sanitize($_POST['pendidikan_terakhir'] ?? '');
    $bidang_keahlian = sanitize($_POST['bidang_keahlian'] ?? '');
    $email = sanitize($_POST['email'] ?? '');
    $no_telepon = sanitize($_POST['no_telepon'] ?? '');
    $urutan_tampil = isset($_POST['urutan_tampil']) ? (int)$_POST['urutan_tampil'] : 99;
    $is_active = isset($_POST['is_active']) ? 1 : 0;
    
    $errors = [];
    
    // Validasi
    if (empty($nama_lengkap)) $errors[] = "Nama lengkap harus diisi";
    if (empty($nip_nidn)) $errors[] = "NIP/NIDN harus diisi";
    if (empty($jabatan)) $errors[] = "Jabatan harus diisi";
    if (empty($email)) $errors[] = "Email harus diisi";
    if ($email && !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Format email tidak valid";
    
    // Check duplicate NIP/NIDN
    if ($action === 'add') {
        $existing = executeQuerySingle("SELECT id FROM pengelola WHERE nip_nidn = ?", [$nip_nidn]);
        if ($existing) {
            $errors[] = "NIP/NIDN sudah terdaftar";
        }
    } elseif ($action === 'edit' && $id) {
        $existing = executeQuerySingle("SELECT id FROM pengelola WHERE nip_nidn = ? AND id != ?", [$nip_nidn, $id]);
        if ($existing) {
            $errors[] = "NIP/NIDN sudah terdaftar";
        }
    }
    
    // Handle photo upload
    $foto_path = '';
    $upload_required = ($action === 'add');
    
    if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
        $upload_result = uploadImage($_FILES['foto'], 'pengelola', 'pengelola');
        
        if ($upload_result['success']) {
            $foto_path = '/pengelola/' . $upload_result['filename'];
            
            // Delete old photo if editing
            if ($action === 'edit' && $id) {
                $old_pengelola = executeQuerySingle("SELECT foto_path FROM pengelola WHERE id = ?", [$id]);
                if ($old_pengelola && $old_pengelola['foto_path']) {
                    deleteFile($old_pengelola['foto_path']);
                }
            }
        } else {
            $errors[] = $upload_result['message'];
        }
    }
    
    // If no errors, save to database
    if (empty($errors)) {
        if ($action === 'add') {
            $query = "INSERT INTO pengelola (nama_lengkap, nip_nidn, jabatan, pendidikan_terakhir, 
                      bidang_keahlian, email, no_telepon, foto_path, urutan_tampil, is_active) 
                      VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $params = [$nama_lengkap, $nip_nidn, $jabatan, $pendidikan_terakhir, 
                      $bidang_keahlian, $email, $no_telepon, $foto_path, $urutan_tampil, $is_active];
            
            $result = executeInsert($query, $params);
            
            if ($result) {
                setFlashMessage('success', 'Pengelola berhasil ditambahkan');
                redirect(ADMIN_URL . '/pengelola.php');
            } else {
                $errors[] = "Gagal menyimpan data";
            }
        } elseif ($action === 'edit' && $id) {
            if ($foto_path) {
                $query = "UPDATE pengelola SET nama_lengkap = ?, nip_nidn = ?, jabatan = ?, 
                          pendidikan_terakhir = ?, bidang_keahlian = ?, email = ?, no_telepon = ?, 
                          foto_path = ?, urutan_tampil = ?, is_active = ? WHERE id = ?";
                $params = [$nama_lengkap, $nip_nidn, $jabatan, $pendidikan_terakhir, 
                          $bidang_keahlian, $email, $no_telepon, $foto_path, $urutan_tampil, $is_active, $id];
            } else {
                $query = "UPDATE pengelola SET nama_lengkap = ?, nip_nidn = ?, jabatan = ?, 
                          pendidikan_terakhir = ?, bidang_keahlian = ?, email = ?, no_telepon = ?, 
                          urutan_tampil = ?, is_active = ? WHERE id = ?";
                $params = [$nama_lengkap, $nip_nidn, $jabatan, $pendidikan_terakhir, 
                          $bidang_keahlian, $email, $no_telepon, $urutan_tampil, $is_active, $id];
            }
            
            $result = executeNonQuery($query, $params);
            
            if ($result !== false) {
                setFlashMessage('success', 'Pengelola berhasil diupdate');
                redirect(ADMIN_URL . '/pengelola.php');
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

// Handle reorder
if ($action === 'reorder' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $order = $_POST['order'] ?? [];
    
    if (!empty($order)) {
        $pdo = beginTransaction();
        try {
            foreach ($order as $index => $pengelola_id) {
                executeNonQuery("UPDATE pengelola SET urutan_tampil = ? WHERE id = ?", [$index + 1, $pengelola_id]);
            }
            commitTransaction($pdo);
            echo json_encode(['success' => true]);
        } catch (Exception $e) {
            rollbackTransaction($pdo);
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
        exit;
    }
}

// Get data for edit
$edit_data = null;
if ($action === 'edit' && $id) {
    $edit_data = executeQuerySingle("SELECT * FROM pengelola WHERE id = ?", [$id]);
    if (!$edit_data) {
        setFlashMessage('error', 'Data tidak ditemukan');
        redirect(ADMIN_URL . '/pengelola.php');
    }
}

// Get list data with search
if ($action === 'list') {
    $search = $_GET['search'] ?? '';
    $filter_jabatan = $_GET['filter_jabatan'] ?? '';
    
    // Build query
    $where = ["1=1"];
    $params = [];
    
    if ($search) {
        $where[] = "(nama_lengkap ILIKE ? OR nip_nidn ILIKE ? OR email ILIKE ? OR bidang_keahlian ILIKE ?)";
        $search_param = '%' . $search . '%';
        $params[] = $search_param;
        $params[] = $search_param;
        $params[] = $search_param;
        $params[] = $search_param;
    }
    
    if ($filter_jabatan) {
        $where[] = "jabatan ILIKE ?";
        $params[] = '%' . $filter_jabatan . '%';
    }
    
    $where_clause = implode(' AND ', $where);
    
    // Get data
    $query = "SELECT * FROM pengelola WHERE " . $where_clause . " ORDER BY urutan_tampil ASC, nama_lengkap ASC";
    $pengelola_list = executeQuery($query, $params);
    
    // Get unique jabatan for filter
    $jabatan_list = executeQuery("SELECT DISTINCT jabatan FROM pengelola WHERE jabatan IS NOT NULL AND jabatan != '' ORDER BY jabatan");
}
?>

<?php if ($action === 'list'): ?>

<!-- List View -->
<div class="space-y-6">
    
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Pengelola Laboratorium</h2>
            <p class="text-gray-600 mt-1">Kelola data tim pengelola laboratorium</p>
        </div>
        <a href="?action=add" class="btn btn-primary">
            <i class="fas fa-plus mr-2"></i>Tambah Pengelola
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
                    placeholder="Cari nama, NIP/NIDN, email, atau keahlian..." 
                    value="<?php echo htmlspecialchars($search); ?>"
                    class="form-input"
                >
            </div>
            
            <!-- Filter Jabatan -->
            <select name="filter_jabatan" class="form-input md:w-64">
                <option value="">Semua Jabatan</option>
                <?php if ($jabatan_list): ?>
                    <?php foreach ($jabatan_list as $jab): ?>
                    <option value="<?php echo htmlspecialchars($jab['jabatan']); ?>" 
                            <?php echo $filter_jabatan === $jab['jabatan'] ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($jab['jabatan']); ?>
                    </option>
                    <?php endforeach; ?>
                <?php endif; ?>
            </select>
            
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-search mr-2"></i>Cari
            </button>
            
            <?php if ($search || $filter_jabatan): ?>
            <a href="?action=list" class="btn bg-gray-500 text-white hover:bg-gray-600">
                <i class="fas fa-times mr-2"></i>Reset
            </a>
            <?php endif; ?>
        </form>
    </div>
    
    <!-- Stats -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="bg-white rounded-lg shadow p-4">
            <p class="text-gray-500 text-sm mb-1">Total Pengelola</p>
            <p class="text-2xl font-bold text-blue-600">
                <?php echo countRows("SELECT COUNT(*) FROM pengelola WHERE is_active = true"); ?>
            </p>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <p class="text-gray-500 text-sm mb-1">Kepala Lab</p>
            <p class="text-2xl font-bold text-purple-600">
                <?php echo countRows("SELECT COUNT(*) FROM pengelola WHERE jabatan ILIKE '%kepala%' AND is_active = true"); ?>
            </p>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <p class="text-gray-500 text-sm mb-1">Teknisi</p>
            <p class="text-2xl font-bold text-green-600">
                <?php echo countRows("SELECT COUNT(*) FROM pengelola WHERE jabatan ILIKE '%teknisi%' AND is_active = true"); ?>
            </p>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <p class="text-gray-500 text-sm mb-1">Peneliti</p>
            <p class="text-2xl font-bold text-orange-600">
                <?php echo countRows("SELECT COUNT(*) FROM pengelola WHERE jabatan ILIKE '%peneliti%' AND is_active = true"); ?>
            </p>
        </div>
    </div>
    
    <!-- Table -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <?php if ($pengelola_list && count($pengelola_list) > 0): ?>
        
        <div class="p-4 border-b bg-gray-50">
            <p class="text-sm text-gray-600">
                <i class="fas fa-info-circle text-blue-500 mr-2"></i>
                Drag & drop baris untuk mengubah urutan tampilan di halaman publik
            </p>
        </div>
        
        <div class="overflow-x-auto">
            <table class="admin-table" id="sortable-table">
                <thead>
                    <tr>
                        <th class="w-12 text-center">
                            <i class="fas fa-grip-vertical text-gray-400"></i>
                        </th>
                        <th class="w-20">Foto</th>
                        <th>Nama & NIP/NIDN</th>
                        <th class="w-48">Jabatan</th>
                        <th class="w-48">Kontak</th>
                        <th class="w-32">Pendidikan</th>
                        <th class="w-24 text-center">Status</th>
                        <th class="w-32 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody id="sortable-body">
                    <?php foreach ($pengelola_list as $item): ?>
                    <tr data-id="<?php echo $item['id']; ?>" class="sortable-row">
                        <td class="text-center cursor-move">
                            <i class="fas fa-grip-vertical text-gray-400"></i>
                        </td>
                        <td>
                            <img 
                                src="<?php echo UPLOAD_URL . htmlspecialchars($item['foto_path']); ?>" 
                                alt="<?php echo htmlspecialchars($item['nama_lengkap']); ?>"
                                class="w-16 h-16 object-cover rounded-full"
                                onerror="this.src='<?php echo ASSETS_URL; ?>/img/no-image.png'"
                            >
                        </td>
                        <td>
                            <p class="font-semibold text-gray-800"><?php echo htmlspecialchars($item['nama_lengkap']); ?></p>
                            <p class="text-sm text-gray-500">NIP/NIDN: <?php echo htmlspecialchars($item['nip_nidn']); ?></p>
                            <?php if ($item['bidang_keahlian']): ?>
                            <p class="text-xs text-blue-600 mt-1">
                                <i class="fas fa-graduation-cap mr-1"></i><?php echo htmlspecialchars($item['bidang_keahlian']); ?>
                            </p>
                            <?php endif; ?>
                        </td>
                        <td>
                            <span class="inline-block px-3 py-1 rounded-full text-sm font-semibold bg-purple-100 text-purple-800">
                                <?php echo htmlspecialchars($item['jabatan']); ?>
                            </span>
                        </td>
                        <td class="text-sm">
                            <div class="space-y-1">
                                <p class="text-gray-600">
                                    <i class="fas fa-envelope text-blue-500 mr-1"></i>
                                    <?php echo htmlspecialchars($item['email']); ?>
                                </p>
                                <?php if ($item['no_telepon']): ?>
                                <p class="text-gray-600">
                                    <i class="fas fa-phone text-green-500 mr-1"></i>
                                    <?php echo htmlspecialchars($item['no_telepon']); ?>
                                </p>
                                <?php endif; ?>
                            </div>
                        </td>
                        <td class="text-center">
                            <span class="text-sm font-semibold text-gray-700">
                                <?php echo htmlspecialchars($item['pendidikan_terakhir']); ?>
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
        
        <?php else: ?>
        <div class="text-center py-12">
            <i class="fas fa-users text-6xl text-gray-300 mb-4"></i>
            <p class="text-gray-500 text-lg">Belum ada pengelola</p>
            <a href="?action=add" class="btn btn-primary mt-4">
                <i class="fas fa-plus mr-2"></i>Tambah Pengelola Pertama
            </a>
        </div>
        <?php endif; ?>
    </div>
    
</div>

<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
<script>
// Initialize Sortable for drag & drop
<?php if ($pengelola_list && count($pengelola_list) > 0): ?>
const tbody = document.getElementById('sortable-body');
const sortable = new Sortable(tbody, {
    animation: 150,
    handle: '.cursor-move',
    ghostClass: 'bg-blue-50',
    onEnd: function(evt) {
        // Get new order
        const rows = tbody.querySelectorAll('.sortable-row');
        const order = Array.from(rows).map(row => row.dataset.id);
        
        // Send to server
        fetch('?action=reorder', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'order=' + JSON.stringify(order)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showToast('Urutan berhasil diupdate', 'success');
            } else {
                showToast('Gagal update urutan: ' + data.message, 'error');
            }
        })
        .catch(error => {
            showToast('Error: ' + error.message, 'error');
        });
    }
});
<?php endif; ?>
</script>

<?php elseif (in_array($action, ['add', 'edit'])): ?>

<!-- Form View -->
<div class="max-w-4xl">
    
    <!-- Header -->
    <div class="mb-6">
        <a href="?action=list" class="text-blue-600 hover:text-blue-800 mb-2 inline-block">
            <i class="fas fa-arrow-left mr-2"></i>Kembali ke List
        </a>
        <h2 class="text-2xl font-bold text-gray-800">
            <?php echo $action === 'add' ? 'Tambah' : 'Edit'; ?> Pengelola
        </h2>
    </div>
    
    <!-- Form -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <form method="POST" enctype="multipart/form-data" class="needs-validation">
            
            <!-- Nama Lengkap -->
            <div class="form-group">
                <label class="form-label" for="nama_lengkap">Nama Lengkap <span class="text-red-500">*</span></label>
                <input 
                    type="text" 
                    id="nama_lengkap" 
                    name="nama_lengkap" 
                    class="form-input" 
                    value="<?php echo $edit_data ? htmlspecialchars($edit_data['nama_lengkap']) : ''; ?>"
                    required
                    maxlength="100"
                    placeholder="Contoh: Dr. Ahmad Santoso, S.Kom., M.Kom"
                >
            </div>
            
            <!-- NIP/NIDN & Jabatan -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="form-group">
                    <label class="form-label" for="nip_nidn">NIP/NIDN <span class="text-red-500">*</span></label>
                    <input 
                        type="text" 
                        id="nip_nidn" 
                        name="nip_nidn" 
                        class="form-input" 
                        value="<?php echo $edit_data ? htmlspecialchars($edit_data['nip_nidn']) : ''; ?>"
                        required
                        maxlength="20"
                        placeholder="Contoh: 0123456789"
                    >
                </div>
                
                <div class="form-group">
                    <label class="form-label" for="jabatan">Jabatan <span class="text-red-500">*</span></label>
                    <input 
                        type="text" 
                        id="jabatan" 
                        name="jabatan" 
                        class="form-input" 
                        value="<?php echo $edit_data ? htmlspecialchars($edit_data['jabatan']) : ''; ?>"
                        required
                        maxlength="50"
                        placeholder="Contoh: Kepala Laboratorium"
                        list="jabatan-list"
                    >
                    <datalist id="jabatan-list">
                        <option value="Kepala Laboratorium">
                        <option value="Teknisi Senior">
                        <option value="Teknisi Junior">
                        <option value="Peneliti">
                        <option value="Asisten Lab">
                    </datalist>
                </div>
            </div>
            
            <!-- Pendidikan & Bidang Keahlian -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="form-group">
                    <label class="form-label" for="pendidikan_terakhir">Pendidikan Terakhir</label>
                    <select id="pendidikan_terakhir" name="pendidikan_terakhir" class="form-input">
                        <option value="">Pilih Pendidikan</option>
                        <option value="D3" <?php echo ($edit_data && $edit_data['pendidikan_terakhir'] === 'D3') ? 'selected' : ''; ?>>D3</option>
                        <option value="S1" <?php echo ($edit_data && $edit_data['pendidikan_terakhir'] === 'S1') ? 'selected' : ''; ?>>S1</option>
                        <option value="S2" <?php echo ($edit_data && $edit_data['pendidikan_terakhir'] === 'S2') ? 'selected' : ''; ?>>S2</option>
                        <option value="S3" <?php echo ($edit_data && $edit_data['pendidikan_terakhir'] === 'S3') ? 'selected' : ''; ?>>S3</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label class="form-label" for="bidang_keahlian">Bidang Keahlian</label>
                    <input 
                        type="text" 
                        id="bidang_keahlian" 
                        name="bidang_keahlian" 
                        class="form-input" 
                        value="<?php echo $edit_data ? htmlspecialchars($edit_data['bidang_keahlian']) : ''; ?>"
                        maxlength="100"
                        placeholder="Contoh: Computer Networks & Security"
                    >
                </div>
            </div>
            
            <!-- Email & No Telepon -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="form-group">
                    <label class="form-label" for="email">Email <span class="text-red-500">*</span></label>
                    <input 
                        type="email" 
                        id="email" 
                        name="email" 
                        class="form-input" 
                        value="<?php echo $edit_data ? htmlspecialchars($edit_data['email']) : ''; ?>"
                        required
                        maxlength="100"
                        placeholder="example@lab.ncs.ac.id"
                    >
                </div>
                
                <div class="form-group">
                    <label class="form-label" for="no_telepon">No. Telepon</label>
                    <input 
                        type="tel" 
                        id="no_telepon" 
                        name="no_telepon" 
                        class="form-input" 
                        value="<?php echo ($edit_data && isset($edit_data['no_telepon']))? htmlspecialchars($edit_data['no_telepon']) : ''; ?>"
                        maxlength="20"
                        placeholder="08123456789"
                    >
                </div>
            </div>
            
            <!-- Foto -->
            <div class="form-group">
                <label class="form-label" for="foto">
                    Foto Profil <span class="text-red-500"><?php echo $action === 'add' ? '*' : ''; ?></span>
                </label>
                <input 
                    type="file" 
                    id="foto" 
                    name="foto" 
                    class="form-input" 
                    accept="image/*"
                    <?php echo $action === 'add' ? 'required' : ''; ?>
                    data-preview="#preview-foto"
                >
                <p class="text-sm text-gray-500 mt-1">Format: JPG, PNG. Maksimal 2MB. Ukuran ideal: 400x400px</p>
                
                <!-- Image Preview -->
                <div class="mt-3">
                    <img 
                        id="preview-foto" 
                        src="<?php echo ($edit_data && isset($edit_data['foto_path'])) ? UPLOAD_URL . htmlspecialchars($edit_data['foto_path']) : ''; ?>" 
                        alt="Preview" 
                        class="image-preview rounded-full <?php echo $edit_data ? '' : 'hidden'; ?>"
                        style="max-width: 200px; max-height: 200px;"
                    >
                </div>
            </div>
            
            <!-- Urutan Tampil -->
            <div class="form-group">
                <label class="form-label" for="urutan_tampil">Urutan Tampil</label>
                <input 
                    type="number" 
                    id="urutan_tampil" 
                    name="urutan_tampil" 
                    class="form-input" 
                    value="<?php echo $edit_data ? $edit_data['urutan_tampil'] : 99; ?>"
                    min="1"
                    max="999"
                >
                <p class="text-sm text-gray-500 mt-1">Angka lebih kecil akan ditampilkan lebih dulu. Default: 99</p>
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
