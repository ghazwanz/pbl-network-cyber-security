<?php
$page_title = "Layanan";
require_once __DIR__ . '/../includes/admin_header.php';

// Handle actions
$action = $_GET['action'] ?? ($_POST['action'] ?? 'list');
$id = $_GET['id'] ?? ($_POST['id'] ?? null);

// --- 1. HANDLE DELETE ---
if ($action === 'delete' && $id) {
    // Mengambil data untuk mendapatkan path gambar
    $layanan = executeQuerySingle("SELECT * FROM layanan WHERE id = ?", [(int)$id]);
    
    if ($layanan) {
        // Hapus file fisik jika ada
        if (!empty($layanan['gambar_path'])) {
            $file_path_abs = $_SERVER['DOCUMENT_ROOT'] . $layanan['gambar_path'];
            if (file_exists($file_path_abs)) {
                unlink($file_path_abs);
            }
        }

        // Hapus dari database
        $result = executeNonQuery("DELETE FROM layanan WHERE id = ?", [(int)$id]);
        
        if ($result) {
            setFlashMessage('success', 'Layanan berhasil dihapus');
        } else {
            setFlashMessage('error', 'Gagal menghapus layanan');
        }
    } else {
        setFlashMessage('error', 'Data tidak ditemukan');
    }
    
    redirect(ADMIN_URL . '/layanan.php');
    exit;
}

// --- 2. HANDLE FORM SUBMISSION (SAVE) ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'save') {
    $errors = [];
    
    // Sanitasi Input
    $nama_layanan = sanitize($_POST['nama_layanan'] ?? '');
    $deskripsi = sanitize($_POST['deskripsi'] ?? '');
    $tipe_layanan = sanitize($_POST['tipe_layanan'] ?? '');
    $status = sanitize($_POST['status'] ?? 'Aktif');
    
    // Validasi
    if (empty($nama_layanan)) $errors[] = "Nama layanan wajib diisi";
    
    // Handle image upload
    $gambar_path = $_POST['gambar_lama'] ?? ''; // Default ke gambar lama jika edit
    
    if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] === UPLOAD_ERR_OK) {
        // Pastikan parameter ke-2 dan ke-3 sesuai dengan struktur folder Anda
        $upload_result = uploadImage($_FILES['gambar'], 'layanan', 'layanan');
        
        if ($upload_result['success']) {
            $gambar_path = '/layanan/' . $upload_result['filename'];
            
            // Hapus gambar lama jika sedang edit dan upload baru berhasil
            if ($id) {
                $old_layanan = executeQuerySingle("SELECT gambar_path FROM layanan WHERE id = ?", [(int)$id]);
                if ($old_layanan && !empty($old_layanan['gambar_path'])) {
                    $old_file_abs = $_SERVER['DOCUMENT_ROOT'] . $old_layanan['gambar_path'];
                    if (file_exists($old_file_abs)) {
                        unlink($old_file_abs);
                    }
                }
            }
        } else {
            $errors[] = $upload_result['message'];
        }
    }

    // Save to Database
    if (empty($errors)) {
        if ($id) {
            // UPDATE
            // Catatan: updated_at tidak perlu di-set manual karena sudah ada Trigger di database
            $query = "UPDATE layanan SET nama_layanan = ?, deskripsi = ?, tipe_layanan = ?, status = ?, gambar_path = ? WHERE id = ?";
            $params = [$nama_layanan, $deskripsi, $tipe_layanan, $status, $gambar_path, (int)$id];
            
            $msg_success = "Layanan berhasil diperbarui";
        } else {
            // INSERT
            // Ambil ID Admin yang sedang login
            $admin = getCurrentUser(); 
            $id_admin = $admin['id'] ?? null;

            $query = "INSERT INTO layanan (nama_layanan, deskripsi, tipe_layanan, status, gambar_path, id_admin) VALUES (?, ?, ?, ?, ?, ?)";
            $params = [$nama_layanan, $deskripsi, $tipe_layanan, $status, $gambar_path, $id_admin];
            
            $msg_success = "Layanan berhasil ditambahkan";
        }

        $result = executeNonQuery($query, $params);

        if ($result !== false) {
            setFlashMessage('success', $msg_success);
            redirect(ADMIN_URL . '/layanan.php');
            exit;
        } else {
            $errors[] = "Terjadi kesalahan database";
        }
    }

    if (!empty($errors)) {
        foreach ($errors as $error) setFlashMessage('error', $error);
    }
}

