<?php
/**
 * Admin Galeri Management
 * File: admin/galeri.php
 */

// Set page title
$page_title = "Kelola Galeri & Agenda";

// Include admin header
require_once __DIR__ . '/../includes/admin_header.php';

// Handle parameters
$action = $_GET['action'] ?? 'list';
$id = $_GET['id'] ?? ($_POST['id'] ?? null);

// --- 1. HANDLE DELETE ---
if ($action === 'delete' && $id) {
    $galeri = executeQuerySingle("SELECT * FROM galeri WHERE id = ?", [$id]);
    
    if ($galeri) {
        if ($galeri['gambar_path']) {
            $file_path_abs = $_SERVER['DOCUMENT_ROOT'] . $galeri['gambar_path'];
            if (file_exists($file_path_abs)) {
                unlink($file_path_abs);
            }
        }
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

// --- 2. HANDLE FORM SUBMISSION (ADD & EDIT) ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['form_action'])) {
    $form_action = $_POST['form_action']; // 'add' atau 'edit'
    
    $judul = sanitize($_POST['judul'] ?? '');
    $deskripsi = sanitize($_POST['deskripsi'] ?? '');
    $tipe = sanitize($_POST['tipe'] ?? 'kegiatan');
    $tanggal_kegiatan = $_POST['tanggal_kegiatan'] ?? '';
    $lokasi = sanitize($_POST['lokasi'] ?? '');
    $is_featured = isset($_POST['is_featured']) ? 1 : 0;
    $id_edit = $_POST['id'] ?? null;
    
    $errors = [];
    
    // Validasi
    if (empty($judul)) $errors[] = "Judul harus diisi";
    if (empty($tanggal_kegiatan)) $errors[] = "Tanggal kegiatan harus diisi";
    
    // Upload Gambar
    $gambar_path = $_POST['gambar_lama'] ?? '';
    
    if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] === UPLOAD_ERR_OK) {
        $upload_result = uploadImage($_FILES['gambar'], 'galeri'); 
        
        if ($upload_result['success']) {
            $gambar_path = '/uploads/galeri/' . $upload_result['filename'];
            
            // Hapus gambar lama jika edit
            if ($form_action === 'edit' && $id_edit) {
                $old_galeri = executeQuerySingle("SELECT gambar_path FROM galeri WHERE id = ?", [$id_edit]);
                if ($old_galeri && $old_galeri['gambar_path']) {
                     if (file_exists($_SERVER['DOCUMENT_ROOT'] . $old_galeri['gambar_path'])) {
                        unlink($_SERVER['DOCUMENT_ROOT'] . $old_galeri['gambar_path']);
                    }
                }
            }
        } else {
            $errors[] = $upload_result['message'];
        }
    }
    
    if (empty($errors)) {
        if ($form_action === 'add') {
            $query = "INSERT INTO galeri (judul, deskripsi, gambar_path, tipe, tanggal_kegiatan, lokasi, is_featured, is_active) 
                      VALUES (?, ?, ?, ?, ?, ?, ?, true)";
            $params = [$judul, $deskripsi, $gambar_path, $tipe, $tanggal_kegiatan, $lokasi, $is_featured];
            $msg_success = 'Galeri berhasil ditambahkan';
        } elseif ($form_action === 'edit' && $id_edit) {
            $query = "UPDATE galeri SET judul = ?, deskripsi = ?, gambar_path = ?, tipe = ?, 
                      tanggal_kegiatan = ?, lokasi = ?, is_featured = ? WHERE id = ?";
            $params = [$judul, $deskripsi, $gambar_path, $tipe, $tanggal_kegiatan, $lokasi, $is_featured, $id_edit];
            $msg_success = 'Galeri berhasil diupdate';
        }
        
        if (isset($query)) {
            $result = executeNonQuery($query, $params);
            if ($result) {
                setFlashMessage('success', $msg_success);
                redirect(ADMIN_URL . '/galeri.php');
            } else {
                setFlashMessage('error', 'Gagal menyimpan data');
            }
        }
    } else {
        foreach ($errors as $error) setFlashMessage('error', $error);
    }
}

// --- 3. GET DATA (LIST & STATS) ---
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$filter_tipe = $_GET['filter_tipe'] ?? '';
$search = $_GET['search'] ?? '';

$limit = 10;
$offset = ($page - 1) * $limit;

// Query Builder
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

// Hitung Total
$total_query = "SELECT COUNT(*) as count FROM galeri WHERE " . $where_clause;
$total_result = executeQuerySingle($total_query, $params);
$total = $total_result['count'] ?? 0;
$total_pages = ceil($total / $limit);

