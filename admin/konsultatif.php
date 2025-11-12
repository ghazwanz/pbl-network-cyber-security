<?php
/**
 * Admin CRUD Konsultatif
 * File: admin/konsultatif.php
 * Purpose: Manage consultation form submissions
 */

// Set page title
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

// Handle Form Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && in_array($action, ['update_status', 'mark_read'])) {
    $errors = [];
    
    if ($action === 'update_status') {
        $status = sanitize($_POST['status'] ?? '');
        $catatan_admin = sanitize($_POST['catatan_admin'] ?? '');
        
        // Validasi
        if (!in_array($status, ['pending', 'dibaca', 'ditanggapi'])) {
            $errors[] = "Status tidak valid";
        }
        
        if (empty($errors)) {
            $result = executeNonQuery(
                "UPDATE konsultatif SET status = ?, catatan_admin = ?, updated_at = NOW() WHERE id = ?",
                [$status, $catatan_admin, (int)$id]
            );
            
            if ($result !== false) {
                setFlashMessage('success', 'Status berhasil diperbarui');
                redirect(ADMIN_URL . '/konsultatif.php');
            } else {
                $errors[] = "Gagal memperbarui status";
            }
        }
    } elseif ($action === 'mark_read') {
        $result = executeNonQuery(
            "UPDATE konsultatif SET status = 'dibaca', updated_at = NOW() WHERE id = ?",
            [(int)$id]
        );
        
        if ($result !== false) {
            setFlashMessage('success', 'Ditandai sebagai sudah dibaca');
            redirect(ADMIN_URL . '/konsultatif.php');
        } else {
            $errors[] = "Gagal memperbarui status";
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

if ($filter_status && in_array($filter_status, ['pending', 'dibaca', 'ditanggapi'])) {
    $where[] = "status = ?";
    $params[] = $filter_status;
}

if ($search) {
    $where[] = "(nama ILIKE ? OR email ILIKE ? OR subjek ILIKE ? OR pesan ILIKE ?)";
    $search_param = '%' . $search . '%';
    $params[] = $search_param;
    $params[] = $search_param;
    $params[] = $search_param;
    $params[] = $search_param;
}

$where_clause = count($where) > 0 ? 'WHERE ' . implode(' AND ', $where) : '';

// Get total count
$count_query = "SELECT COUNT(*) FROM konsultatif " . $where_clause;
$total_records = countRows($count_query, $params);
$total_pages = ceil($total_records / $limit);

// Get data
$query = "SELECT * FROM konsultatif " . $where_clause . " ORDER BY created_at DESC LIMIT ? OFFSET ?";
$params[] = $limit;
$params[] = $offset;
$konsultatif_list = executeQuery($query, $params);

// Get statistics
$count_pending = countRows("SELECT COUNT(*) FROM konsultatif WHERE status = 'pending'");
$count_dibaca = countRows("SELECT COUNT(*) FROM konsultatif WHERE status = 'dibaca'");
$count_ditanggapi = countRows("SELECT COUNT(*) FROM konsultatif WHERE status = 'ditanggapi'");
$total_konsultatif = countRows("SELECT COUNT(*) FROM konsultatif");

// Get detail if requested
$detail_id = $_GET['detail'] ?? null;
$detail = null;
if ($detail_id) {
    $detail = executeQuerySingle("SELECT * FROM konsultatif WHERE id = ?", [(int)$detail_id]);
}
?>

<style>
.status-badge {
    padding: 0.375rem 0.75rem;
    border-radius: 0.375rem;
    font-size: 0.875rem;
    font-weight: 600;
    display: inline-flex;
    align-items: center;
    gap: 0.25rem;
}

.status-pending {
    background: #FEF3C7;
    color: #92400E;
}

.status-dibaca {
    background: #DBEAFE;
    color: #1E40AF;
}

.status-ditanggapi {
    background: #D1FAE5;
    color: #065F46;
}

.message-preview {
    max-height: 3em;
    overflow: hidden;
    text-overflow: ellipsis;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
}

.action-btn {
    padding: 0.25rem 0.5rem;
    font-size: 0.875rem;
    border: none;
    border-radius: 0.25rem;
    cursor: pointer;
    transition: all 0.2s;
}

.modal-backdrop {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.5);
    z-index: 1040;
    display: none;
}

.modal-backdrop.show {
    display: block;
}

.modal-dialog {
    position: fixed;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    width: 90%;
    max-width: 600px;
    max-height: 90vh;
    overflow-y: auto;
    z-index: 1050;
    display: none;
}

.modal-dialog.show {
    display: block;
}
</style>

<div class="container-fluid px-4 py-5">
    
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1">
                <i class="fas fa-comments text-primary me-2"></i>Konsultatif
            </h2>
            <p class="text-muted mb-0">Kelola pesan konsultatif dari pengunjung</p>
        </div>
    </div>
    
    <?php displayFlashMessage(); ?>
    
    <!-- Statistics Cards -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-muted mb-1 small">Total Pesan</p>
                            <h3 class="mb-0 fw-bold"><?php echo $total_konsultatif; ?></h3>
                        </div>
                        <div class="bg-primary bg-opacity-10 p-3 rounded">
                            <i class="fas fa-envelope text-primary fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-muted mb-1 small">Pending</p>
                            <h3 class="mb-0 fw-bold text-warning"><?php echo $count_pending; ?></h3>
                        </div>
                        <div class="bg-warning bg-opacity-10 p-3 rounded">
                            <i class="fas fa-clock text-warning fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-muted mb-1 small">Dibaca</p>
                            <h3 class="mb-0 fw-bold text-info"><?php echo $count_dibaca; ?></h3>
                        </div>
                        <div class="bg-info bg-opacity-10 p-3 rounded">
                            <i class="fas fa-eye text-info fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-muted mb-1 small">Ditanggapi</p>
                            <h3 class="mb-0 fw-bold text-success"><?php echo $count_ditanggapi; ?></h3>
                        </div>
                        <div class="bg-success bg-opacity-10 p-3 rounded">
                            <i class="fas fa-check-circle text-success fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Filter & Search -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" class="row g-3">
                <div class="col-md-4">
                    <label class="form-label small text-muted">Filter Status</label>
                    <select name="filter" class="form-select" onchange="this.form.submit()">
                        <option value="">Semua Status</option>
                        <option value="pending" <?php echo $filter_status === 'pending' ? 'selected' : ''; ?>>Pending</option>
                        <option value="dibaca" <?php echo $filter_status === 'dibaca' ? 'selected' : ''; ?>>Dibaca</option>
                        <option value="ditanggapi" <?php echo $filter_status === 'ditanggapi' ? 'selected' : ''; ?>>Ditanggapi</option>
                    </select>
                </div>
                
                <div class="col-md-8">
                    <label class="form-label small text-muted">Pencarian</label>
                    <div class="input-group">
                        <input type="text" name="search" class="form-control" 
                               value="<?php echo htmlspecialchars($search); ?>"
                               placeholder="Cari nama, email, subjek, atau pesan...">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-search"></i> Cari
                        </button>
                        <?php if ($search || $filter_status): ?>
                        <a href="konsultatif.php" class="btn btn-secondary">
                            <i class="fas fa-times"></i> Reset
                        </a>
                        <?php endif; ?>
                    </div>
                </div>
            </form>
        </div>
    </div>
    
    <!-- Data Table -->
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            
            <?php if ($konsultatif_list && count($konsultatif_list) > 0): ?>
            
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th width="5%">#</th>
                            <th width="15%">Pengirim</th>
                            <th width="15%">Kontak</th>
                            <th width="20%">Instansi</th>
                            <th width="20%">Pesan</th>
                            <th width="10%">Tanggal</th>
                            <th width="5%" class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($konsultatif_list as $index => $item): ?>
                        <tr>
                            <td><?php echo $offset + $index + 1; ?></td>
                            <td>
                                <strong><?php echo htmlspecialchars($item['nama_pengisi']); ?></strong>
                            </td>
                            <td>
                                <small class="d-block text-muted">
                                    <i class="fas fa-envelope me-1"></i><?php echo htmlspecialchars($item['email']); ?>
                                </small>
                                <small class="d-block text-muted">
                                    <i class="fas fa-phone me-1"></i><?php echo isset($item['telepon'])? htmlspecialchars($item['telepon']):'-'; ?>
                                </small>
                            </td>
                            <td><?php echo htmlspecialchars($item['instansi']); ?></td>
                            <td>
                                <div class="message-preview text-muted small">
                                    <?php echo htmlspecialchars($item['pesan']); ?>
                                </div>
                            </td>
                            <td>
                                <small><?php echo date('d/m/Y', strtotime($item['created_at'])); ?></small>
                            </td>
        
                            <td class="text-center">
                                <div class="btn-group btn-group-sm">
                                    <button type="button" class="btn btn-info" 
                                            onclick="showDetail(<?php echo $item['id']; ?>)"
                                            title="Lihat Detail">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    
                                    <?php if ($item['is_approved'] === true): ?>
                                    <form method="POST" style="display:inline;" 
                                          onsubmit="return confirm('Tandai sebagai sudah dibaca?');">
                                        <input type="hidden" name="action" value="mark_read">
                                        <input type="hidden" name="id" value="<?php echo $item['id']; ?>">
                                        <button type="submit" class="btn btn-primary" title="Tandai Dibaca">
                                            <i class="fas fa-check"></i>
                                        </button>
                                    </form>
                                    <?php endif; ?>
                                    
                                    <form method="POST" style="display:inline;" 
                                          onsubmit="return confirm('Yakin ingin menghapus data ini?');">
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="id" value="<?php echo $item['id']; ?>">
                                        <button type="submit" class="btn btn-danger" title="Hapus">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
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
            
            <?php else: ?>
            
            <div class="text-center py-5">
                <i class="fas fa-inbox text-muted" style="font-size: 4rem;"></i>
                <h5 class="mt-3 text-muted">Tidak ada data konsultatif</h5>
                <p class="text-muted">
                    <?php if ($search): ?>
                    Tidak ditemukan hasil untuk "<?php echo htmlspecialchars($search); ?>"
                    <?php else: ?>
                    Belum ada pesan konsultatif yang masuk
                    <?php endif; ?>
                </p>
            </div>
            
            <?php endif; ?>
            
        </div>
    </div>
    
</div>

<!-- Detail Modal -->
<div class="modal-backdrop" id="modalBackdrop" onclick="closeModal()"></div>
<div class="modal-dialog" id="detailModal">
    <div class="card border-0 shadow-lg">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>Detail Konsultatif</h5>
            <button type="button" class="btn-close btn-close-white" onclick="closeModal()"></button>
        </div>
        <div class="card-body" id="detailContent">
            <div class="text-center py-5">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function showDetail(id) {
    const backdrop = document.getElementById('modalBackdrop');
    const modal = document.getElementById('detailModal');
    const content = document.getElementById('detailContent');
    
    backdrop.classList.add('show');
    modal.classList.add('show');
    
    // Fetch detail via AJAX
    fetch(`?detail=${id}`)
        .then(response => response.text())
        .then(html => {
            // Extract detail data from response
            const parser = new DOMParser();
            const doc = parser.parseFromString(html, 'text/html');
            const detailData = <?php echo json_encode($detail ?? null); ?>;
            
            if (detailData) {
                renderDetail(detailData);
            } else {
                // Reload page with detail parameter
                window.location.href = `?detail=${id}`;
            }
        });
}

function renderDetail(data) {
    const content = document.getElementById('detailContent');
    
    const statusClass = {
        'pending': 'status-pending',
        'dibaca': 'status-dibaca',
        'ditanggapi': 'status-ditanggapi'
    };
    
    const statusLabel = {
        'pending': 'Pending',
        'dibaca': 'Dibaca',
        'ditanggapi': 'Ditanggapi'
    };
    
    const statusIcon = {
        'pending': 'fa-clock',
        'dibaca': 'fa-eye',
        'ditanggapi': 'fa-check-circle'
    };
    
    content.innerHTML = `
        <div class="mb-3">
            <label class="small text-muted">Pengirim</label>
            <h5 class="mb-0">${escapeHtml(data.nama)}</h5>
        </div>
        
        <div class="row mb-3">
            <div class="col-md-6">
                <label class="small text-muted">Email</label>
                <p class="mb-0"><i class="fas fa-envelope me-2 text-primary"></i>${escapeHtml(data.email)}</p>
            </div>
            <div class="col-md-6">
                <label class="small text-muted">Telepon</label>
                <p class="mb-0"><i class="fas fa-phone me-2 text-primary"></i>${escapeHtml(data.telepon)}</p>
            </div>
        </div>
        
        <div class="mb-3">
            <label class="small text-muted">Subjek</label>
            <h6 class="mb-0">${escapeHtml(data.subjek)}</h6>
        </div>
        
        <div class="mb-3">
            <label class="small text-muted">Pesan</label>
            <div class="border rounded p-3 bg-light">
                ${escapeHtml(data.pesan).replace(/\n/g, '<br>')}
            </div>
        </div>
        
        <div class="row mb-3">
            <div class="col-md-6">
                <label class="small text-muted">Tanggal Kirim</label>
                <p class="mb-0">${formatDate(data.created_at)}</p>
            </div>
            <div class="col-md-6">
                <label class="small text-muted">Status</label>
                <p class="mb-0">
                    <span class="status-badge ${statusClass[data.status]}">
                        <i class="fas ${statusIcon[data.status]}"></i>
                        ${statusLabel[data.status]}
                    </span>
                </p>
            </div>
        </div>
        
        <hr>
        
        <form method="POST" onsubmit="return confirm('Simpan perubahan status?');">
            <input type="hidden" name="action" value="update_status">
            <input type="hidden" name="id" value="${data.id}">
            
            <div class="mb-3">
                <label class="form-label">Update Status</label>
                <select name="status" class="form-select" required>
                    <option value="pending" ${data.status === 'pending' ? 'selected' : ''}>Pending</option>
                    <option value="dibaca" ${data.status === 'dibaca' ? 'selected' : ''}>Dibaca</option>
                    <option value="ditanggapi" ${data.status === 'ditanggapi' ? 'selected' : ''}>Ditanggapi</option>
                </select>
            </div>
            
            <div class="mb-3">
                <label class="form-label">Catatan Admin</label>
                <textarea name="catatan_admin" class="form-control" rows="3" 
                          placeholder="Catatan internal (tidak terlihat oleh pengirim)...">${escapeHtml(data.catatan_admin || '')}</textarea>
            </div>
            
            <div class="d-flex gap-2 justify-content-end">
                <button type="button" class="btn btn-secondary" onclick="closeModal()">
                    <i class="fas fa-times me-2"></i>Tutup
                </button>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save me-2"></i>Simpan
                </button>
            </div>
        </form>
    `;
}

function closeModal() {
    document.getElementById('modalBackdrop').classList.remove('show');
    document.getElementById('detailModal').classList.remove('show');
}

function escapeHtml(text) {
    const map = {
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        '"': '&quot;',
        "'": '&#039;'
    };
    return text ? String(text).replace(/[&<>"']/g, m => map[m]) : '';
}

function formatDate(dateStr) {
    const date = new Date(dateStr);
    const options = { year: 'numeric', month: 'long', day: 'numeric', hour: '2-digit', minute: '2-digit' };
    return date.toLocaleDateString('id-ID', options);
}

<?php if ($detail): ?>
// Auto-show modal if detail is in URL
document.addEventListener('DOMContentLoaded', function() {
    const detailData = <?php echo json_encode($detail); ?>;
    if (detailData) {
        document.getElementById('modalBackdrop').classList.add('show');
        document.getElementById('detailModal').classList.add('show');
        renderDetail(detailData);
    }
});
<?php endif; ?>
</script>

<?php
// Include admin footer
require_once __DIR__ . '/../includes/admin_footer.php';
?>
