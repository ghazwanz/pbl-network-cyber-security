
    <?php
        require_once __DIR__ . '/../includes/header.php';
        $pdo = getDBConnection();
    ?> 
    
    <main>
        <section class="text-center py-24 sm:py-32">
            <div class="container mx-auto px-4 max-w-3xl flex flex-col items-center">
                
                <div class="inline-flex items-center space-x-2 bg-white border border-gray-200 rounded-full px-5 py-2.5 text-orange-500 font-bold text-sm mb-6">
                    <img src="../assets/icons/zap1.svg">
                    <span>INOVASI TEKNOLOGI KEAMANAN</span>
                </div>

                <h1 class="text-5xl md:text-6xl font-medium text-gray-900 leading-tight mb-6">
                    Selamat Datang di Laboratorium Network & Cyber security
                </h1>

                <p class="text-lg text-gray-500 max-w-2xl mb-10">
                    Lorem ipsum dolor sit amet, consectetur adipisicing elit. Ipsam minima voluptatem aperiam commodi accusantium nobis in, illum ipsa sint officia.
                </p>

                <div class="flex flex-col sm:flex-row space-y-4 sm:space-y-0 sm:space-x-8 mb-10">
                    <div class="flex items-center space-x-2">
                        <img src="../assets/icons/check-circle1.svg">
                        <span class="font-medium text-gray-700">Jaringan</span>
                    </div>
                    <div class="flex items-center space-x-2">
                        <img src="../assets/icons/check-circle1.svg">
                        <span class="font-medium text-gray-700">Capture The Flag</span>
                    </div>
                    <div class="flex items-center space-x-2">
                        <img src="../assets/icons/check-circle1.svg">
                        <span class="font-medium text-gray-700">Security System</span>
                    </div>
                </div>

                <a href="./layanan.php" class="inline-flex items-center space-x-2 bg-[#1B2D62] text-white font-bold text-base px-7 py-3.5 rounded-lg shadow-sm transition hover:bg-[#2C4AA4] hover:-translate-y-0.5">
                    <span>Jelajahi Tentang Kami</span>
                    <img src="../assets/icons/arrow-up-right1.svg">
                </a>

            </div>
        </section>

        <section class="bg-white py-24 sm:py-32 border-t-4 border-b-4 ">
            <div class="container mx-auto max-w-7xl px-4 grid grid-cols-1 lg:grid-cols-5 lg:gap-16">
                
                <div class="lg:col-span-2 flex flex-col justify-center mb-16 lg:mb-0">
                    
                    <div class="inline-flex self-start items-center space-x-2 bg-[#1B2D62] text-white font-bold text-xs rounded-full px-4 py-1.5 mb-6">
                        <img src="../assets/icons/award2.svg" alt="" srcset="">
                        <span>INOVASI KARYA</span>
                    </div>

                    <h2 class="text-4xl md:text-5xl font-inter font-medium text-gray-900 leading-tight mb-6">
                        Jelajahi Layanan Terbaik Kami
                    </h2>
                    
                    <p class="text-lg text-gray-500 mb-10">
                        Lorem ipsum dolor sit amet, consectetur adipisicing elit. Ipsam minima voluptatem aperiam commodi accusantium nobis in, illum ipsa sint.
                    </p>

                    <a href="./profil.php" class="self-start inline-flex items-center space-x-2 bg-[#1B2D62] text-white font-bold text-base px-7 py-3.5 rounded-lg shadow-sm transition hover:bg-[#2C4AA4] hover:-translate-y-0.5">
                        <span>Pelajari Lebih Lanjut</span>
                        <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="3" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 19.5l15-15m0 0H8.25m11.25 0v11.25" />
                        </svg>
                    </a>
                </div>

                <div class="lg:col-span-3">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        
                        <?php
                            try {
                                $stmt = $pdo->prepare("SELECT * FROM layanan WHERE status = 'Aktif' ORDER BY id ASC LIMIT 4");
                                $stmt->execute();
                                $layananList = $stmt->fetchAll(PDO::FETCH_ASSOC);
                            } catch (PDOException $e) {
                                echo "Error fetching data: " . $e->getMessage();
                                $layananList = [];
                            }
                        ?>

                        <?php if (!empty($layananList)): ?>
                            <?php foreach ($layananList as $item): ?>
                                <div class="bg-white border border-gray-200 rounded-xl p-8 shadow-lg transition duration-300 hover:shadow-xl hover:-translate-y-1">
                                    <div class="inline-flex items-center justify-center w-16 h-16 bg-orange-500 rounded-lg mb-6">
                                        <img src="../uploads<?= htmlspecialchars($item['gambar_path']) ?>" >
                                    </div>
                                    <h3 class="text-xl font-inter font-medium text-gray-900 mb-2">
                                        <?= htmlspecialchars($item['nama_layanan']) ?>
                                    </h3>
                                    <p class="text-gray-500">
                                        <?= htmlspecialchars($item['deskripsi']) ?>
                                    </p>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p class="text-gray-500 col-span-2">Belum ada data layanan tersedia.</p>
                        <?php endif; ?>

                    </div>
                </div>

            </div>
        </section>  

        <section class="py-24 sm:py-32">
            <div class="container mx-auto max-w-7xl px-4">
                
                <div class="max-w-3xl mx-auto text-center mb-16">
                    <div class="inline-flex items-center space-x-2 bg-white border border-gray-200 rounded-full px-5 py-2.5 text-orange-500 font-bold text-sm mb-6">
                        <img src="../assets/icons/award1.svg" alt="" srcset="">
                        <span>INOVASI KARYA</span>
                    </div>

                    <h2 class="text-4xl md:text-5xl font-inter font-medium text-gray-900 leading-tight mb-6">
                        Publikasi & Arsip Terbaru
                    </h2>
                    
                    <p class="text-lg text-gray-500">
                        Lorem ipsum dolor sit amet, consectetur adipisicing elit. Ipsam minima voluptatem aperiam commodi accusantium nobis in, illum ipsa sint officia.
                    </p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">

                    <div class="bg-white border border-gray-200 rounded-xl p-8 shadow-lg flex flex-col transition duration-300 hover:shadow-xl hover:-translate-y-1">
                        <div class="flex items-center justify-start gap-6 mb-6">
                            <div class="inline-flex items-center justify-center w-14 h-14 bg-orange-500 rounded-lg">
                                <svg class="w-7 h-7 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" /></svg>
                            </div>
                            <span class="bg-orange-100 text-orange-700 text-xs font-semibold px-3 py-1 rounded-full">PENELITIAN</span>
                        </div>
                        <div class="flex-grow">
                            <h3 class="text-2xl font-inter font-medium text-gray-900 mb-4">Analisis Material Nano untuk Aplikasi</h3>
                            <p class="text-gray-500 mb-8">Nequ  e porro quisquam est qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit...</p>
                        </div>
                        <div class="flex justify-between items-center text-sm">
                            <a href="#" class="text-[#1B2D62] font-semibold inline-flex items-center space-x-1.5 group">
                                <span>Unduh PDF</span>
                                <svg class="w-4 h-4 transition-transform group-hover:translate-x-1" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" /></svg>
                            </a>
                            <div class="flex items-center space-x-2 text-[#1B2D62] font-medium">
                                <img src="../assets/icons/download1.svg">
                                <span>21</span>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white border border-gray-200 rounded-xl p-8 shadow-lg flex flex-col transition duration-300 hover:shadow-xl hover:-translate-y-1">
                        <div class="flex items-center justify-start gap-6 mb-6">
                            <div class="inline-flex items-center justify-center w-14 h-14 bg-orange-500 rounded-lg">
                                <svg class="w-7 h-7 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" /></svg>
                            </div>
                            <span class="bg-orange-100 text-orange-700 text-xs font-semibold px-3 py-1 rounded-full">PENELITIAN</span>
                        </div>
                        <div class="flex-grow">
                            <h3 class="text-2xl font-inter font-medium text-gray-900 mb-4">A Review: Performance Analysis of Single...</h3>
                            <p class="text-gray-500 mb-8">Neque porro quisquam est qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit...</p>
                        </div>
                        <div class="flex justify-between items-center text-sm">
                            <a href="#" class="text-[#1B2D62] font-semibold inline-flex items-center space-x-1.5 group">
                                <span>Unduh PDF</span>
                                <svg class="w-4 h-4 transition-transform group-hover:translate-x-1" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" /></svg>
                            </a>
                            <div class="flex items-center space-x-2 text-[#1B2D62] font-medium">
                                <img src="../assets/icons/download1.svg">
                                <span>19</span>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white border border-gray-200 rounded-xl p-8 shadow-lg flex flex-col transition duration-300 hover:shadow-xl hover:-translate-y-1">
                        <div class="flex items-center justify-start gap-6 mb-6">
                            <div class="inline-flex items-center justify-center w-14 h-14 bg-orange-500 rounded-lg">
                                <svg class="w-7 h-7 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" /></svg>
                            </div>
                            <span class="bg-orange-100 text-orange-700 text-xs font-semibold px-3 py-1 rounded-full">PENELITIAN</span>
                        </div>
                        <div class="flex-grow">
                            <h3 class="text-2xl font-inter font-medium text-gray-900 mb-4">Spatial and Temporal Analysis of Mangrove...</h3>
                            <p class="text-gray-500 mb-8">Neque porro quisquam est qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit...</p>
                        </div>
                        <div class="flex justify-between items-center text-sm">
                            <a href="#" class="text-[#1B2D62] font-semibold inline-flex items-center space-x-1.5 group">
                                <span>Unduh PDF</span>
                                <svg class="w-4 h-4 transition-transform group-hover:translate-x-1" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" /></svg>
                            </a>
                            <div class="flex items-center space-x-2 text-[#1B2D62] font-medium">
                                <img src="../assets/icons/download1.svg">
                                <span>15</span>
                            </div>
                        </div>
                    </div>

                <div class="text-center mt-16"> 
                    <a href="./arsip.php" class="inline-flex items-center space-x-2 bg-[#1B2D62] text-white font-bold text-base px-7 py-3.5 rounded-lg shadow-sm transition hover:bg-[#2C4AA4] hover:-translate-y-0.5">
                        <span>Lihat Semua Dokumen</span>
                        <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="3" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 19.5l15-15m0 0H8.25m11.25 0v11.25" />
                        </svg>
                    </a>
                </div>

            </div>
        </section>

        <section class="py-24 sm:py-32 bg-white-50">
            <div class="container mx-auto max-w-7xl px-4">
                
                <div class="max-w-3xl mx-auto text-center mb-16">
                    <div class="inline-flex items-center space-x-2 bg-white border border-gray-200 rounded-full px-5 py-2.5 text-orange-500 font-bold text-sm mb-6">
                        <img src="../assets/icons/activity1.svg">
                        <span>SERING DITANYAKAN</span>
                    </div>

                    <h2 class="text-4xl md:text-5xl font-inter font-medium text-gray-900 leading-tight mb-6">
                        Layanan Konsultatif
                    </h2>
                    
                    <p class="text-lg text-gray-500">
                        Temukan jawaban atas pertanyaan umum seputar layanan penelitian dan konsultasi kami di bawah ini.
                    </p>
                </div>

                <div class="max-w-4xl mx-auto space-y-4">
    
                    <?php
                    
                    try {
                        $stmtFaq = $pdo->prepare("SELECT pertanyaan, jawaban FROM konsultatif WHERE jawaban IS NOT NULL AND jawaban != '' ORDER BY id DESC LIMIT 5");
                        $stmtFaq->execute();
                        $faqList = $stmtFaq->fetchAll(PDO::FETCH_ASSOC);
                    } catch (PDOException $e) {
                        echo "Error fetching FAQ: " . $e->getMessage();
                        $faqList = [];
                    }
                    ?>

                    <?php if (!empty($faqList)): ?>
                        <?php foreach ($faqList as $faq): ?>
                            <div class="accordion-item border-b border-gray-200 transition-all duration-300 rounded-2xl">
                                <button class="accordion-header flex justify-between items-start w-full p-6 text-left group">
                                    <span class="text-xl font-inter font-medium text-gray-900 transition-colors group-hover:text-orange-500">
                                        <?= htmlspecialchars($faq['pertanyaan']) ?>
                                    </span>
                                    
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon text-gray-500 min-w-[24px] transition-colors duration-300 group-hover:text-orange-500">
                                        <polyline points="6 9 12 15 18 9"></polyline>
                                    </svg>
                                </button>
                                <div class="accordion-content overflow-hidden transition-all duration-500 ease-in-out max-h-0">
                                    <p class="px-6 pb-6 text-base text-gray-500 leading-relaxed">
                                        <?= htmlspecialchars($faq['jawaban']) ?>
                                    </p>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="text-center py-8 text-gray-500">
                            <p>Belum ada pertanyaan yang sering diajukan saat ini.</p>
                        </div>  
                    <?php endif; ?>

                </div>

            </div>
        </section>

        <section class="py-24 sm:py-32">
            <div class="container mx-auto max-w-7xl px-4">
                
                <div class="max-w-3xl mx-auto text-center mb-16">
                    
                    <div class="inline-flex items-center space-x-2 bg-white border border-gray-200 rounded-full px-5 py-2.5 text-orange-500 font-bold text-sm mb-6">
                        <img src="../assets/icons/activity1.svg">
                        <span>AKTIVITAS KAMI</span>
                    </div>

                    <h2 class="text-4xl md:text-5xl font-inter font-medium text-gray-900 leading-tight mb-6">
                        Galeri Terbaru
                    </h2>
                    
                    <p class="text-lg text-gray-500">
                        Lorem ipsum dolor sit amet, consectetur adipisicing elit. Ipsam minima voluptatem aperiam commodi accusantium nobis in, illum ipsa sint officia.
                    </p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">

                    <?php
                    try {
                        $stmtGaleri = $pdo->prepare("SELECT * FROM galeri WHERE is_active = true ORDER BY tanggal_kegiatan DESC LIMIT 3");
                        $stmtGaleri->execute();
                        $galeriList = $stmtGaleri->fetchAll(PDO::FETCH_ASSOC);
                    } catch (PDOException $e) {
                        echo "Error fetching Galeri: " . $e->getMessage();
                        $galeriList = [];
                    }
                    ?>

                    <?php if (!empty($galeriList)): ?>
                        <?php foreach ($galeriList as $item): ?>
                            <div class="bg-white border border-gray-200 rounded-xl p-6 shadow-lg transition duration-300 hover:shadow-xl hover:-translate-y-1 flex flex-col h-full">
                                
                                <div class="rounded-lg mb-6 overflow-hidden h-48 bg-gray-100">
                                    <img src="../uploads<?= htmlspecialchars($item['gambar_path']) ?>" 
                                        alt="<?= htmlspecialchars($item['judul']) ?>" 
                                        class="w-full h-full object-cover transform hover:scale-105 transition duration-500">
                                </div>

                                <div class="mb-4">
                                    <span class="bg-orange-100 text-orange-700 text-xs font-semibold px-3 py-1 rounded-full inline-block">
                                        <?= htmlspecialchars(strtoupper($item['tipe'])) ?>
                                    </span>
                                </div>

                                <h3 class="text-2xl font-inter font-medium text-gray-900 leading-snug">
                                    <?= htmlspecialchars($item['judul']) ?>
                                </h3>

                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="col-span-1 lg:col-span-3 text-center py-10 text-gray-500">
                            <p>Belum ada kegiatan terbaru yang diunggah.</p>
                        </div>
                    <?php endif; ?>

                </div>

                <div class="text-center mt-16">
                    <a href="./galeri.php" class="inline-flex items-center space-x-2 bg-[#1B2D62] text-white font-bold text-base px-7 py-3.5 rounded-lg shadow-sm transition hover:bg-[#2C4AA4] hover:-translate-y-0.5">
                        <span>Lihat Semua Kegiatan</span>
                        <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="3" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 19.5l15-15m0 0H8.25m11.25 0v11.25" />
                        </svg>
                    </a>
                </div>

            </div>
        </section>
    </main>

    <?php
        require_once __DIR__ . '/../includes/footer.php';
    ?>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const accordionHeaders = document.querySelectorAll(".accordion-header");

            function activateItem(item) {
                item.classList.add('bg-white', 'border', 'border-gray-200', 'shadow-sm');
                item.classList.remove('border-b', 'border-transparent'); 
            }

            function deactivateItem(item) {
                item.classList.remove('bg-white', 'border', 'shadow-sm');
                item.classList.add('border-b', 'border-gray-200');
            }

            accordionHeaders.forEach(header => {
                header.addEventListener("click", function() {
                    const currentItem = this.parentElement;
                    const content = this.nextElementSibling;
                    const icon = this.querySelector('.icon');
                    const isOpen = !content.classList.contains('max-h-0');

                    document.querySelectorAll('.accordion-item').forEach(item => {
                        if (item !== currentItem) {
                            const otherContent = item.querySelector('.accordion-content');
                            const otherIcon = item.querySelector('.icon');

                            otherContent.classList.add('max-h-0');
                            otherContent.classList.remove('max-h-96');
                            
                            if(otherIcon) otherIcon.classList.remove('rotate-180');

                            deactivateItem(item);
                        }
                    });

                    if (isOpen) {
                        content.classList.add('max-h-0');
                        content.classList.remove('max-h-96');
                        icon.classList.remove('rotate-180');
                        
                        deactivateItem(currentItem);

                    } else {
                        content.classList.remove('max-h-0');
                        content.classList.add('max-h-96');
                        icon.classList.add('rotate-180');

                        activateItem(currentItem);
                    }
                });
            });
        });
    </script>