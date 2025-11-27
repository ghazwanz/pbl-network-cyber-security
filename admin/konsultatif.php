<?php
/**
 * Admin CRUD Konsultatif
 * File: admin/konsultatif.php
 * Purpose: Manage consultation form submissions
 */

$page_title = "Konsultatif";
require_once __DIR__ . '/../includes/admin_header.php';

// Handle actions
$action = $_GET['action'] ?? ($_POST['action'] ?? 'list');
$id = $_GET['id'] ?? ($_POST['id'] ?? null);

// Handle Delete
if ($action === 'delete' && $id) {
    $konsultatif = executeQuerySingle("SELECT * FROM konsultatif WHERE id = ?", [(int)$id]);
    
    if ($konsultatif) {
        $result = executeNonQuery("DELETE FROM konsultatif WHERE id = ?", [(int)$id]);
        
        if ($result) {
            setFlashMessage('success', 'Data konsultatif berhasil dihapus');
        } else {
            setFlashMessage('error', 'Gagal menghapus data konsultatif');
        }
    } else {
        setFlashMessage('error', 'Data tidak ditemukan');
    }
    
    redirect(ADMIN_URL . '/konsultatif.php');
}

// Handle Form Submission (Update Jawaban & Auto Status)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'update_status') {
    $errors = [];
    
    // 1. Ambil jawaban admin
    $jawaban = trim($_POST['jawaban'] ?? '');   
    
    // 2. Tentukan status OTOMATIS
    // Jika jawaban ada isinya -> 'terjawab'. Jika kosong -> 'belum terjawab'.
    $status = !empty($jawaban) ? 'terjawab' : 'belum terjawab';
    
    // (Validasi status dihapus karena kita yang tentukan sendiri secara otomatis)
    
    if (empty($errors)) {
        $result = executeNonQuery(
            "UPDATE konsultatif SET status = ?, jawaban = ?, updated_at = NOW() WHERE id = ?",
            [$status, $jawaban, (int)$id]
        );
        
        if ($result !== false) {
            setFlashMessage('success', 'Jawaban berhasil disimpan dan status diperbarui');
            redirect(ADMIN_URL . '/konsultatif.php');
        } else {
            $errors[] = "Gagal memperbarui data";
        }
    }
    
    // Show errors
    if (!empty($errors)) {
        foreach ($errors as $error) {
            setFlashMessage('error', $error);
        }
    }
}

// Get filter
$filter_status = $_GET['filter'] ?? '';
$search = $_GET['search'] ?? '';
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 10;
$offset = ($page - 1) * $limit;

// Build query
$where = [];
$params = [];

// Filter SQL disesuaikan dengan ENUM baru
if ($filter_status && in_array($filter_status, ['belum terjawab', 'terjawab'])) {
    $where[] = "k.status = ?";
    $params[] = $filter_status;
}

if ($search) {
    $where[] = "(k.nama ILIKE ? OR k.email ILIKE ? OR k.subjek ILIKE ? OR k.pesan ILIKE ? OR u.nama_lengkap ILIKE ?)";
    $search_param = '%' . $search . '%';
    $params[] = $search_param;
    $params[] = $search_param;
    $params[] = $search_param;
    $params[] = $search_param;
    $params[] = $search_param;
}

$where_clause = count($where) > 0 ? 'WHERE ' . implode(' AND ', $where) : '';

// Get total count
$count_query = "SELECT COUNT(*) 
                FROM konsultatif k 
                LEFT JOIN users u ON k.id_admin = u.id " . $where_clause;
$total_records = countRows($count_query, $params);
$total_pages = ceil($total_records / $limit);

// Get data
$query = "SELECT k.*, u.nama_lengkap AS nama_admin 
          FROM konsultatif k 
          LEFT JOIN users u ON k.id_admin = u.id " . $where_clause . " 
          ORDER BY k.created_at DESC LIMIT ? OFFSET ?";

$params[] = $limit;
$params[] = $offset;
$konsultatif_list = executeQuery($query, $params);

// Get statistics (Disederhanakan menjadi 2 status)
$count_belum = countRows("SELECT COUNT(*) FROM konsultatif WHERE status = 'belum terjawab'");
$count_terjawab = countRows("SELECT COUNT(*) FROM konsultatif WHERE status = 'terjawab'");
$total_konsultatif = countRows("SELECT COUNT(*) FROM konsultatif");

?>

