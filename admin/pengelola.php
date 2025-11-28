<?php
/**
 * Admin Pengelola Management
 * File: admin/pengelola.php
 */

// Set page title
$page_title = "Kelola Pengelola";

// Include admin header
require_once __DIR__ . '/../includes/admin_header.php';

// Handle parameters
$action = $_GET['action'] ?? 'list';
$id = $_GET['id'] ?? ($_POST['id'] ?? null);

// --- 1. HANDLE DELETE ---
if ($action === 'delete' && $id) {
    $pengelola = executeQuerySingle("SELECT * FROM pengelola WHERE id = ?", [$id]);

    if ($pengelola) {
        // Check relations (Arsip)
        $arsip_count = countRows("SELECT COUNT(*) FROM arsip_pengelola WHERE pengelola_id = ?", [$id]);

        if ($arsip_count > 0) {
            setFlashMessage('error', 'Gagal: Pengelola ini memiliki arsip terkait. Hapus relasi arsip terlebih dahulu.');
        } else {
            // Delete photo file
            if ($pengelola['foto_path']) {
                $file_path_abs = $_SERVER['DOCUMENT_ROOT'] . $pengelola['foto_path'];
                if (file_exists($file_path_abs)) {
                    unlink($file_path_abs);
                }
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
    exit;
}

// --- 2. HANDLE FORM SUBMISSION (ADD & EDIT VIA MODAL) ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['form_action'])) {
    $form_action = $_POST['form_action']; // 'add' or 'edit'
    $id_edit = $_POST['id'] ?? null;

    $nama_lengkap = sanitize($_POST['nama_lengkap'] ?? '');
    $nip_nidn = sanitize($_POST['nip_nidn'] ?? '');
    $jabatan = sanitize($_POST['jabatan'] ?? '');
    $pendidikan_terakhir = sanitize($_POST['pendidikan_terakhir'] ?? '');
    $bidang_keahlian = sanitize($_POST['bidang_keahlian'] ?? '');
    $email = sanitize($_POST['email'] ?? '');
    $no_telepon = sanitize($_POST['no_telepon'] ?? '');
    $urutan_tampil = isset($_POST['urutan_tampil']) ? (int) $_POST['urutan_tampil'] : 99;
    $is_active = isset($_POST['is_active']) ? 1 : 0;

    $errors = [];

    // Validasi Dasar
    if (empty($nama_lengkap))
        $errors[] = "Nama lengkap harus diisi";
    if (empty($nip_nidn))
        $errors[] = "NIP/NIDN harus diisi";
    if (empty($jabatan))
        $errors[] = "Jabatan harus diisi";
    if (empty($email))
        $errors[] = "Email harus diisi";
    if ($email && !filter_var($email, FILTER_VALIDATE_EMAIL))
        $errors[] = "Format email tidak valid";

    // Cek Duplikasi NIP/NIDN
    if ($form_action === 'add') {
        $existing = executeQuerySingle("SELECT id FROM pengelola WHERE nip_nidn = ?", [$nip_nidn]);
        if ($existing)
            $errors[] = "NIP/NIDN sudah terdaftar";
    } elseif ($form_action === 'edit' && $id_edit) {
        $existing = executeQuerySingle("SELECT id FROM pengelola WHERE nip_nidn = ? AND id != ?", [$nip_nidn, $id_edit]);
        if ($existing)
            $errors[] = "NIP/NIDN sudah terdaftar pada pengelola lain";
    }

    // Handle Photo Upload
    $foto_path = $_POST['foto_lama'] ?? '';

    if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
        $upload_result = uploadImage($_FILES['foto'], 'pengelola', 'pengelola');

        if ($upload_result['success']) {
            $foto_path = '/pengelola/' . $upload_result['filename'];

            // Hapus foto lama jika mode edit
            if ($form_action === 'edit' && $id_edit) {
                $old_data = executeQuerySingle("SELECT foto_path FROM pengelola WHERE id = ?", [$id_edit]);
                if ($old_data && $old_data['foto_path']) {
                    $old_file_abs = $_SERVER['DOCUMENT_ROOT'] . $old_data['foto_path'];
                    if (file_exists($old_file_abs))
                        unlink($old_file_abs);
                }
            }
        } else {
            $errors[] = $upload_result['message'];
        }
    }

    // Simpan ke Database
    if (empty($errors)) {
        if ($form_action === 'add') {
            $query = "INSERT INTO pengelola (nama_lengkap, nip_nidn, jabatan, pendidikan_terakhir, 
                      bidang_keahlian, email, no_telepon, foto_path, urutan_tampil, is_active) 
                      VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $params = [
                $nama_lengkap,
                $nip_nidn,
                $jabatan,
                $pendidikan_terakhir,
                $bidang_keahlian,
                $email,
                $no_telepon,
                $foto_path,
                $urutan_tampil,
                $is_active
            ];
            $msg_success = "Pengelola berhasil ditambahkan";
        } elseif ($form_action === 'edit' && $id_edit) {
            $query = "UPDATE pengelola SET nama_lengkap = ?, nip_nidn = ?, jabatan = ?, 
                      pendidikan_terakhir = ?, bidang_keahlian = ?, email = ?, no_telepon = ?, 
                      foto_path = ?, urutan_tampil = ?, is_active = ? WHERE id = ?";
            $params = [
                $nama_lengkap,
                $nip_nidn,
                $jabatan,
                $pendidikan_terakhir,
                $bidang_keahlian,
                $email,
                $no_telepon,
                $foto_path,
                $urutan_tampil,
                $is_active,
                $id_edit
            ];
            $msg_success = "Pengelola berhasil diperbarui";
        }

        if (isset($query)) {
            $result = executeNonQuery($query, $params); // Pastikan pakai executeInsert untuk INSERT jika helper membedakan

            if ($result) {
                setFlashMessage('success', $msg_success);
                redirect(ADMIN_URL . '/pengelola.php');
                exit;
            } else {
                setFlashMessage('error', 'Gagal menyimpan data ke database');
            }
        }
    } else {
        foreach ($errors as $error)
            setFlashMessage('error', $error);
    }
}

