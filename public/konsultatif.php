<?php
/**
 * Public Konsultatif Page
 * File: public/konsultatif.php
 * Design Reference: Active SaaS (Orange Accent)
 */

// Set page title
$page_title = "Konsultatif - Laboratorium NCS";

// Include public header
require_once __DIR__ . '/../includes/header.php';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = sanitize($_POST['nama']);
    $email = sanitize($_POST['email']);
    $telepon = sanitize($_POST['telepon']);
    $subjek = sanitize($_POST['subjek']);
    $pesan = sanitize($_POST['pesan']);
    
    // Validate
    $errors = [];
    
    if (empty($nama)) {
        $errors[] = 'Nama harus diisi';
    }
    
    if (empty($email)) {
        $errors[] = 'Email harus diisi';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Format email tidak valid';
    }
    
    if (empty($telepon)) {
        $errors[] = 'Telepon harus diisi';
    }
    
    if (empty($subjek)) {
        $errors[] = 'Subjek harus diisi';
    }
    
    if (empty($pesan)) {
        $errors[] = 'Pesan harus diisi';
    } elseif (strlen($pesan) < 20) {
        $errors[] = 'Pesan minimal 20 karakter';
    }
    
    if (count($errors) === 0) {
        // Insert to database
        $result = executeInsert(
            "INSERT INTO konsultatif (nama, email, telepon, subjek, pesan, status) 
             VALUES (?, ?, ?, ?, ?, 'pending')",
            [$nama, $email, $telepon, $subjek, $pesan]
        );
        
        if ($result) {
            setFlashMessage('success', 'Pesan berhasil dikirim! Kami akan segera menghubungi Anda.');
            redirect(SITE_URL . '/konsultatif.php');
        } else {
            $errors[] = 'Gagal mengirim pesan. Silakan coba lagi.';
        }
    }
}
?>

<style>
/* Active SaaS Inspired Design */
:root {
    --orange-50: #FFF7ED;
    --orange-100: #FFEDD5;
    --orange-500: #F97316;
    --orange-600: #EA580C;
    --orange-700: #C2410C;
    --gray-50: #F9FAFB;
    --gray-800: #1F2937;
    --gray-900: #111827;
}

.hero-gradient {
    background: linear-gradient(135deg, var(--orange-600) 0%, var(--orange-700) 100%);
}

.form-container {
    background: white;
    border-radius: 1rem;
    box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
}

.form-control:focus {
    border-color: var(--orange-500);
    box-shadow: 0 0 0 0.2rem rgba(249, 115, 22, 0.25);
}

.btn-submit {
    background: linear-gradient(135deg, var(--orange-500) 0%, var(--orange-600) 100%);
    color: white;
    border: none;
    padding: 1rem 2.5rem;
    font-size: 1.125rem;
    font-weight: 600;
    border-radius: 0.75rem;
    transition: all 0.3s ease;
}

.btn-submit:hover {
    box-shadow: 0 10px 15px -3px rgba(249, 115, 22, 0.4);
    transform: translateY(-2px);
    color: white;
}

.form-label {
    font-weight: 600;
    color: var(--gray-800);
    margin-bottom: 0.5rem;
}

.form-control, .form-select {
    border: 2px solid #E5E7EB;
    padding: 0.75rem 1rem;
    border-radius: 0.5rem;
    transition: all 0.2s;
}

.form-control:hover, .form-select:hover {
    border-color: var(--orange-500);
}

.char-counter {
    font-size: 0.875rem;
    color: #6B7280;
    text-align: right;
}

.char-counter.warning {
    color: var(--orange-600);
    font-weight: 600;
}

.info-card {
    background: var(--orange-50);
    border-left: 4px solid var(--orange-500);
    padding: 1.5rem;
    border-radius: 0.5rem;
    margin-bottom: 1.5rem;
}

.contact-info {
    background: white;
    border-radius: 1rem;
    padding: 2rem;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
}

.contact-item {
    display: flex;
    align-items: start;
    gap: 1rem;
    padding: 1rem 0;
    border-bottom: 1px solid #E5E7EB;
}

.contact-item:last-child {
    border-bottom: none;
}

.contact-icon {
    width: 48px;
    height: 48px;
    background: linear-gradient(135deg, var(--orange-500), var(--orange-600));
    border-radius: 0.75rem;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    flex-shrink: 0;
}

.alert-errors {
    background: #FEE2E2;
    border-left: 4px solid #DC2626;
    color: #991B1B;
    padding: 1rem 1.5rem;
    border-radius: 0.5rem;
    margin-bottom: 1.5rem;
}
</style>