<div class="space-y-6">
    
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h2 class="text-lg font-semibold text-blue-900"><i class="fa-solid fa-comments mr-2"></i>Konsultatif</h2>
            <p class="text-sm text-gray-600">Kelola pesan konsultatif dari pengunjung</p>
        </div>
    </div>
    
    <?php displayFlashMessage(); ?>

    <div class="bg-white rounded-lg shadow-md p-4">
            <form method="GET" class="flex flex-col md:flex-row gap-4">
                <div class="flex-1">
                    <input 
                        type="text" 
                        name="search"
                        value="<?php echo htmlspecialchars($search); ?>"
                        placeholder="Cari judul, tanggal, atau jawaban..."
                        class="form-input"
                    >  
                </div>

                <select name="filter" class="form-input md:w-48" onchange="this.form.submit()">
                    <option value="">Semua Status</option>
                    <option value="belum terjawab" <?php echo $filter_status === 'belum terjawab' ? 'selected' : ''; ?>>Belum Terjawab</option>
                    <option value="terjawab" <?php echo $filter_status === 'terjawab' ? 'selected' : ''; ?>>Terjawab</option>
                </select>

                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-search"></i> Cari
                </button>

                <?php if ($search || $filter_status): ?>
                <a href="?action=list" class="btn bg-gray-500 text-white hover:bg-gray-600">
                    <i class="fas fa-times mr-2"></i> Reset
                </a>
                <?php endif; ?>
            </form>
    </div>

    <div class="mb-6 grid grid-cols-1 md:grid-cols-3 gap-4">

        <div class="bg-white rounded-lg shadow p-4 border-l-4 border-blue-500">
            <p class="text-gray-500 text-sm mb-1 uppercase font-bold tracking-wider">Total Pertanyaan</p>
            <h3 class="text-2xl font-bold text-gray-800"><?php echo $total_konsultatif; ?></h3>
        </div>

        <div class="bg-white rounded-lg shadow p-4 border-l-4 border-orange-500">
            <p class="text-gray-500 text-sm mb-1 uppercase font-bold tracking-wider">Belum Terjawab</p>
            <h3 class="text-2xl font-bold text-orange-600"><?php echo $count_belum; ?></h3>
        </div>

        <div class="bg-white rounded-lg shadow p-4 border-l-4 border-green-500">
            <p class="text-gray-500 text-sm mb-1 uppercase font-bold tracking-wider">Terjawab</p>
            <h3 class="text-2xl font-bold text-green-600"><?php echo $count_terjawab; ?></h3>
        </div>

    </div>
    
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <?php if ($konsultatif_list && count($konsultatif_list) > 0): ?>
        <div class="overflow-x-auto">
            
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th class="w-[2%] ">No.</th>
                            <th class="w-[30%]">Pertanyaan</th>
                            <th class="w-[5%]">Tanggal</th>
                            <th class="">Jawaban</th>
                            <th class="w-[8%]">Admin</th>
                            <th class="w-[5%]">Status</th>
                            <th class="w-[7%]">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($konsultatif_list as $index => $item): ?>
                        
                        <?php 
                            // MODIFIKASI: Pastikan data nama dan email juga ada untuk modal detail
                            $json_data = htmlspecialchars(json_encode([
                                "id" => $item["id"],
                                "nama" => $item["nama"] ?? 'Anonim', 
                                "email" => $item["email"] ?? '-', 
                                "pertanyaan" => $item["pertanyaan"],
                                "status" => $item["status"],
                                "jawaban" => $item["jawaban"] ?? "",
                                "created_at" => date('d/m/Y H:i:s', strtotime($item['created_at'])) 
                            ]), ENT_QUOTES, 'UTF-8');
                        ?>
                        
                        <tr> 
                            <td class = "text-center"><?php echo $offset + $index + 1; ?></td>
                            
                            <td>
                                <div class="line-clamp-2 text-black-500 text-base">
                                    <?php echo htmlspecialchars($item['pertanyaan']); ?>
                                </div>
                            </td>
                            <td>
                                <small><?php echo date('d/m/Y', strtotime($item['created_at'])); ?></small>
                            </td>

                            <td>
                                <div class="line-clamp-2 text-black-500 text-base">
                                    <?php echo htmlspecialchars($item['jawaban'] ?? '-'); ?>
                                </div>
                            </td>

                            <td>
                                <div class="line-clamp-2 text-black-500 text-base">
                                    <?php echo htmlspecialchars($item['nama_admin'] ?? '-'); ?>
                                </div>
                            </td>
                            
                            <td class="whitespace-nowrap">
                                <span class="inline-block px-3 py-1 rounded-full text-xs font-semibold <?php echo ($item['status'] == 'terjawab') ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'; ?>">
                                    <?php echo ($item['status'] == 'terjawab') ? 'Terjawab' : 'Belum Terjawab'; ?>
                                </span>
                            </td>

                            <td class="whitespace-nowrap text-sm font-medium">
                                <div class="flex items-center gap-3">
                                    
                                    <button type="button" 
                                        onclick='openDetailModal(<?php echo $json_data; ?>)'
                                        class="text-gray-600 hover:text-gray-800 transition-colors"
                                        title="Lihat Detail">
                                        <i class="fas fa-eye text-base text-green-500"></i>
                                    </button>
                                    
                                    <button type="button" 
                                        onclick='openJawabModal(<?php echo $json_data; ?>)' 
                                        class="text-blue-600 hover:text-blue-800 transition-colors"
                                        title="Jawab Konsultasi">
                                        <i class="fas fa-edit text-base "></i>
                                    </button>

                                    <form method="POST" onsubmit="return confirm('Yakin ingin menghapus data ini?');" class="inline">
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="id" value="<?php echo $item['id']; ?>">
                                        
                                        <button type="submit" 
                                            class="text-red-600 hover:text-red-800 transition-colors"
                                            title="Hapus Data">
                                            <i class="fas fa-trash text-base"></i>
                                        </button>
                                    </form>

                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

            
            <?php if ($total_pages > 1): ?>
            <div class="d-flex justify-content-between align-items-center mt-4">
                <div class="text-muted small">
                    Menampilkan <?php echo $offset + 1; ?> - <?php echo min($offset + $limit, $total_records); ?> 
                    dari <?php echo $total_records; ?> data
                </div>
                
                <nav>
                    <?php
                        $url = 'konsultatif.php?';
                        if ($filter_status) $url .= 'filter=' . urlencode($filter_status) . '&';
                        if ($search) $url .= 'search=' . urlencode($search) . '&';
                        echo createPagination($page, $total_pages, $url);
                    ?>
                </nav>
            </div>
            <?php endif; ?>
            
            <?php else: 
            
                // Tentukan pesan berdasarkan filter
                $empty_message = 'Belum ada pesan konsultatif yang masuk.'; // Default
                if ($search) {
                    $empty_message = 'Tidak ditemukan hasil untuk "' . htmlspecialchars($search) . '"';
                } elseif ($filter_status === 'terjawab') {
                    $empty_message = 'Belum ada pertanyaan yang dijawab.';
                } elseif ($filter_status === 'belum terjawab') {
                    // Sesuai permintaan:
                    $empty_message = 'Tidak ada pertanyaan.'; 
                }
            
            ?>
            
            <div class="text-center py-12">
                <i class="fas fa-inbox text-6xl text-gray-300 mb-4"></i>
                <p class="text-gray-500 text-lg">
                    <?php 
                        echo $empty_message; 
                    ?>
                </p>
            </div>
            
            <?php endif; ?>
                        
        </div>
    </div>
    
