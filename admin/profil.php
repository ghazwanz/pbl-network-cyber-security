<?php
/**
 * Admin CRUD Profil Laboratorium
 * File: admin/profil.php
 * Purpose: Manage single lab profile record
 */

// Set page title
$page_title = "Profil Laboratorium";

// Include admin header
require_once __DIR__ . '/../includes/admin_header.php';

// Handle actions
$action = $_GET['action'] ?? 'edit';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'update') {
    // Get form data
    $nama_lab = sanitize($_POST['nama_lab'] ?? '');
    $deskripsi = sanitize($_POST['deskripsi'] ?? '');
    $visi = sanitize($_POST['visi'] ?? '');
    $misi = sanitize($_POST['misi'] ?? '');
    $sejarah = sanitize($_POST['sejarah'] ?? '');
    $alamat = sanitize($_POST['alamat'] ?? '');
    $email = sanitize($_POST['email'] ?? '');
    $telepon = sanitize($_POST['telepon'] ?? '');
    $website = sanitize($_POST['website'] ?? '');
    $facebook = sanitize($_POST['facebook'] ?? '');
    $twitter = sanitize($_POST['twitter'] ?? '');
    $instagram = sanitize($_POST['instagram'] ?? '');
    $linkedin = sanitize($_POST['linkedin'] ?? '');
    
    $errors = [];
    
    // Validasi
    if (empty($nama_lab)) $errors[] = "Nama laboratorium harus diisi";
    if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Format email tidak valid";
    
    // Handle logo upload
    $logo_path = $_POST['old_logo_path'] ?? null;
    if (isset($_FILES['logo']) && $_FILES['logo']['error'] === UPLOAD_ERR_OK) {
        $upload_result = uploadImage($_FILES['logo'], 'profil', 'logo');
        
        if ($upload_result['success']) {
            // Delete old logo if exists
            if ($logo_path) {
                deleteFile($logo_path);
            }
            $logo_path = $upload_result['path'];
        } else {
            $errors[] = $upload_result['message'];
        }
    }
    
    // If no errors, save to database
    if (empty($errors)) {
        // Check if profile exists
        $existing = executeQuerySingle("SELECT id FROM profil_lab LIMIT 1");
        
        if ($existing) {
            // Update existing profile
            $result = executeNonQuery(
                "UPDATE profil_lab SET 
                    nama_lab = ?, deskripsi = ?, visi = ?, misi = ?, sejarah = ?,
                    logo_path = ?, alamat = ?, email = ?, telepon = ?, website = ?,
                    facebook = ?, twitter = ?, instagram = ?, linkedin = ?, updated_at = NOW()
                 WHERE id = ?",
                [
                    $nama_lab, $deskripsi, $visi, $misi, $sejarah,
                    $logo_path, $alamat, $email, $telepon, $website,
                    $facebook, $twitter, $instagram, $linkedin, $existing['id']
                ]
            );
            
            if ($result !== false) {
                setFlashMessage('success', 'Profil laboratorium berhasil diperbarui');
                redirect(ADMIN_URL . '/profil.php');
            } else {
                $errors[] = "Gagal memperbarui profil laboratorium";
            }
        } else {
            // Insert new profile
            $result = executeInsert(
                "INSERT INTO profil_lab (
                    nama_lab, deskripsi, visi, misi, sejarah, logo_path,
                    alamat, email, telepon, website, facebook, twitter, instagram, linkedin
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)",
                [
                    $nama_lab, $deskripsi, $visi, $misi, $sejarah, $logo_path,
                    $alamat, $email, $telepon, $website, $facebook, $twitter, $instagram, $linkedin
                ]
            );
            
            if ($result) {
                setFlashMessage('success', 'Profil laboratorium berhasil dibuat');
                redirect(ADMIN_URL . '/profil.php');
            } else {
                $errors[] = "Gagal membuat profil laboratorium";
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

// Get current profile
$profil = executeQuerySingle("SELECT * FROM profil_lab LIMIT 1");

// Include admin header
require_once __DIR__ . '/../includes/admin_header.php';
?>

<style>
.rich-textarea {
    min-height: 150px;
    font-family: ui-sans-serif, system-ui, -apple-system, sans-serif;
}

.logo-preview {
    width: 120px;
    height: 120px;
    object-fit: contain;
    border: 2px solid #E5E7EB;
    border-radius: 0.5rem;
    padding: 0.5rem;
    background: white;
}

.input-group-icon {
    position: relative;
}

.input-group-icon .icon {
    position: absolute;
    left: 1rem;
    top: 50%;
    transform: translateY(-50%);
    color: #9CA3AF;
}

.input-group-icon input {
    padding-left: 2.75rem;
}

.char-counter {
    font-size: 0.875rem;
    color: #6B7280;
}

.form-section {
    background: white;
    border-radius: 0.75rem;
    padding: 1.5rem;
    margin-bottom: 1.5rem;
    border: 1px solid #E5E7EB;
}

.form-section-title {
    font-size: 1.125rem;
    font-weight: 700;
    color: #1F2937;
    margin-bottom: 1rem;
    padding-bottom: 0.75rem;
    border-bottom: 2px solid #E5E7EB;
}
</style>

<div class="container-fluid px-4 py-5">
    
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1">
                <i class="fas fa-building text-primary me-2"></i>Profil Laboratorium
            </h2>
            <p class="text-muted mb-0">Kelola informasi profil laboratorium</p>
        </div>
    </div>
    
    <?php displayFlashMessage(); ?>
    
    <!-- Profile Form -->
    <form method="POST" enctype="multipart/form-data" id="profileForm">
        <input type="hidden" name="action" value="update">
        <?php if ($profil && $profil['logo_path']): ?>
        <input type="hidden" name="old_logo_path" value="<?php echo htmlspecialchars($profil['logo_path']); ?>">
        <?php endif; ?>
        
        <!-- Basic Info Section -->
        <div class="form-section">
            <h3 class="form-section-title">
                <i class="fas fa-info-circle text-primary me-2"></i>Informasi Dasar
            </h3>
            
            <div class="row">
                <div class="col-md-8">
                    <div class="mb-3">
                        <label class="form-label">Nama Laboratorium <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="nama_lab" required
                               value="<?php echo htmlspecialchars($profil['nama_lab'] ?? ''); ?>"
                               placeholder="Contoh: Laboratorium Network & Computer System">
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="mb-3">
                        <label class="form-label">Logo Laboratorium</label>
                        <?php if ($profil && $profil['logo_path']): ?>
                        <div class="mb-2">
                            <img src="<?php echo SITE_URL . htmlspecialchars($profil['logo_path']); ?>" 
                                 alt="Current Logo" class="logo-preview">
                        </div>
                        <?php endif; ?>
                        <input type="file" class="form-control" name="logo" accept="image/*">
                        <small class="text-muted">Format: JPG, PNG, GIF (Max 2MB)</small>
                    </div>
                </div>
            </div>
            
            <div class="mb-3">
                <label class="form-label">Deskripsi Singkat</label>
                <textarea class="form-control" name="deskripsi" rows="3" maxlength="500" 
                          placeholder="Deskripsi singkat tentang laboratorium..."><?php echo htmlspecialchars($profil['deskripsi'] ?? ''); ?></textarea>
                <div class="char-counter text-end mt-1">
                    <span id="deskripsi_count">0</span>/500 karakter
                </div>
            </div>
        </div>
        
        <!-- Vision & Mission Section -->
        <div class="form-section">
            <h3 class="form-section-title">
                <i class="fas fa-bullseye text-success me-2"></i>Visi & Misi
            </h3>
            
            <div class="mb-3">
                <label class="form-label">Visi</label>
                <textarea class="form-control rich-textarea" name="visi" maxlength="1000"
                          placeholder="Tuliskan visi laboratorium..."><?php echo htmlspecialchars($profil['visi'] ?? ''); ?></textarea>
                <div class="char-counter text-end mt-1">
                    <span id="visi_count">0</span>/1000 karakter
                </div>
            </div>
            
            <div class="mb-3">
                <label class="form-label">Misi</label>
                <textarea class="form-control rich-textarea" name="misi" maxlength="2000"
                          placeholder="Tuliskan misi laboratorium (gunakan enter untuk poin baru)..."><?php echo htmlspecialchars($profil['misi'] ?? ''); ?></textarea>
                <div class="char-counter text-end mt-1">
                    <span id="misi_count">0</span>/2000 karakter
                </div>
            </div>
        </div>
        
        <!-- History Section -->
        <div class="form-section">
            <h3 class="form-section-title">
                <i class="fas fa-history text-warning me-2"></i>Sejarah
            </h3>
            
            <div class="mb-3">
                <label class="form-label">Sejarah Laboratorium</label>
                <textarea class="form-control rich-textarea" name="sejarah" maxlength="3000"
                          placeholder="Tuliskan sejarah dan perkembangan laboratorium..."><?php echo htmlspecialchars($profil['sejarah'] ?? ''); ?></textarea>
                <div class="char-counter text-end mt-1">
                    <span id="sejarah_count">0</span>/3000 karakter
                </div>
            </div>
        </div>
        
        <!-- Contact Info Section -->
        <div class="form-section">
            <h3 class="form-section-title">
                <i class="fas fa-address-book text-info me-2"></i>Informasi Kontak
            </h3>
            
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Alamat</label>
                    <textarea class="form-control" name="alamat" rows="3" maxlength="500"
                              placeholder="Alamat lengkap laboratorium..."><?php echo htmlspecialchars($profil['alamat'] ?? ''); ?></textarea>
                </div>
                
                <div class="col-md-6">
                    <div class="mb-3 input-group-icon">
                        <label class="form-label">Email</label>
                        <i class="fas fa-envelope icon"></i>
                        <input type="email" class="form-control" name="email"
                               value="<?php echo htmlspecialchars($profil['email'] ?? ''); ?>"
                               placeholder="email@lab.com">
                    </div>
                    
                    <div class="mb-3 input-group-icon">
                        <label class="form-label">Telepon</label>
                        <i class="fas fa-phone icon"></i>
                        <input type="text" class="form-control" name="telepon"
                               value="<?php echo htmlspecialchars($profil['telepon'] ?? ''); ?>"
                               placeholder="(021) 1234-5678">
                    </div>
                    
                    <div class="mb-3 input-group-icon">
                        <label class="form-label">Website</label>
                        <i class="fas fa-globe icon"></i>
                        <input type="url" class="form-control" name="website"
                               value="<?php echo htmlspecialchars($profil['website'] ?? ''); ?>"
                               placeholder="https://lab.example.com">
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Social Media Section -->
        <div class="form-section">
            <h3 class="form-section-title">
                <i class="fas fa-share-alt text-primary me-2"></i>Media Sosial
            </h3>
            
            <div class="row">
                <div class="col-md-6 mb-3 input-group-icon">
                    <label class="form-label">Facebook</label>
                    <i class="fab fa-facebook icon"></i>
                    <input type="url" class="form-control" name="facebook"
                           value="<?php echo htmlspecialchars($profil['facebook'] ?? ''); ?>"
                           placeholder="https://facebook.com/labname">
                </div>
                
                <div class="col-md-6 mb-3 input-group-icon">
                    <label class="form-label">Twitter/X</label>
                    <i class="fab fa-twitter icon"></i>
                    <input type="url" class="form-control" name="twitter"
                           value="<?php echo htmlspecialchars($profil['twitter'] ?? ''); ?>"
                           placeholder="https://twitter.com/labname">
                </div>
                
                <div class="col-md-6 mb-3 input-group-icon">
                    <label class="form-label">Instagram</label>
                    <i class="fab fa-instagram icon"></i>
                    <input type="url" class="form-control" name="instagram"
                           value="<?php echo htmlspecialchars($profil['instagram'] ?? ''); ?>"
                           placeholder="https://instagram.com/labname">
                </div>
                
                <div class="col-md-6 mb-3 input-group-icon">
                    <label class="form-label">LinkedIn</label>
                    <i class="fab fa-linkedin icon"></i>
                    <input type="url" class="form-control" name="linkedin"
                           value="<?php echo htmlspecialchars($profil['linkedin'] ?? ''); ?>"
                           placeholder="https://linkedin.com/company/labname">
                </div>
            </div>
        </div>
        
        <!-- Action Buttons -->
        <div class="d-flex gap-2 justify-content-end">
            <button type="submit" class="btn btn-primary btn-lg px-5">
                <i class="fas fa-save me-2"></i>Simpan Profil
            </button>
        </div>
        
    </form>
    
</div>

<script>
// Character counter
function updateCharCount(textarea, counterId) {
    const counter = document.getElementById(counterId);
    if (counter && textarea) {
        counter.textContent = textarea.value.length;
        
        const maxLength = textarea.getAttribute('maxlength');
        if (maxLength) {
            const remaining = maxLength - textarea.value.length;
            if (remaining < 50) {
                counter.classList.add('text-danger');
            } else {
                counter.classList.remove('text-danger');
            }
        }
    }
}

// Initialize character counters
document.addEventListener('DOMContentLoaded', function() {
    const textareas = [
        { element: document.querySelector('[name="deskripsi"]'), counter: 'deskripsi_count' },
        { element: document.querySelector('[name="visi"]'), counter: 'visi_count' },
        { element: document.querySelector('[name="misi"]'), counter: 'misi_count' },
        { element: document.querySelector('[name="sejarah"]'), counter: 'sejarah_count' }
    ];
    
    textareas.forEach(({ element, counter }) => {
        if (element) {
            updateCharCount(element, counter);
            element.addEventListener('input', () => updateCharCount(element, counter));
        }
    });
    
    // Logo preview
    const logoInput = document.querySelector('[name="logo"]');
    if (logoInput) {
        logoInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file && file.type.startsWith('image/')) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const preview = document.querySelector('.logo-preview');
                    if (preview) {
                        preview.src = e.target.result;
                    } else {
                        const img = document.createElement('img');
                        img.src = e.target.result;
                        img.className = 'logo-preview mb-2';
                        logoInput.parentElement.insertBefore(img, logoInput);
                    }
                };
                reader.readAsDataURL(file);
            }
        });
    }
    
    // Form validation
    const form = document.getElementById('profileForm');
    if (form) {
        form.addEventListener('submit', function(e) {
            const namaLab = form.querySelector('[name="nama_lab"]');
            if (!namaLab.value.trim()) {
                e.preventDefault();
                alert('Nama laboratorium harus diisi');
                namaLab.focus();
                return false;
            }
        });
    }
});
</script>

<?php
// Include admin footer
require_once __DIR__ . '/../includes/admin_footer.php';
?>