<!-- Hero Section -->
<section class="hero-gradient text-white py-20 relative" data-aos="fade-down">
    <div class="container mx-auto px-4">
        <div class="max-w-4xl mx-auto text-center">
            <h1 class="text-5xl md:text-6xl font-bold mb-6" data-aos="fade-up" data-aos-delay="100">
                Konsultasi dengan Kami
            </h1>
            <p class="text-xl md:text-2xl text-orange-100 mb-8" data-aos="fade-up" data-aos-delay="200">
                Hubungi laboratorium untuk konsultasi, kerjasama, atau pertanyaan lainnya
            </p>
        </div>
    </div>
    
    <!-- Wave Divider -->
    <div class="absolute bottom-0 left-0 right-0">
        <svg viewBox="0 0 1440 120" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M0 120L60 105C120 90 240 60 360 45C480 30 600 30 720 37.5C840 45 960 60 1080 67.5C1200 75 1320 75 1380 75L1440 75V120H1380C1320 120 1200 120 1080 120C960 120 840 120 720 120C600 120 480 120 360 120C240 120 120 120 60 120H0Z" fill="white"/>
        </svg>
    </div>
</section>

<!-- Form Section -->
<section class="py-16 bg-gray-50">
    <div class="container mx-auto px-4">
        <div class="max-w-6xl mx-auto">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                
                <!-- Contact Info Sidebar -->
                <div class="lg:col-span-1" data-aos="fade-right">
                    <div class="contact-info sticky" style="top: 2rem;">
                        <h3 class="text-2xl font-bold text-gray-900 mb-6">
                            <i class="fas fa-address-book text-orange-600 mr-2"></i>
                            Informasi Kontak
                        </h3>
                        
                        <div class="contact-item">
                            <div class="contact-icon">
                                <i class="fas fa-map-marker-alt text-xl"></i>
                            </div>
                            <div>
                                <h4 class="font-semibold text-gray-900 mb-1">Alamat</h4>
                                <p class="text-gray-600 text-sm">
                                    Gedung Teknik Informatika<br>
                                    Universitas XYZ<br>
                                    Jl. Contoh No. 123, Jakarta
                                </p>
                            </div>
                        </div>
                        
                        <div class="contact-item">
                            <div class="contact-icon">
                                <i class="fas fa-envelope text-xl"></i>
                            </div>
                            <div>
                                <h4 class="font-semibold text-gray-900 mb-1">Email</h4>
                                <a href="mailto:lab.ncs@example.com" class="text-orange-600 hover:text-orange-700 text-sm">
                                    lab.ncs@example.com
                                </a>
                            </div>
                        </div>
                        
                        <div class="contact-item">
                            <div class="contact-icon">
                                <i class="fas fa-phone text-xl"></i>
                            </div>
                            <div>
                                <h4 class="font-semibold text-gray-900 mb-1">Telepon</h4>
                                <a href="tel:+62211234567" class="text-orange-600 hover:text-orange-700 text-sm">
                                    +62 21 1234 5678
                                </a>
                            </div>
                        </div>
                        
                        <div class="contact-item">
                            <div class="contact-icon">
                                <i class="fas fa-clock text-xl"></i>
                            </div>
                            <div>
                                <h4 class="font-semibold text-gray-900 mb-1">Jam Operasional</h4>
                                <p class="text-gray-600 text-sm">
                                    Senin - Jumat<br>
                                    08:00 - 16:00 WIB
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Form -->
                <div class="lg:col-span-2" data-aos="fade-left">
                    <div class="form-container p-8">
                        <h3 class="text-2xl font-bold text-gray-900 mb-2">
                            Kirim Pesan
                        </h3>
                        <p class="text-gray-600 mb-6">
                            Isi formulir di bawah ini dan kami akan segera menghubungi Anda kembali.
                        </p>
                        
                        <?php if (isset($errors) && count($errors) > 0): ?>
                        <div class="alert-errors">
                            <strong><i class="fas fa-exclamation-triangle mr-2"></i>Terjadi Kesalahan:</strong>
                            <ul class="mt-2 ml-6 list-disc">
                                <?php foreach ($errors as $error): ?>
                                <li><?php echo htmlspecialchars($error); ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                        <?php endif; ?>
                        
                        <?php displayFlashMessage(); ?>
                        
                        <form method="POST" id="konsultatifForm">
                            <div class="row g-4">
                                <div class="col-md-6">
                                    <label class="form-label">
                                        Nama Lengkap <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" class="form-control" name="nama" required
                                           value="<?php echo htmlspecialchars($_POST['nama'] ?? ''); ?>"
                                           placeholder="Masukkan nama lengkap Anda">
                                </div>
                                
                                <div class="col-md-6">
                                    <label class="form-label">
                                        Email <span class="text-danger">*</span>
                                    </label>
                                    <input type="email" class="form-control" name="email" required
                                           value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>"
                                           placeholder="nama@email.com">
                                </div>
                                
                                <div class="col-md-6">
                                    <label class="form-label">
                                        Nomor Telepon <span class="text-danger">*</span>
                                    </label>
                                    <input type="tel" class="form-control" name="telepon" required
                                           value="<?php echo htmlspecialchars($_POST['telepon'] ?? ''); ?>"
                                           placeholder="08xx xxxx xxxx">
                                </div>
                                
                                <div class="col-md-6">
                                    <label class="form-label">
                                        Subjek <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" class="form-control" name="subjek" required
                                           value="<?php echo htmlspecialchars($_POST['subjek'] ?? ''); ?>"
                                           placeholder="Topik konsultasi">
                                </div>
                                
                                <div class="col-12">
                                    <label class="form-label">
                                        Pesan <span class="text-danger">*</span>
                                    </label>
                                    <textarea class="form-control" name="pesan" rows="6" required
                                              maxlength="2000" id="pesanTextarea"
                                              placeholder="Tuliskan pesan Anda dengan detail (minimal 20 karakter)..."><?php echo htmlspecialchars($_POST['pesan'] ?? ''); ?></textarea>
                                    <div class="char-counter mt-2" id="charCounter">
                                        <span id="charCount">0</span>/2000 karakter (minimal 20)
                                    </div>
                                </div>
                                
                                <div class="col-12">
                                    <div class="info-card">
                                        <i class="fas fa-info-circle text-orange-600 mr-2"></i>
                                        <strong>Catatan:</strong> Semua field bertanda <span class="text-danger">*</span> wajib diisi. 
                                        Kami akan merespons pesan Anda dalam 1-2 hari kerja.
                                    </div>
                                </div>
                                
                                <div class="col-12">
                                    <button type="submit" class="btn btn-submit w-100">
                                        <i class="fas fa-paper-plane mr-2"></i>
                                        Kirim Pesan
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                
            </div>
        </div>
    </div>