</div>

<div id="modalKonsultatif" class="fixed inset-0 z-50 hidden" aria-labelledby="modal-title-jawab" role="dialog" aria-modal="true">
    <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" onclick="closeModal('modalKonsultatif')"></div>
    <div class="fixed inset-0 z-10 overflow-y-auto">
        <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
            <div class="relative transform overflow-hidden rounded-lg bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-2xl">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 border-b border-gray-100">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-semibold leading-6 text-gray-900 flex items-center gap-2">
                            <span class="bg-blue-100 text-blue-600 rounded-full p-1.5 w-8 h-8 flex items-center justify-center">
                                <i class="fas fa-reply"></i>
                            </span>
                            Jawab Konsultasi
                        </h3>
                        <button type="button" class="text-gray-400 hover:text-gray-500 focus:outline-none" onclick="closeModal('modalKonsultatif')">
                            <i class="fas fa-times text-xl"></i>
                        </button>
                    </div>
                </div>

                <form method="POST" id="formKonsultatif">   
                    <input type="hidden" name="action" value="update_status">
                    <input type="hidden" name="id" id="konsultatifId">

                    <div class="px-4 py-5 sm:p-6 space-y-4">

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Pertanyaan</label>
                            <textarea id="pertanyaan" rows="3" readonly
                                      class="block w-full rounded-md border-gray-300 bg-gray-50 text-gray-600 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm border p-2.5 cursor-not-allowed resize-none"></textarea>
                        </div>

                        <div>
                            <label for="jawaban" class="block text-sm font-medium text-gray-700 mb-1">Jawaban / Tanggapan <span class="text-red-500">*</span></label>
                            <textarea name="jawaban" id="jawaban" rows="5" required
                                      class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm border p-2.5"
                                      placeholder="Tuliskan jawaban untuk pertanyaan di atas..."></textarea>
                            <p class="mt-1 text-xs text-gray-500">Jawaban ini akan disimpan ke database.</p>
                        </div>

                    </div>

                    <div class="bg-gray-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6 border-t border-gray-200">
                        <button type="submit" class="inline-flex w-full justify-center rounded-md border border-transparent bg-blue-600 px-4 py-2 text-base font-medium text-white shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 sm:ml-3 sm:w-auto sm:text-sm">
                            <i class="fas fa-paper-plane mr-2 mt-1"></i> Simpan & Jawab
                        </button>
                        <button type="button" onclick="closeModal('modalKonsultatif')" class="mt-3 inline-flex w-full justify-center rounded-md border border-gray-300 bg-white px-4 py-2 text-base font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                            Batal
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div id="modalDetail" class="fixed inset-0 z-50 hidden" aria-labelledby="modal-title-detail" role="dialog" aria-modal="true">
    <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" onclick="closeModal('modalDetail')"></div>
    <div class="fixed inset-0 z-10 overflow-y-auto">
        <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
            <div class="relative transform overflow-hidden rounded-lg bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-2xl">
                
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 border-b border-gray-100">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-semibold leading-6 text-gray-900 flex items-center gap-2">
                            <span class="bg-gray-100 text-gray-600 rounded-full p-1.5 w-8 h-8 flex items-center justify-center">
                                <i class="fas fa-eye"></i>
                            </span>
                            Detail Konsultasi
                        </h3>
                        <button type="button" class="text-gray-400 hover:text-gray-500 focus:outline-none" onclick="closeModal('modalDetail')">
                            <i class="fas fa-times text-xl"></i>
                        </button>
                    </div>
                </div>

                <div class="px-4 py-5 sm:p-6 space-y-4">
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal & Waktu</label>
                            <p id="detailTanggal" class="text-gray-800 font-semibold"></p>
                        </div>
                    </div>

                    <hr class="border-gray-100">

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Pertanyaan</label>
                        <div id="detailPertanyaan" class="p-3 bg-gray-50 rounded-md border border-gray-200 text-gray-700 whitespace-pre-wrap"></div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Jawaban Admin</label>
                        <div id="detailJawaban" class="p-3 bg-gray-50 rounded-md border border-gray-200 text-gray-700 whitespace-pre-wrap"></div>
                    </div>

                </div>

                <div class="bg-gray-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6 border-t border-gray-200">
                    <button type="button" onclick="closeModal('modalDetail')" class="inline-flex w-full justify-center rounded-md border border-gray-300 bg-white px-4 py-2 text-base font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 sm:ml-3 sm:w-auto sm:text-sm">
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>


