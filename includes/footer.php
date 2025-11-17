    </div>
    <!-- End Main Content Wrapper -->
    
    <!-- Footer -->
    <footer class="bg-[#1B2D62] text-white">
        <div class="container mx-auto max-w-7xl px-4 py-16 sm:py-24">
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-12 gap-12">
                
                <div class="sm:col-span-2 md:col-span-5 ">
                    <a href="#" class="inline-block bg-white p-2 rounded-lg mb-4">
                        <img src="../assets/img/jti.webp">
                    </a>
                    <p class="text-indigo-200 leading-relaxed">
                        Lorem ipsum dolor sit amet, consectetur adipisicing elit. Ipsam minima voluptatem aperiam commodi accusantium nobis in, illum ipsa sint officia.
                    </p>
                </div>

                <div class="md:col-span-2 md:ml-auto ">
                    <h4 class="text-lg font-semibold text-white mb-5">Menu</h4>
                    <ul class="space-y-3">
                        <li><a href="#" class="text-indigo-200 hover:text-white transition-colors">Profil</a></li>
                        <li><a href="#" class="text-indigo-200 hover:text-white transition-colors">Arsip</a></li>
                        <li><a href="#" class="text-indigo-200 hover:text-white transition-colors">Galeri</a></li>
                        <li><a href="#" class="text-indigo-200 hover:text-white transition-colors">Layanan</a></li>
                    </ul>
                </div>

                <div class="md:col-span-3">
                    <h4 class="text-lg font-semibold text-white mb-5">Kontak Kami</h4>
                    <ul class="space-y-3 text-indigo-200">
                        <li>+628123456789</li>
                        <li>labncs@mail.com</li>
                    </ul>
                </div>

                <div class="md:col-span-2">
                    <h4 class="text-lg font-semibold text-white mb-5">Social</h4>
                    <div class="flex space-x-4">
                        <a href="#" class="text-indigo-200 hover:text-white transition-colors">
                            <span class="sr-only">Facebook</span>
                            <img src="../assets/icons/facebook.svg">
                        </a>
                        <a href="#" class="text-indigo-200 hover:text-white transition-colors">
                            <span class="sr-only">Instagram</span>
                            <img src="../assets/icons/instagram.svg">
                        </a>
                        <a href="#" class="text-indigo-200 hover:text-white transition-colors">
                            <span class="sr-only">Twitter</span>
                            <img src="../assets/icons/twitter.svg">
                        </a>
                        <a href="#" class="text-indigo-200 hover:text-white transition-colors">
                            <span class="sr-only">YouTube</span>
                            <img src="../assets/icons/youtube.svg">
                        </a>
                    </div>
                </div>

            </div>
        </div>

        <div class="container mx-auto max-w-7xl px-4">
            <div class="border-t border-indigo-800"></div>
        </div>

        <div>
            <div class="container mx-auto max-w-7xl px-4 py-8">
                <p class="text-center text-indigo-300 text-sm">
                    © 2025 Laboratorium Network & Security | All Rights Reserved
                </p>
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