// --- 3. GET DATA LIST & FILTERS ---
$search = $_GET['search'] ?? '';
$filter_jabatan = $_GET['filter_jabatan'] ?? '';

// Build Query
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

// Fetch Data
$query = "SELECT * FROM pengelola WHERE " . $where_clause . " ORDER BY urutan_tampil ASC, nama_lengkap ASC";
$pengelola_list = executeQuery($query, $params);

// Fetch Jabatan unik untuk filter
$jabatan_list = executeQuery("SELECT DISTINCT jabatan FROM pengelola WHERE jabatan IS NOT NULL AND jabatan != '' ORDER BY jabatan");

?>

<div class="space-y-6">

    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Pengelola Laboratorium</h2>
            <p class="text-gray-600 mt-1">Kelola data tim pengelola laboratorium</p>
        </div>
        <button onclick="openModalAdd()"
            class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded-lg shadow transition flex items-center gap-2">
            <i class="fas fa-plus"></i>Tambah Pengelola
        </button>
    </div>

    <div class="bg-white rounded-lg shadow-md p-4">
        <form method="GET" class="flex flex-col md:flex-row gap-4">
            <div class="flex-1">
                <input type="text" name="search" placeholder="Cari nama, NIP/NIDN, email..."
                    value="<?php echo htmlspecialchars($search); ?>"
                    class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none">
            </div>

            <select name="filter_jabatan"
                class="w-full md:w-64 border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none">
                <option value="">Semua Jabatan</option>
                <?php if ($jabatan_list): ?>
                    <?php foreach ($jabatan_list as $jab): ?>
                        <option value="<?php echo htmlspecialchars($jab['jabatan']); ?>" <?php echo $filter_jabatan === $jab['jabatan'] ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($jab['jabatan']); ?>
                        </option>
                    <?php endforeach; ?>
                <?php endif; ?>
            </select>

            <button type="submit"
                class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded-lg shadow transition">
                <i class="fas fa-search mr-2"></i>Cari
            </button>

            <?php if ($search || $filter_jabatan): ?>
                <a href="pengelola.php"
                    class="bg-gray-500 hover:bg-gray-600 text-white px-5 py-2 rounded-lg shadow transition flex items-center">
                    <i class="fas fa-times mr-2"></i>Reset
                </a>
            <?php endif; ?>
        </form>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="bg-white rounded-lg shadow p-4 border-l-4 border-blue-500">
            <p class="text-gray-500 text-sm mb-1 uppercase font-bold">Total Pengelola</p>
            <p class="text-2xl font-bold text-gray-800">
                <?php echo countRows("SELECT COUNT(*) FROM pengelola WHERE is_active = true"); ?>
            </p>
        </div>
        <div class="bg-white rounded-lg shadow p-4 border-l-4 border-purple-500">
            <p class="text-gray-500 text-sm mb-1 uppercase font-bold">Kepala Lab</p>
            <p class="text-2xl font-bold text-purple-600">
                <?php echo countRows("SELECT COUNT(*) FROM pengelola WHERE jabatan ILIKE '%kepala%' AND is_active = true"); ?>
            </p>
        </div>
        <div class="bg-white rounded-lg shadow p-4 border-l-4 border-green-500">
            <p class="text-gray-500 text-sm mb-1 uppercase font-bold">Teknisi</p>
            <p class="text-2xl font-bold text-green-600">
                <?php echo countRows("SELECT COUNT(*) FROM pengelola WHERE jabatan ILIKE '%teknisi%' AND is_active = true"); ?>
            </p>
        </div>
        <div class="bg-white rounded-lg shadow p-4 border-l-4 border-orange-500">
            <p class="text-gray-500 text-sm mb-1 uppercase font-bold">Peneliti</p>
            <p class="text-2xl font-bold text-orange-600">
                <?php echo countRows("SELECT COUNT(*) FROM pengelola WHERE jabatan ILIKE '%peneliti%' AND is_active = true"); ?>
            </p>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <?php if ($pengelola_list && count($pengelola_list) > 0): ?>
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-50 text-gray-600 text-xs uppercase font-bold border-b border-gray-200">
                            <th class="px-6 py-4 w-20">Foto</th>
                            <th class="px-6 py-4">Nama & NIP/NIDN</th>
                            <th class="px-6 py-4 w-48">Jabatan</th>
                            <th class="px-6 py-4 w-48">Kontak</th>
                            <th class="px-6 py-4 w-24 text-center">Status</th>
                            <th class="px-6 py-4 w-40 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <?php foreach ($pengelola_list as $item): ?>
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-6 py-4">
                                    <img src="<?php echo UPLOAD_URL . htmlspecialchars($item['foto_path']); ?>"
                                        alt="<?php echo htmlspecialchars($item['nama_lengkap']); ?>"
                                        class="w-12 h-12 object-cover rounded-full border border-gray-200"
                                        onerror="this.src='<?php echo ASSETS_URL; ?>/img/no-image.png'">
                                </td>
                                <td class="px-6 py-4">
                                    <p class="font-bold text-gray-800 text-sm">
                                        <?php echo htmlspecialchars($item['nama_lengkap']); ?>
                                    </p>
                                    <p class="text-xs text-gray-500">NIP: <?php echo htmlspecialchars($item['nip_nidn']); ?></p>
                                    <?php if ($item['bidang_keahlian']): ?>
                                        <p class="text-xs text-blue-600 mt-1"><i
                                                class="fas fa-graduation-cap mr-1"></i><?php echo htmlspecialchars($item['bidang_keahlian']); ?>
                                        </p>
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-4">
                                    <span
                                        class="inline-block px-3 py-1 rounded-full text-xs font-semibold bg-purple-100 text-purple-800 border border-purple-200">
                                        <?php echo htmlspecialchars($item['jabatan']); ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600">
                                    <div class="flex flex-col gap-1">
                                        <span class="flex items-center text-xs"><i
                                                class="fas fa-envelope text-blue-500 w-4"></i>
                                            <?php echo htmlspecialchars($item['email']); ?></span>
                                        <?php if ($item['no_telepon']): ?>
                                            <span class="flex items-center text-xs"><i class="fas fa-phone text-green-500 w-4"></i>
                                                <?php echo htmlspecialchars($item['no_telepon']); ?></span>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span
                                        class="inline-block px-2 py-1 rounded text-xs font-medium <?php echo $item['is_active'] ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'; ?>">
                                        <?php echo $item['is_active'] ? 'Aktif' : 'Nonaktif'; ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <div class="flex items-center justify-center gap-2">
                                        <button onclick='viewDetail(<?php echo json_encode($item); ?>)'
                                            class="text-green-500 hover:bg-green-50 p-2 rounded-lg transition"
                                            title="Lihat Detail">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button onclick='openModalEdit(<?php echo json_encode($item); ?>)'
                                            class="text-blue-500 hover:bg-blue-50 p-2 rounded-lg transition" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <a href="?action=delete&id=<?php echo $item['id']; ?>"
                                            class="text-red-500 hover:bg-red-50 p-2 rounded-lg transition" title="Hapus"
                                            onclick="return confirm('Yakin hapus data ini?');">
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
                <p class="text-gray-500 text-lg">Belum ada data pengelola</p>
                <button onclick="openModalAdd()" class="text-blue-600 hover:underline mt-2">Tambah Pengelola
                    Pertama</button>
            </div>
        <?php endif; ?>
    </div>
