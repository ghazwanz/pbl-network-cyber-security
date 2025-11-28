<?php
/**
 * Admin Sarana Management
 * File: admin/sarana.php
 */

// Set page title
$page_title = "Kelola Sarana & Prasarana";

// Include admin header
require_once __DIR__ . '/../includes/admin_header.php';

$upload_dir = __DIR__ . '/../uploads/sarana/';
if (!file_exists($upload_dir)) {
    mkdir($upload_dir, 0755, true);
}

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

// Handle Form Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'save') {
    $errors = [];
    $nama_sarana = sanitize($_POST['nama_sarana'] ?? '');
    $deskripsi = sanitize($_POST['deskripsi'] ?? '');
    $id = $_POST['id'] ?? null;
    
    // Handle image upload
    $gambar_url = '';
    if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] === UPLOAD_ERR_OK) {
        $allowed_types = ['image/jpeg', 'image/png', 'image/jpg', 'image/gif', 'image/webp'];
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
                
                // Delete old image if editing
                if ($id) {
                    $old_sarana = executeQuerySingle("SELECT gambar FROM sarana WHERE id = ?", [(int)$id]);
                    if ($old_sarana && !empty($old_sarana['gambar'])) {
                        $old_file = __DIR__ . '/..' . $old_sarana['gambar'];
                        if (file_exists($old_file)) {
                            unlink($old_file);
                        }
                    }
                }
            } else {
                $errors[] = "Gagal upload gambar";
            }
        } else {
            $errors[] = "Format gambar tidak valid atau ukuran terlalu besar (max 2MB)";
        }
    }

    $spesifikasi = sanitize($_POST['spesifikasi'] ?? '');
    $jumlah = isset($_POST['jumlah']) ? (int)$_POST['jumlah'] : 1;
    $kondisi = sanitize($_POST['kondisi'] ?? 'Baik');
    $is_active = isset($_POST['is_active']) ? 1 : 0;
    
    // Validasi
    if (empty($nama_sarana)) $errors[] = "Nama sarana harus diisi";
    if ($jumlah < 1) $errors[] = "Jumlah minimal 1";
    if (!in_array($kondisi, ['Baik', 'Rusak Ringan', 'Rusak Berat'])) $errors[] = "Kondisi tidak valid";
    
    // Save to Database
    if (empty($errors)) {
        if ($id) {
            // Update
            if ($gambar_url) {
                // Ada upload gambar baru
                $query = "UPDATE sarana SET nama_sarana = ?, gambar = ?, deskripsi = ?, spesifikasi = ?, jumlah = ?, kondisi = ?, is_active = ? WHERE id = ?";
                $params = [$nama_sarana, $gambar_url, $deskripsi, $spesifikasi, $jumlah, $kondisi, $is_active, (int)$id];
            } else {
                // Tidak upload gambar baru, keep yang lama
                $query = "UPDATE sarana SET nama_sarana = ?, deskripsi = ?, spesifikasi = ?, jumlah = ?, kondisi = ?, is_active = ? WHERE id = ?";
                $params = [$nama_sarana, $deskripsi, $spesifikasi, $jumlah, $kondisi, $is_active, (int)$id];
            }
            $msg_success = "Sarana berhasil diperbarui";
        } else {
            // Insert - Gambar boleh kosong
            $id_admin = getCurrentUser() ?? null; // Ambil ID user dari session
            $query = "INSERT INTO sarana (nama_sarana, gambar, deskripsi, spesifikasi, jumlah, kondisi, is_active, id_admin) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
            $params = [$nama_sarana, $gambar_url, $deskripsi, $spesifikasi, $jumlah, $kondisi, $is_active, $id_admin['id']];
            $msg_success = "Sarana berhasil ditambahkan";
        }

        $result = executeNonQuery($query, $params);

        if ($result !== false) {
            setFlashMessage('success', $msg_success);
            redirect(ADMIN_URL . '/sarana.php');
            exit;
        } else {
            $errors[] = "Terjadi kesalahan database";
        }
    }
    if (!empty($errors)) {
        foreach ($errors as $error) setFlashMessage('error', $error);
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
    $query = "SELECT s.*, u.nama_lengkap as created_by_name 
              FROM sarana s 
              LEFT JOIN users u ON s.id_admin = u.id 
              WHERE " . $where_clause . " 
              ORDER BY s.nama_sarana ASC 
              LIMIT ? OFFSET ?";
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
        <button type="button" data-toggle="modal" data-target="#modalSarana" onclick="resetForm()" class="btn btn-primary">
            <i class="fas fa-plus mr-2"></i>Tambah Sarana
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
        <div class="bg-white rounded-lg shadow p-4 border-l-4 border-blue-500">
            <p class="text-gray-500 text-sm mb-1 uppercase font-bold tracking-wider">Total Item</p>
            <p class="text-2xl font-bold text-gray-800">
                <?php echo countRows("SELECT COUNT(*) FROM sarana WHERE is_active = true"); ?>
            </p>
        </div>
        <div class="bg-white rounded-lg shadow p-4 border-l-4 border-green-500">
            <p class="text-gray-500 text-sm mb-1 uppercase font-bold tracking-wider">Kondisi Baik</p>
            <p class="text-2xl font-bold text-green-600">
                <?php echo countRows("SELECT COUNT(*) FROM sarana WHERE kondisi = 'Baik' AND is_active = true"); ?>
            </p>
        </div>
        <div class="bg-white rounded-lg shadow p-4 border-l-4 border-yellow-500">
            <p class="text-gray-500 text-sm mb-1 uppercase font-bold tracking-wider">Rusak Ringan</p>
            <p class="text-2xl font-bold text-yellow-600">
                <?php echo countRows("SELECT COUNT(*) FROM sarana WHERE kondisi = 'Rusak Ringan' AND is_active = true"); ?>
            </p>
        </div>
        <div class="bg-white rounded-lg shadow p-4 border-l-4 border-red-500">
            <p class="text-gray-500 text-sm mb-1 uppercase font-bold tracking-wider">Rusak Berat</p>
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
                        <th class="w-40">Dibuat Oleh</th>
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
                        <td>
                            <div class="flex items-center gap-2">
                                <div class="w-8 h-8 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 font-semibold text-sm">
                                    <?php 
                                    if ($item['created_by_name']) {
                                        $initials = '';
                                        $words = explode(' ', $item['created_by_name']);
                                        foreach ($words as $word) {
                                            $initials .= strtoupper(substr($word, 0, 1));
                                            if (strlen($initials) >= 2) break;
                                        }
                                        echo htmlspecialchars($initials);
                                    } else {
                                        echo '?';
                                    }
                                    ?>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-800">
                                        <?php echo $item['created_by_name'] ? htmlspecialchars($item['created_by_name']) : 'Unknown'; ?>
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
                        <td class="text-center">
                            <div class="flex items-center justify-center gap-2">
                                <button type="button" data-toggle="modal" data-target="#modalSarana" onclick='editData(<?= json_encode($item) ?>)' class="text-blue-600 hover:text-blue-800" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </button>
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
        <button type="button" data-toggle="modal" data-target="#modalSarana" onclick="resetForm()" class="btn btn-primary mt-4">
            <i class="fas fa-plus mr-2"></i>Tambah Sarana Pertama
        </button>
    </div>
    <?php endif; ?>
    </div>
    
</div>

<!-- Modal Tambah/Edit Sarana -->
    <div class="fixed inset-0 bg-slate-950/50 flex justify-center items-center opacity-0 pointer-events-none transition-opacity duration-300 ease-out z-[9999]" id="modalSarana" aria-hidden="true">
    <div class="bg-white rounded-xl shadow-2xl border border-slate-200 w-full max-w-3xl scale-95 transition-transform duration-300 max-h-[90vh] overflow-y-auto mx-4">
        
        <!-- Modal Header -->
        <div class="p-5 pb-3 flex justify-between items-center border-b border-slate-200 sticky top-0 bg-white z-10">
            <h1 class="text-lg text-slate-800 font-semibold" id="modalTitle">
                <i class="fas fa-box mr-2 text-blue-600"></i>Tambah Sarana
            </h1>
            <button type="button" data-dismiss="modal" class="inline-grid place-items-center text-slate-600 hover:bg-slate-200/30 rounded-md min-w-[34px] min-h-[34px] transition-all">
                <svg width="1.5em" height="1.5em" stroke-width="1.5" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" color="currentColor" class="h-5 w-5">
                    <path d="M6.75827 17.2426L12.0009 12M17.2435 6.75736L12.0009 12M12.0009 12L6.75827 6.75736M12.0009 12L17.2435 17.2426" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"></path>
                </svg>
            </button>
        </div>
        
        <!-- Modal Body -->
        <form action="?action=save" method="POST" enctype="multipart/form-data">
            <div class="p-6 pt-4">
                <input type="hidden" name="id" id="inputId">
                <input type="hidden" name="action" value="save">

                <div class="space-y-4">
                    <!-- Nama Sarana -->
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">Nama Sarana <span class="text-red-500">*</span></label>
                        <input type="text" name="nama_sarana" id="inputNama" required 
                               class="w-full border border-slate-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 focus:outline-none"
                               placeholder="Contoh: Server Rack 42U" maxlength="100">
                    </div>

                    <!-- Gambar -->
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">Gambar Sarana</label>
                        <input type="file" name="gambar" id="inputGambar" accept="image/*"
                               class="w-full border border-slate-300 rounded-lg px-4 py-2.5 focus:outline-none file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 cursor-pointer"
                               onchange="previewImage(this)">
                        <p class="text-xs text-slate-500 mt-2" id="fileHelpText">
                            <i class="fas fa-info-circle mr-1"></i>Format: JPG, PNG, GIF. Maksimal 2MB.
                        </p>
                        <div class="mt-3">
                            <img id="preview-image" src="" alt="Preview" class="w-32 h-32 object-cover rounded-lg border hidden">
                        </div>
                    </div>

                    <!-- Deskripsi -->
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">Deskripsi</label>
                        <textarea name="deskripsi" id="inputDeskripsi" rows="3"
                                  class="w-full border border-slate-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 focus:outline-none"
                                  placeholder="Deskripsi singkat tentang sarana" maxlength="500"></textarea>
                    </div>

                    <!-- Spesifikasi -->
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">Spesifikasi</label>
                        <textarea name="spesifikasi" id="inputSpesifikasi" rows="3"
                                  class="w-full border border-slate-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 focus:outline-none"
                                  placeholder="Spesifikasi teknis sarana" maxlength="500"></textarea>
                    </div>

                    <!-- Jumlah & Kondisi -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-2">Jumlah <span class="text-red-500">*</span></label>
                            <input type="number" name="jumlah" id="inputJumlah" required min="1" max="9999" value="1"
                                   class="w-full border border-slate-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 focus:outline-none">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-2">Kondisi <span class="text-red-500">*</span></label>
                            <select name="kondisi" id="inputKondisi" required
                                    class="w-full border border-slate-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 focus:outline-none">
                                <option value="Baik">Baik</option>
                                <option value="Rusak Ringan">Rusak Ringan</option>
                                <option value="Rusak Berat">Rusak Berat</option>
                            </select>
                        </div>
                    </div>

                    <!-- Status Aktif -->
                    <div>
                        <label class="flex items-center">
                            <input type="checkbox" name="is_active" id="inputIsActive" checked
                                   class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                            <span class="ml-2 text-sm text-gray-700">Aktif (tampilkan dalam sistem)</span>
                        </label>
                    </div>
                </div>
            </div>

            <!-- Modal Footer -->
            <div class="p-5 pt-3 flex justify-end gap-3 border-t border-slate-200 sticky bottom-0 bg-white">
                <button type="button" data-dismiss="modal" class="inline-flex items-center justify-center px-4 py-2 rounded-md text-sm font-medium text-slate-600 hover:bg-slate-100 transition-all">
                    <i class="fas fa-times mr-2"></i>Batal
                </button>
                <button type="submit" class="inline-flex items-center justify-center px-4 py-2 rounded-md text-sm font-medium shadow-sm bg-blue-600 text-white hover:bg-blue-700 transition-all">
                    <i class="fas fa-save mr-2"></i>Simpan
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function resetForm() {
    $('#inputId').val('');
    $('#inputNama').val('');
    $('#inputGambar').val('');
    $('#inputDeskripsi').val('');
    $('#inputSpesifikasi').val('');
    $('#inputJumlah').val(1);
    $('#inputKondisi').val('Baik');
    $('#inputIsActive').prop('checked', true);
    $('#modalTitle').html('<i class="fas fa-plus mr-2 text-blue-600"></i>Tambah Sarana');
    $('#fileHelpText').html('<i class="fas fa-info-circle mr-1"></i>Format: JPG, PNG, GIF. Maksimal 2MB.');
    $('#preview-image').attr('src', '').addClass('hidden');
}

function editData(data) {
    $('#inputId').val(data.id);
    $('#inputNama').val(data.nama_sarana);
    $('#inputDeskripsi').val(data.deskripsi);
    $('#inputSpesifikasi').val(data.spesifikasi);
    $('#inputJumlah').val(data.jumlah);
    $('#inputKondisi').val(data.kondisi);
    $('#inputIsActive').prop('checked', data.is_active == 1);
    $('#inputGambar').val('');
    $('#modalTitle').html('<i class="fas fa-edit mr-2 text-blue-600"></i>Edit Sarana');
    $('#fileHelpText').html('<i class="fas fa-info-circle mr-1"></i>Biarkan kosong jika tetap menggunakan gambar lama.');
    
    if (data.gambar) {
        const protocol = window.location.protocol;
        const host = window.location.host;
        const pathArray = window.location.pathname.split('/');
        const basePath = '/' + pathArray[1];
        const imageUrl = protocol + '//' + host + basePath + data.gambar;
        $('#preview-image').attr('src', imageUrl).removeClass('hidden');
    } else {
        $('#preview-image').attr('src', '').addClass('hidden');
    }
}

function previewImage(input) {
    const preview = document.getElementById('preview-image');
    
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            preview.src = e.target.result;
            preview.classList.remove('hidden');
        };
        reader.readAsDataURL(input.files[0]);
    } else {
        preview.src = '';
        preview.classList.add('hidden');
    }
}
</script>

<?php endif; ?>

<?php
// Include admin footer
require_once __DIR__ . '/../includes/admin_footer.php';
?>
