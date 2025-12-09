<?php
// Fetch profil_lab data for footer
if (!isset($profil)) {
    $profil = executeQuerySingle("SELECT * FROM profil_lab LIMIT 1");
}
$footer_email = $profil['email'] ?? 'labncs@mail.com';
$footer_telepon = $profil['no_telepon'] ?? '+628123456789';
$footer_alamat = $profil['alamat'] ?? 'Jl. Soekarno Hatta No.9, Jatimulyo, Kec. Lowokwaru, Kota Malang, Jawa Timur 65141';
$footer_youtube = $profil['youtube'] ?? '#';
$footer_instagram = $profil['instagram'] ?? '#';
$footer_github = $profil['github'] ?? '#';
?>
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
                        <?php echo htmlspecialchars($footer_alamat); ?>
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
                        <li><a href="./kontak.php" class="<?php echo ($current_page == 'kontak.php') ? 'text-white font-semibold' : 'text-[#EDEDED]'; ?> hover:text-white transition-colors">Kontak</a></li>
                    </ul>
                </div>
    
                <div class="md:col-span-3">
                    <h4 class="text-lg font-semibold text-white mb-5">Kontak Kami</h4>
                    <ul class="space-y-3 text-[#EDEDED]">
                        <li><?php echo htmlspecialchars($footer_telepon); ?></li>
                        <li><?php echo htmlspecialchars($footer_email); ?></li>
                    </ul>
                </div>
    
                <div class="md:col-span-2">
                    <h4 class="text-lg font-semibold text-white mb-5">Social</h4>
                    <div class="flex space-x-4 items-center">
                        <a href="<?php echo htmlspecialchars($footer_instagram); ?>" target="_blank" class="text-[#EDEDED] hover:text-white transition-colors">
                            <span class="sr-only">Instagram</span>
                            <img src="../assets/icons/instagram.svg">
                        </a>
                        <a href="<?php echo htmlspecialchars($footer_youtube); ?>" target="_blank" class="text-[#EDEDED] hover:text-white transition-colors">
                            <span class="sr-only">YouTube</span>
                            <img src="../assets/icons/youtube.svg">
                        </a>
                        <a href="<?php echo htmlspecialchars($footer_github); ?>" target="_blank" class="text-[#EDEDED] hover:text-white transition-colors">
                            <span class="sr-only">GitHub</span>
                            <i class="fab fa-github text-xl"></i>
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