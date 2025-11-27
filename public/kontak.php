<?php
/**
 * Halaman Kontak Kami - Network & Cyber Security Lab
 * 
 * Halaman untuk menampilkan informasi kontak, form kontak,
 * dan detail laboratorium Network & Cyber Security
 */

// Include header
include '../includes/header.php';

// Form handling
$success_message = '';
$error_message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize input data
    $name = htmlspecialchars(trim($_POST['name'] ?? ''));
    $email = filter_var(trim($_POST['email'] ?? ''), FILTER_SANITIZE_EMAIL);
    $subject = htmlspecialchars(trim($_POST['subject'] ?? ''));
    $phone = htmlspecialchars(trim($_POST['phone'] ?? ''));
    $message = htmlspecialchars(trim($_POST['message'] ?? ''));
    
    // Basic validation
    if (empty($name) || empty($email) || empty($subject) || empty($message)) {
        $error_message = "Semua field yang bertanda * wajib diisi.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_message = "Format email tidak valid.";
    } elseif (strlen($message) < 10) {
        $error_message = "Pesan minimal harus 10 karakter.";
    } else {
        // Here you can add email sending logic or database storage
        // For now, just show success message
        $success_message = "Terima kasih! Pesan Anda telah berhasil dikirim. Kami akan segera menghubungi Anda.";
        
        // Clear form data after successful submission
        $name = $email = $subject = $phone = $message = '';
    }
}
?>

<!-- Hero Section -->
<section class="py-24 sm:py-32 px-4 sm:px-6 lg:px-8">
    <div class="mx-auto max-w-7xl">
        <div class="text-center max-w-3xl mx-auto">
            <!-- Badge -->
            <div class="inline-flex items-center space-x-2 bg-orange-100 px-4 py-2 rounded-full mb-6" data-aos="fade-up">
                <svg class="w-5 h-5 text-orange-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                </svg>
                <span class="text-orange-700 font-medium text-sm">Kontak Kami</span>
            </div>
            
            <!-- Heading -->
            <h1 class="font-inter text-4xl sm:text-5xl lg:text-6xl font-medium text-gray-900 mb-6" data-aos="fade-up" data-aos-delay="100">
                Hubungi Kami
            </h1>
            
            <!-- Subheading -->
            <p class="text-lg sm:text-xl text-gray-500 leading-relaxed" data-aos="fade-up" data-aos-delay="200">
                Tim kami siap membantu menjawab pertanyaan Anda seputar layanan laboratorium, 
                konsultasi penelitian, atau kerjasama akademik.
            </p>
        </div>
    </div>
</section>

<!-- Contact Information Cards Section -->
<section class="pb-16 sm:pb-20 px-4 sm:px-6 lg:px-8">
    <div class="mx-auto max-w-7xl">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            <!-- Alamat Card -->
            <div class="bg-white border border-gray-200 rounded-xl p-8 shadow-sm hover:shadow-md transition-shadow duration-300" data-aos="fade-up" data-aos-delay="100">
                <div class="inline-flex items-center justify-center w-16 h-16 bg-orange-500 rounded-lg mb-6">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                </div>
                <h3 class="text-2xl font-inter font-medium text-gray-900 mb-2">Alamat</h3>
                <p class="text-gray-500 leading-relaxed">
                    Laboratorium Network & Cyber Security<br>
                    Gedung Teknologi Informasi Lt. 3<br>
                    Jl. Universitas No. 123<br>
                    Surabaya, Jawa Timur 60293
                </p>
            </div>

            <!-- Email Card -->
            <div class="bg-white border border-gray-200 rounded-xl p-8 shadow-sm hover:shadow-md transition-shadow duration-300" data-aos="fade-up" data-aos-delay="200">
                <div class="inline-flex items-center justify-center w-16 h-16 bg-orange-500 rounded-lg mb-6">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                </div>
                <h3 class="text-2xl font-inter font-medium text-gray-900 mb-2">Email</h3>
                <p class="text-gray-500 leading-relaxed mb-2">
                    Kirim email kepada kami untuk pertanyaan atau kerjasama:
                </p>
                <a href="mailto:lab.cybersecurity@university.ac.id" class="text-orange-500 hover:text-orange-600 font-medium transition">
                    lab.cybersecurity@university.ac.id
                </a>
            </div>

            <!-- Telepon Card -->
            <div class="bg-white border border-gray-200 rounded-xl p-8 shadow-sm hover:shadow-md transition-shadow duration-300" data-aos="fade-up" data-aos-delay="300">
                <div class="inline-flex items-center justify-center w-16 h-16 bg-orange-500 rounded-lg mb-6">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                    </svg>
                </div>
                <h3 class="text-2xl font-inter font-medium text-gray-900 mb-2">Telepon</h3>
                <p class="text-gray-500 leading-relaxed mb-2">
                    Hubungi kami langsung melalui telepon:
                </p>
                <a href="tel:+6231234567890" class="text-orange-500 hover:text-orange-600 font-medium block transition">
                    +62 31 2345 6789
                </a>
                <a href="https://wa.me/6281234567890" target="_blank" class="text-orange-500 hover:text-orange-600 font-medium inline-flex items-center space-x-1 mt-2 transition">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/>
                    </svg>
                    <span>WhatsApp</span>
                </a>
            </div>
        </div>
    </div>
