<?php

$page_title = "Layanan";

require_once __DIR__ . '/../includes/admin_header.php';

// --- LOGIC HANDLER ---

// Handle actions
$action = $_GET['action'] ?? ($_POST['action'] ?? 'list');
$id = $_GET['id'] ?? ($_POST['id'] ?? null);

// 1. Handle Delete
if ($action === 'delete' && $id) {
    // Pastikan fungsi executeQuerySingle ada di sistem Anda
    $layanan = executeQuerySingle("SELECT * FROM layanan WHERE id = ?", [(int)$id]);
    
    if ($layanan) {
        // Hapus file gambar fisik jika ada
        if (!empty($layanan['gambar_path']) && file_exists($layanan['gambar_path'])) {
            unlink($layanan['gambar_path']);
        }

        // Pastikan fungsi executeNonQuery ada di sistem Anda
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

// 2. Handle Form Submission (Create & Update)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'save') {
    $errors = [];
    
    // Sanitasi Input
    $nama_layanan = sanitize($_POST['nama_layanan'] ?? '');
    $deskripsi = sanitize($_POST['deskripsi'] ?? '');
    $tipe_layanan = sanitize($_POST['tipe_layanan'] ?? '');
    $status = sanitize($_POST['status'] ?? 'Aktif');
    $gambar_path = $_POST['gambar_lama'] ?? ''; // Default gambar lama

    // Validasi Sederhana
    if (empty($nama_layanan)) $errors[] = "Nama layanan wajib diisi";

    // --- LOGIKA UPLOAD GAMBAR ---
    if (isset($_FILES['gambar_path']) && $_FILES['gambar_path']['error'] == 0) {
        $target_dir = "uploads/";
        if (!is_dir($target_dir)) { mkdir($target_dir, 0777, true); }

        $file_extension = pathinfo($_FILES["gambar_path"]["name"], PATHINFO_EXTENSION);
        $allowed_ext = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        
        if (in_array(strtolower($file_extension), $allowed_ext)) {
            $file_name = time() . "_" . uniqid() . "." . $file_extension;
            $target_file = $target_dir . $file_name;

            if (move_uploaded_file($_FILES["gambar_path"]["tmp_name"], $target_file)) {
                // Hapus gambar lama jika ada update gambar baru
                if (!empty($gambar_path) && file_exists($gambar_path)) {
                    unlink($gambar_path);
                }
                $gambar_path = $target_file;
            } else {
                $errors[] = "Gagal mengupload gambar ke server";
            }
        } else {
            $errors[] = "Format file tidak diizinkan (hanya JPG, PNG, GIF, WEBP)";
        }
    }

    // Eksekusi Database jika tidak ada error
    if (empty($errors)) {
        if ($id) {
            // UPDATE
            $query = "UPDATE layanan SET nama_layanan = ?, deskripsi = ?, tipe_layanan = ?, status = ?, gambar_path = ?, updated_at = NOW() WHERE id = ?";
            $params = [$nama_layanan, $deskripsi, $tipe_layanan, $status, $gambar_path, (int)$id];
            $msg_success = "Layanan berhasil diperbarui";
        } else {
            // INSERT
            $query = "INSERT INTO layanan (nama_layanan, deskripsi, tipe_layanan, status, gambar_path) VALUES (?, ?, ?, ?, ?)";
            $params = [$nama_layanan, $deskripsi, $tipe_layanan, $status, $gambar_path];
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

    // Show errors if any
    if (!empty($errors)) {
        foreach ($errors as $error) {
            setFlashMessage('error', $error);
        }
    }
}

// 3. Get Data (List, Filter, Pagination)
$search = $_GET['search'] ?? '';
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 10;
$offset = ($page - 1) * $limit;

// Build query conditions
$where = [];
$params = [];

if ($search) {
    $where[] = "(nama_layanan ILIKE ? OR tipe_layanan ILIKE ?)";
    $search_param = '%' . $search . '%';
    $params[] = $search_param;
    $params[] = $search_param;
}

$where_clause = count($where) > 0 ? 'WHERE ' . implode(' AND ', $where) : '';

// Get total records (for pagination)
$count_query = "SELECT COUNT(*) FROM layanan " . $where_clause;
$total_records = countRows($count_query, $params); 
$total_pages = ceil($total_records / $limit);

// Get Main Data
$query = "SELECT * FROM layanan " . $where_clause . " ORDER BY id DESC LIMIT ? OFFSET ?";
$params[] = $limit;
$params[] = $offset;
$layanan_list = executeQuery($query, $params);

// Hitung Statistik
$count_aktif = countRows("SELECT COUNT(*) FROM layanan WHERE status = 'Aktif'");
$count_nonaktif = countRows("SELECT COUNT(*) FROM layanan WHERE status = 'Non-Aktif'");
$count_layanan = countRows("SELECT COUNT(*) FROM layanan ");

?>

<div class="flex h-screen overflow-hidden">
    
    <div class="flex-1 flex flex-col min-w-0 overflow-hidden ">

        <div class="mb-12 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold text-gray-800"><i class="fa-solid fa-headset mr-2"></i>Daftar Layanan</h2>
            <p class="text-gray-600 mt-1">Kelola data layanan.</p>
        </div>
                <div class="flex flex-col sm:flex-row gap-3">
                    <form action="" method="GET" class="flex">
                        <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" placeholder="Cari layanan..." class="border rounded-l px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <button type="submit" class="bg-gray-200 px-4 py-2 rounded-r hover:bg-gray-300"><i class="fas fa-search"></i></button>
                    </form>
                    <button onclick="bukaModalTambah()" class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 rounded-lg shadow transition flex items-center gap-2">
                        <i class="fas fa-plus"></i> Tambah Layanan
                    </button>
                </div>
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

            <div class="pt-0">
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-gray-50 text-gray-600 text-xs uppercase font-bold border-b border-gray-200">
                                    <th class="px-6 py-4 w-24">Gambar</th>
                                    <th class="px-6 py-4 w-48">Judul Layanan</th> 
                                    <th class="px-6 py-4">Deskripsi</th>
                                    <th class="px-6 py-4 w-32">Tipe</th>
                                    <th class="px-6 py-4 w-32">Status</th>
                                    <th class="px-6 py-4 text-right w-32">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                <?php if(!empty($layanan_list)): ?>
                                    <?php foreach($layanan_list as $row): ?>
                                    <tr class="hover:bg-gray-50 transition">
                                        <td class="px-6 py-4">
                                            <div class="w-12 h-12 rounded-lg overflow-hidden bg-gray-100 border">
                                                <img src="<?= !empty($row['gambar_path']) ? htmlspecialchars($row['gambar_path']) : 'https://via.placeholder.com/150?text=No+Img' ?>" 
                                                     alt="Img" class="w-full h-full object-cover">
                                            </div>
                                        </td>
                                        
                                        <td class="px-6 py-4 font-semibold text-gray-800 text-sm">
                                            <?= htmlspecialchars($row['nama_layanan']) ?>
                                        </td>

                                        <td class="px-6 py-4">
                                            <p class="text-sm text-gray-600 line-clamp-2 max-w-sm">
                                                <?= htmlspecialchars($row['deskripsi']) ?>
                                            </p>
                                        </td>

                                        <td class="px-6 py-4">
                                            <span class="px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                <?= htmlspecialchars($row['tipe_layanan']) ?>
                                            </span>
                                        </td>

                                        <td class="px-6 py-4">
                                            <?php if($row['status'] == 'Aktif'): ?>
                                                <span class="inline-flex items-center gap-1 text-green-600 text-sm font-medium">
                                                    <i class="fas fa-check-circle"></i> Aktif
                                                </span>
                                            <?php else: ?>
                                                <span class="inline-flex items-center gap-1 text-red-500 text-sm font-medium">
                                                    <i class="fas fa-times-circle"></i> Non-Aktif
                                                </span>
                                            <?php endif; ?>
                                        </td>

                                        <td class="px-6 py-4 text-right whitespace-nowrap">
                                            <button onclick='viewDetail(<?= json_encode($row) ?>)' class="text-green-500 hover:bg-green-50 p-2 rounded-lg transition mr-1" title="Lihat Detail">
                                                <i class="fas fa-eye"></i>
                                            </button>

                                            <button onclick='editData(<?= json_encode($row) ?>)' class="text-blue-500 hover:bg-blue-50 p-2 rounded-lg transition mr-1" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </button>

                                            <a href="?action=delete&id=<?= $row['id'] ?>" onclick="return confirm('Yakin hapus layanan ini?')" class="text-red-500 hover:bg-red-50 p-2 rounded-lg transition" title="Hapus">
                                                <i class="fas fa-trash-alt"></i>
                                            </a>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="6" class="px-6 py-8 text-center text-gray-500">
                                            Belum ada data layanan.
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>

                    <?php if($total_pages > 1): ?>
                    <div class="px-6 py-4 border-t flex justify-between items-center">
                        <span class="text-sm text-gray-600">Halaman <?= $page ?> dari <?= $total_pages ?></span>
                        <div class="flex gap-2">
                            <?php if($page > 1): ?>
                                <a href="?page=<?= $page - 1 ?>&search=<?= $search ?>" class="px-3 py-1 bg-gray-200 rounded hover:bg-gray-300 text-sm">Prev</a>
                            <?php endif; ?>
                            <?php if($page < $total_pages): ?>
                                <a href="?page=<?= $page + 1 ?>&search=<?= $search ?>" class="px-3 py-1 bg-gray-200 rounded hover:bg-gray-300 text-sm">Next</a>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

    </div>
</div>

<div id="modalLayanan" class="fixed inset-0 bg-black bg-opacity-50 z-[60] hidden flex items-center justify-center opacity-0 transition-opacity duration-300">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-lg transform scale-95 transition-transform duration-300" id="modalContent">
        <div class="flex justify-between items-center p-5 border-b">
            <h3 class="text-lg font-bold text-gray-800" id="modalTitle">Tambah Layanan</h3>
            <button onclick="tutupModal()" class="text-gray-400 hover:text-red-500 text-xl">&times;</button>
        </div>
        
        <form action="?action=save" method="POST" enctype="multipart/form-data" class="p-6">
            <input type="hidden" name="id" id="inputId">
            <input type="hidden" name="action" value="save"> 
            <input type="hidden" name="gambar_lama" id="inputGambarLama">

            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nama Layanan</label>
                    <input type="text" name="nama_layanan" id="inputNama" required 
                           class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none"
                           placeholder="Contoh: Pen Test">
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tipe Layanan</label>
                        <input type="text" name="tipe_layanan" id="inputTipe" required
                               class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none"
                               placeholder="Contoh: Jasa">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                        <select name="status" id="inputStatus" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none">
                            <option value="Aktif">Aktif</option>
                            <option value="Non-Aktif">Non-Aktif</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Gambar Layanan</label>
                    <input type="file" name="gambar_path" id="inputGambar" accept="image/*"
                           class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                    <p class="text-xs text-gray-500 mt-1" id="fileHelpText">Format: JPG, PNG. Biarkan kosong jika tidak ingin mengubah gambar (saat edit).</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Deskripsi</label>
                    <textarea name="deskripsi" id="inputDeskripsi" rows="3" required
                              class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none"></textarea>
                </div>
            </div>

            <div class="mt-6 flex justify-end gap-3">
                <button type="button" onclick="tutupModal()" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">Batal</button>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition shadow">Simpan</button>
            </div>
        </form>
    </div>
</div>

<div id="modalDetail" class="fixed inset-0 bg-black bg-opacity-50 z-[70] hidden flex items-center justify-center opacity-0 transition-opacity duration-300">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-2xl transform scale-95 transition-transform duration-300 overflow-hidden" id="modalDetailContent">
        
        <div class="flex justify-between items-center p-5 border-b bg-gray-50">
            <h3 class="text-lg font-bold text-gray-800">Detail Layanan</h3>
            <button onclick="tutupModalDetail()" class="text-gray-400 hover:text-red-500 text-xl transition">
                <i class="fas fa-times"></i>
            </button>
        </div>
        
        <div class="p-6 overflow-y-auto max-h-[80vh]">
            <div class="flex flex-col md:flex-row gap-6">
                
                <div class="w-full md:w-1/2">
                    <div class="rounded-lg overflow-hidden shadow-sm border border-gray-200 bg-gray-100 flex items-center justify-center min-h-[200px]">
                        <img id="detailGambar" src="" alt="Detail Gambar" class="w-full h-auto object-cover max-h-64">
                    </div>
                </div>

                <div class="w-full md:w-1/2 space-y-4">
                    <div>
                        <label class="text-xs font-bold text-gray-500 uppercase tracking-wide">Nama Layanan</label>
                        <h4 id="detailNama" class="text-xl font-bold text-gray-900 mt-1"></h4>
                    </div>

                    <div class="flex gap-3">
                        <div>
                            <label class="text-xs font-bold text-gray-500 uppercase tracking-wide">Tipe</label>
                            <div id="detailTipe" class="mt-1"></div>
                        </div>
                        <div>
                            <label class="text-xs font-bold text-gray-500 uppercase tracking-wide">Status</label>
                            <div id="detailStatus" class="mt-1"></div>
                        </div>
                    </div>

                    <div>
                        <label class="text-xs font-bold text-gray-500 uppercase tracking-wide">Deskripsi</label>
                        <p id="detailDeskripsi" class="text-gray-600 text-sm mt-1 leading-relaxed text-justify"></p>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-gray-50 p-4 flex justify-end">
            <button onclick="tutupModalDetail()" class="px-5 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition font-medium">
                Tutup
            </button>
        </div>
    </div>
</div>

<script>
    // --- VARIABEL MODAL CREATE/EDIT ---
    const modal = document.getElementById('modalLayanan');
    const modalContent = document.getElementById('modalContent');

    // --- VARIABEL MODAL DETAIL (BARU) ---
    const modalDetail = document.getElementById('modalDetail');
    const modalDetailContent = document.getElementById('modalDetailContent');

    // --- FUNGSI MODAL CREATE/EDIT ---
    function bukaModalTambah() {
        document.getElementById('inputId').value = '';
        document.getElementById('inputNama').value = '';
        document.getElementById('inputTipe').value = '';
        document.getElementById('inputStatus').value = 'Aktif';
        document.getElementById('inputGambar').value = '';
        document.getElementById('inputGambarLama').value = ''; 
        document.getElementById('inputDeskripsi').value = '';
        document.getElementById('modalTitle').innerText = 'Tambah Layanan Baru';
        document.getElementById('fileHelpText').innerText = 'Upload gambar baru.';
        
        modal.classList.remove('hidden');
        setTimeout(() => {
            modal.classList.remove('opacity-0');
            modalContent.classList.remove('scale-95');
            modalContent.classList.add('scale-100');
        }, 10);
    }

    function editData(data) {
        document.getElementById('inputId').value = data.id;
        document.getElementById('inputNama').value = data.nama_layanan;
        document.getElementById('inputTipe').value = data.tipe_layanan;
        document.getElementById('inputStatus').value = data.status;
        document.getElementById('inputGambar').value = '';
        document.getElementById('inputGambarLama').value = data.gambar_path;
        document.getElementById('fileHelpText').innerText = 'Biarkan kosong jika tetap menggunakan gambar lama.';
        document.getElementById('inputDeskripsi').value = data.deskripsi;
        document.getElementById('modalTitle').innerText = 'Edit Layanan';

        modal.classList.remove('hidden');
        setTimeout(() => {
            modal.classList.remove('opacity-0');
            modalContent.classList.remove('scale-95');
            modalContent.classList.add('scale-100');
        }, 10);
    }

    function tutupModal() {
        modal.classList.add('opacity-0');
        modalContent.classList.remove('scale-100');
        modalContent.classList.add('scale-95');
        setTimeout(() => {
            modal.classList.add('hidden');
        }, 300);
    }

    // --- FUNGSI MODAL DETAIL (BARU) ---
    function viewDetail(data) {
        // Isi data ke elemen detail
        document.getElementById('detailNama').innerText = data.nama_layanan;
        document.getElementById('detailDeskripsi').innerText = data.deskripsi;

        // Handle gambar
        const imgElement = document.getElementById('detailGambar');
        if (data.gambar_path && data.gambar_path.trim() !== "") {
            imgElement.src = data.gambar_path;
        } else {
            imgElement.src = "https://via.placeholder.com/400x300?text=No+Image";
        }

        // Handle Badge Tipe
        document.getElementById('detailTipe').innerHTML = 
            `<span class="px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">${data.tipe_layanan}</span>`;

        // Handle Badge Status
        const statusEl = document.getElementById('detailStatus');
        if (data.status === 'Aktif') {
            statusEl.innerHTML = `<span class="inline-flex items-center gap-1 text-green-700 bg-green-100 px-3 py-1 rounded-full text-xs font-medium"><i class="fas fa-check-circle"></i> Aktif</span>`;
        } else {
            statusEl.innerHTML = `<span class="inline-flex items-center gap-1 text-red-700 bg-red-100 px-3 py-1 rounded-full text-xs font-medium"><i class="fas fa-times-circle"></i> Non-Aktif</span>`;
        }

        // Buka Modal
        modalDetail.classList.remove('hidden');
        setTimeout(() => {
            modalDetail.classList.remove('opacity-0');
            modalDetailContent.classList.remove('scale-95');
            modalDetailContent.classList.add('scale-100');
        }, 10);
    }

    function tutupModalDetail() {
        modalDetail.classList.add('opacity-0');
        modalDetailContent.classList.remove('scale-100');
        modalDetailContent.classList.add('scale-95');
        setTimeout(() => {
            modalDetail.classList.add('hidden');
        }, 300);
    }
    
    // Tutup jika klik di luar modal
    window.onclick = function(event) {
        if (event.target == modal) { tutupModal(); }
        if (event.target == modalDetail) { tutupModalDetail(); }
    }
</script>