// --- 3. GET DATA & FILTER ---
$search = $_GET['search'] ?? '';
$filter_tipe = $_GET['filter'] ?? '';

$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 10;
$offset = ($page - 1) * $limit;

// Ambil list tipe layanan yang unik dari database untuk dropdown filter
$types_query = "SELECT DISTINCT tipe_layanan FROM layanan WHERE tipe_layanan IS NOT NULL AND tipe_layanan != '' ORDER BY tipe_layanan ASC";
$existing_types = executeQuery($types_query);

// Build Query
$where = [];
$params = [];

if ($filter_tipe) {
    $where[] = "tipe_layanan = ?";
    $params[] = $filter_tipe;
}

if ($search) {
    $where[] = "(nama_layanan ILIKE ? OR tipe_layanan ILIKE ?)";
    $search_param = '%' . $search . '%';
    $params[] = $search_param;
    $params[] = $search_param;
}

$where_clause = count($where) > 0 ? 'WHERE ' . implode(' AND ', $where) : '';

// Hitung Total Data (Pagination)
$count_query = "SELECT COUNT(*) FROM layanan " . $where_clause;
$total_records = countRows($count_query, $params); 
$total_pages = ceil($total_records / $limit);

// Query Utama
$query = "SELECT l.*, u.nama_lengkap as created_by_name 
          FROM layanan l 
          LEFT JOIN users u ON l.id_admin = u.id 
          " . $where_clause . " 
          ORDER BY l.nama_layanan ASC 
          LIMIT ? OFFSET ?";
$params[] = $limit;
$params[] = $offset;
$layanan_list = executeQuery($query, $params);

// Hitung Statistik Dashboard
$count_aktif = countRows("SELECT COUNT(*) FROM layanan WHERE status = 'Aktif'");
$count_nonaktif = countRows("SELECT COUNT(*) FROM layanan WHERE status = 'Non-Aktif'");
$count_layanan = countRows("SELECT COUNT(*) FROM layanan");

?>