<script>
    const modalJawab = document.getElementById('modalKonsultatif');
    const modalDetail = document.getElementById('modalDetail'); 
    const form = document.getElementById('formKonsultatif');

    // Fungsi membuka modal JAWAB
    function openJawabModal(data) {
        // 1. Reset form agar bersih
        form.reset();

        // 2. Isi hidden ID
        document.getElementById('konsultatifId').value = data.id;

        // 3. Isi data Readonly (Info Pengirim & Pertanyaan)
        document.getElementById('pertanyaan').value = data.pertanyaan || '';

        // Isi jawaban jika sudah pernah dijawab sebelumnya (jawaban)
        document.getElementById('jawaban').value = data.jawaban || '';

        // 5. Tampilkan Modal
        modalJawab.classList.remove('hidden');
    }
    
    // FUNGSI BARU: Membuka modal DETAIL
    function openDetailModal(data) {
        // 1. Isi konten modal Detail
        document.getElementById('detailTanggal').textContent = data.created_at || '-';
        document.getElementById('detailPertanyaan').textContent = data.pertanyaan || 'Tidak ada pertanyaan.';
        document.getElementById('detailJawaban').textContent = data.jawaban || 'Belum ada jawaban.';

        // 2. Tampilkan Modal Detail
        modalDetail.classList.remove('hidden');
    }

    // Fungsi menutup modal (Menerima ID modal)
    function closeModal(modalId) {
        document.getElementById(modalId).classList.add('hidden');
    }

    // Tutup jika klik di luar area modal (backdrop)
    window.onclick = function(event) {
        const backdropJawab = document.querySelector('#modalKonsultatif .bg-opacity-75'); 
        const backdropDetail = document.querySelector('#modalDetail .bg-opacity-75'); 
        
        if (event.target === backdropJawab) {
            closeModal('modalKonsultatif');
        }
        if (event.target === backdropDetail) {
            closeModal('modalDetail');
        }
    }
</script>

<?php
    require_once __DIR__ . '/../includes/admin_footer.php';
?>