</section>

<!-- Contact Form Section -->
<section class="py-16 sm:py-20 px-4 sm:px-6 lg:px-8">
    <div class="mx-auto max-w-7xl">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 lg:gap-16">
            <!-- Form Column -->
            <div data-aos="fade-right">
                <div class="md:sticky md:top-24 block">
                    <div class="mb-8">
                        <h2 class="font-inter text-3xl sm:text-5xl font-medium text-gray-900 mb-4">
                            Kirim Pesan
                        </h2>
                        <p class="text-gray-500 text-lg">
                            Isi formulir di bawah ini dan kami akan merespon secepat mungkin
                        </p>
                    </div>
    
                    <!-- Success Message -->
                    <?php if (!empty($success_message)): ?>
                    <div class="bg-green-50 border border-green-200 text-green-700 px-6 py-4 rounded-lg mb-6" role="alert">
                        <div class="flex items-start">
                            <svg class="w-6 h-6 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            <p><?php echo $success_message; ?></p>
                        </div>
                    </div>
                    <?php endif; ?>
    
                    <!-- Error Message -->
                    <?php if (!empty($error_message)): ?>
                    <div class="bg-red-50 border border-red-200 text-red-700 px-6 py-4 rounded-lg mb-6" role="alert">
                        <div class="flex items-start">
                            <svg class="w-6 h-6 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                            </svg>
                            <p><?php echo $error_message; ?></p>
                        </div>
                    </div>
                    <?php endif; ?>
    
                    <!-- Contact Form -->
                    <form method="POST" action="" class="bg-white border border-gray-200 rounded-xl p-8 shadow-sm">
                        <!-- Pesan -->
                        <div class="mb-6">
                            <label class="block text-gray-700 font-medium mb-2" for="message">
                                Pesan <span class="text-red-500">*</span>
                            </label>
                            <textarea 
                                id="message" 
                                name="message"
                                rows="6"
                                class="w-full px-4 py-3 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#1B2D62] focus:border-transparent transition resize-none"
                                placeholder="Tulis pesan Anda di sini (minimal 10 karakter)"
                                required
                            ><?php echo htmlspecialchars($message ?? ''); ?></textarea>
                        </div>
    
                        <!-- Submit Button -->
                        <button type="submit" class="w-full inline-flex items-center justify-center space-x-2 bg-[#1B2D62] text-white font-bold text-base px-7 py-3.5 rounded-lg shadow-sm transition hover:bg-[#2C4AA4] hover:-translate-y-0.5">
                            <span>Kirim Pesan</span>
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                            </svg>
                        </button>
                    </form>
                </div>
            </div>

            <!-- Info Column -->
            <div data-aos="fade-left">
                <!-- Operating Hours -->
                <div class="bg-white border border-gray-200 rounded-xl p-8 shadow-sm mb-8">
                    <div class="flex items-center space-x-3 mb-6">
                        <div class="w-12 h-12 bg-orange-500 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <h3 class="font-inter text-2xl font-bold text-gray-900">Jam Operasional</h3>
                    </div>
                    
                    <div class="space-y-3">
                        <div class="flex justify-between items-center py-2 border-b border-gray-100">
                            <span class="text-gray-700 font-medium">Senin - Jumat</span>
                            <span class="text-gray-500">08.00 - 16.00 WIB</span>
                        </div>
                        <div class="flex justify-between items-center py-2 border-b border-gray-100">
                            <span class="text-gray-700 font-medium">Sabtu</span>
                            <span class="text-gray-500">08.00 - 12.00 WIB</span>
                        </div>
                        <div class="flex justify-between items-center py-2">
                            <span class="text-gray-700 font-medium">Minggu & Libur</span>
                            <span class="text-red-500 font-medium">Tutup</span>
                        </div>
                    </div>
                </div>

                <!-- Social Media & Quick Links -->
                <div class="bg-white border border-gray-200 rounded-xl p-8 shadow-sm">
                    <h3 class="font-inter text-2xl font-bold text-gray-900 mb-6">Media Sosial</h3>
                    
                    <div class="space-y-4">
                        <!-- Instagram -->
                        <a href="#" class="flex items-center space-x-4 p-3 rounded-lg hover:bg-gray-50 transition group">
                            <div class="w-10 h-10 bg-gradient-to-br from-purple-500 to-pink-500 rounded-lg flex items-center justify-center">
                                <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/>
                                </svg>
                            </div>
                            <div class="flex-1">
                                <p class="text-gray-900 font-medium group-hover:text-orange-500 transition">Instagram</p>
                                <p class="text-gray-500 text-sm">@lab_cybersecurity</p>
                            </div>
                            <svg class="w-5 h-5 text-gray-400 group-hover:text-orange-500 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                            </svg>
                        </a>

                        <!-- LinkedIn -->
                        <a href="#" class="flex items-center space-x-4 p-3 rounded-lg hover:bg-gray-50 transition group">
                            <div class="w-10 h-10 bg-blue-600 rounded-lg flex items-center justify-center">
                                <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/>
                                </svg>
                            </div>
                            <div class="flex-1">
                                <p class="text-gray-900 font-medium group-hover:text-orange-500 transition">LinkedIn</p>
                                <p class="text-gray-500 text-sm">Network & Cyber Security Lab</p>
                            </div>
                            <svg class="w-5 h-5 text-gray-400 group-hover:text-orange-500 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                            </svg>
                        </a>

                        <!-- YouTube -->
                        <a href="#" class="flex items-center space-x-4 p-3 rounded-lg hover:bg-gray-50 transition group">
                            <div class="w-10 h-10 bg-red-600 rounded-lg flex items-center justify-center">
                                <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/>
                                </svg>
                            </div>
                            <div class="flex-1">
                                <p class="text-gray-900 font-medium group-hover:text-orange-500 transition">YouTube</p>
                                <p class="text-gray-500 text-sm">Lab Cyber Security</p>
                            </div>
                            <svg class="w-5 h-5 text-gray-400 group-hover:text-orange-500 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                            </svg>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Map Section -->
