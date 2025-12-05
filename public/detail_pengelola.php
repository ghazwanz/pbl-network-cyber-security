<?php
/**
 * Halaman Detail Pengelola (Fixed Connection & Database Match)
 * File: public/detail_pengelola.php
 */

require_once __DIR__ . '/../includes/header.php';

$pdo = getDBConnection(); 
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;


$stmt = $pdo->prepare("SELECT * FROM pengelola WHERE id = ? LIMIT 1");
$stmt->execute([$id]);
$pengelola = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$pengelola) {
    echo "<script>alert('Data pengelola tidak ditemukan!'); window.location.href='profil.php';</script>";
    exit;
}

$queryArsip = "
    SELECT a.*
    FROM arsip a
    JOIN arsip_pengelola ap ON a.id = ap.arsip_id
    WHERE ap.pengelola_id = ? AND a.is_active = true
    ORDER BY a.tahun_publikasi DESC
";
$stmtArsip = $pdo->prepare($queryArsip);
$stmtArsip->execute([$id]);
$daftar_publikasi = $stmtArsip->fetchAll(PDO::FETCH_ASSOC);

$imgSrc = !empty($pengelola['foto_path']) && file_exists("../uploads" . $pengelola['foto_path']) 
          ? UPLOAD_URL . $pengelola['foto_path'] 
          : ASSETS_URL . '/img/no-image.png';

?>

