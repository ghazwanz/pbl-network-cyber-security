</div>
<!-- End Main Content Wrapper -->

<!-- Scroll to Top Button -->
<button id="scrollToTop" class="fixed bottom-8 right-8 w-14 h-14 bg-gradient-to-r from-orange-500 to-orange-600 text-white rounded-full shadow-2xl hover:scale-110 transition-all duration-300 opacity-0 pointer-events-none z-50">
    <i class="fas fa-arrow-up text-xl"></i>
</button>

<!-- Footer -->
<footer class="bg-[#1B2D62] text-white">
    <div class="px-4">
        <div class="container mx-auto max-w-7xl py-16 sm:py-24">
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-12 gap-12">
    
                <div class="sm:col-span-2 md:col-span-5 ">
                    <a href="#" class="inline-block bg-white p-2 rounded-lg mb-4">
                        <img src="../assets/img/jti.webp">
                    </a>
                    <p class="text-white leading-relaxed">
                        Jl. Soekarno Hatta No.9, Jatimulyo, Kec. Lowokwaru, Kota Malang, Jawa Timur 65141
                    </p>
                </div>
    
                <div class="md:col-span-2 md:ml-auto ">
                    <?php
                    // Get current page name for footer
                    $current_page = basename($_SERVER['PHP_SELF']);
                    ?>
                    <h4 class="text-lg font-semibold text-white mb-5">Menu</h4>
                    <ul class="space-y-3">
                        <li><a href="./profil.php" class="<?php echo ($current_page == 'profil.php') ? 'text-white font-semibold' : 'text-[#EDEDED]'; ?> hover:text-white transition-colors">Profil</a></li>
                        <li><a href="./arsip.php" class="<?php echo ($current_page == 'arsip.php') ? 'text-white font-semibold' : 'text-[#EDEDED]'; ?> hover:text-white transition-colors">Arsip</a></li>
                        <li><a href="./galeri.php" class="<?php echo ($current_page == 'galeri.php') ? 'text-white font-semibold' : 'text-[#EDEDED]'; ?> hover:text-white transition-colors">Galeri</a></li>
                        <li><a href="./layanan.php" class="<?php echo ($current_page == 'layanan.php') ? 'text-white font-semibold' : 'text-[#EDEDED]'; ?> hover:text-white transition-colors">Layanan</a></li>
                    </ul>
                </div>
    
                <div class="md:col-span-3">
                    <h4 class="text-lg font-semibold text-white mb-5">Kontak Kami</h4>
                    <ul class="space-y-3 text-[#EDEDED]">
                        <li>+628123456789</li>
                        <li>labncs@mail.com</li>
                    </ul>
                </div>
    
                <div class="md:col-span-2">
                    <h4 class="text-lg font-semibold text-white mb-5">Social</h4>
                    <div class="flex space-x-4">
                        <a href="#" class="text-[#EDEDED] hover:text-white transition-colors">
                            <span class="sr-only">Facebook</span>
                            <img src="../assets/icons/facebook.svg">
                        </a>
                        <a href="#" class="text-[#EDEDED] hover:text-white transition-colors">
                            <span class="sr-only">Instagram</span>
                            <img src="../assets/icons/instagram.svg">
                        </a>
                        <a href="#" class="text-[#EDEDED] hover:text-white transition-colors">
                            <span class="sr-only">Twitter</span>
                            <img src="../assets/icons/twitter.svg">
                        </a>
                        <a href="#" class="text-[#EDEDED] hover:text-white transition-colors">
                            <span class="sr-only">YouTube</span>
                            <img src="../assets/icons/youtube.svg">
                        </a>
                    </div>
                </div>
    
            </div>
        </div>
    </div>

    <div class="px-4">
        <div class="container mx-auto max-w-7xl">
            <div class="border-t border-[#EDEDED]"></div>
        </div>
    </div>

    <div class="px-4">
        <div class="container mx-auto max-w-7xl py-8">
            <p class="text-center text-[#EDEDED] text-[18px]">
                &copy; 2025 Laboratorium Network & Security | All Rights Reserved
            </p>
        </div>
    </div>
    </div>
</footer>
<script src="https://unpkg.com/@material-tailwind/html@3.0.0-beta.7/dist/material-tailwind.umd.min.js" defer></script>
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script>
    AOS.init({
        duration: 800,
        easing: 'ease-in-out',
        once: true,
        offset: 100
    });
</script>
<script src="../assets/js/script.js"></script>
</body>

</html>