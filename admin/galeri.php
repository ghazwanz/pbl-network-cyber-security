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
        // Delete image file using deleteFile function
        if ($galeri['gambar_path']) {
            deleteFile($galeri['gambar_path']);
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
    if (empty($judul))
        $errors[] = "Judul harus diisi";
    if (empty($tanggal_kegiatan))
        $errors[] = "Tanggal kegiatan harus diisi";

    // Upload Gambar
    $gambar_path = $_POST['gambar_lama'] ?? '';

    if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] === UPLOAD_ERR_OK) {
        $upload_result = uploadImage($_FILES['gambar'], 'galeri');

        if ($upload_result['success']) {
            $gambar_path = '/galeri/' . $upload_result['filename'];

            // Hapus gambar lama jika edit
            if ($form_action === 'edit' && $id_edit) {
                $old_galeri = executeQuerySingle("SELECT gambar_path FROM galeri WHERE id = ?", [$id_edit]);
                if ($old_galeri && $old_galeri['gambar_path']) {
                    deleteFile($old_galeri['gambar_path']);
                }
            }
        } else {
            $errors[] = $upload_result['message'];
        }
    }

    if (empty($errors)) {
        // Ambil ID Admin yang sedang login
        $admin = getCurrentUser();
        $id_admin = $admin['id'] ?? null;

        if ($form_action === 'add') {
            $query = "INSERT INTO galeri (judul, deskripsi, gambar_path, tipe, tanggal_kegiatan, lokasi, is_featured, is_active, id_admin) 
                      VALUES (?, ?, ?, ?, ?, ?, ?, true, ?)";
            $params = [
                $judul,
                $deskripsi,
                $gambar_path,
                $tipe,
                $tanggal_kegiatan,
                $lokasi,
                $is_featured,
                $id_admin
            ];
            $msg_success = 'Galeri berhasil ditambahkan';
        } elseif ($form_action === 'edit' && $id_edit) {
            $query = "UPDATE galeri SET judul = ?, deskripsi = ?, gambar_path = ?, tipe = ?, 
                      tanggal_kegiatan = ?, lokasi = ?, is_featured = ?, id_admin = ? WHERE id = ?";
            $params = [
                $judul,
                $deskripsi,
                $gambar_path,
                $tipe,
                $tanggal_kegiatan,
                $lokasi,
                $is_featured,
                $id_admin,
                $id_edit
            ];
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
        foreach ($errors as $error)
            setFlashMessage('error', $error);
    }
}

// --- 3. GET DATA (LIST & STATS) ---
$page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
$filter_tipe = $_GET['filter_tipe'] ?? '';
$search = $_GET['search'] ?? '';

$limit = 10;
$offset = ($page - 1) * $limit;

// Build Query
$where = [];
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

$where_clause = count($where) > 0 ? 'WHERE ' . implode(' AND ', $where) : '';

// Hitung Total Data (Pagination)
$count_query = "SELECT COUNT(*) FROM galeri " . $where_clause;
$total = countRows($count_query, $params);
$total_pages = ceil($total / $limit);