<div class="flex flex-col overflow-hidden">
    <div class="flex-1 flex flex-col overflow-hidden">
        
        <div class="mb-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h2 class="text-lg font-semibold text-blue-900"><i class="fa-solid fa-headset mr-2"></i>Daftar Layanan</h2>
                <p class="text-sm text-gray-600">Kelola data layanan laboratorium.</p>
            </div>
            
            <div class="flex flex-col sm:flex-row gap-3">
                <button type="button" data-toggle="modal" data-target="#modalLayanan" onclick="resetForm()" class="inline-flex items-center justify-center border select-none font-sans font-medium text-center transition-all duration-300 ease-in text-sm rounded-md py-2.5 px-5 shadow-sm hover:shadow-md bg-blue-600 border-blue-600 text-white hover:bg-blue-700 gap-2">
                    <i class="fas fa-plus"></i> Tambah Layanan
                </button>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md p-4 mb-6">
            <form method="GET" class="flex flex-col md:flex-row gap-4">
                
                <div class="flex-1">
                    <input 
                        type="text" 
                        name="search"
                        value="<?php echo htmlspecialchars($search); ?>"
                        placeholder="Cari nama layanan atau deskripsi..."
                        class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 focus:outline-none"
                    >  
                </div>

                <select name="filter" class="w-full md:w-48 border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 focus:outline-none" onchange="this.form.submit()">
                    <option value="">Semua Tipe</option>
                    
                    <?php 
                    $hasJasa = false;
                    if (!empty($existing_types)) {
                        foreach ($existing_types as $type) {
                            $selected = ($filter_tipe === $type['tipe_layanan']) ? 'selected' : '';
                            echo '<option value="'.$type['tipe_layanan'].'" '.$selected.'>'.$type['tipe_layanan'].'</option>';
                            if ($type['tipe_layanan'] === 'Jasa') $hasJasa = true;
                        }
                    }
                    if (!$hasJasa) {
                        echo '<option value="Jasa" '.($filter_tipe === 'Jasa' ? 'selected' : '').'>Jasa</option>';
                    }
                    ?>
                </select>

                <button type="submit" class="inline-flex items-center justify-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                    <i class="fas fa-search mr-2"></i> Cari
                </button>

                <?php if ($search || $filter_tipe): ?>
                <a href="?action=list" class="inline-flex items-center justify-center px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition">
                    <i class="fas fa-times mr-2"></i> Reset
                </a>
                <?php endif; ?>
            </form>
        </div>

        <div class="mb-6 grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="bg-white rounded-lg shadow p-4 border-l-4 border-blue-500">
                <p class="text-gray-500 text-sm mb-1 uppercase font-bold tracking-wider">Total Layanan</p>
                <p class="text-2xl font-bold text-gray-800"><?php echo $count_layanan; ?></p>
            </div>
            <div class="bg-white rounded-lg shadow p-4 border-l-4 border-green-500">
                <p class="text-gray-500 text-sm mb-1 uppercase font-bold tracking-wider">Status Aktif</p>
                <p class="text-2xl font-bold text-green-600"><?php echo $count_aktif; ?></p>
            </div>
            <div class="bg-white rounded-lg shadow p-4 border-l-4 border-orange-500">
                <p class="text-gray-500 text-sm mb-1 uppercase font-bold tracking-wider">Status Non-Aktif</p>
                <p class="text-2xl font-bold text-orange-600"><?php echo $count_nonaktif; ?></p>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            
            <?php if(!empty($layanan_list)): ?>
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-gray-50 text-gray-600 text-xs uppercase font-bold border-b border-gray-200">
                                <th class="px-6 py-4 w-24">Gambar</th>
                                <th class="px-6 py-4 w-48">Judul Layanan</th> 
                                <th class="px-6 py-4">Deskripsi</th>
                                <th class="px-6 py-4 w-40">Dibuat Oleh</th>
                                <th class="px-6 py-4 w-32">Tipe</th>
                                <th class="px-6 py-4 w-32">Status</th>
                                <th class="px-6 py-4 text-left w-32">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            <?php foreach($layanan_list as $row): ?>
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-6 py-4">
                                    <div class="w-12 h-12 rounded-lg overflow-hidden bg-gray-100 border">
                                        <img src="<?= !empty($row['gambar_path']) ? UPLOAD_URL . $row['gambar_path'] : 'https://via.placeholder.com/150?text=No+Img' ?>" 
                                             alt="Img" class="w-full h-full object-cover">
                                    </div>
                                </td>
                                <td class="px-6 py-4 font-semibold text-gray-800 text-sm">
                                    <?= $row['nama_layanan'] ?>
                                    <div class="text-xs text-gray-400 font-normal mt-1">By: <?= $row['created_by_name'] ?? 'System' ?></div>
                                </td>
                                <td class="px-6 py-4">
                                    <p class="text-sm text-gray-600 line-clamp-2 max-w-sm">
                                        <?= $row['deskripsi'] ?>
                                    </p>
                                </td>
                            <td class="px-6 py-4">
                            <div class="flex items-center gap-2">
                                <div class="w-8 h-8 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 font-semibold text-sm">
                                    <?php 
                                    if ($row['created_by_name']) {
                                        $initials = '';
                                        $words = explode(' ', $row['created_by_name']);
                                        foreach ($words as $word) {
                                            $initials .= strtoupper(substr($word, 0, 1));
                                            if (strlen($initials) >= 2) break;
                                        }
                                        echo $initials;
                                    } else {
                                        echo '?';
                                    }
                                    ?>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-800">
                                        <?php echo $row['created_by_name'] ? $row['created_by_name'] : 'Unknown'; ?>
                                    </p>
                                    <?php if (!empty($row['created_at'])): ?>
                                    <p class="text-xs text-gray-500">
                                        <?php 
                                        $date = new DateTime($row['created_at']);
                                        echo $date->format('d M Y'); 
                                        ?>
                                    </p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </td>
                                <td class="px-6 py-4">
                                    <span class="px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        <?= $row['tipe_layanan'] ?>
                                    </span>
                                </td>
                                <td class="px-6 whitespace-nowrap">
                                    <span class="inline-block px-3 py-1 rounded-full text-xs font-semibold <?php echo ($row['status'] == 'Aktif') ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'; ?>">
                                         <?= $row['status'] ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-left whitespace-nowrap">
                                    <div class="flex items-center gap-2">
                                        <button data-toggle="modal" data-target="#modalDetail" onclick='getData(<?= json_encode($row) ?>)' class="text-green-500 hover:bg-green-50 p-2 rounded-lg transition" title="Lihat Detail">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button data-toggle="modal" data-target="#modalLayanan" onclick='editData(<?= json_encode($row) ?>)' class="text-blue-500 hover:bg-blue-50 p-2 rounded-lg transition" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <a href="?action=delete&id=<?= $row['id'] ?>" onclick="return confirm('Yakin hapus layanan ini?')" class="text-red-500 hover:bg-red-50 p-2 rounded-lg transition" title="Hapus">
                                            <i class="fas fa-trash-alt"></i>    
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <?php if ($total_pages > 1): ?>
                <div class="d-flex justify-content-between align-items-center p-4 border-t border-gray-200">
                    <div class="text-gray-500 text-sm">
                        Menampilkan <?php echo $offset + 1; ?> - <?php echo min($offset + $limit, $total_records); ?>
                        dari <?php echo $total_records; ?> data
                    </div>
                    <nav class="flex gap-2">
                        <?php
                            $url = 'layanan.php?';
                            if ($filter_tipe) $url .= 'filter=' . urlencode($filter_tipe) . '&';
                            if ($search) $url .= 'search=' . urlencode($search) . '&';
                            echo createPagination($page, $total_pages, $url);
                        ?>
                    </nav>
                </div>
                <?php endif; ?>

            <?php else: ?>
                
                <?php 
                    $empty_message = 'Belum ada data layanan.';
                    if ($search) {
                        $empty_message = 'Tidak ditemukan layanan untuk "' . $search . '"';
                    } elseif ($filter_tipe) {
                        $empty_message = 'Tidak ada layanan dengan tipe "' . $filter_tipe . '"';
                    }
                ?>

                <div class="text-center py-12 px-4">
                    <i class="fas fa-inbox text-6xl text-gray-300 mb-4"></i>
                    <p class="text-gray-500 text-lg">
                        <?php echo $empty_message; ?>
                    </p>
                </div>

            <?php endif; ?>

        </div>
    </div>