// Ambil Data
$query = "SELECT * FROM galeri WHERE " . $where_clause . " ORDER BY tanggal_kegiatan DESC, created_at DESC LIMIT ? OFFSET ?";
$params[] = $limit;
$params[] = $offset;
$galeri_list = executeQuery($query, $params);

// Hitung Statistik
$count_agenda = countRows("SELECT COUNT(*) FROM galeri WHERE tipe = 'agenda' AND is_active = true");
$count_kegiatan = countRows("SELECT COUNT(*) FROM galeri WHERE tipe = 'kegiatan' AND is_active = true");
?>

<div class="space-y-6">
    
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Galeri & Agenda</h2>
            <p class="text-gray-600 mt-1">Kelola foto kegiatan dan agenda laboratorium</p>
        </div>
        <button onclick="openModalAdd()" class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded-lg shadow transition flex items-center gap-2">
            <i class="fas fa-plus"></i>Tambah Galeri
        </button>
    </div>
    
    <div class="bg-white rounded-lg shadow-md p-4">
        <form method="GET" class="flex flex-col md:flex-row gap-4">
            <div class="flex-1">
                <input type="text" name="search" placeholder="Cari judul, deskripsi, atau lokasi..." value="<?php echo htmlspecialchars($search); ?>" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none">
            </div>
            
            <!-- Filter Tipe -->
            <select name="filter_tipe" class="form-input md:w-48" onchange="this.form.submit()">
                <option value="">Semua Tipe</option>
                <option value="agenda" <?php echo $filter_tipe === 'agenda' ? 'selected' : ''; ?>>Agenda</option>
                <option value="kegiatan" <?php echo $filter_tipe === 'kegiatan' ? 'selected' : ''; ?>>Kegiatan</option>
            </select>
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded-lg shadow transition"><i class="fas fa-search mr-2"></i>Cari</button>
            <?php if ($search || $filter_tipe): ?>
                <a href="?action=list" class="bg-gray-500 hover:bg-gray-600 text-white px-5 py-2 rounded-lg shadow transition"><i class="fas fa-times mr-2"></i>Reset</a>
            <?php endif; ?>
        </form>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="bg-white rounded-lg shadow p-4 border-l-4 border-blue-500">
            <p class="text-gray-500 text-sm mb-1 uppercase font-bold tracking-wider">Total Galeri</p>
            <p class="text-2xl font-bold text-gray-800"><?php echo $total; ?></p>
        </div>
        <div class="bg-white rounded-lg shadow p-4 border-l-4 border-green-500">
            <p class="text-gray-500 text-sm mb-1 uppercase font-bold tracking-wider">Agenda</p>
            <p class="text-2xl font-bold text-green-600"><?php echo $count_agenda; ?></p>
        </div>
        <div class="bg-white rounded-lg shadow p-4 border-l-4 border-orange-500">
            <p class="text-gray-500 text-sm mb-1 uppercase font-bold tracking-wider">Kegiatan</p>
            <p class="text-2xl font-bold text-orange-600"><?php echo $count_kegiatan; ?></p>
        </div>
    </div>
    
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <?php if ($galeri_list && count($galeri_list) > 0): ?>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50 text-gray-600 text-xs uppercase font-bold border-b border-gray-200">
                        <th class="px-6 py-4 w-24">Gambar</th>
                        <th class="px-6 py-4">Judul & Info</th>
                        <th class="px-6 py-4 w-32 text-center">Tipe</th>
                        <th class="px-6 py-4 w-40">Tanggal</th>
                        <th class="px-6 py-4 w-32 text-center">Status</th>
                        <th class="px-6 py-4 w-40 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <?php foreach ($galeri_list as $item): ?>
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-6 py-4">
                            <div class="w-16 h-16 rounded-lg overflow-hidden bg-gray-100 border">
                                <img src="<?php echo htmlspecialchars($item['gambar_path']); ?>" class="w-full h-full object-cover" onerror="this.src='https://via.placeholder.com/150?text=No+Img'">
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <p class="font-bold text-gray-800 text-sm"><?php echo htmlspecialchars($item['judul']); ?></p>
                            <?php if ($item['lokasi']): ?>
                                <p class="text-xs text-gray-500 mt-1"><i class="fas fa-map-marker-alt mr-1"></i><?php echo htmlspecialchars($item['lokasi']); ?></p>
                            <?php endif; ?>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <?php if($item['tipe'] === 'agenda'): ?>
                                <span class="inline-block px-3 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800 border border-green-200">Agenda</span>
                            <?php else: ?>
                                <span class="inline-block px-3 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-800 border border-blue-200">Kegiatan</span>
                            <?php endif; ?>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600">
                            <i class="far fa-calendar-alt mr-1"></i>
                            <?php echo date('d M Y', strtotime($item['tanggal_kegiatan'])); ?>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <div class="flex flex-col gap-1 items-center">
                                <span class="inline-block px-2 py-0.5 rounded text-xs font-medium <?php echo $item['is_active'] ? 'text-green-600 bg-green-50' : 'text-red-600 bg-red-50'; ?>">
                                    <?php echo $item['is_active'] ? 'Aktif' : 'Nonaktif'; ?>
                                </span>
                                <?php if ($item['is_featured']): ?>
                                <span class="inline-block px-2 py-0.5 rounded text-xs font-medium bg-yellow-100 text-yellow-800 border border-yellow-200"><i class="fas fa-star text-xs mr-1"></i>Featured</span>
                                <?php endif; ?>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <div class="flex items-center justify-center gap-2">
                                <button onclick='viewDetail(<?php echo json_encode($item); ?>)' class="text-green-500 hover:bg-green-50 p-2 rounded-lg transition" title="Lihat Detail"><i class="fas fa-eye"></i></button>
                                <button onclick='openModalEdit(<?php echo json_encode($item); ?>)' class="text-blue-500 hover:bg-blue-50 p-2 rounded-lg transition" title="Edit"><i class="fas fa-edit"></i></button>
                                <a href="?action=delete&id=<?php echo $item['id']; ?>" class="text-red-500 hover:bg-red-50 p-2 rounded-lg transition" title="Hapus" onclick="return confirm('Yakin ingin menghapus data ini?');"><i class="fas fa-trash"></i></a>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        
        <?php if ($total_pages > 1): ?>
        <div class="px-6 py-4 border-t flex justify-between items-center bg-gray-50">
            <span class="text-sm text-gray-600">Halaman <?php echo $page; ?> dari <?php echo $total_pages; ?></span>
            <div class="flex gap-2">
                <?php if ($page > 1): ?>
                    <a href="?page=<?php echo $page - 1; ?>&search=<?php echo urlencode($search); ?>&filter_tipe=<?php echo $filter_tipe; ?>" class="px-3 py-1 bg-white border rounded hover:bg-gray-100 text-sm">Prev</a>
                <?php endif; ?>
                <?php if ($page < $total_pages): ?>
                    <a href="?page=<?php echo $page + 1; ?>&search=<?php echo urlencode($search); ?>&filter_tipe=<?php echo $filter_tipe; ?>" class="px-3 py-1 bg-white border rounded hover:bg-gray-100 text-sm">Next</a>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>
        <?php else: ?>
        <div class="text-center py-12">
            <i class="fas fa-images text-6xl text-gray-300 mb-4"></i>
            <p class="text-gray-500 text-lg">Belum ada galeri</p>
            <button onclick="openModalAdd()" class="inline-block mt-4 text-blue-600 hover:underline">Tambah Galeri Pertama</button>
        </div>
        <?php endif; ?>
    </div>