<section class="py-16 sm:py-24 px-4 sm:px-6 lg:px-8 bg-white" data-aos="fade-up">
    <div class="mx-auto max-w-5xl">
        <div class="text-center mb-12">
            <h2 class="font-inter text-3xl sm:text-5xl font-medium text-gray-900 mb-4">
                Lokasi Kami
            </h2>
            <p class="text-gray-500 text-lg">
                Temukan lokasi laboratorium kami di kampus
            </p>
        </div>

        <div class="rounded-xl overflow-hidden shadow-lg border border-gray-200">
            <!-- Google Maps Embed - Replace with your actual location -->
            <iframe 
                src="https://www.google.com/maps/embed?pb=!1m17!1m12!1m3!1d247.67!2d112.614628!3d-7.944108!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m2!1m1!2zN8KwNTYnMzguNyJTIDExMsKwMzYnNTIuNyJF!5e0!3m2!1sen!2sid!4v1234567890!5m2!1sen!2sid"
                width="100%" 
                height="500" 
                style="border:0;" 
                allowfullscreen="" 
                loading="lazy" 
                referrerpolicy="no-referrer-when-downgrade"
                class="w-full"
            ></iframe>
        </div>

        <!-- Location Info Below Map -->
        <div class="mt-16 grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="flex items-start space-x-4">
                <div class="w-12 h-12 bg-orange-100 rounded-lg flex items-center justify-center flex-shrink-0">
                    <svg class="w-6 h-6 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/>
                    </svg>
                </div>
                <div>
                    <h4 class="font-inter text-xl font-medium text-gray-900 mb-1">Akses Mudah</h4>
                    <p class="text-gray-500 text-sm">Terletak di gedung utama dengan akses parkir yang memadai</p>
                </div>
            </div>

            <div class="flex items-start space-x-4">
                <div class="w-12 h-12 bg-orange-100 rounded-lg flex items-center justify-center flex-shrink-0">
                    <svg class="w-6 h-6 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                </div>
                <div>
                    <h4 class="font-inter text-xl font-medium text-gray-900 mb-1">Reservasi</h4>
                    <p class="text-gray-500 text-sm">Hubungi kami untuk membuat janji kunjungan atau konsultasi</p>
                </div>
            </div>

            <div class="flex items-start space-x-4">
                <div class="w-12 h-12 bg-orange-100 rounded-lg flex items-center justify-center flex-shrink-0">
                    <svg class="w-6 h-6 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                    </svg>
                </div>
                <div>
                    <h4 class="font-inter text-xl font-medium text-gray-900 mb-1">Tim Siap Membantu</h4>
                    <p class="text-gray-500 text-sm">Tim profesional kami siap menjawab pertanyaan Anda</p>
                </div>
            </div>
        </div>
    </div>
</section>

<?php
// Include footer
include '../includes/footer.php';
?>
