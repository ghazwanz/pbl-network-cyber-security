            </main>
            
        </div>
    </div>
    
    <!-- Mobile Sidebar Overlay -->
    <div id="mobile-sidebar-overlay" class="fixed inset-0 bg-black bg-opacity-50 z-40 hidden md:hidden"></div>
    
    <!-- Mobile Sidebar -->
    <aside id="mobile-sidebar" class="fixed inset-y-0 left-0 bg-gray-800 text-white w-64 transform -translate-x-full transition-transform duration-200 ease-in-out z-50 md:hidden">
        <!-- Logo -->
        <div class="flex items-center justify-between h-16 bg-gray-900 px-4">
            <span class="text-xl font-bold">Admin Panel</span>
            <button id="close-mobile-sidebar" class="text-white">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        
        <!-- Navigation -->
        <nav class="overflow-y-auto py-4" style="height: calc(100% - 8rem);">
            <ul class="space-y-1 px-3">
                <li>
                    <a href="<?php echo ADMIN_URL; ?>/index.php" class="flex items-center px-4 py-3 rounded hover:bg-gray-700 transition">
                        <i class="fas fa-home mr-3"></i>
                        <span>Dashboard</span>
                    </a>
                </li>
                
                <li class="pt-4 pb-2 px-4">
                    <span class="text-xs font-semibold text-gray-400 uppercase">Content Management</span>
                </li>
                
                <li>
                    <a href="<?php echo ADMIN_URL; ?>/galeri.php" class="flex items-center px-4 py-3 rounded hover:bg-gray-700 transition">
                        <i class="fas fa-images mr-3"></i>
                        <span>Galeri & Agenda</span>
                    </a>
                </li>
                
                <li>
                    <a href="<?php echo ADMIN_URL; ?>/pengelola.php" class="flex items-center px-4 py-3 rounded hover:bg-gray-700 transition">
                        <i class="fas fa-users mr-3"></i>
                        <span>Pengelola</span>
                    </a>
                </li>
                
                <li>
                    <a href="<?php echo ADMIN_URL; ?>/arsip.php" class="flex items-center px-4 py-3 rounded hover:bg-gray-700 transition">
                        <i class="fas fa-file-pdf mr-3"></i>
                        <span>Arsip</span>
                    </a>
                </li>
                
                <li>
                    <a href="<?php echo ADMIN_URL; ?>/sarana.php" class="flex items-center px-4 py-3 rounded hover:bg-gray-700 transition">
                        <i class="fas fa-laptop mr-3"></i>
                        <span>Sarana Prasarana</span>
                    </a>
                </li>
                
                <li>
                    <a href="<?php echo ADMIN_URL; ?>/konsultatif.php" class="flex items-center px-4 py-3 rounded hover:bg-gray-700 transition">
                        <i class="fas fa-comments mr-3"></i>
                        <span>Konsultatif</span>
                    </a>
                </li>
                
                <li class="pt-4 pb-2 px-4">
                    <span class="text-xs font-semibold text-gray-400 uppercase">Settings</span>
                </li>
                
                <li>
                    <a href="<?php echo ADMIN_URL; ?>/profil_lab.php" class="flex items-center px-4 py-3 rounded hover:bg-gray-700 transition">
                        <i class="fas fa-building mr-3"></i>
                        <span>Profil Lab</span>
                    </a>
                </li>
            </ul>
        </nav>
        
        <!-- User Info -->
        <div class="absolute bottom-0 left-0 right-0 p-4 bg-gray-900">
            <div class="flex items-center">
                <div class="w-10 h-10 rounded-full bg-blue-500 flex items-center justify-center">
                    <i class="fas fa-user"></i>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-semibold"><?php echo htmlspecialchars($current_user['nama_lengkap']); ?></p>
                    <p class="text-xs text-gray-400"><?php echo htmlspecialchars($current_user['role']); ?></p>
                </div>
            </div>
        </div>
    </aside>
    
    <!-- jQuery CDN -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    
    <!-- Custom Admin JS -->
    <script src="<?php echo ASSETS_URL; ?>/js/admin.js"></script>
    
    <!-- Mobile Sidebar Toggle Script -->
    <script>
        $(document).ready(function() {
            // Open mobile sidebar
            $('#mobile-sidebar-toggle').click(function() {
                $('#mobile-sidebar').removeClass('-translate-x-full');
                $('#mobile-sidebar-overlay').removeClass('hidden');
            });
            
            // Close mobile sidebar
            $('#close-mobile-sidebar, #mobile-sidebar-overlay').click(function() {
                $('#mobile-sidebar').addClass('-translate-x-full');
                $('#mobile-sidebar-overlay').addClass('hidden');
            });
        });
    </script>
</body>
</html>

<?php
// Flush output buffer if it was started
if (ob_get_level()) {
    ob_end_flush();
}
?>