// Query Utama
$query = "SELECT g.*, u.nama_lengkap AS created_by_name
          FROM galeri g
          LEFT JOIN users u ON g.id_admin = u.id
          " . $where_clause . "
          ORDER BY g.tanggal_kegiatan ASC
          LIMIT ? OFFSET ?";
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
            <h2 class="text-lg font-semibold text-blue-900"><i class="fas fa-images mr-2"></i>Galeri & Agenda</h2>
            <p class="text-gray-600 mt-1">Kelola foto kegiatan dan agenda laboratorium</p>
        </div>
        <button type="button" data-toggle="modal" data-target="#modalForm" onclick="openModalAdd()"
            class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded-lg shadow transition flex items-center gap-2">
            <i class="fas fa-plus"></i>Tambah Galeri
        </button>
    </div>

    <div class="bg-white rounded-lg shadow-md p-4">
        <form method="GET" class="flex flex-col md:flex-row gap-4">
            <div class="flex-1">
                <input type="text" name="search" placeholder="Cari judul, deskripsi, atau lokasi..."
                    value="<?php echo htmlspecialchars($search); ?>"
                    class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none">
            </div>
            <select name="filter_tipe"
                class="border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none md:w-48"
                onchange="this.form.submit()">
                <option value="">Semua Tipe</option>
                <option value="agenda" <?php echo $filter_tipe === 'agenda' ? 'selected' : ''; ?>>Agenda</option>
                <option value="kegiatan" <?php echo $filter_tipe === 'kegiatan' ? 'selected' : ''; ?>>Kegiatan</option>
            </select>
            <button type="submit"
                class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded-lg shadow transition"><i
                    class="fas fa-search mr-2"></i>Cari</button>
            <?php if ($search || $filter_tipe): ?>
                <a href="?action=list"
                    class="bg-gray-500 hover:bg-gray-600 text-white px-5 py-2 rounded-lg shadow transition"><i
                        class="fas fa-times mr-2"></i>Reset</a>
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

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
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
                            <th class="px-6 py-4 w-40 text-center">Dibuat oleh</th>
                            <th class="px-6 py-4 w-40 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <?php foreach ($galeri_list as $item): ?>
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-6 py-4">
                                    <div class="w-16 h-16 rounded-lg overflow-hidden bg-gray-100 border">
                                        <img src="<?php echo "../uploads/" . htmlspecialchars($item['gambar_path']); ?>"
                                            class="w-full h-full object-cover"
                                            onerror="this.src='https://via.placeholder.com/150?text=No+Img'">
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <p class="font-bold text-gray-800 text-sm"><?php echo htmlspecialchars($item['judul']); ?>
                                    </p>
                                    <?php if ($item['lokasi']): ?>
                                        <p class="text-xs text-gray-500 mt-1"><i
                                                class="fas fa-map-marker-alt mr-1"></i><?php echo htmlspecialchars($item['lokasi']); ?>
                                        </p>
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <?php if ($item['tipe'] === 'agenda'): ?>
                                        <span
                                            class="inline-block px-3 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800 border border-green-200">Agenda</span>
                                    <?php else: ?>
                                        <span
                                            class="inline-block px-3 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-800 border border-blue-200">Kegiatan</span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600">
                                    <i class="far fa-calendar-alt mr-1"></i>
                                    <?php echo date('d M Y', strtotime($item['tanggal_kegiatan'])); ?>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <div class="flex flex-col gap-1 items-center">
                                        <span
                                            class="inline-block px-2 py-0.5 rounded text-xs font-medium <?php echo $item['is_active'] ? 'text-green-600 bg-green-50' : 'text-red-600 bg-red-50'; ?>">
                                            <?php echo $item['is_active'] ? 'Aktif' : 'Nonaktif'; ?>
                                        </span>
                                        <?php if ($item['is_featured']): ?>
                                            <span
                                                class="inline-block px-2 py-0.5 rounded text-xs font-medium bg-yellow-100 text-yellow-800 border border-yellow-200"><i
                                                    class="fas fa-star text-xs mr-1"></i>Featured</span>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-2">
                                        <div
                                            class="w-8 h-8 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 font-semibold text-sm">
                                            <?php
                                            if ($item['created_by_name']) {
                                                $initials = '';
                                                $words = explode(' ', $item['created_by_name']);
                                                foreach ($words as $word) {
                                                    $initials .= strtoupper(substr($word, 0, 1));
                                                    if (strlen($initials) >= 2)
                                                        break;
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
                                <td class="px-6 py-4 text-center">
                                    <div class="flex items-center justify-center gap-2">
                                        <button type="button" data-toggle="modal" data-target="#modalDetail"
                                            onclick='viewDetail(<?php echo json_encode($item); ?>)'
                                            class="text-green-500 hover:bg-green-50 p-2 rounded-lg transition"
                                            title="Lihat Detail"><i class="fas fa-eye"></i></button>
                                        <button type="button" data-toggle="modal" data-target="#modalForm"
                                            onclick='openModalEdit(<?php echo json_encode($item); ?>)'
                                            class="text-blue-500 hover:bg-blue-50 p-2 rounded-lg transition" title="Edit"><i
                                                class="fas fa-edit"></i></button>
                                        <a href="?action=delete&id=<?php echo $item['id']; ?>"
                                            class="text-red-500 hover:bg-red-50 p-2 rounded-lg transition" title="Hapus"
                                            onclick="return confirm('Yakin ingin menghapus data ini?');"><i
                                                class="fas fa-trash"></i></a>
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
                            <a href="?page=<?php echo $page - 1; ?>&search=<?php echo urlencode($search); ?>&filter_tipe=<?php echo $filter_tipe; ?>"
                                class="px-3 py-1 bg-white border rounded hover:bg-gray-100 text-sm">Prev</a>
                        <?php endif; ?>
                        <?php if ($page < $total_pages): ?>
                            <a href="?page=<?php echo $page + 1; ?>&search=<?php echo urlencode($search); ?>&filter_tipe=<?php echo $filter_tipe; ?>"
                                class="px-3 py-1 bg-white border rounded hover:bg-gray-100 text-sm">Next</a>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endif; ?>
        <?php else: ?>
            <div class="text-center py-12">
                <i class="fas fa-images text-6xl text-gray-300 mb-4"></i>
                <p class="text-gray-500 text-lg">Belum ada galeri</p>
                <button type="button" data-toggle="modal" data-target="#modalForm" onclick="openModalAdd()"
                    class="inline-block mt-4 text-blue-600 hover:underline">Tambah Galeri Pertama</button>
            </div>
        <?php endif; ?>
    </div>
</div>

<div id="modalForm" aria-hidden="true"
    class="fixed inset-0 bg-slate-950/50 flex justify-center items-center opacity-0 pointer-events-none transition-opacity duration-300 ease-out z-[9999]">
    <div
        class="bg-white rounded-xl shadow-2xl border border-slate-200 w-full max-w-2xl scale-95 transition-transform duration-300 max-h-[90vh] overflow-y-auto mx-4">

        <div class="p-5 pb-3 flex justify-between items-center border-b border-slate-200 sticky top-0 bg-white z-10">
            <h1 id="modalFormTitle" class="text-lg text-slate-800 font-semibold">
                <i class="fas fa-images mr-2 text-blue-600"></i>Tambah Galeri
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

        <form method="POST" enctype="multipart/form-data">
            <div class="p-6 pt-4">
                <input type="hidden" name="form_action" id="formAction" value="add">
                <input type="hidden" name="id" id="formId">
                <input type="hidden" name="gambar_lama" id="formGambarLama">

                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">Judul <span
                                class="text-red-500">*</span></label>
                        <input type="text" name="judul" id="formJudul" required
                            class="w-full border border-slate-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 focus:outline-none"
                            placeholder="Judul kegiatan...">
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-2">Tipe <span
                                    class="text-red-500">*</span></label>
                            <select name="tipe" id="formTipe" required
                                class="w-full border border-slate-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 focus:outline-none">
                                <option value="kegiatan">Kegiatan (Dokumentasi)</option>
                                <option value="agenda">Agenda (Akan Datang)</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-2">Tanggal Kegiatan <span
                                    class="text-red-500">*</span></label>
                            <input type="date" name="tanggal_kegiatan" id="formTanggal" required
                                class="w-full border border-slate-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 focus:outline-none">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">Lokasi</label>
                        <input type="text" name="lokasi" id="formLokasi"
                            class="w-full border border-slate-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 focus:outline-none"
                            placeholder="Lokasi kegiatan...">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">Gambar</label>
                        <input type="file" name="gambar" id="formGambar" data-preview="#preview-image" accept="image/*"
                            class="w-full border border-slate-300 rounded-lg px-4 py-2.5 focus:outline-none file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 cursor-pointer">
                        <p class="text-xs text-slate-500 mt-2" id="formGambarHelp">
                            <i class="fas fa-info-circle mr-1"></i>Format: JPG, PNG, GIF, WEBP. Maksimal 5MB.
                        </p>
                        <div class="mt-3">
                            <img id="preview-image" src="" alt="Preview"
                                class="w-32 h-32 object-cover rounded-lg border hidden">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">Deskripsi</label>
                        <textarea name="deskripsi" id="formDeskripsi" rows="4"
                            class="w-full border border-slate-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 focus:outline-none"
                            placeholder="Deskripsi singkat kegiatan..."></textarea>
                    </div>

                    <div class="flex items-center">
                        <input type="checkbox" name="is_featured" id="formFeatured"
                            class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                        <label for="formFeatured" class="ml-2 block text-sm text-gray-900">Tampilkan di homepage sebagai
                            <strong>Featured</strong></label>
                    </div>
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

<div id="modalDetail" aria-hidden="true"
    class="fixed inset-0 bg-slate-950/50 flex justify-center items-center opacity-0 pointer-events-none transition-opacity duration-300 ease-out z-[9999]">
    <div
        class="bg-white rounded-xl shadow-2xl border border-slate-200 w-full max-w-2xl scale-95 transition-transform duration-300 max-h-[90vh] overflow-y-auto mx-4">

        <div class="p-5 pb-3 flex justify-between items-center border-b border-slate-200 sticky top-0 bg-white z-10">
            <h1 class="text-lg text-slate-800 font-semibold">
                <i class="fas fa-eye mr-2 text-blue-600"></i>Detail Galeri
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

        <div class="p-5 pt-4">
            <div class="flex flex-col gap-6">

                <div class="w-fit mx-auto">
                    <div
                        class="rounded-lg overflow-hidden shadow-sm border border-gray-200 bg-gray-100 flex items-center justify-center min-h-[200px]">
                        <img id="detailGambar" src="" alt="Detail Gambar"
                            class="w-full max-w-[350px] aspect-square object-cover">
                    </div>
                </div>

                <div class="w-full space-y-4">
                    <div>
                        <label class="text-xs font-bold text-gray-500 uppercase tracking-wide">Judul</label>
                        <h4 id="detailJudul" class="text-xl font-bold text-gray-900 mt-1"></h4>
                        <div id="detailFeatured" class="mt-2"></div>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="text-xs font-bold text-gray-500 uppercase tracking-wide">Tipe</label>
                            <div id="detailTipe" class="mt-1 text-sm text-gray-800 font-medium"></div>
                        </div>
                        <div>
                            <label class="text-xs font-bold text-gray-500 uppercase tracking-wide">Tanggal</label>
                            <p id="detailTanggal" class="text-sm font-medium text-gray-800 mt-1"></p>
                        </div>
                    </div>

                    <div>
                        <label class="text-xs font-bold text-gray-500 uppercase tracking-wide">Lokasi</label>
                        <p id="detailLokasi" class="text-sm text-gray-700 mt-1">
                            <i class="fas fa-map-marker-alt text-red-500 mr-1"></i><span></span>
                        </p>
                    </div>
                    <div>
                        <label class="text-xs font-bold text-gray-500 uppercase tracking-wide">Deskripsi</label>
                        <p id="detailDeskripsi" class="text-gray-600 text-sm leading-relaxed break-words"></p>
                    </div>
                </div>
            </div>
        </div>

        <div class="p-5 pt-3 flex justify-end gap-3 border-t border-slate-200 sticky bottom-0 bg-white">
            <button type="button" data-dismiss="modal"
                class="inline-flex items-center justify-center px-4 py-2 rounded-md text-sm font-medium text-slate-600 hover:bg-slate-100 transition-all">
                <i class="fas fa-times mr-2"></i>Tutup
            </button>
        </div>
    </div>
</div>

<script>
    // --- FORM MODAL LOGIC (ADD/EDIT) ---
    function openModalAdd() {
        // Reset Form
        $('#modalFormTitle').html('<i class="fas fa-plus-circle mr-2 text-blue-600"></i>Tambah Galeri');
        $('#formAction').val('add');
        $('#formId').val('');
        $('#formGambarLama').val('');

        $('#formJudul').val('');
        $('#formTipe').val('kegiatan');
        $('#formTanggal').val('');
        $('#formLokasi').val('');
        $('#formDeskripsi').val('');
        $('#formFeatured').prop('checked', false);

        $('#formGambar').val('');
        $('#formGambarHelp').html('<i class="fas fa-info-circle mr-1"></i>Format: JPG, PNG, GIF, WEBP. Maksimal 5MB.');
        $('#preview-image').attr('src', '').addClass('hidden');
    }

    function openModalEdit(data) {
        // Populate Form
        $('#modalFormTitle').html('<i class="fas fa-edit mr-2 text-blue-600"></i>Edit Galeri');
        $('#formAction').val('edit');
        $('#formId').val(data.id);
        $('#formGambarLama').val(data.gambar_path);

        $('#formJudul').val(data.judul);
        $('#formTipe').val(data.tipe);
        $('#formTanggal').val(data.tanggal_kegiatan);
        $('#formLokasi').val(data.lokasi || '');
        $('#formDeskripsi').val(data.deskripsi || '');
        $('#formFeatured').prop('checked', data.is_featured == 1);

        $('#formGambar').val('');
        $('#formGambarHelp').html('<i class="fas fa-info-circle mr-1"></i>Biarkan kosong jika tidak ingin mengganti gambar.');
        if (data.gambar_path) {
            $('#preview-image').attr('src', `../uploads/${data.gambar_path}`).removeClass('hidden');
        } else {
            $('#preview-image').attr('src', '').addClass('hidden');
        }
    }

    // --- DETAIL MODAL LOGIC (READ ONLY) ---
    function viewDetail(data) {
        $('#detailJudul').text(data.judul);
        $('#detailDeskripsi').text(data.deskripsi || '-');
        $('#detailLokasi span').text(data.lokasi || '-');
        $('#detailCreator').text(data.created_at || '-');

        const dateObj = new Date(data.tanggal_kegiatan);
        const options = { year: 'numeric', month: 'long', day: 'numeric' };
        $('#detailTanggal').text(dateObj.toLocaleDateString('id-ID', options));

        const imgSrc = data.gambar_path ? `../uploads/${data.gambar_path}` : 'https://via.placeholder.com/400x300?text=No+Image';
        $('#detailGambar').attr('src', imgSrc);

        let tipeBadge = '';
        if (data.tipe === 'agenda') {
            tipeBadge = '<span class="px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800 border border-green-200">Agenda</span>';
        } else {
            tipeBadge = '<span class="px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800 border border-blue-200">Kegiatan</span>';
        }
        $('#detailTipe').html(tipeBadge);

        const featuredBadge = data.is_featured ? '<span class="inline-block bg-yellow-100 text-yellow-800 text-xs px-2 py-1 rounded-full border border-yellow-200"><i class="fas fa-star mr-1"></i>Featured</span>' : '';
        $('#detailFeatured').html(featuredBadge);
    }
</script>

<?php
// Include admin footer
require_once __DIR__ . '/../includes/admin_footer.php';
?>