</div>

<div id="modalForm"
    class="fixed inset-0 bg-black bg-opacity-50 z-[60] hidden flex items-center justify-center opacity-0 transition-opacity duration-300">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-3xl transform scale-95 transition-transform duration-300 overflow-hidden"
        id="modalFormContent">

        <div class="flex justify-between items-center p-5 border-b bg-gray-50">
            <h3 id="modalFormTitle" class="text-lg font-bold text-gray-800">Tambah Pengelola</h3>
            <button onclick="closeModalForm()" class="text-gray-400 hover:text-red-500 text-xl transition"><i
                    class="fas fa-times"></i></button>
        </div>

        <form method="POST" enctype="multipart/form-data" class="p-6">
            <input type="hidden" name="form_action" id="formAction" value="add">
            <input type="hidden" name="id" id="formId">
            <input type="hidden" name="foto_lama" id="formFotoLama">

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1">Nama Lengkap <span
                                class="text-red-500">*</span></label>
                        <input type="text" name="nama_lengkap" id="formNama"
                            class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none"
                            required placeholder="Dr. Nama Lengkap, Gelar">
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1">NIP / NIDN <span
                                class="text-red-500">*</span></label>
                        <input type="text" name="nip_nidn" id="formNip"
                            class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none"
                            required placeholder="1234567890">
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1">Jabatan <span
                                class="text-red-500">*</span></label>
                        <input type="text" name="jabatan" id="formJabatan" list="jabatanList"
                            class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none"
                            required placeholder="Kepala Laboratorium">
                        <datalist id="jabatanList">
                            <option value="Kepala Laboratorium">
                            <option value="Teknisi">
                            <option value="Laboran">
                            <option value="Asisten Peneliti">
                        </datalist>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1">Pendidikan Terakhir</label>
                        <select name="pendidikan_terakhir" id="formPendidikan"
                            class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none">
                            <option value="">-- Pilih --</option>
                            <option value="D3">D3</option>
                            <option value="S1">S1</option>
                            <option value="S2">S2</option>
                            <option value="S3">S3</option>
                        </select>
                    </div>
                </div>

                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1">Email <span
                                class="text-red-500">*</span></label>
                        <input type="email" name="email" id="formEmail"
                            class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none"
                            required placeholder="email@contoh.com">
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1">No. Telepon</label>
                        <input type="tel" name="no_telepon" id="formTelp"
                            class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none"
                            placeholder="08123456789">
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1">Bidang Keahlian</label>
                        <input type="text" name="bidang_keahlian" id="formKeahlian"
                            class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none"
                            placeholder="Jaringan, Keamanan, dll">
                    </div>
                    <div class="flex gap-4">
                        <div class="flex-1">
                            <label class="block text-sm font-bold text-gray-700 mb-1">Urutan</label>
                            <input type="number" name="urutan_tampil" id="formUrutan"
                                class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none"
                                value="99">
                        </div>
                        <div class="flex items-center pt-6">
                            <input type="checkbox" name="is_active" id="formActive"
                                class="w-4 h-4 text-blue-600 rounded" checked>
                            <label for="formActive" class="ml-2 text-sm text-gray-700">Aktif</label>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-4 pt-4 border-t">
                <label class="block text-sm font-bold text-gray-700 mb-2">Foto Profil</label>
                <div class="flex items-center gap-4">
                    <img id="formPreviewImg" src="" class="w-16 h-16 rounded-full object-cover border hidden">
                    <input type="file" name="foto" id="formFoto"
                        class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100"
                        accept="image/*" onchange="previewImage(this)">
                </div>
                <p id="fotoHelp" class="text-xs text-gray-500 mt-1">Upload foto baru (JPG, PNG). Maks 2MB.</p>
            </div>

            <div class="flex items-center justify-end gap-3 mt-6 pt-4 border-t bg-gray-50 -mx-6 -mb-6 p-4">
                <button type="button" onclick="closeModalForm()"
                    class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">Batal</button>
                <button type="submit"
                    class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 shadow transition">Simpan
                    Data</button>
            </div>
        </form>
    </div>
