<?php

/**
 * Admin CRUD Profil Laboratorium
 * File: admin/profil.php
 * Purpose: Manage single lab profile record (Read & Update only)
 */

// Set page title
$page_title = "Profil Laboratorium";

// Include admin header
require_once __DIR__ . '/../includes/admin_header.php';

// Get current profile
$profil = executeQuerySingle("SELECT p.*, u.nama_lengkap as updated_by_name FROM profil_lab p LEFT JOIN users u ON p.id_admin = u.id LIMIT 1");
$id_admin = getCurrentUser()['id'] ?? null;

// Handle form submissions berdasarkan konteks
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $profil) {
    $action = $_POST['action'];
    $errors = [];

    // === UPDATE INFORMASI DASAR ===
    if ($action === 'update_basic') {
        $nama_lab = sanitize($_POST['nama_lab'] ?? '');
        $alamat = sanitize($_POST['alamat'] ?? '');

        // Validasi
        if (empty($nama_lab)) $errors[] = "Nama laboratorium harus diisi";

        // Handle logo upload
        $logo_path = $profil['logo_path'];
        if (isset($_FILES['logo']) && $_FILES['logo']['error'] === UPLOAD_ERR_OK) {
            $upload_result = uploadImage($_FILES['logo'], 'profil', 'logo');

            if ($upload_result['success']) {
                if ($logo_path) {
                    deleteFile($logo_path);
                }
                $logo_path = '/profil/' . $upload_result['filename'];
            } else {
                $errors[] = $upload_result['message'];
            }
        }

        if (empty($errors)) {
            $result = executeNonQuery(
                "UPDATE profil_lab SET nama_lab = ?, alamat = ?, logo_path = ?, id_admin = ?, updated_at = NOW() WHERE id = ?",
                [$nama_lab, $alamat, $logo_path, $id_admin, $profil['id']]
            );
            if ($result !== false) {
                setFlashMessage('success', 'Informasi dasar berhasil diperbarui');
                redirect(ADMIN_URL . '/profil.php');
                exit;
            } else {
                $errors[] = "Gagal memperbarui informasi dasar";
            }
        }
    }

    // === UPDATE VISI & MISI ===
    elseif ($action === 'update_visi_misi') {
        $visi = sanitize($_POST['visi'] ?? '');
        $misi_raw = $_POST['misi'] ?? [];

        // Konversi misi array ke JSON format
        $misi_clean = array_values(array_filter(array_map('trim', $misi_raw)));
        $misi_json = json_encode($misi_clean, JSON_UNESCAPED_UNICODE);

        $result = executeNonQuery(
            "UPDATE profil_lab SET visi = ?, misi = ?, id_admin = ?, updated_at = NOW() WHERE id = ?",
            [$visi, $misi_json, $id_admin, $profil['id']]
        );
        if ($result !== false) {
            setFlashMessage('success', 'Visi & Misi berhasil diperbarui');
            redirect(ADMIN_URL . '/profil.php');
            exit;
        } else {
            $errors[] = "Gagal memperbarui visi & misi";
        }
    }

    // === UPDATE SEJARAH & DOKUMENTASI ===
    elseif ($action === 'update_sejarah') {
        $sejarah = sanitize($_POST['sejarah'] ?? '');

        // Handle multiple gambar lab upload
        $gambar_array = [];

        // Get existing images that should be kept
        $existing_gambar = $_POST['existing_gambar'] ?? [];
        if (!empty($existing_gambar)) {
            $gambar_array = array_filter($existing_gambar);
        }

        // Handle new image uploads
        if (isset($_FILES['gambar_lab']) && is_array($_FILES['gambar_lab']['name'])) {
            $file_count = count($_FILES['gambar_lab']['name']);

            for ($i = 0; $i < $file_count; $i++) {
                if ($_FILES['gambar_lab']['error'][$i] === UPLOAD_ERR_OK) {
                    $file = [
                        'name' => $_FILES['gambar_lab']['name'][$i],
                        'type' => $_FILES['gambar_lab']['type'][$i],
                        'tmp_name' => $_FILES['gambar_lab']['tmp_name'][$i],
                        'error' => $_FILES['gambar_lab']['error'][$i],
                        'size' => $_FILES['gambar_lab']['size'][$i]
                    ];

                    $upload_result = uploadImage($file, 'profil', 'lab');

                    if ($upload_result['success']) {
                        $gambar_array[] = '/profil/' . $upload_result['filename'];
                    } else {
                        $errors[] = "Gagal upload gambar: " . $upload_result['message'];
                    }
                }
            }
        }

        // Konversi ke JSON format
        $gambar_json = json_encode(array_values($gambar_array), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

        if (empty($errors)) {
            $result = executeNonQuery(
                "UPDATE profil_lab SET sejarah = ?, gambar_lab_path = ?, id_admin = ?, updated_at = NOW() WHERE id = ?",
                [$sejarah, $gambar_json, $id_admin, $profil['id']]
            );
            if ($result !== false) {
                setFlashMessage('success', 'Sejarah & Dokumentasi berhasil diperbarui');
                redirect(ADMIN_URL . '/profil.php');
                exit;
            } else {
                $errors[] = "Gagal memperbarui sejarah & dokumentasi";
            }
        }
    }

    // === UPDATE KONTAK ===
    elseif ($action === 'update_kontak') {
        $email = sanitize($_POST['email'] ?? '');
        $no_telepon = sanitize($_POST['no_telepon'] ?? '');

        if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = "Format email tidak valid";
        }

        if (empty($errors)) {
            $result = executeNonQuery(
                "UPDATE profil_lab SET email = ?, no_telepon = ?, id_admin = ?, updated_at = NOW() WHERE id = ?",
                [$email, $no_telepon, $id_admin, $profil['id']]
            );
            if ($result !== false) {
                setFlashMessage('success', 'Informasi kontak berhasil diperbarui');
                redirect(ADMIN_URL . '/profil.php');
                exit;
            } else {
                $errors[] = "Gagal memperbarui informasi kontak";
            }
        }
    }

    // === UPDATE SOCIAL MEDIA ===
    elseif ($action === 'update_social') {
        $youtube = sanitize($_POST['youtube'] ?? '');
        $instagram = sanitize($_POST['instagram'] ?? '');
        $github = sanitize($_POST['github'] ?? '');

        $result = executeNonQuery(
            "UPDATE profil_lab SET youtube = ?, instagram = ?, github = ?, id_admin = ?, updated_at = NOW() WHERE id = ?",
            [$youtube, $instagram, $github, $id_admin, $profil['id']]
        );
        if ($result !== false) {
            setFlashMessage('success', 'Media sosial berhasil diperbarui');
            redirect(ADMIN_URL . '/profil.php');
            exit;
        } else {
            $errors[] = "Gagal memperbarui media sosial";
        }
    }

    // === DELETE GAMBAR ===
    elseif ($action === 'delete_gambar') {
        $gambar_to_delete = $_POST['gambar_path'] ?? '';

        if (!empty($gambar_to_delete)) {
            // Parse existing gambar array dari JSON
            $existing_gambar = [];
            if (!empty($profil['gambar_lab_path'])) {
                $existing_gambar = json_decode($profil['gambar_lab_path'], true) ?? [];
            }

            // Remove the deleted image
            $new_gambar = array_values(array_filter($existing_gambar, function ($g) use ($gambar_to_delete) {
                return $g !== $gambar_to_delete;
            }));

            // Delete file
            deleteFile($gambar_to_delete);

            // Update database dengan JSON format
            $gambar_json = json_encode($new_gambar, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

            $result = executeNonQuery(
                "UPDATE profil_lab SET gambar_lab_path = ?, id_admin = ?, updated_at = NOW() WHERE id = ?",
                [$gambar_json, $id_admin, $profil['id']]
            );

            if ($result !== false) {
                setFlashMessage('success', 'Gambar berhasil dihapus');
            } else {
                setFlashMessage('error', 'Gagal menghapus gambar');
            }
            redirect(ADMIN_URL . '/profil.php');
            exit;
        }
    }

    if (!empty($errors)) {
        foreach ($errors as $error) {
            setFlashMessage('error', $error);
        }
    }

    // Refresh data
    $profil = executeQuerySingle("SELECT p.*, u.nama_lengkap as updated_by_name FROM profil_lab p LEFT JOIN users u ON p.id_admin = u.id LIMIT 1");
}

// Konversi misi dari JSON ke array PHP
$misi_array = [];
if ($profil && !empty($profil['misi'])) {
    $decoded = json_decode($profil['misi'], true);
    if (is_array($decoded)) {
        $misi_array = $decoded;
    }
}

// Konversi gambar_lab_path dari JSON ke array PHP
$gambar_array = [];
if ($profil && !empty($profil['gambar_lab_path'])) {
    $decoded = json_decode($profil['gambar_lab_path'], true);
    if (is_array($decoded)) {
        $gambar_array = $decoded;
    }
}
?>

<div class="space-y-6">

    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h2 class="text-lg font-semibold text-blue-900">
                <i class="fas fa-building mr-2"></i>Profil Laboratorium
            </h2>
            <p class="text-gray-600 mt-1">Kelola informasi profil laboratorium</p>
        </div>
        <?php if ($profil && !empty($profil['updated_at'])): ?>
            <div class="text-sm text-gray-500">
                <i class="fas fa-clock mr-1"></i>Terakhir diperbarui:
                <?php
                $date = new DateTime($profil['updated_at']);
                echo $date->format('d M Y, H:i');
                ?>
                <?php if (!empty($profil['updated_by_name'])): ?>
                    oleh <span class="font-medium"><?php echo $profil['updated_by_name']; ?></span>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="bg-white rounded-lg shadow p-4 border-l-4 border-blue-500">
            <p class="text-gray-500 text-sm mb-1 uppercase font-bold tracking-wider">Nama Lab</p>
            <p class="text-lg font-bold text-gray-800 truncate">
                <?php echo $profil ? $profil['nama_lab'] : '-'; ?>
            </p>
        </div>
        <div class="bg-white rounded-lg shadow p-4 border-l-4 border-green-500">
            <p class="text-gray-500 text-sm mb-1 uppercase font-bold tracking-wider">Jumlah Misi</p>
            <p class="text-lg font-bold text-green-600">
                <?php echo count($misi_array); ?> Misi
            </p>
        </div>
        <div class="bg-white rounded-lg shadow p-4 border-l-4 border-purple-500">
            <p class="text-gray-500 text-sm mb-1 uppercase font-bold tracking-wider">Dokumentasi</p>
            <p class="text-lg font-bold text-purple-600">
                <?php echo count($gambar_array); ?> Gambar
            </p>
        </div>
        <div class="bg-white rounded-lg shadow p-4 border-l-4 border-orange-500">
            <p class="text-gray-500 text-sm mb-1 uppercase font-bold tracking-wider">Social Media</p>
            <p class="text-lg font-bold text-orange-600">
                <?php
                $social_count = 0;
                if ($profil) {
                    if (!empty($profil['youtube'])) $social_count++;
                    if (!empty($profil['instagram'])) $social_count++;
                    if (!empty($profil['github'])) $social_count++;
                }
                echo $social_count . ' Terhubung';
                ?>
            </p>
        </div>
    </div>

    <?php if (!$profil): ?>
        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-6 text-center">
            <i class="fas fa-exclamation-triangle text-4xl text-yellow-500 mb-3"></i>
            <h3 class="text-lg font-bold text-yellow-800 mb-2">Profil Belum Dibuat</h3>
            <p class="text-yellow-700">Silakan buat data profil laboratorium terlebih dahulu melalui database.</p>
        </div>
    <?php else: ?>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            <!-- Sidebar - Logo Preview -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-lg shadow-md overflow-hidden sticky top-4">
                    <div class="bg-gradient-to-r from-blue-600 to-blue-800 p-6 text-center">
                        <?php if ($profil['logo_path']): ?>
                            <img src="<?php echo UPLOAD_URL . $profil['logo_path']; ?>"
                                alt="Logo Lab"
                                class="w-32 h-32 object-contain mx-auto bg-white rounded-lg p-2 shadow-lg"
                                onerror="this.src='<?php echo ASSETS_URL; ?>/img/no-image.png'">
                        <?php else: ?>
                            <div class="w-32 h-32 bg-white/20 rounded-lg flex items-center justify-center mx-auto">
                                <i class="fas fa-building text-4xl text-white/60"></i>
                            </div>
                        <?php endif; ?>
                        <h3 class="text-xl font-bold text-white mt-4"><?php echo $profil['nama_lab'] ?? 'Nama Lab'; ?></h3>
                    </div>

                    <div class="p-6 space-y-4">
                        <div>
                            <p class="text-xs text-gray-500 uppercase tracking-wide font-bold">Email</p>
                            <p class="text-sm font-medium text-gray-800 mt-1">
                                <?php echo $profil['email'] ? $profil['email'] : '-'; ?>
                            </p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 uppercase tracking-wide font-bold">Telepon</p>
                            <p class="text-sm font-medium text-gray-800 mt-1">
                                <?php echo $profil['no_telepon'] ? $profil['no_telepon'] : '-'; ?>
                            </p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 uppercase tracking-wide font-bold">Alamat</p>
                            <p class="text-sm font-medium text-gray-800 mt-1">
                                <?php echo $profil['alamat'] ? $profil['alamat'] : '-'; ?>
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Content - Forms -->
            <div class="lg:col-span-2 space-y-6">

                <!-- Form: Informasi Dasar -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h3 class="text-lg font-bold text-gray-800 mb-4 pb-3 border-b border-gray-200">
                        <i class="fas fa-info-circle text-blue-600 mr-2"></i>Informasi Dasar
                    </h3>

                    <form method="POST" enctype="multipart/form-data" class="space-y-4">
                        <input type="hidden" name="action" value="update_basic">

                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">
                                Nama Laboratorium <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="nama_lab" class="form-input" required
                                value="<?php echo $profil['nama_lab'] ?? ''; ?>"
                                placeholder="Nama Laboratorium">
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">Alamat</label>
                            <textarea name="alamat" class="form-input" rows="3"
                                placeholder="Alamat lengkap laboratorium..."><?php echo $profil['alamat'] ?? ''; ?></textarea>
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">Logo Laboratorium</label>
                            <?php if ($profil['logo_path']): ?>
                                <div class="mb-2">
                                    <img src="<?php echo UPLOAD_URL . $profil['logo_path']; ?>"
                                        alt="Current Logo" class="w-20 h-20 object-contain border rounded p-1"
                                        onerror="this.src='<?php echo ASSETS_URL; ?>/img/no-image.png'">
                                </div>
                            <?php endif; ?>
                            <input type="file" name="logo" id="inputLogo" accept="image/*" class="form-input">
                            <p class="text-xs text-gray-500 mt-1">Format: JPG, PNG, GIF. Max 2MB. Kosongkan jika tidak ingin mengubah.</p>
                        </div>

                        <div class="flex justify-end pt-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save mr-2"></i>Simpan Informasi Dasar
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Form: Visi & Misi -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h3 class="text-lg font-bold text-gray-800 mb-4 pb-3 border-b border-gray-200">
                        <i class="fas fa-bullseye text-green-600 mr-2"></i>Visi & Misi
                    </h3>

                    <form method="POST" class="space-y-4">
                        <input type="hidden" name="action" value="update_visi_misi">

                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">Visi</label>
                            <textarea name="visi" class="form-input" rows="4"
                                placeholder="Tuliskan visi laboratorium..."><?php echo $profil['visi'] ?? ''; ?></textarea>
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">Misi</label>
                            <div id="misiContainer" class="space-y-3">
                                <?php
                                // Pastikan minimal ada 1 input kosong jika data kosong
                                if (empty($misi_array)) {
                                    $misi_array = [''];
                                }
                                ?>

                                <?php foreach ($misi_array as $index => $misi): ?>
                                    <div class="flex gap-2 misi-item items-center">
                                        <span class="text-sm font-bold text-gray-400 w-16 text-right misi-number">
                                            Misi <?php echo $index + 1; ?>
                                        </span>
                                        <input type="text" name="misi[]" class="form-input flex-1"
                                            value="<?php echo htmlspecialchars($misi); ?>"
                                            placeholder="Tuliskan misi...">

                                        <button type="button" onclick="removeMisi(this)" class="p-2 text-red-500 hover:text-red-700 transition-colors" title="Hapus Misi">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </div>
                                <?php endforeach; ?>
                            </div>

                            <div class="ml-16 mt-3">
                                <button type="button" onclick="addMisi()" class="text-sm px-4 py-2 bg-blue-50 text-blue-600 rounded-lg hover:bg-blue-100 font-medium transition-colors">
                                    <i class="fas fa-plus mr-1"></i>Tambah Poin Misi
                                </button>
                            </div>
                        </div>

                        <div class="flex justify-end pt-4 border-t mt-4">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save mr-2"></i>Simpan Visi & Misi
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Form: Sejarah & Dokumentasi -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h3 class="text-lg font-bold text-gray-800 mb-4 pb-3 border-b border-gray-200">
                        <i class="fas fa-history text-orange-600 mr-2"></i>Sejarah & Dokumentasi
                    </h3>

                    <form method="POST" enctype="multipart/form-data" class="space-y-4">
                        <input type="hidden" name="action" value="update_sejarah">

                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">Sejarah Laboratorium</label>
                            <textarea name="sejarah" class="form-input" rows="5"
                                placeholder="Tuliskan sejarah laboratorium..."><?php echo $profil['sejarah'] ?? ''; ?></textarea>
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">Galeri Gambar Laboratorium</label>

                            <?php if (!empty($gambar_array)): ?>
                                <p class="text-xs text-gray-500 mb-2 font-bold uppercase">Gambar Tersimpan:</p>
                                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
                                    <?php foreach ($gambar_array as $gambar): ?>
                                        <div class="relative group border rounded-lg overflow-hidden shadow-sm">
                                            <img src="<?php echo UPLOAD_URL . $gambar; ?>"
                                                alt="Gambar Lab"
                                                class="w-full h-32 object-cover"
                                                onerror="this.src='<?php echo ASSETS_URL; ?>/img/no-image.png'">

                                            <input type="hidden" name="existing_gambar[]" value="<?php echo htmlspecialchars($gambar); ?>">

                                            <div class="absolute inset-0 bg-black/50 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                                                <button type="button" onclick="deleteGambar('<?php echo htmlspecialchars($gambar); ?>')"
                                                    class="bg-red-500 text-white px-3 py-1 rounded-full text-sm hover:bg-red-600 transform hover:scale-105 transition-all">
                                                    <i class="fas fa-trash-alt mr-1"></i> Hapus
                                                </button>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>

                            <div id="uploadArea" class="border-2 border-dashed border-gray-300 rounded-lg p-6 hover:border-blue-400 hover:bg-blue-50/50 transition-all text-center cursor-pointer">
                                <i class="fas fa-cloud-upload-alt text-3xl text-gray-400 mb-2"></i>
                                <p class="text-sm font-bold text-gray-600 mb-1">Upload Gambar Baru</p>
                                <p class="text-xs text-gray-400 mb-4">Format: JPG, PNG. Max 2MB per file.</p>

                                <input type="file" name="gambar_lab[]" id="inputGambarLab" accept="image/*" multiple class="hidden">
                                <label for="inputGambarLab" class="cursor-pointer bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 text-sm font-medium inline-block">
                                    <i class="fas fa-folder-open mr-1"></i> Pilih File
                                </label>
                            </div>

                            <div id="previewContainer" class="mt-4" style="display: none;">
                                <div class="flex items-center justify-between mb-2">
                                    <p class="text-xs text-blue-600 font-bold uppercase">
                                        <i class="fas fa-images mr-1"></i> Akan Diupload: <span id="previewCount">0</span> gambar
                                    </p>
                                    <button type="button" id="clearPreview" class="text-xs text-red-500 hover:text-red-700">
                                        <i class="fas fa-times mr-1"></i> Batal
                                    </button>
                                </div>
                                <div class="grid grid-cols-2 md:grid-cols-4 gap-4" id="newImagePreview"></div>
                            </div>
                        </div>

                        <div class="flex justify-end pt-4 border-t">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save mr-2"></i>Simpan Sejarah & Dokumentasi
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Form: Informasi Kontak -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h3 class="text-lg font-bold text-gray-800 mb-4 pb-3 border-b border-gray-200">
                        <i class="fas fa-address-book text-purple-600 mr-2"></i>Informasi Kontak
                    </h3>

                    <form method="POST" class="space-y-4">
                        <input type="hidden" name="action" value="update_kontak">

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-1">Email</label>
                                <div class="relative">
                                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
                                        <i class="fas fa-envelope"></i>
                                    </span>
                                    <input type="email" name="email" class="form-input pl-10"
                                        value="<?php echo htmlspecialchars($profil['email'] ?? ''); ?>"
                                        placeholder="email@laboratorium.com">
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-1">No. Telepon</label>
                                <div class="relative">
                                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
                                        <i class="fas fa-phone"></i>
                                    </span>
                                    <input type="text" name="no_telepon" class="form-input pl-10"
                                        value="<?php echo htmlspecialchars($profil['no_telepon'] ?? ''); ?>"
                                        placeholder="(021) 1234-5678">
                                </div>
                            </div>
                        </div>

                        <div class="flex justify-end pt-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save mr-2"></i>Simpan Kontak
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Form: Social Media -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h3 class="text-lg font-bold text-gray-800 mb-4 pb-3 border-b border-gray-200">
                        <i class="fas fa-share-alt text-pink-600 mr-2"></i>Media Sosial
                    </h3>

                    <form method="POST" class="space-y-4">
                        <input type="hidden" name="action" value="update_social">

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-1">YouTube</label>
                                <div class="relative">
                                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-red-500">
                                        <i class="fab fa-youtube"></i>
                                    </span>
                                    <input type="url" name="youtube" class="form-input pl-10"
                                        value="<?php echo htmlspecialchars($profil['youtube'] ?? ''); ?>"
                                        placeholder="https://youtube.com/@channel">
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-1">Instagram</label>
                                <div class="relative">
                                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-pink-500">
                                        <i class="fab fa-instagram"></i>
                                    </span>
                                    <input type="url" name="instagram" class="form-input pl-10"
                                        value="<?php echo htmlspecialchars($profil['instagram'] ?? ''); ?>"
                                        placeholder="https://instagram.com/labname">
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-1">GitHub</label>
                                <div class="relative">
                                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-700">
                                        <i class="fab fa-github"></i>
                                    </span>
                                    <input type="url" name="github" class="form-input pl-10"
                                        value="<?php echo htmlspecialchars($profil['github'] ?? ''); ?>"
                                        placeholder="https://github.com/labname">
                                </div>
                            </div>
                        </div>

                        <div class="flex justify-end pt-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save mr-2"></i>Simpan Media Sosial
                            </button>
                        </div>
                    </form>
                </div>

            </div>
        </div>

    <?php endif; ?>

</div>

<!-- Form untuk delete gambar -->
<form id="deleteGambarForm" method="POST" class="hidden">
    <input type="hidden" name="action" value="delete_gambar">
    <input type="hidden" name="gambar_path" id="gambarPathToDelete">
</form>
<?php require_once __DIR__ . '/../includes/admin_footer.php'; ?>

<script>
    $(document).ready(function() {
        // 1. Preview Logo Utama (Basic Info)
        $('#inputLogo').on('change', function() {
            const file = this.files[0];
            if (file) {
                // Logic preview logo (opsional, jika ingin ditambahkan)
            }
        });

        // 2. Preview Multiple Gambar Lab (Sejarah Form)
        $('#inputGambarLab').on('change', function() {
            var $input = $(this);
            var $container = $('#newImagePreview');
            var $wrapper = $('#previewContainer');
            var files = this.files;
            
            // Bersihkan preview sebelumnya
            $container.empty();
            
            if (files.length > 0) {
                // Tampilkan container preview
                $wrapper.show();
                $('#previewCount').text(files.length);
                
                // Loop semua file yang dipilih
                $.each(files, function(index, file) {
                    if (file.type.match('image.*')) {
                        var reader = new FileReader();
                        
                        reader.onload = function(e) {
                            var previewHtml = 
                                '<div class="relative rounded-lg overflow-hidden border-2 border-blue-300 shadow-sm group">' +
                                    '<img src="' + e.target.result + '" class="w-full h-32 object-cover" alt="Preview">' +
                                    '<div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent"></div>' +
                                    '<div class="absolute bottom-0 left-0 right-0 p-2">' +
                                        '<p class="text-white text-xs truncate font-medium">' + file.name + '</p>' +
                                        '<p class="text-white/70 text-xs">' + formatFileSize(file.size) + '</p>' +
                                    '</div>' +
                                    '<div class="absolute top-2 right-2">' +
                                        '<span class="bg-blue-500 text-white text-xs px-2 py-1 rounded-full">Baru</span>' +
                                    '</div>' +
                                '</div>';
                            
                            $container.append(previewHtml);
                        };
                        
                        reader.readAsDataURL(file);
                    }
                });
            } else {
                $wrapper.hide();
            }
        });
        
        // Tombol batal/clear preview
        $('#clearPreview').on('click', function() {
            $('#inputGambarLab').val('');
            $('#newImagePreview').empty();
            $('#previewContainer').hide();
        });
        
        // Drag and drop support
        var $uploadArea = $('#uploadArea');
        
        $uploadArea.on('dragover dragenter', function(e) {
            e.preventDefault();
            e.stopPropagation();
            $(this).addClass('border-blue-500 bg-blue-50');
        });
        
        $uploadArea.on('dragleave drop', function(e) {
            e.preventDefault();
            e.stopPropagation();
            $(this).removeClass('border-blue-500 bg-blue-50');
        });
        
        $uploadArea.on('drop', function(e) {
            var files = e.originalEvent.dataTransfer.files;
            $('#inputGambarLab')[0].files = files;
            $('#inputGambarLab').trigger('change');
        });
    });
    
    // Helper function untuk format ukuran file
    function formatFileSize(bytes) {
        if (bytes === 0) return '0 Bytes';
        var k = 1024;
        var sizes = ['Bytes', 'KB', 'MB', 'GB'];
        var i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    }

    // 3. Manajemen Misi Dinamis
    function updateMisiNumbers() {
        // Update label nomor urut setiap kali ada perubahan
        $('.misi-item').each(function(index) {
            $(this).find('.misi-number').text('Misi ' + (index + 1));
            $(this).find('input').attr('placeholder', 'Misi ' + (index + 1));
        });
    }

    function addMisi() {
        var container = $('#misiContainer');
        var newIndex = container.children().length + 1;

        var newItem = 
            '<div class="flex gap-2 misi-item items-center">' +
                '<span class="text-sm font-bold text-gray-400 w-16 text-right misi-number">Misi ' + newIndex + '</span>' +
                '<input type="text" name="misi[]" class="form-input flex-1" placeholder="Misi ' + newIndex + '">' +
                '<button type="button" onclick="removeMisi(this)" class="p-2 text-red-500 hover:text-red-700 transition-colors">' +
                    '<i class="fas fa-trash-alt"></i>' +
                '</button>' +
            '</div>';

        container.append(newItem);
        // Fokus ke input yang baru dibuat
        container.find('.misi-item:last input').focus();
    }

    function removeMisi(btn) {
        var container = $('#misiContainer');

        // Cek jumlah item
        if (container.children().length > 1) {
            // Hapus elemen parent (div.misi-item)
            $(btn).closest('.misi-item').remove();
            // Update ulang nomor urut
            updateMisiNumbers();
        } else {
            showToast('Minimal harus ada 1 misi!', 'warning');
        }
    }

    // 4. Delete Gambar Existing
    function deleteGambar(path) {
        if (confirm('Apakah Anda yakin ingin menghapus gambar ini secara permanen?')) {
            $('#gambarPathToDelete').val(path);
            $('#deleteGambarForm').submit();
        }
    }
</script>
