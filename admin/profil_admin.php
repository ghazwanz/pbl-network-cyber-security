<?php
/**
 * Admin Profile Page
 * File: admin/profil_admin.php
 */

// Set page title
$page_title = "Profil Akun";

// Include admin header
require_once __DIR__ . '/../includes/admin_header.php';

// Get current user data
$current_user = getCurrentUser();

if (!$current_user) {
    setFlashMessage('error', 'Session expired. Please login again.');
    redirect(SITE_URL . '/login.php');
    exit;
}

// Handle Form Submission (Update Profile)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $action = $_POST['action'];
    $errors = [];
    
    if ($action === 'update_profile') {
        $nama_lengkap = sanitize($_POST['nama_lengkap'] ?? '');
        $email = sanitize($_POST['email'] ?? '');
        $username = sanitize($_POST['username'] ?? '');
        
        // Validasi
        if (empty($nama_lengkap)) $errors[] = "Nama lengkap harus diisi";
        if (empty($email)) $errors[] = "Email harus diisi";
        if (empty($username)) $errors[] = "Username harus diisi";
        if ($email && !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Format email tidak valid";
        
        // Check duplicate username
        $existing_username = executeQuerySingle(
            "SELECT id FROM users WHERE username = ? AND id != ?",
            [$username, $current_user['id']]
        );
        if ($existing_username) $errors[] = "Username sudah digunakan";
        
        // Check duplicate email
        $existing_email = executeQuerySingle(
            "SELECT id FROM users WHERE email = ? AND id != ?",
            [$email, $current_user['id']]
        );
        if ($existing_email) $errors[] = "Email sudah digunakan";
        
        // Update database
        if (empty($errors)) {
            $query = "UPDATE users SET nama_lengkap = ?, email = ?, username = ? WHERE id = ?";
            $params = [$nama_lengkap, $email, $username, $current_user['id']];
            
            $result = executeNonQuery($query, $params);
            
            if ($result !== false) {
                setFlashMessage('success', 'Profil berhasil diperbarui');
                $_SESSION["admin_data"] = [
                    'id' => $current_user['id'],
                    'username' => $username,
                    'nama_lengkap' => $nama_lengkap,
                    'email' => $email,
                    'role' => $current_user['role']
                ];
                redirect(ADMIN_URL . '/profil_admin.php');
                exit;
            } else {
                $errors[] = "Gagal memperbarui profil";
            }
        }
    } elseif ($action === 'change_password') {
        $current_password = $_POST['current_password'] ?? '';
        $new_password = $_POST['new_password'] ?? '';
        $confirm_password = $_POST['confirm_password'] ?? '';
        
        // Validasi
        if (empty($current_password)) $errors[] = "Password lama harus diisi";
        if (empty($new_password)) $errors[] = "Password baru harus diisi";
        if (empty($confirm_password)) $errors[] = "Konfirmasi password harus diisi";
        if ($new_password !== $confirm_password) $errors[] = "Konfirmasi password tidak cocok";
        if (strlen($new_password) < 6) $errors[] = "Password baru minimal 6 karakter";
        
        // Check current password
        if (empty($errors)) {
            $hashed_current = md5($current_password);
            if ($hashed_current !== $current_user['password']) {
                $errors[] = "Password lama tidak sesuai";
            }
        }
        
        // Update password
        if (empty($errors)) {
            $hashed_new = md5($new_password);
            $query = "UPDATE users SET password = ? WHERE id = ?";
            $params = [$hashed_new, $current_user['id']];
            
            $result = executeNonQuery($query, $params);
            
            if ($result !== false) {
                setFlashMessage('success', 'Password berhasil diubah');
                redirect(ADMIN_URL . '/profil_admin.php');
                exit;
            } else {
                $errors[] = "Gagal mengubah password";
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

// Refresh user data
$user_data = executeQuerySingle("SELECT * FROM users WHERE id = ?", [$current_user['id']]);
?>

<div class="space-y-6">
    
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h2 class="text-lg font-semibold text-blue-900"><i class="fa-solid fa-user mr-2"></i>Profil Akun</h2>
            <p class="text-gray-600 mt-1">Kelola informasi akun administrator</p>
        </div>
    </div>
    
    <!-- Profile Card -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- Sidebar Profile -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <div class="bg-gradient-to-r from-blue-600 to-blue-800 p-6 text-center">
                    <div class="w-24 h-24 mx-auto bg-white rounded-full flex items-center justify-center text-blue-600 text-3xl font-bold shadow-lg">
                        <?php
                        $initials = '';
                        $words = explode(' ', $user_data['nama_lengkap']);
                        foreach ($words as $word) {
                            $initials .= strtoupper(substr($word, 0, 1));
                            if (strlen($initials) >= 2) break;
                        }
                        echo htmlspecialchars($initials);
                        ?>
                    </div>
                    <h3 class="text-xl font-bold text-white mt-4"><?php echo htmlspecialchars($user_data['nama_lengkap']); ?></h3>
                    <p class="text-blue-100 text-sm">@<?php echo htmlspecialchars($user_data['username']); ?></p>
                </div>
                
                <div class="p-6 space-y-4">
                    <div>
                        <p class="text-xs text-gray-500 uppercase tracking-wide font-bold">Role</p>
                        <p class="text-sm font-medium text-gray-800 mt-1">
                            <span class="inline-block px-3 py-1 rounded-full bg-purple-100 text-purple-800 text-xs font-semibold">
                                <?php echo strtoupper(htmlspecialchars($user_data['role'])); ?>
                            </span>
                        </p>
                    </div>
                    
                    <div>
                        <p class="text-xs text-gray-500 uppercase tracking-wide font-bold">Status</p>
                        <p class="text-sm font-medium text-gray-800 mt-1">
                            <span class="inline-block px-3 py-1 rounded-full <?php echo $user_data['is_active'] ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'; ?> text-xs font-semibold">
                                <?php echo $user_data['is_active'] ? 'Aktif' : 'Nonaktif'; ?>
                            </span>
                        </p>
                    </div>
                    
                    <div>
                        <p class="text-xs text-gray-500 uppercase tracking-wide font-bold">Bergabung Sejak</p>
                        <p class="text-sm font-medium text-gray-800 mt-1">
                            <?php
                            $date = new DateTime($user_data['created_at']);
                            echo $date->format('d F Y');
                            ?>
                        </p>
                    </div>
                    
                    <div>
                        <p class="text-xs text-gray-500 uppercase tracking-wide font-bold">Terakhir Diperbarui</p>
                        <p class="text-sm font-medium text-gray-800 mt-1">
                            <?php
                            $date = new DateTime($user_data['updated_at']);
                            echo $date->format('d F Y H:i');
                            ?>
                        </p>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-6">
            
            <!-- Edit Profile Form -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="text-lg font-bold text-gray-800 mb-4">
                    <i class="fas fa-user-edit mr-2 text-blue-600"></i>Informasi Profil
                </h3>
                
                <form method="POST" class="space-y-4">
                    <input type="hidden" name="action" value="update_profile">
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Nama Lengkap <span class="text-red-500">*</span></label>
                        <input type="text" name="nama_lengkap" 
                               value="<?php echo htmlspecialchars($user_data['nama_lengkap']); ?>"
                               class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-blue-500 focus:outline-none"
                               required maxlength="100"
                               placeholder="Nama Lengkap">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Username <span class="text-red-500">*</span></label>
                        <input type="text" name="username" 
                               value="<?php echo htmlspecialchars($user_data['username']); ?>"
                               class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-blue-500 focus:outline-none"
                               required maxlength="50"
                               placeholder="Username">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Email <span class="text-red-500">*</span></label>
                        <input type="email" name="email" 
                               value="<?php echo htmlspecialchars($user_data['email']); ?>"
                               class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-blue-500 focus:outline-none"
                               required maxlength="100"
                               placeholder="email@example.com">
                    </div>
                    
                    <div class="flex justify-end pt-4">
                        <button type="submit" class="inline-flex items-center justify-center px-5 py-2.5 rounded-lg text-sm font-medium shadow-sm bg-blue-600 text-white hover:bg-blue-700 transition-all">
                            <i class="fas fa-save mr-2"></i>Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
            
            <!-- Change Password Form -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="text-lg font-bold text-gray-800 mb-4">
                    <i class="fas fa-key mr-2 text-orange-600"></i>Ubah Password
                </h3>
                
                <form method="POST" class="space-y-4">
                    <input type="hidden" name="action" value="change_password">
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Password Lama <span class="text-red-500">*</span></label>
                        <input type="password" name="current_password" 
                               class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-blue-500 focus:outline-none"
                               required
                               placeholder="Masukkan password lama">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Password Baru <span class="text-red-500">*</span></label>
                        <input type="password" name="new_password" 
                               class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-blue-500 focus:outline-none"
                               required minlength="6"
                               placeholder="Minimal 6 karakter">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Konfirmasi Password Baru <span class="text-red-500">*</span></label>
                        <input type="password" name="confirm_password" 
                               class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-blue-500 focus:outline-none"
                               required minlength="6"
                               placeholder="Ketik ulang password baru">
                    </div>
                    
                    <div class="flex justify-end pt-4">
                        <button type="submit" class="inline-flex items-center justify-center px-5 py-2.5 rounded-lg text-sm font-medium shadow-sm bg-orange-600 text-white hover:bg-orange-700 transition-all">
                            <i class="fas fa-key mr-2"></i>Ubah Password
                        </button>
                    </div>
                </form>
            </div>
            
        </div>
    </div>
    
</div>

<?php

// Include admin footer
require_once __DIR__ . '/../includes/admin_footer.php';
?>