</div>

<div id="modalDetail"
    class="fixed inset-0 bg-black bg-opacity-50 z-[70] hidden flex items-center justify-center opacity-0 transition-opacity duration-300">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-lg transform scale-95 transition-transform duration-300 overflow-hidden"
        id="modalDetailContent">

        <div class="bg-gradient-to-r from-blue-600 to-blue-800 p-6 text-center relative">
            <button onclick="closeModalDetail()"
                class="absolute top-4 right-4 text-white/70 hover:text-white text-2xl transition"><i
                    class="fas fa-times"></i></button>

            <div class="relative w-24 h-24 mx-auto mb-3">
                <img id="detailFoto" src=""
                    class="w-full h-full object-cover rounded-full border-4 border-white shadow-lg bg-white">
                <div id="detailStatusBadge"
                    class="absolute bottom-0 right-0 w-6 h-6 rounded-full border-2 border-white"></div>
            </div>

            <h3 id="detailNama" class="text-xl font-bold text-white"></h3>
            <p id="detailNip" class="text-blue-100 text-sm"></p>
            <div id="detailJabatan"
                class="mt-2 inline-block px-3 py-1 bg-white/20 backdrop-blur-sm rounded-full text-white text-xs font-semibold">
            </div>
        </div>

        <div class="p-6 space-y-4">
            <div class="grid grid-cols-2 gap-4 text-sm">
                <div>
                    <p class="text-gray-500 text-xs uppercase tracking-wide">Pendidikan</p>
                    <p id="detailPendidikan" class="font-medium text-gray-800"></p>
                </div>
                <div>
                    <p class="text-gray-500 text-xs uppercase tracking-wide">Bidang Keahlian</p>
                    <p id="detailKeahlian" class="font-medium text-gray-800"></p>
                </div>
            </div>

            <hr class="border-gray-100">

            <div class="space-y-3">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-full bg-blue-50 flex items-center justify-center text-blue-600"><i
                            class="fas fa-envelope"></i></div>
                    <div>
                        <p class="text-gray-500 text-xs">Email</p>
                        <p id="detailEmail" class="text-sm font-medium text-gray-800"></p>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-full bg-green-50 flex items-center justify-center text-green-600"><i
                            class="fas fa-phone"></i></div>
                    <div>
                        <p class="text-gray-500 text-xs">No. Telepon</p>
                        <p id="detailTelp" class="text-sm font-medium text-gray-800"></p>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-gray-50 p-4 text-center">
            <button onclick="closeModalDetail()"
                class="px-6 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 font-medium w-full">Tutup</button>
        </div>
    </div>