</div>

<div id="modalForm" class="fixed inset-0 bg-black bg-opacity-50 z-[60] hidden flex items-center justify-center opacity-0 transition-opacity duration-300">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-2xl transform scale-95 transition-transform duration-300 overflow-hidden" id="modalFormContent">
        
        <div class="flex justify-between items-center p-5 border-b bg-gray-50">
            <h3 id="modalFormTitle" class="text-lg font-bold text-gray-800">Tambah Galeri</h3>
            <button onclick="closeModalForm()" class="text-gray-400 hover:text-red-500 text-xl transition"><i class="fas fa-times"></i></button>
        </div>
        
        <form method="POST" enctype="multipart/form-data" class="p-6 space-y-4">
            <input type="hidden" name="form_action" id="formAction" value="add">
            <input type="hidden" name="id" id="formId">
            <input type="hidden" name="gambar_lama" id="formGambarLama">

            <div>
                <label class="block text-sm font-bold text-gray-700 mb-1">Judul <span class="text-red-500">*</span></label>
                <input type="text" name="judul" id="formJudul" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none" required placeholder="Judul kegiatan...">
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1">Tipe <span class="text-red-500">*</span></label>
                    <select name="tipe" id="formTipe" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none" required>
                        <option value="kegiatan">Kegiatan (Dokumentasi)</option>
                        <option value="agenda">Agenda (Akan Datang)</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1">Tanggal Kegiatan <span class="text-red-500">*</span></label>
                    <input type="date" name="tanggal_kegiatan" id="formTanggal" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none" required>
                </div>
            </div>

            <div>
                <label class="block text-sm font-bold text-gray-700 mb-1">Lokasi</label>
                <input type="text" name="lokasi" id="formLokasi" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none" placeholder="Lokasi kegiatan...">
            </div>

            <div>
                <label class="block text-sm font-bold text-gray-700 mb-1">Gambar</label>
                <input type="file" name="gambar" id="formGambar" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100" accept="image/*" onchange="previewImage(this)">
                <p class="text-xs text-gray-500 mt-1" id="formGambarHelp">Upload gambar baru (JPG, PNG). Maks 2MB.</p>
                <div class="mt-2">
                    <img id="formPreviewImg" src="" class="max-h-32 rounded shadow hidden">
                </div>
            </div>

            <div>
                <label class="block text-sm font-bold text-gray-700 mb-1">Deskripsi</label>
                <textarea name="deskripsi" id="formDeskripsi" rows="3" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none" placeholder="Deskripsi singkat..."></textarea>
            </div>

            <div class="flex items-center">
                <input type="checkbox" name="is_featured" id="formFeatured" class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                <label for="formFeatured" class="ml-2 block text-sm text-gray-900">Tampilkan di homepage sebagai <strong>Featured</strong></label>
            </div>

            <div class="flex items-center gap-4 pt-4 border-t">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg shadow transition font-medium"><i class="fas fa-save mr-2"></i>Simpan</button>
                <button type="button" onclick="closeModalForm()" class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-6 py-2 rounded-lg transition font-medium">Batal</button>
            </div>
        </form>
    </div>
</div>

<div id="modalDetail" class="fixed inset-0 bg-black bg-opacity-50 z-[70] hidden flex items-center justify-center opacity-0 transition-opacity duration-300">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-2xl transform scale-95 transition-transform duration-300 overflow-hidden" id="modalDetailContent">
        <div class="flex justify-between items-center p-5 border-b bg-gray-50">
            <h3 class="text-lg font-bold text-gray-800">Detail Galeri</h3>
            <button onclick="closeModalDetail()" class="text-gray-400 hover:text-red-500 text-xl transition"><i class="fas fa-times"></i></button>
        </div>
        <div class="p-6 overflow-y-auto max-h-[80vh]">
            <div class="flex flex-col md:flex-row gap-6">
                <div class="w-full md:w-1/2">
                    <div class="rounded-lg overflow-hidden shadow-sm border border-gray-200 bg-gray-100 flex items-center justify-center">
                        <img id="detailGambar" src="" class="w-full h-auto object-cover max-h-64">
                    </div>
                </div>
                <div class="w-full md:w-1/2 space-y-4">
                    <div>
                        <label class="text-xs font-bold text-gray-500 uppercase tracking-wide">Judul</label>
                        <h4 id="detailJudul" class="text-xl font-bold text-gray-900 mt-1 leading-tight"></h4>
                        <div id="detailFeatured" class="mt-1"></div>
                    </div>
                    <div class="flex gap-3">
                        <div>
                            <label class="text-xs font-bold text-gray-500 uppercase tracking-wide">Tipe</label>
                            <div id="detailTipe" class="mt-1"></div>
                        </div>
                        <div>
                            <label class="text-xs font-bold text-gray-500 uppercase tracking-wide">Tanggal</label>
                            <p id="detailTanggal" class="text-sm font-medium text-gray-800 mt-1"></p>
                        </div>
                    </div>
                    <div>
                        <label class="text-xs font-bold text-gray-500 uppercase tracking-wide">Lokasi</label>
                        <p id="detailLokasi" class="text-sm text-gray-700 mt-1"><i class="fas fa-map-marker-alt text-red-500 mr-1"></i><span></span></p>
                    </div>
                    <div>
                        <label class="text-xs font-bold text-gray-500 uppercase tracking-wide">Deskripsi</label>
                        <p id="detailDeskripsi" class="text-gray-600 text-sm mt-1 leading-relaxed text-justify"></p>
                    </div>
                </div>
            </div>
        </div>
        <div class="bg-gray-50 p-4 flex justify-end">
            <button onclick="closeModalDetail()" class="px-5 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition font-medium">Tutup</button>
        </div>
    </div>
</div>

<script>
    // --- VARIABLES ---
    const modalForm = document.getElementById('modalForm');
    const modalFormContent = document.getElementById('modalFormContent');
    const modalDetail = document.getElementById('modalDetail');
    const modalDetailContent = document.getElementById('modalDetailContent');

    // --- FORM MODAL LOGIC (ADD/EDIT) ---
    function openModalAdd() {
        // Reset Form
        document.getElementById('modalFormTitle').innerText = 'Tambah Galeri';
        document.getElementById('formAction').value = 'add';
        document.getElementById('formId').value = '';
        document.getElementById('formGambarLama').value = '';
        
        document.getElementById('formJudul').value = '';
        document.getElementById('formTipe').value = 'kegiatan';
        document.getElementById('formTanggal').value = '';
        document.getElementById('formLokasi').value = '';
        document.getElementById('formDeskripsi').value = '';
        document.getElementById('formFeatured').checked = false;
        
        document.getElementById('formGambar').value = ''; // Reset file input
        document.getElementById('formGambarHelp').innerText = 'Upload gambar baru (JPG, PNG). Maks 2MB.';
        document.getElementById('formPreviewImg').src = '';
        document.getElementById('formPreviewImg').classList.add('hidden');
        
        showModal(modalForm, modalFormContent);
    }

    function openModalEdit(data) {
        // Populate Form
        document.getElementById('modalFormTitle').innerText = 'Edit Galeri';
        document.getElementById('formAction').value = 'edit';
        document.getElementById('formId').value = data.id;
        document.getElementById('formGambarLama').value = data.gambar_path;
        
        document.getElementById('formJudul').value = data.judul;
        document.getElementById('formTipe').value = data.tipe;
        document.getElementById('formTanggal').value = data.tanggal_kegiatan;
        document.getElementById('formLokasi').value = data.lokasi;
        document.getElementById('formDeskripsi').value = data.deskripsi;
        document.getElementById('formFeatured').checked = (data.is_featured == 1);
        
        document.getElementById('formGambar').value = ''; // Reset file input
        document.getElementById('formGambarHelp').innerText = 'Biarkan kosong jika tidak ingin mengganti gambar.';
        
        if (data.gambar_path) {
            document.getElementById('formPreviewImg').src = data.gambar_path;
            document.getElementById('formPreviewImg').classList.remove('hidden');
        } else {
            document.getElementById('formPreviewImg').classList.add('hidden');
        }
        
        showModal(modalForm, modalFormContent);
    }

    function closeModalForm() {
        hideModal(modalForm, modalFormContent);
    }

    function previewImage(input) {
        const preview = document.getElementById('formPreviewImg');
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                preview.src = e.target.result;
                preview.classList.remove('hidden');
            }
            reader.readAsDataURL(input.files[0]);
        }
    }

    // --- DETAIL MODAL LOGIC (READ ONLY) ---
    function viewDetail(data) {
        document.getElementById('detailJudul').innerText = data.judul;
        document.getElementById('detailDeskripsi').innerText = data.deskripsi || '-';
        document.getElementById('detailLokasi').querySelector('span').innerText = data.lokasi || '-';
        
        const dateObj = new Date(data.tanggal_kegiatan);
        const options = { year: 'numeric', month: 'long', day: 'numeric' };
        document.getElementById('detailTanggal').innerText = dateObj.toLocaleDateString('id-ID', options);

        const imgElement = document.getElementById('detailGambar');
        imgElement.src = data.gambar_path ? data.gambar_path : "https://via.placeholder.com/400x300?text=No+Image";

        const tipeEl = document.getElementById('detailTipe');
        if (data.tipe === 'agenda') {
            tipeEl.innerHTML = `<span class="px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800 border border-green-200">Agenda</span>`;
        } else {
            tipeEl.innerHTML = `<span class="px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800 border border-blue-200">Kegiatan</span>`;
        }

        const featuredEl = document.getElementById('detailFeatured');
        featuredEl.innerHTML = data.is_featured ? `<span class="inline-block bg-yellow-100 text-yellow-800 text-xs px-2 py-0.5 rounded"><i class="fas fa-star mr-1"></i>Featured Content</span>` : '';

        showModal(modalDetail, modalDetailContent);
    }

    function closeModalDetail() {
        hideModal(modalDetail, modalDetailContent);
    }

    // --- GENERIC MODAL ANIMATION ---
    function showModal(modal, content) {
        modal.classList.remove('hidden');
        setTimeout(() => {
            modal.classList.remove('opacity-0');
            content.classList.remove('scale-95');
            content.classList.add('scale-100');
        }, 10);
    }

    function hideModal(modal, content) {
        modal.classList.add('opacity-0');
        content.classList.remove('scale-100');
        content.classList.add('scale-95');
        setTimeout(() => {
            modal.classList.add('hidden');
        }, 300);
    }

    // Close on click outside
    window.onclick = function(event) {
        if (event.target == modalForm) closeModalForm();
        if (event.target == modalDetail) closeModalDetail();
    }
</script>

<?php
// Include admin footer
require_once __DIR__ . '/../includes/admin_footer.php';
?>