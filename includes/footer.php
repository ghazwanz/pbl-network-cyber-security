    </div>
    <!-- End Main Content Wrapper -->
    
    <!-- Footer -->
    <footer class="bg-gray-800 text-white mt-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- About Section -->
                <div>
                    <h3 class="text-xl font-bold mb-4">Tentang Lab NCS</h3>
                    <p class="text-gray-300 leading-relaxed">
                        Laboratorium Network and Cybersecurity adalah pusat penelitian dan pengembangan 
                        di bidang jaringan komputer dan keamanan siber.
                    </p>
                </div>
                
                <!-- Quick Links -->
                <div>
                    <h3 class="text-xl font-bold mb-4">Menu Cepat</h3>
                    <ul class="space-y-2">
                        <li><a href="<?php echo SITE_URL; ?>/index.php" class="text-gray-300 hover:text-white transition duration-200">Beranda</a></li>
                        <li><a href="<?php echo SITE_URL; ?>/profil.php" class="text-gray-300 hover:text-white transition duration-200">Profil</a></li>
                        <li><a href="<?php echo SITE_URL; ?>/galeri.php" class="text-gray-300 hover:text-white transition duration-200">Galeri</a></li>
                        <li><a href="<?php echo SITE_URL; ?>/arsip.php" class="text-gray-300 hover:text-white transition duration-200">Arsip</a></li>
                        <li><a href="<?php echo SITE_URL; ?>/layanan.php" class="text-gray-300 hover:text-white transition duration-200">Layanan</a></li>
                    </ul>
                </div>
                
                <!-- Contact Info -->
                <div>
                    <h3 class="text-xl font-bold mb-4">Kontak</h3>
                    <ul class="space-y-2 text-gray-300">
                        <li class="flex items-center">
                            <i class="fas fa-map-marker-alt mr-3"></i>
                            <span>Jl. Universitas No. 123, Indonesia</span>
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-phone mr-3"></i>
                            <span>+62 123 4567 890</span>
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-envelope mr-3"></i>
                            <span>info@lab.ncs.ac.id</span>
                        </li>
                    </ul>
                    
                    <!-- Social Media -->
                    <div class="flex space-x-4 mt-4">
                        <a href="#" class="text-gray-300 hover:text-white transition duration-200">
                            <i class="fab fa-facebook fa-lg"></i>
                        </a>
                        <a href="#" class="text-gray-300 hover:text-white transition duration-200">
                            <i class="fab fa-instagram fa-lg"></i>
                        </a>
                        <a href="#" class="text-gray-300 hover:text-white transition duration-200">
                            <i class="fab fa-twitter fa-lg"></i>
                        </a>
                        <a href="#" class="text-gray-300 hover:text-white transition duration-200">
                            <i class="fab fa-youtube fa-lg"></i>
                        </a>
                    </div>
                </div>
            </div>
            
            <!-- Copyright -->
            <div class="border-t border-gray-700 mt-8 pt-8 text-center text-gray-400">
                <p>&copy; <?php echo date('Y'); ?> <?php echo SITE_NAME; ?>. All rights reserved.</p>
                <p class="mt-2 text-sm">Developed with <i class="fas fa-heart text-red-500"></i> by Lab NCS Team</p>
            </div>
        </div>
    </footer>
    
    <!-- Back to Top Button -->
    <button id="back-to-top" class="fixed bottom-8 right-8 bg-blue-600 text-white p-3 rounded-full shadow-lg hover:bg-blue-700 transition duration-200 hidden z-50">
        <i class="fas fa-arrow-up"></i>
    </button>
    
    <!-- jQuery CDN -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    
    <!-- AOS JS -->
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    
    <!-- Custom JavaScript -->
    <script src="<?php echo ASSETS_URL; ?>/js/script.js"></script>
    
    <!-- Initialize AOS -->
    <script>
        AOS.init({
            duration: 800,
            easing: 'ease-in-out',
            once: true,
            offset: 100
        });
    </script>
    
    <!-- Mobile Menu Toggle -->
    <script>
        $(document).ready(function() {
            // Mobile menu toggle
            $('#mobile-menu-button').click(function() {
                $('#mobile-menu').slideToggle(200);
            });
            
            // Back to top button
            $(window).scroll(function() {
                if ($(this).scrollTop() > 300) {
                    $('#back-to-top').fadeIn();
                } else {
                    $('#back-to-top').fadeOut();
                }
            });
            
            $('#back-to-top').click(function() {
                $('html, body').animate({scrollTop: 0}, 600);
                return false;
            });
            
            // Close mobile menu when clicking outside
            $(document).click(function(event) {
                if (!$(event.target).closest('#mobile-menu-button, #mobile-menu').length) {
                    $('#mobile-menu').slideUp(200);
                }
            });
        });
    </script>
</body>
</html>