<main class="px-4 bg-white">
    <div class="container mx-auto max-w-7xl py-16 pt-32 lg:pt-40">
        
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-12">
            
            <div class="lg:col-span-4 space-y-8" data-aos="fade-right">
                
                <div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-100 p-2">
                    <div class="aspect-[3/4] rounded-xl overflow-hidden bg-gray-100 relative group">
                        <img src="<?= htmlspecialchars($imgSrc) ?>" 
                             alt="<?= htmlspecialchars($pengelola['nama_lengkap']) ?>" 
                             class="w-full h-full object-cover object-top transition-transform duration-500 group-hover:scale-105">
                    </div>
                </div>

                <div class="bg-gray-50 rounded-2xl p-6 border border-gray-200 space-y-4">
                    <h3 class="text-[#1B2D62] font-bold border-b border-gray-200 pb-2 mb-4">Data Diri</h3>
                    
                    <?php 
                    $data_diri = [
                        'NIP / NIDN' => $pengelola['nip_nidn'] ?? '-', 
                        'Program Studi' => 'Teknik Informatika',
                        'Jabatan' => $pengelola['jabatan'] ?? '-'
                    ];

                    foreach ($data_diri as $label => $value): 
                        if ($value == '-' || empty($value)) continue;
                    ?>
                    <div>
                        <p class="text-xs text-gray-500 uppercase tracking-wider font-semibold"><?= $label ?></p>
                        <p class="text-[#1B2D62] font-medium"><?= htmlspecialchars($value) ?></p>
                    </div>
                    <?php endforeach; ?>
                </div>

                <div class="bg-white rounded-2xl p-6 border border-gray-200 shadow-sm space-y-4">
                    <h3 class="text-[#1B2D62] font-bold border-b border-gray-100 pb-2 mb-4">Kontak</h3>
                    
                    <?php if (!empty($pengelola['email'])): ?>
                    <div class="flex items-start gap-3">
                        <i class="fas fa-envelope text-orange-500 mt-1"></i>
                        <div class="overflow-hidden">
                            <p class="text-xs text-gray-500">Email</p>
                            <a href="mailto:<?= htmlspecialchars($pengelola['email']) ?>" class="text-sm font-medium text-blue-600 hover:underline truncate block">
                                <?= htmlspecialchars($pengelola['email']) ?>
                            </a>
                        </div>
                    </div>
                    <?php endif; ?>

                    <?php if (!empty($pengelola['no_telepon'])): ?>
                    <div class="flex items-start gap-3">
                        <i class="fas fa-phone text-orange-500 mt-1"></i>
                        <div>
                            <p class="text-xs text-gray-500">Telepon</p>
                            <p class="text-sm text-gray-700 leading-snug">
                                <?= htmlspecialchars($pengelola['no_telepon']) ?>
                            </p>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>

            </div>

            <div class="lg:col-span-8 space-y-12" data-aos="fade-left">

                <!-- Label dan Nama Pengelola -->
                <div class="text-left">
                    <div class="inline-flex items-center gap-2 px-5 py-2.5 bg-white border-2 border-orange-200 rounded-full text-orange-600 font-bold mb-4 shadow-lg">
                        <i class="fas fa-id-badge"></i>
                        <span class="text-sm tracking-wide">PROFIL PENGELOLA LAB</span>
                    </div>

                    <h1 class="text-4xl md:text-5xl font-bold text-[#1B2D62] mb-2">
                        <?= htmlspecialchars($pengelola['nama_lengkap']) ?>
                    </h1>
                    
                    <p class="text-xl text-gray-500 font-medium">
                        <?= htmlspecialchars($pengelola['jabatan']) ?>
                    </p>
                </div>

                <div>
                    <?php if (!empty($pengelola['bidang_keahlian'])): ?>
                    <h2 class="text-xl font-bold text-[#1B2D62] mb-4 flex items-center gap-2">
                        <span class="w-1 h-6 bg-orange-500 rounded-full block"></span>
                        Bidang Keahlian
                    </h2>
                    <div class="flex flex-wrap gap-2 mb-6">
                        <?php 
                            $skills = explode(',', $pengelola['bidang_keahlian']);
                            foreach($skills as $skill):
                                if(trim($skill) == '') continue;
                        ?>
                            <span class="px-4 py-1.5 bg-blue-50 text-[#1B2D62] rounded-full text-sm font-semibold border border-blue-100 hover:bg-blue-100 transition-colors cursor-default">
                                <?= htmlspecialchars(trim($skill)) ?>
                            </span>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>

                    <div class="flex flex-wrap gap-3 pb-8 border-b border-gray-100">
                        <?php 
                        $links = [
                            'Google Scholar' => ['url' => $pengelola['link_google_scholar'] ?? '', 'icon' => 'fa-graduation-cap'],
                            'Sinta' => ['url' => $pengelola['link_sinta'] ?? '', 'icon' => 'fa-book'],
                        ];

                        foreach ($links as $name => $data):
                            if (empty($data['url'])) continue;
                        ?>
                            <a href="<?= htmlspecialchars($data['url']) ?>" target="_blank" 
                               class="inline-flex items-center gap-2 px-4 py-2 bg-white text-gray-600 border border-gray-300 rounded-lg text-sm font-medium hover:border-orange-500 hover:text-orange-600 transition-all hover:-translate-y-0.5 shadow-sm">
                                <i class="fa-solidw <?php echo $data['icon'] ?> text-lg"></i> <?= $name ?>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-8">
                    
                    <?php if (!empty($pengelola['bio'])): ?>
                    <div class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm">
                        <h3 class="text-xl font-bold text-[#1B2D62] mb-4 flex items-center gap-2">
                            <span class="w-1 h-6 bg-orange-500 rounded-full block"></span>
                            Bio
                        </h3>
                        <div class="prose text-gray-700 leading-relaxed text-sm text-justify">
                            <?= nl2br(htmlspecialchars($pengelola['bio'])) ?>
                        </div>
                    </div>
                    <?php endif; ?>

                    <?php if (!empty($pengelola['pendidikan_terakhir'])): ?>
                    <div class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm">
                        <h3 class="text-xl font-bold text-[#1B2D62] mb-4 flex items-center gap-2">
                            <span class="w-1 h-6 bg-orange-500 rounded-full block"></span>
                            Pendidikan Terakhir
                        </h3>
                        <div class="flex items-center gap-3">
                             <div class="w-10 h-10 bg-blue-50 text-blue-600 rounded-full flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-user-graduate"></i>
                             </div>
                             <p class="text-lg font-medium text-gray-800">
                                 <?= htmlspecialchars($pengelola['pendidikan_terakhir']) ?>
                             </p>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>

                <div>
                    <div class="flex justify-between items-end mb-6 border-b border-gray-100 pb-2">
                        <h3 class="text-2xl font-bold text-[#1B2D62]">Sorotan Publikasi</h3>
                        
                    </div>

                    <?php if (count($daftar_publikasi) > 0): ?>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <?php foreach($daftar_publikasi as $pub): 
                                $link_baca = !empty($pub['doi']) ? $pub['doi'] : 
                                             (!empty($pub['file_pdf_path']) ? UPLOAD_URL . $pub['file_pdf_path'] : '#');
                            ?>
                            <div class="group bg-white rounded-xl p-5 border border-gray-200 hover:border-orange-300 hover:shadow-lg transition-all duration-300 flex flex-col h-full">
                                
                                <div class="flex-1">
                                    <div class="mb-2">
                                        <span class="text-[10px] font-bold uppercase tracking-wide px-2 py-0.5 bg-gray-100 text-gray-500 rounded">
                                            <?= htmlspecialchars($pub['kategori']) ?>
                                        </span>
                                    </div>

                                    <h4 class="font-bold text-[#1B2D62] mb-3 line-clamp-3 group-hover:text-blue-600 transition-colors leading-snug">
                                        <?= htmlspecialchars($pub['judul']) ?>
                                    </h4>
                                </div>

                                <div class="mt-4 pt-4 border-t border-gray-50 flex justify-between items-center">
                                    <div class="flex items-center gap-2 text-xs text-gray-500">
                                        <span class="font-semibold text-orange-500"><?= htmlspecialchars($pub['tahun_publikasi']) ?></span>
                                        <?php if (!empty($pub['penerbit'])): ?>
                                        <span class="text-gray-300">|</span>
                                        <span class="truncate max-w-[100px]" title="<?= htmlspecialchars($pub['penerbit']) ?>">
                                            <?= htmlspecialchars($pub['penerbit']) ?>
                                        </span>
                                        <?php endif; ?>
                                    </div>
                                    
                                    <a href="<?= htmlspecialchars($link_baca) ?>" target="_blank" class="text-xs font-bold text-blue-600 border border-blue-600 px-3 py-1 rounded hover:bg-blue-600 hover:text-white transition-colors">
                                        Baca
                                    </a>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-10 bg-gray-50 rounded-xl border border-dashed border-gray-300">
                            <p class="text-gray-500 text-sm">Belum ada data publikasi yang terhubung dengan pengelola ini.</p>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="pt-8 mt-8 border-t border-gray-100 flex justify-end">
                    <a href="profil.php" class="inline-flex items-center justify-center gap-2 bg-[#1B2D62] text-white font-bold px-8 py-3 rounded-xl shadow-md hover:bg-orange-500 transition-all duration-300">
                        <i class="fas fa-arrow-left"></i>
                        Kembali ke Profil
                    </a>
                </div>

            </div>
        </div>
    </div>

</main>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>