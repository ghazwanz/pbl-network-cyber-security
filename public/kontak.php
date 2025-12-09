<?php
/**
 * Public Kontak Page
 * File: public/kontak.php
 * Updated: Form Card Clean Style (Removed Top Orange Border)
 */

// Set page title
$page_title = "Kontak - Laboratorium NCS";

// Include header
require_once __DIR__ . '/../includes/header.php';

// Fetch profil_lab data
$profil = executeQuerySingle("SELECT * FROM profil_lab LIMIT 1");
$email = $profil['email'] ?? 'labncs@polinema.ac.id';
$no_telepon = $profil['no_telepon'] ?? '+62 31 2345 6789';
$alamat = $profil['alamat'] ?? 'Gedung TI Lt. 3, Jl. Soekarno Hatta No.9, Malang';
$youtube = $profil['youtube'] ?? '#';
$instagram = $profil['instagram'] ?? '#';
$github = $profil['github'] ?? '#';

// Form handling
$success_message = '';
$error_message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize input data
    $message = sanitize($_POST['message'] ?? '');
    
    // Validasi
    if (empty($message)) {
        $error_message = "Pertanyaan wajib diisi.";
    } elseif (strlen($message) < 10) {
        $error_message = "Pertanyaan minimal harus 10 karakter.";
    } elseif (strlen($message) > 1000) {
        $error_message = "Pertanyaan maksimal 1000 karakter.";
    } else {
        $query = "INSERT INTO konsultatif (pertanyaan, created_at) VALUES (?, NOW())";
        $result = executeInsert($query, [$message]);
        
        if ($result) {
            $success_message = "Terima kasih! Pertanyaan Anda telah berhasil dikirim.";
            $message = '';
        } else {
            $error_message = "Maaf, terjadi kesalahan saat mengirim pertanyaan. Silakan coba lagi.";
        }
    }
}
?>

