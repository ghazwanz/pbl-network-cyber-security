<?php
/**
 * Public Kontak Page
 * File: public/kontak.php
 * Design Reference: Single Section Contact Page with Form & Info Cards
 */

// Set page title
$page_title = "Kontak - Laboratorium NCS";

// Include header
require_once __DIR__ . '/../includes/header.php';

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

<!-- Single Contact Section -->
<main class="relative min-h-screen bg-gradient-to-br from-[#F8FCFF] via-white to-orange-50 overflow-hidden">
    <!-- Decorative Elements -->
    <div class="absolute top-20 right-10 w-72 h-72 bg-orange-100 rounded-full mix-blend-multiply filter blur-2xl opacity-30"></div>
    <div class="absolute bottom-20 left-10 w-72 h-72 bg-blue-100 rounded-full mix-blend-multiply filter blur-2xl opacity-30"></div>

    <div class="mx-auto px-4 py-32 lg:py-40">
        <!-- Header Section -->
        <div class="text-center mb-16" data-aos="fade-up">
            <!-- Badge -->
            <div class="inline-flex items-center gap-2 px-5 py-2.5 bg-white border-2 border-orange-200 rounded-full text-orange-600 font-bold mb-6 shadow-lg">
                <i class="fas fa-headset text-orange-500"></i>
                <span class="text-sm tracking-wide">HUBUNGI KAMI</span>
            </div>
            
            <!-- Heading -->
            <h1 class="text-4xl md:text-5xl lg:text-6xl font-medium text-[#1B2D62] mb-6 font-inter leading-tight">
                Kontak Laboratorium NCS
            </h1>
            
            <!-- Subtitle -->
            <p class="text-lg text-gray-500 font-inter max-w-2xl mx-auto">
                Ada pertanyaan seputar layanan laboratorium atau kerjasama? Kami siap membantu Anda.
            </p>
        </div>

        <!-- Main Content Grid -->
        <div class="max-w-7xl mx-auto">
            <div class="grid grid-cols-1 lg:grid-cols-5 gap-8 lg:gap-12">
                
                <!-- Left Column - Form (3 cols) -->
                <div class="lg:col-span-3" data-aos="fade-right">
                    <div class="bg-white rounded-3xl border border-gray-200 p-8 lg:p-10 shadow-sm">
                        <!-- Form Header -->
                        <div class="flex items-center gap-4 mb-8">
                            <div class="w-12 h-12 bg-orange-100 rounded-xl flex items-center justify-center">
                                <i class="fas fa-comment-dots text-orange-500 text-lg"></i>
                            </div>
                            <div>
                                <h2 class="text-xl font-bold text-[#1B2D62] font-inter">Kirim Pertanyaan</h2>
                                <p class="text-gray-500 text-sm font-inter">Sampaikan pertanyaan Anda kepada kami.</p>
                            </div>
                        </div>

                        <!-- Success Message -->
                        <?php if (!empty($success_message)): ?>
                        <div class="bg-green-50 border border-green-200 text-green-700 px-5 py-4 rounded-xl mb-6" role="alert">
                            <div class="flex items-center gap-3">
                                <i class="fas fa-check-circle text-green-500"></i>
                                <p class="font-inter text-sm"><?php echo $success_message; ?></p>
                            </div>
                        </div>
                        <?php endif; ?>

                        <!-- Error Message -->
                        <?php if (!empty($error_message)): ?>
                        <div class="bg-red-50 border border-red-200 text-red-700 px-5 py-4 rounded-xl mb-6" role="alert">
                            <div class="flex items-center gap-3">
                                <i class="fas fa-exclamation-circle text-red-500"></i>
                                <p class="font-inter text-sm"><?php echo $error_message; ?></p>
                            </div>
                        </div>
                        <?php endif; ?>

                        <!-- Contact Form -->
                        <form method="POST" action="" class="space-y-5">
                            <!-- Message/Question -->
                            <div>
                                <label class="block text-[#1B2D62] font-semibold mb-2 text-sm font-inter" for="message">
                                    PERTANYAAN <span class="text-red-500">*</span>
                                </label>
                                <textarea 
                                    id="message" 
                                    name="message"
                                    rows="6"
                                    class="w-full px-4 py-3.5 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:ring-1 focus:ring-orange-500 focus:border-orange-500 focus:bg-white transition resize-none font-inter text-gray-700"
                                    placeholder="Tulis pertanyaan Anda di sini... (minimal 10 karakter)"
                                    required
                                    minlength="10"
                                    maxlength="1000"
                                ><?php echo htmlspecialchars($message ?? ''); ?></textarea>
                                <p class="text-gray-400 text-xs mt-2 font-inter">Minimal 10 karakter, maksimal 1000 karakter.</p>
                            </div>

                            <!-- Submit Button -->
                            <button type="submit" class="w-full inline-flex items-center justify-center gap-3 bg-gradient-to-r from-orange-500 to-orange-600 text-white font-semibold text-base px-8 py-4 rounded-xl hover:shadow-lg hover:scale-[1.02] transition-all duration-300 font-inter">
                                <span>Kirim Pertanyaan</span>
                                <i class="fas fa-paper-plane"></i>
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Right Column - Info Cards (2 cols) -->
                <div class="lg:col-span-2 space-y-5" data-aos="fade-left">
                    <!-- Email Card -->
                    <div class="bg-white rounded-2xl border border-gray-200 p-6 shadow-sm hover:shadow-md transition-shadow duration-300 group">
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 bg-orange-100 rounded-xl flex items-center justify-center flex-shrink-0 group-hover:bg-orange-500 transition-colors duration-300">
                                <i class="fas fa-envelope text-orange-500 text-lg group-hover:text-white transition-colors duration-300"></i>
                            </div>
                            <div>
                                <h3 class="text-lg font-bold text-[#1B2D62] font-inter">Email</h3>
                                <a href="mailto:labncs@polinema.ac.id" class="text-gray-500 hover:text-orange-500 transition text-sm font-inter">
                                    labncs@polinema.ac.id
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Phone Card -->
                    <div class="bg-white rounded-2xl border border-gray-200 p-6 shadow-sm hover:shadow-md transition-shadow duration-300 group">
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center flex-shrink-0 group-hover:bg-blue-500 transition-colors duration-300">
                                <i class="fas fa-phone-alt text-blue-500 text-lg group-hover:text-white transition-colors duration-300"></i>
                            </div>
                            <div>
                                <h3 class="text-lg font-bold text-[#1B2D62] font-inter">Telepon</h3>
                                <a href="tel:+6231234567890" class="text-gray-500 hover:text-blue-500 transition text-sm font-inter">
                                    +62 31 2345 6789
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Location Card -->
                    <div class="bg-white rounded-2xl border border-gray-200 p-6 shadow-sm hover:shadow-md transition-shadow duration-300 group">
                        <div class="flex items-start gap-4">
                            <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center flex-shrink-0 group-hover:bg-green-500 transition-colors duration-300">
                                <i class="fas fa-map-marker-alt text-green-500 text-lg group-hover:text-white transition-colors duration-300"></i>
                            </div>
                            <div>
                                <h3 class="text-lg font-bold text-[#1B2D62] font-inter">Lokasi</h3>
                                <p class="text-gray-500 text-sm font-inter leading-relaxed">
                                    Gedung TI Lt. 3, Jl. Soekarno Hatta No.9, Malang
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- WhatsApp Card -->
                    <div class="bg-white rounded-2xl border border-gray-200 p-6 shadow-sm hover:shadow-md transition-shadow duration-300 group">
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center flex-shrink-0 group-hover:bg-green-500 transition-colors duration-300">
                                <i class="fab fa-whatsapp text-green-500 text-xl group-hover:text-white transition-colors duration-300"></i>
                            </div>
                            <div>
                                <h3 class="text-lg font-bold text-[#1B2D62] font-inter">WhatsApp</h3>
                                <a href="https://wa.me/6281234567890" target="_blank" class="text-gray-500 hover:text-green-500 transition text-sm font-inter">
                                    +62 812 3456 7890
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Social Media Card -->
                    <div class="bg-white rounded-2xl border border-gray-200 p-6 shadow-sm">
                        <h3 class="text-lg font-bold text-[#1B2D62] font-inter mb-4">Ikuti Kami</h3>
                        <div class="flex items-center gap-3">
                            <a href="#" class="w-11 h-11 bg-gradient-to-br from-purple-500 to-pink-500 rounded-xl flex items-center justify-center hover:scale-110 transition-transform duration-300">
                                <i class="fab fa-instagram text-white"></i>
                            </a>
                            <a href="#" class="w-11 h-11 bg-blue-600 rounded-xl flex items-center justify-center hover:scale-110 transition-transform duration-300">
                                <i class="fab fa-linkedin-in text-white"></i>
                            </a>
                            <a href="#" class="w-11 h-11 bg-red-600 rounded-xl flex items-center justify-center hover:scale-110 transition-transform duration-300">
                                <i class="fab fa-youtube text-white"></i>
                            </a>
                            <a href="#" class="w-11 h-11 bg-[#1B2D62] rounded-xl flex items-center justify-center hover:scale-110 transition-transform duration-300">
                                <i class="fab fa-github text-white"></i>
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
