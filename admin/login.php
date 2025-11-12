<?php
/**
 * Admin Login Page
 * File: admin/login.php
 */

// Load configuration
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/session.php';

// Redirect jika sudah login
redirectIfLoggedIn();

// Handle form submission
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = sanitize($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    
    // Validasi input
    if (empty($username) || empty($password)) {
        $error = 'Username dan password harus diisi';
    } else {
        // Query user dari database
        $query = "SELECT * FROM users WHERE username = ? AND is_active = true";
        $user = executeQuerySingle($query, [$username]);
        
        if ($user) {
            // Verifikasi password
            // Menggunakan MD5 untuk development (sesuai schema.sql)
            // Untuk production, gunakan password_hash & password_verify
            $password_hash = md5($password);
            
            if ($password_hash === $user['password']) {
                // Login berhasil
                setLoginSession($user);
                setFlashMessage('success', 'Login berhasil! Selamat datang, ' . $user['nama_lengkap']);
                redirect(ADMIN_URL . '/index.php');
            } else {
                $error = 'Password salah';
            }
        } else {
            $error = 'Username tidak ditemukan atau akun tidak aktif';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Admin - <?php echo SITE_NAME; ?></title>
    
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center p-4">
    
    <div class="max-w-md w-full">
        <!-- Login Card -->
        <div class="bg-white rounded-2xl shadow-2xl overflow-hidden">
            
            <!-- Header -->
            <div class="bg-gradient-to-r from-blue-600 to-blue-800 p-8 text-center">
                <div class="w-20 h-20 bg-white rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-user-shield text-4xl text-blue-600"></i>
                </div>
                <h1 class="text-2xl font-bold text-white mb-2">Admin Panel</h1>
                <p class="text-blue-100"><?php echo SITE_NAME; ?></p>
            </div>
            
            <!-- Form -->
            <div class="p-8">
                
                <?php if ($error): ?>
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6 flex items-center">
                    <i class="fas fa-exclamation-circle mr-3"></i>
                    <span><?php echo htmlspecialchars($error); ?></span>
                </div>
                <?php endif; ?>
                
                <form method="POST" action="" id="loginForm">
                    
                    <!-- Username -->
                    <div class="mb-6">
                        <label for="username" class="block text-gray-700 font-semibold mb-2">
                            <i class="fas fa-user mr-2 text-gray-400"></i>Username
                        </label>
                        <input 
                            type="text" 
                            id="username" 
                            name="username" 
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                            placeholder="Masukkan username"
                            value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>"
                            required
                            autofocus
                        >
                    </div>
                    
                    <!-- Password -->
                    <div class="mb-6">
                        <label for="password" class="block text-gray-700 font-semibold mb-2">
                            <i class="fas fa-lock mr-2 text-gray-400"></i>Password
                        </label>
                        <div class="relative">
                            <input 
                                type="password" 
                                id="password" 
                                name="password" 
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition pr-12"
                                placeholder="Masukkan password"
                                required
                            >
                            <button 
                                type="button" 
                                id="togglePassword" 
                                class="absolute right-4 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gray-600"
                            >
                                <i class="fas fa-eye" id="eyeIcon"></i>
                            </button>
                        </div>
                    </div>
                    
                    <!-- Remember Me (Optional) -->
                    <div class="mb-6 flex items-center justify-between">
                        <label class="flex items-center">
                            <input type="checkbox" name="remember" class="mr-2 w-4 h-4 text-blue-600">
                            <span class="text-sm text-gray-600">Ingat saya</span>
                        </label>
                        <a href="#" class="text-sm text-blue-600 hover:text-blue-800 transition">Lupa password?</a>
                    </div>
                    
                    <!-- Submit Button -->
                    <button 
                        type="submit" 
                        class="w-full bg-gradient-to-r from-blue-600 to-blue-800 text-white font-semibold py-3 rounded-lg hover:from-blue-700 hover:to-blue-900 transition duration-200 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5"
                    >
                        <i class="fas fa-sign-in-alt mr-2"></i>Login
                    </button>
                    
                </form>
                
                <!-- Info -->
                <div class="mt-6 text-center">
                    <p class="text-sm text-gray-500">
                        <i class="fas fa-info-circle mr-1"></i>
                        Default: admin / admin123
                    </p>
                </div>
                
            </div>
            
            <!-- Footer -->
            <div class="bg-gray-50 px-8 py-4 border-t">
                <div class="flex items-center justify-between text-sm text-gray-600">
                    <a href="<?php echo SITE_URL; ?>/index.php" class="hover:text-blue-600 transition">
                        <i class="fas fa-home mr-1"></i>Kembali ke Website
                    </a>
                    <span>&copy; <?php echo date('Y'); ?> <?php echo SITE_NAME; ?></span>
                </div>
            </div>
            
        </div>
        
        <!-- Security Info -->
        <div class="mt-6 text-center text-white text-sm">
            <p><i class="fas fa-shield-alt mr-2"></i>Koneksi aman dengan enkripsi</p>
        </div>
        
    </div>
    
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    
    <script>
        $(document).ready(function() {
            // Toggle password visibility
            $('#togglePassword').click(function() {
                const passwordField = $('#password');
                const eyeIcon = $('#eyeIcon');
                
                if (passwordField.attr('type') === 'password') {
                    passwordField.attr('type', 'text');
                    eyeIcon.removeClass('fa-eye').addClass('fa-eye-slash');
                } else {
                    passwordField.attr('type', 'password');
                    eyeIcon.removeClass('fa-eye-slash').addClass('fa-eye');
                }
            });
            
            // Form validation
            $('#loginForm').submit(function(e) {
                const username = $('#username').val().trim();
                const password = $('#password').val();
                
                if (username === '' || password === '') {
                    e.preventDefault();
                    alert('Username dan password harus diisi!');
                    return false;
                }
            });
            
            // Auto focus on username
            $('#username').focus();
        });
    </script>
    
</body>
</html>