</div>

<div class="fixed inset-0 bg-slate-950/50 flex justify-center items-center opacity-0 pointer-events-none transition-opacity duration-300 ease-out z-[9999]" id="modalLayanan" aria-hidden="true">
    <div class="bg-white rounded-xl shadow-2xl border border-slate-200 w-full max-w-2xl scale-95 transition-transform duration-300 max-h-[90vh] overflow-y-auto mx-4">
        
        <div class="p-5 pb-3 flex justify-between items-center border-b border-slate-200 sticky top-0 bg-white z-10">
            <h1 class="text-lg text-slate-800 font-semibold" id="modalTitle">
                <i class="fas fa-headset mr-2 text-blue-600"></i>Tambah Layanan
            </h1>
            <button type="button" data-dismiss="modal" class="inline-grid place-items-center text-slate-600 hover:bg-slate-200/30 rounded-md min-w-[34px] min-h-[34px] transition-all">
                <svg width="1.5em" height="1.5em" stroke-width="1.5" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" color="currentColor" class="h-5 w-5">
                    <path d="M6.75827 17.2426L12.0009 12M17.2435 6.75736L12.0009 12M12.0009 12L6.75827 6.75736M12.0009 12L17.2435 17.2426" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"></path>
                </svg>
            </button>
        </div>
        
        <form action="?action=save" method="POST" enctype="multipart/form-data">
            <div class="p-6 pt-4">
                <input type="hidden" name="id" id="inputId">
                <input type="hidden" name="action" value="save">
                <input type="hidden" name="gambar_lama" id="inputGambarLama">

                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">Nama Layanan <span class="text-red-500">*</span></label>
                        <input type="text" name="nama_layanan" id="inputNama" required 
                               class="w-full border border-slate-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 focus:outline-none"
                               placeholder="Contoh: Pen Test">
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-2">Tipe Layanan <span class="text-red-500">*</span></label>
                            <select name="tipe_layanan" id="inputTipe" required
                                    class="w-full border border-slate-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 focus:outline-none bg-white">
                                <option value="">Pilih Tipe</option>
                                <option value="Jasa">Jasa</option>
                                <option value="Produk">Produk</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-2">Status <span class="text-red-500">*</span></label>
                            <select name="status" id="inputStatus" class="w-full border border-slate-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 focus:outline-none">
                                <option value="Aktif">Aktif</option>
                                <option value="Non-Aktif">Non-Aktif</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">Gambar Layanan</label>
                        <input type="file" name="gambar" id="inputGambar" data-preview="#preview-image" accept="image/*"
                               class="w-full border border-slate-300 rounded-lg px-4 py-2.5 focus:outline-none file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 cursor-pointer">
                        <p class="text-xs text-slate-500 mt-2" id="fileHelpText">
                            <i class="fas fa-info-circle mr-1"></i>Format: JPG, PNG, GIF, WEBP. Maksimal 5MB.
                        </p>
                        <div class="mt-3">
                            <img id="preview-image" src="" alt="Preview" class="w-32 h-32 object-cover rounded-lg border hidden">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">Deskripsi <span class="text-red-500">*</span></label>
                        <textarea name="deskripsi" id="inputDeskripsi" rows="4" required
                                  class="w-full border border-slate-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 focus:outline-none"
                                  placeholder="Jelaskan detail layanan..."></textarea>
                    </div>
                </div>
            </div>

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

