<?php

$page_title = "Layanan";
require_once __DIR__ . '/../includes/admin_header.php';

// Handle actions
$action = $_GET['action'] ?? ($_POST['action'] ?? 'list');
$id = $_GET['id'] ?? ($_POST['id'] ?? null);

// Handle Delete
if ($action === 'delete' && $id) {
    // Pastikan fungsi executeQuerySingle ada di sistem Anda
    $layanan = executeQuerySingle("SELECT * FROM layanan WHERE id = ?", [(int)$id]);
    
    if ($layanan) {
        // Delete image file
        if ($layanan['gambar_path']) {
            deleteFile($layanan['gambar_path']);
        }

        
        // Delete from database
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

// Handle Form Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'save') {
    $errors = [];
    $nama_layanan = sanitize($_POST['nama_layanan'] ?? '');
    $deskripsi = sanitize($_POST['deskripsi'] ?? '');
    $tipe_layanan = sanitize($_POST['tipe_layanan'] ?? '');
    $status = sanitize($_POST['status'] ?? 'Aktif');
    
    // Validasi
    if (empty($nama_layanan)) $errors[] = "Nama layanan wajib diisi";
    
    // Handle image upload
    $gambar_path = '';
    
    if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] === UPLOAD_ERR_OK) {
        $upload_result = uploadImage($_FILES['gambar'], 'layanan', 'layanan');
        
        if ($upload_result['success']) {
            $gambar_path = '/layanan/' . $upload_result['filename'];
            
            // Delete old image if editing
            if ($id) {
                $old_layanan = executeQuerySingle("SELECT gambar_path FROM layanan WHERE id = ?", [(int)$id]);
                if ($old_layanan && $old_layanan['gambar_path']) {
                    deleteFile($old_layanan['gambar_path']);
                }
            }
        } else {
            $errors[] = $upload_result['message'];
        }
    }

    // Save to Database
    if (empty($errors)) {
        if ($id) {
            // Update
            if ($gambar_path) {
                $query = "UPDATE layanan SET nama_layanan = ?, deskripsi = ?, tipe_layanan = ?, status = ?, gambar_path = ?, updated_at = NOW() WHERE id = ?";
                $params = [$nama_layanan, $deskripsi, $tipe_layanan, $status, $gambar_path, (int)$id];
            } else {
                $query = "UPDATE layanan SET nama_layanan = ?, deskripsi = ?, tipe_layanan = ?, status = ?, updated_at = NOW() WHERE id = ?";
                $params = [$nama_layanan, $deskripsi, $tipe_layanan, $status, (int)$id];
            }
            $msg_success = "Layanan berhasil diperbarui";
        } else {
            // Insert
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

    if (!empty($errors)) {
        foreach ($errors as $error) setFlashMessage('error', $error);
    }
}

// Get Data
$search = $_GET['search'] ?? '';
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 10;
$offset = ($page - 1) * $limit;

$where = [];
$params = [];

if ($search) {
    $where[] = "(nama_layanan ILIKE ? OR tipe_layanan ILIKE ?)";
    $search_param = '%' . $search . '%';
    $params[] = $search_param;
    $params[] = $search_param;
}

$where_clause = count($where) > 0 ? 'WHERE ' . implode(' AND ', $where) : '';
$count_query = "SELECT COUNT(*) FROM layanan " . $where_clause;
$total_records = countRows($count_query, $params); 
$total_pages = ceil($total_records / $limit);

$query = "SELECT * FROM layanan " . $where_clause . " ORDER BY id DESC LIMIT ? OFFSET ?";
$params[] = $limit;
$params[] = $offset;
$layanan_list = executeQuery($query, $params);

// Hitung Statistik
$count_aktif = countRows("SELECT COUNT(*) FROM layanan WHERE status = 'Aktif'");
$count_nonaktif = countRows("SELECT COUNT(*) FROM layanan WHERE status = 'Non-Aktif'");
$count_layanan = countRows("SELECT COUNT(*) FROM layanan ");

?>

<div class="flex flex-col overflow-hidden">
    <div class="flex-1 flex flex-col overflow-hidden">
        <div class="mb-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h2 class="text-lg font-semibold text-blue-900"><i class="fa-solid fa-headset mr-2"></i>Daftar Layanan</h2>
                <p class="text-sm text-gray-600">Kelola data layanan.</p>
            </div>
            
            <div class="flex flex-col sm:flex-row gap-3">
                <form action="" method="GET" class="flex">
                    <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" placeholder="Cari layanan..." class="border rounded-l px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <button type="submit" class="bg-gray-200 px-4 py-2 rounded-r hover:bg-gray-300"><i class="fas fa-search"></i></button>
                </form>
                <button type="button" data-toggle="modal" data-target="#modalLayanan" onclick="resetForm()" class="inline-flex items-center justify-center border select-none font-sans font-medium text-center transition-all duration-300 ease-in text-sm rounded-md py-2.5 px-5 shadow-sm hover:shadow-md bg-blue-600 border-blue-600 text-white hover:bg-blue-700 gap-2">
                    <i class="fas fa-plus"></i> Tambah Layanan
                </button>
                </div>
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
                            <th class="px-6 py-4 text-right w-24">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <?php if(!empty($layanan_list)): ?>
                            <?php foreach($layanan_list as $row): ?>
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-6 py-4">
                                    <div class="w-12 h-12 rounded-lg overflow-hidden bg-gray-100 border">
                                        <img src="<?= !empty($row['gambar_path']) ? UPLOAD_URL . htmlspecialchars($row['gambar_path']) : 'https://via.placeholder.com/150?text=No+Img' ?>" 
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
                                    <span class="inline-flex items-center gap-1 text-sm font-medium <?= $row['status'] == 'Aktif' ? 'text-green-600' : 'text-red-500' ?>">
                                        <i class="fas fa-<?= $row['status'] == 'Aktif' ? 'check' : 'times' ?>-circle"></i> <?= $row['status'] ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right whitespace-nowrap">
                                    <button data-toggle="modal" data-target="#modalDetail"' onclick='getData(<?= json_encode($row) ?>)' class="text-green-500 hover:bg-green-50 p-2 rounded-lg transition mr-1" title="Lihat Detail">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button data-toggle="modal" data-target="#modalLayanan" onclick='editData(<?= json_encode($row) ?>)' class="text-blue-500 hover:bg-blue-50 p-2 rounded-lg transition mr-1" title="Edit">
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

<!-- Modal -->
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
                            <input type="text" name="tipe_layanan" id="inputTipe" required
                                   class="w-full border border-slate-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 focus:outline-none"
                                   placeholder="Contoh: Jasa">
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
        
        <div class="p-6 pt-4">
            <div class="flex flex-col md:flex-row gap-6">
                
                <div class="w-full md:w-1/2">
                    <div class="rounded-lg overflow-hidden shadow-sm border border-gray-200 bg-gray-100 flex items-center justify-center min-h-[200px]">
                        <img id="detail-preview-image" src="" alt="Detail Gambar" class="w-full h-auto object-cover max-h-64">
                    </div>
                </div>

                <div class="w-full md:w-1/2 space-y-4">
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
                        <p id="detail-deskripsi" class="text-gray-600 text-sm mt-1 leading-relaxed"></p>
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
    function resetForm() {
        $('#inputId').val('');
        $('#inputNama').val('');
        $('#inputTipe').val('');
        $('#inputStatus').val('Aktif');
        $('#inputGambar').val('');
        $('#inputDeskripsi').val('');
        $('#modalTitle').html('<i class="fas fa-plus-circle mr-2 text-blue-600"></i>Tambah Layanan Baru');
        $('#fileHelpText').html('<i class="fas fa-info-circle mr-1"></i>Format: JPG, PNG, GIF, WEBP. Maksimal 5MB.');
        $('#preview-image').attr('src', '').addClass('hidden');
    }

    function editData(data) {
        $('#inputId').val(data.id);
        $('#inputNama').val(data.nama_layanan);
        $('#inputTipe').val(data.tipe_layanan);
        $('#inputStatus').val(data.status);
        $('#inputGambar').val('');
        $('#inputDeskripsi').val(data.deskripsi);
        $('#modalTitle').html('<i class="fas fa-edit mr-2 text-blue-600"></i>Edit Layanan');
        $('#fileHelpText').html('<i class="fas fa-info-circle mr-1"></i>Biarkan kosong jika tetap menggunakan gambar lama.');
        
        if (data.gambar_path) {
            $('#preview-image').attr('src', `../uploads/${data.gambar_path}`).removeClass('hidden');
        } else {
            $('#preview-image').attr('src', '').addClass('hidden');
        }
    }

    function getData(data) {
        $('#detail-nama').text(data.nama_layanan);
        $('#detail-tipe').html(`<span class="px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">${data.tipe_layanan}</span>`);
        
        const statusClass = data.status === 'Aktif' ? 'text-green-600' : 'text-red-500';
        const statusIcon = data.status === 'Aktif' ? 'check' : 'times';
        $('#detail-status').html(`<span class="inline-flex items-center gap-1 text-sm font-medium ${statusClass}"><i class="fas fa-${statusIcon}-circle"></i> ${data.status}</span>`);
        
        $('#detail-deskripsi').text(data.deskripsi);
        
        if (data.gambar_path) {
            $('#detail-preview-image').attr('src', `../uploads/${data.gambar_path}`);
        } else {
            $('#detail-preview-image').attr('src', 'https://via.placeholder.com/400x300?text=No+Image');
        }
    }
</script>

<?php require_once __DIR__ . '/../includes/admin_footer.php'; ?>