<main class="relative min-h-screen px-4 overflow-hidden">
    
    <div class="absolute inset-0 bg-gradient-to-b from-orange-50/80 via-white to-blue-50/60"></div>
    
    <div class="absolute -left-20 top-0 w-[60%] h-full bg-gradient-to-br from-orange-100/40 via-orange-50/30 to-transparent blur-3xl pointer-events-none"></div>
    <div class="absolute -right-20 top-0 w-[60%] h-full bg-gradient-to-bl from-blue-100/40 via-indigo-50/20 to-transparent blur-3xl pointer-events-none"></div>
    
    <div class="absolute inset-0 opacity-[0.015] pointer-events-none" style="background-image: url('data:image/svg+xml,%3Csvg viewBox=%220 0 256 256%22 xmlns=%22http://www.w3.org/2000/svg%22%3E%3Cfilter id=%22noise%22%3E%3CfeTurbulence type=%22fractalNoise%22 baseFrequency=%220.9%22 numOctaves=%224%22 stitchTiles=%22stitch%22/%3E%3C/filter%3E%3Crect width=%22100%25%22 height=%22100%25%22 filter=%22url(%23noise)%22/%3E%3C/svg%3E');"></div>

    <div class="mx-auto py-32 lg:py-40 relative">
        
        <div class="text-center mb-16" data-aos="fade-up">
            <div class="inline-flex items-center gap-2.5 px-5 py-2.5 bg-white/80 backdrop-blur-sm border border-orange-200/60 rounded-full text-orange-600 font-semibold text-sm mb-6 shadow-lg shadow-orange-100/50">
                <span class="w-2 h-2 bg-orange-500 rounded-full animate-pulse"></span>
                <span class="tracking-wide">HUBUNGI KAMI</span>
            </div>
            
            <h1 class="text-4xl md:text-5xl lg:text-6xl font-medium text-[#1B2D62] mb-6 leading-tight tracking-tight">
                Kontak <br class="hidden sm:block">
                <span class="bg-gradient-to-r from-[#1B2D62] via-[#2C4AA4] to-orange-500 bg-clip-text text-transparent">Laboratorium NCS</span>
            </h1>
            
            <p class="text-lg text-gray-600 max-w-2xl mx-auto leading-relaxed">
                Ada pertanyaan seputar layanan laboratorium atau kerjasama? Kami siap membantu Anda dengan solusi terbaik.
            </p>
        </div>

        <div class="max-w-7xl mx-auto">
            <div class="grid grid-cols-1 lg:grid-cols-5 gap-8 lg:gap-12 items-start">
                
                <div class="lg:col-span-3" data-aos="fade-right">
                    <div class="bg-white/90 backdrop-blur-sm rounded-3xl border border-gray-100 p-8 lg:p-10 shadow-2xl shadow-blue-900/5 relative overflow-hidden group">
                        
                        <div class="flex items-center gap-5 mb-8">
                            <div class="w-14 h-14 bg-gradient-to-br from-orange-100 to-orange-50 rounded-2xl flex items-center justify-center shadow-inner border border-orange-100">
                                <i class="fas fa-comment-dots text-orange-500 text-2xl"></i>
                            </div>
                            <div>
                                <h2 class="text-2xl font-bold text-[#1B2D62]">Kirim Pertanyaan</h2>
                                <p class="text-gray-500">Sampaikan pesan Anda kepada kami.</p>
                            </div>
                        </div>

                        <?php if (!empty($success_message)): ?>
                        <div class="bg-green-50/80 border border-green-200 text-green-700 px-5 py-4 rounded-xl mb-6 backdrop-blur-sm animate-fade-in" role="alert">
                            <div class="flex items-center gap-3">
                                <div class="w-6 h-6 bg-green-100 rounded-full flex items-center justify-center flex-shrink-0">
                                    <i class="fas fa-check text-xs text-green-600"></i>
                                </div>
                                <p class="text-sm font-medium"><?php echo $success_message; ?></p>
                            </div>
                        </div>
                        <?php endif; ?>

                        <?php if (!empty($error_message)): ?>
                        <div class="bg-red-50/80 border border-red-200 text-red-700 px-5 py-4 rounded-xl mb-6 backdrop-blur-sm animate-fade-in" role="alert">
                            <div class="flex items-center gap-3">
                                <div class="w-6 h-6 bg-red-100 rounded-full flex items-center justify-center flex-shrink-0">
                                    <i class="fas fa-exclamation text-xs text-red-600"></i>
                                </div>
                                <p class="text-sm font-medium"><?php echo $error_message; ?></p>
                            </div>
                        </div>
                        <?php endif; ?>

                        <form method="POST" action="" class="space-y-6">
                            <div class="group/input">
                                <label class="block text-[#1B2D62] font-semibold mb-2.5 text-sm tracking-wide" for="message">
                                    PERTANYAAN <span class="text-orange-500">*</span>
                                </label>
                                <div class="relative">
                                    <textarea 
                                        id="message" 
                                        name="message"
                                        rows="6"
                                        class="w-full px-5 py-4 bg-gray-50 border border-gray-200 rounded-2xl focus:outline-none focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500 focus:bg-white transition-all resize-none text-gray-700 placeholder-gray-400"
                                        placeholder="Tulis pertanyaan Anda di sini... (minimal 10 karakter)"
                                        required
                                        minlength="10"
                                        maxlength="1000"
                                    ><?php echo htmlspecialchars($message ?? ''); ?></textarea>
                                    <div class="absolute bottom-4 right-4 text-gray-400 pointer-events-none">
                                        <i class="fas fa-pen text-sm opacity-50"></i>
                                    </div>
                                </div>
                                <p class="text-gray-400 text-xs mt-2 ml-1">Minimal 10 karakter, maksimal 1000 karakter.</p>
                            </div>

                            <button type="submit" class="w-full group/btn inline-flex items-center justify-center gap-3 bg-gradient-to-r from-orange-500 to-orange-600 text-white font-semibold text-base px-8 py-4 rounded-xl shadow-lg shadow-orange-500/30 hover:shadow-xl hover:shadow-orange-500/40 hover:scale-[1.01] active:scale-[0.99] transition-all duration-300">
                                <span>Kirim Pertanyaan</span>
                                <i class="fas fa-paper-plane transition-transform duration-300 group-hover/btn:translate-x-1 group-hover/btn:-translate-y-1"></i>
                            </button>
                        </form>
                    </div>
                </div>

                <div class="lg:col-span-2 space-y-6" data-aos="fade-left">
                    
                    <div class="bg-white rounded-2xl border border-gray-100 p-6 shadow-sm hover:shadow-xl hover:shadow-blue-900/5 transition-all duration-300 group">
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 bg-gradient-to-br from-orange-100 to-orange-50 rounded-xl flex items-center justify-center flex-shrink-0 group-hover:scale-110 transition-transform duration-300 border border-orange-100">
                                <i class="fas fa-envelope text-orange-500 text-lg"></i>
                            </div>
                            <div>
                                <h3 class="text-base font-bold text-[#1B2D62] mb-0.5">Email</h3>
                                <a href="mailto:<?php echo htmlspecialchars($email); ?>" class="text-gray-500 hover:text-orange-600 transition-colors text-sm font-medium">
                                    <?php echo htmlspecialchars($email); ?>
                                </a>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-2xl border border-gray-100 p-6 shadow-sm hover:shadow-xl hover:shadow-blue-900/5 transition-all duration-300 group">
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 bg-gradient-to-br from-blue-100 to-blue-50 rounded-xl flex items-center justify-center flex-shrink-0 group-hover:scale-110 transition-transform duration-300 border border-blue-100">
                                <i class="fas fa-phone-alt text-blue-600 text-lg"></i>
                            </div>
                            <div>
                                <h3 class="text-base font-bold text-[#1B2D62] mb-0.5">Telepon</h3>
                                <a href="tel:<?php echo htmlspecialchars(str_replace([' ', '-'], '', $no_telepon)); ?>" class="text-gray-500 hover:text-blue-600 transition-colors text-sm font-medium">
                                    <?php echo htmlspecialchars($no_telepon); ?>
                                </a>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-2xl border border-gray-100 p-6 shadow-sm hover:shadow-xl hover:shadow-blue-900/5 transition-all duration-300 group">
                        <div class="flex items-start gap-4">
                            <div class="w-12 h-12 bg-gradient-to-br from-emerald-100 to-emerald-50 rounded-xl flex items-center justify-center flex-shrink-0 group-hover:scale-110 transition-transform duration-300 border border-emerald-100">
                                <i class="fas fa-map-marker-alt text-emerald-600 text-lg"></i>
                            </div>
                            <div>
                                <h3 class="text-base font-bold text-[#1B2D62] mb-1">Lokasi</h3>
                                <p class="text-gray-500 text-sm leading-relaxed">
                                    <?php echo htmlspecialchars($alamat); ?>
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-gradient-to-br from-[#1B2D62] to-[#2C4AA4] rounded-2xl p-6 shadow-lg shadow-blue-900/20 text-white relative overflow-hidden">
                        <div class="absolute inset-0 opacity-10 bg-[url('data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMjAiIGhlaWdodD0iMjAiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+PGNpcmNsZSBjeD0iMSIgY3k9IjEiIHI9IjEiIGZpbGw9IiNmZmYiLz48L3N2Zz4=')]"></div>
                        
                        <h3 class="text-base font-bold mb-4 relative z-10">Ikuti Sosial Media Kami</h3>
                        <div class="flex items-center gap-3 relative z-10">
                            <a href="<?php echo htmlspecialchars($instagram); ?>" target="_blank" class="w-10 h-10 bg-white/10 backdrop-blur-md rounded-xl flex items-center justify-center hover:bg-white hover:text-pink-600 hover:scale-110 transition-all duration-300 border border-white/10">
                                <i class="fab fa-instagram"></i>
                            </a>
                            <a href="<?php echo htmlspecialchars($youtube); ?>" target="_blank" class="w-10 h-10 bg-white/10 backdrop-blur-md rounded-xl flex items-center justify-center hover:bg-white hover:text-red-600 hover:scale-110 transition-all duration-300 border border-white/10">
                                <i class="fab fa-youtube"></i>
                            </a>
                            <a href="<?php echo htmlspecialchars($github); ?>" target="_blank" class="w-10 h-10 bg-white/10 backdrop-blur-md rounded-xl flex items-center justify-center hover:bg-white hover:text-gray-900 hover:scale-110 transition-all duration-300 border border-white/10">
                                <i class="fab fa-github"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<?php
// Include footer
require_once __DIR__ . '/../includes/footer.php';
?>