<div id="modalDetail" aria-hidden="true" class="fixed inset-0 bg-slate-950/50 flex justify-center items-center opacity-0 pointer-events-none transition-opacity duration-300 ease-out z-[9999]">
    <div class="bg-white rounded-xl shadow-2xl border border-slate-200 w-full max-w-2xl scale-95 transition-transform duration-300 max-h-[90vh] overflow-y-auto mx-4">
        
        <div class="p-5 pb-3 flex justify-between items-center border-b border-slate-200 sticky top-0 bg-white z-10">
            <h1 class="text-lg text-slate-800 font-semibold">
                <i class="fas fa-eye mr-2 text-blue-600"></i>Detail Layanan
            </h1>
            <button type="button" data-dismiss="modal" class="inline-grid place-items-center text-slate-600 hover:bg-slate-200/30 rounded-md min-w-[34px] min-h-[34px] transition-all">
                <svg width="1.5em" height="1.5em" stroke-width="1.5" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" color="currentColor" class="h-5 w-5">
                    <path d="M6.75827 17.2426L12.0009 12M17.2435 6.75736L12.0009 12M12.0009 12L6.75827 6.75736M12.0009 12L17.2435 17.2426" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"></path>
                </svg>
            </button>
        </div>
        
        <div class="p-5 pt-4">
            <div class="flex flex-col gap-6">
                
                <div class="w-fit mx-auto">
                    <div class="rounded-lg overflow-hidden shadow-sm border border-gray-200 bg-gray-100 flex items-center justify-center min-h-[200px]">
                        <img id="detail-preview-image" src="" alt="Detail Gambar" class="w-full max-w-[350px] aspect-square object-cover">
                    </div>
                </div>

                <div class="w-full space-y-4">
                    <div>
                        <label class="text-xs font-bold text-gray-500 uppercase tracking-wide">Nama Layanan</label>
                        <h4 id="detail-nama" class="text-xl font-bold text-gray-900 mt-1"></h4>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="text-xs font-bold text-gray-500 uppercase tracking-wide">Tipe</label>
                            <div id="detail-tipe" class="mt-1 text-sm text-gray-800 font-medium"></div>
                        </div>
                        <div>
                            <label class="text-xs font-bold text-gray-500 uppercase tracking-wide">Status</label>
                            <div id="detail-status" class="mt-1"></div>
                        </div>
                    </div>

                    <div>
                        <label class="text-xs font-bold text-gray-500 uppercase tracking-wide">Deskripsi</label>
                        <p id="detail-deskripsi" class="text-gray-600 text-sm mt-1 leading-relaxed break-words"></p>
                    </div>
                </div>
            </div>
        </div>

        <div class="p-5 pt-3 flex justify-end gap-3 border-t border-slate-200 sticky bottom-0 bg-white">
            <button type="button" data-dismiss="modal" class="inline-flex items-center justify-center px-4 py-2 rounded-md text-sm font-medium text-slate-600 hover:bg-slate-100 transition-all">
                <i class="fas fa-times mr-2"></i>Tutup
            </button>
        </div>
    </div>