</section>

<!-- Map Section (Optional) -->
<section class="py-16 bg-white">
    <div class="container mx-auto px-4">
        <div class="max-w-6xl mx-auto" data-aos="fade-up">
            <h3 class="text-3xl font-bold text-center text-gray-900 mb-4">
                Lokasi Kami
            </h3>
            <p class="text-center text-gray-600 mb-8">
                Kunjungi laboratorium kami untuk konsultasi langsung
            </p>
            
            <div class="rounded-xl overflow-hidden shadow-lg" style="height: 400px;">
                <iframe 
                    src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3966.666666666666!2d106.8166666!3d-6.2000000!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x0%3A0x0!2zNsKwMTInMDAuMCJTIDEwNsKwNDknMDAuMCJF!5e0!3m2!1sen!2sid!4v1234567890"
                    width="100%" 
                    height="100%" 
                    style="border:0;" 
                    allowfullscreen="" 
                    loading="lazy">
                </iframe>
            </div>
        </div>
    </div>
</section>

<script>
// Character counter
const textarea = document.getElementById('pesanTextarea');
const charCount = document.getElementById('charCount');
const charCounter = document.getElementById('charCounter');

function updateCharCount() {
    const length = textarea.value.length;
    charCount.textContent = length;
    
    if (length < 20) {
        charCounter.classList.add('warning');
    } else {
        charCounter.classList.remove('warning');
    }
    
    if (length > 1900) {
        charCounter.classList.add('warning');
    }
}

textarea.addEventListener('input', updateCharCount);

// Initialize
updateCharCount();

// Form validation
const form = document.getElementById('konsultatifForm');
form.addEventListener('submit', function(e) {
    const pesan = textarea.value;
    
    if (pesan.length < 20) {
        e.preventDefault();
        alert('Pesan minimal 20 karakter!');
        textarea.focus();
        return false;
    }
    
    // Show loading state
    const btn = form.querySelector('button[type="submit"]');
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Mengirim...';
});

// Phone validation (optional)
const phoneInput = document.querySelector('input[name="telepon"]');
phoneInput.addEventListener('input', function(e) {
    // Remove non-numeric characters
    let value = e.target.value.replace(/[^0-9+]/g, '');
    e.target.value = value;
});
</script>

<?php
// Include public footer
require_once __DIR__ . '/../includes/footer.php';
?>