</div>

<script>
    // --- VARIABLES ---
    const modalForm = document.getElementById('modalForm');
    const modalFormContent = document.getElementById('modalFormContent');
    const modalDetail = document.getElementById('modalDetail');
    const modalDetailContent = document.getElementById('modalDetailContent');

    // --- FORM MODAL LOGIC ---
    function openModalAdd() {
        document.getElementById('modalFormTitle').innerText = 'Tambah Pengelola';
        document.getElementById('formAction').value = 'add';
        document.getElementById('formId').value = '';
        document.getElementById('formFotoLama').value = '';

        // Reset Inputs
        document.getElementById('formNama').value = '';
        document.getElementById('formNip').value = '';
        document.getElementById('formJabatan').value = '';
        document.getElementById('formPendidikan').value = '';
        document.getElementById('formKeahlian').value = '';
        document.getElementById('formEmail').value = '';
        document.getElementById('formTelp').value = '';
        document.getElementById('formUrutan').value = '99';
        document.getElementById('formActive').checked = true;

        // Reset Foto
        document.getElementById('formFoto').value = '';
        document.getElementById('fotoHelp').innerText = 'Upload foto baru (JPG, PNG). Maks 2MB.';
        document.getElementById('formPreviewImg').src = '';
        document.getElementById('formPreviewImg').classList.add('hidden');

        showModal(modalForm, modalFormContent);
    }

    function openModalEdit(data) {
        document.getElementById('modalFormTitle').innerText = 'Edit Pengelola';
        document.getElementById('formAction').value = 'edit';
        document.getElementById('formId').value = data.id;
        document.getElementById('formFotoLama').value = data.foto_path;

        // Fill Inputs
        document.getElementById('formNama').value = data.nama_lengkap;
        document.getElementById('formNip').value = data.nip_nidn;
        document.getElementById('formJabatan').value = data.jabatan;
        document.getElementById('formPendidikan').value = data.pendidikan_terakhir || '';
        document.getElementById('formKeahlian').value = data.bidang_keahlian || '';
        document.getElementById('formEmail').value = data.email;
        document.getElementById('formTelp').value = data.no_telepon || '';
        document.getElementById('formUrutan').value = data.urutan_tampil;
        document.getElementById('formActive').checked = (data.is_active == 1);

        // Handle Foto
        document.getElementById('formFoto').value = '';
        document.getElementById('fotoHelp').innerText = 'Biarkan kosong jika tidak ingin mengganti foto.';

        if (data.foto_path) {
            document.getElementById('formPreviewImg').src = "<?php echo UPLOAD_URL; ?>" + data.foto_path;
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
            reader.onload = function (e) {
                preview.src = e.target.result;
                preview.classList.remove('hidden');
            }
            reader.readAsDataURL(input.files[0]);
        }
    }

    // --- DETAIL MODAL LOGIC ---
    function viewDetail(data) {
        document.getElementById('detailNama').innerText = data.nama_lengkap;
        document.getElementById('detailNip').innerText = "NIP/NIDN: " + data.nip_nidn;
        document.getElementById('detailJabatan').innerText = data.jabatan;
        document.getElementById('detailPendidikan').innerText = data.pendidikan_terakhir || '-';
        document.getElementById('detailKeahlian').innerText = data.bidang_keahlian || '-';
        document.getElementById('detailEmail').innerText = data.email;
        document.getElementById('detailTelp').innerText = data.no_telepon || '-';

        const imgEl = document.getElementById('detailFoto');
        imgEl.src = data.foto_path ? "<?php echo UPLOAD_URL; ?>" + data.foto_path : "<?php echo ASSETS_URL; ?>/img/no-image.png";

        const statusBadge = document.getElementById('detailStatusBadge');
        if (data.is_active == 1) {
            statusBadge.className = "absolute bottom-0 right-0 w-6 h-6 rounded-full border-2 border-white bg-green-500";
            statusBadge.title = "Aktif";
        } else {
            statusBadge.className = "absolute bottom-0 right-0 w-6 h-6 rounded-full border-2 border-white bg-red-500";
            statusBadge.title = "Nonaktif";
        }

        showModal(modalDetail, modalDetailContent);
    }

    function closeModalDetail() {
        hideModal(modalDetail, modalDetailContent);
    }

    // --- GENERIC ANIMATION ---
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

    window.onclick = function (event) {
        if (event.target == modalForm) closeModalForm();
        if (event.target == modalDetail) closeModalDetail();
    }
</script>

<?php require_once __DIR__ . '/../includes/admin_footer.php'; ?>
?>