</div>

<script>
    // Reset form saat tombol tambah diklik
    function resetForm() {
        $('#inputId').val('');
        $('#inputNama').val('');
        $('#inputTipe').val('');
        $('#inputStatus').val('Aktif');
        $('#inputGambar').val('');
        $('#inputGambarLama').val('');
        $('#inputDeskripsi').val('');
        $('#modalTitle').html('<i class="fas fa-plus-circle mr-2 text-blue-600"></i>Tambah Layanan Baru');
        $('#fileHelpText').html('<i class="fas fa-info-circle mr-1"></i>Format: JPG, PNG, GIF, WEBP. Maksimal 5MB.');
        $('#preview-image').attr('src', '').addClass('hidden');
    }

    // Isi form saat tombol edit diklik
    function editData(data) {
        $('#inputId').val(data.id);
        $('#inputNama').val(data.nama_layanan);
        $('#inputTipe').val(data.tipe_layanan);
        $('#inputStatus').val(data.status);
        $('#inputGambar').val(''); // File input tidak bisa diisi value-nya demi keamanan browser
        $('#inputGambarLama').val(data.gambar_path);
        $('#inputDeskripsi').val(data.deskripsi);
        
        $('#modalTitle').html('<i class="fas fa-edit mr-2 text-blue-600"></i>Edit Layanan');
        $('#fileHelpText').html('<i class="fas fa-info-circle mr-1"></i>Biarkan kosong jika tetap menggunakan gambar lama.');
        
        if (data.gambar_path) {
            // Pastikan path image benar sesuai konfigurasi UPLOAD_URL
            const imgPath = "<?php echo UPLOAD_URL; ?>" + data.gambar_path;
            $('#preview-image').attr('src', imgPath).removeClass('hidden');
        } else {
            $('#preview-image').attr('src', '').addClass('hidden');
        }
    }

    // Isi modal detail saat tombol mata diklik
    function getData(data) {
        $('#detail-nama').text(data.nama_layanan);
        $('#detail-tipe').html(`<span class="px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">${data.tipe_layanan}</span>`);
        
        const statusClass = data.status === 'Aktif' ? 'text-green-600' : 'text-red-500';
        const statusIcon = data.status === 'Aktif' ? 'check' : 'times';
        $('#detail-status').html(`<span class="inline-flex items-center gap-1 text-sm font-medium ${statusClass}"><i class="fas fa-${statusIcon}-circle"></i> ${data.status}</span>`);
        
        $('#detail-deskripsi').text(data.deskripsi);
        
        if (data.gambar_path) {
            const imgPath = "<?php echo UPLOAD_URL; ?>" + data.gambar_path;
            $('#detail-preview-image').attr('src', imgPath);
        } else {
            $('#detail-preview-image').attr('src', 'https://via.placeholder.com/400x300?text=No+Image');
        }
    }
</script>

<?php require_once __DIR__ . '/../includes/admin_footer.php'; ?>