<?php
    require_once __DIR__ . '/../includes/header.php';
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Layanan Kami (Tailwind CSS)</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <script src="https://unpkg.com/feather-icons"></script>
    
    <script src="https://cdn.tailwindcss.com"></script>
    
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    // Atur 'Poppins' sebagai font default
                    fontFamily: {
                        sans: ['Poppins', 'sans-serif'],
                    },
                    // Tambahkan warna kustom dari gambar
                    colors: {
                        brand: {
                            red: '#E53935',
                            'red-light': '#fff3f2',
                            text: '#333',
                            subtext: '#5a6578',
                        }
                    }
                }
            }
        }
    </script>
</head>
<body class="font-sans bg-white text-brand-text">

    <section class="py-16 sm:py-24">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <div class="text-center max-w-2xl mx-auto mb-12 sm:mb-16">
                <span class="text-sm font-semibold text-brand-red uppercase tracking-wide">LAYANAN KAMI</span>
                <h2 class="mt-2 text-3xl font-bold tracking-tight text-gray-900 sm:text-4xl">
                    Apa yang Kami Tawarkan
                </h2>
                <p class="mt-4 text-lg text-brand-subtext">
                    Jelajahi layanan dan fasilitas yang kami sediakan untuk mendukung kebutuhan praktikum, penelitian, dan pengembangan kompetensi di laboratorium kami.
                </p>
            </div>

            <div class="grid grid-cols-1 gap-8 md:grid-cols-2 lg:grid-cols-3">
                
                <div class="bg-white border border-gray-100 rounded-xl shadow-lg p-6 sm:p-8 transition duration-300 hover:shadow-xl hover:-translate-y-1">
                    <div class="w-12 h-12 rounded-full bg-brand-red-light flex items-center justify-center mb-6">
                        <i data-feather="monitor" class="text-brand-red"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-2">Workstation</h3>
                    <p class="text-sm text-brand-subtext">Disediakan workstation dengan spesifikasi tinggi untuk tugas akhir komputasi berat, pemrosesan data besar, dan simulasi unggulan.</p>
                </div>

                <div class="bg-white border border-gray-100 rounded-xl shadow-lg p-6 sm:p-8 transition duration-300 hover:shadow-xl hover:-translate-y-1">
                    <div class="w-12 h-12 rounded-full bg-brand-red-light flex items-center justify-center mb-6">
                        <i data-feather="file-text" class="text-brand-red"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-2">Perangkat Lunak</h3>
                    <p class="text-sm text-brand-subtext">Tersedia perangkat lunak berlisensi khusus untuk kebutuhan riset (skripsi/tesis).</p>
                </div>

                <div class="bg-white border border-gray-100 rounded-xl shadow-lg p-6 sm:p-8 transition duration-300 hover:shadow-xl hover:-translate-y-1">
                    <div class="w-12 h-12 rounded-full bg-brand-red-light flex items-center justify-center mb-6">
                        <i data-feather="file-text" class="text-brand-red"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-2">Perangkat Lunak</h3>
                    <p class="text-sm text-brand-subtext">Tersedia perangkat lunak berlisensi khusus untuk kebutuhan riset (skripsi/tesis).</p>
                </div>

                <div class="bg-white border border-gray-100 rounded-xl shadow-lg p-6 sm:p-8 transition duration-300 hover:shadow-xl hover:-translate-y-1">
                    <div class="w-12 h-12 rounded-full bg-brand-red-light flex items-center justify-center mb-6">
                        <i data-feather="message-circle" class="text-brand-red"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-2">Workshop & Seminar</h3>
                    <p class="text-sm text-brand-subtext">Kegiatan workshop, seminar, serta pelatihan internal mengenai keamanan jaringan, ethical hacking, dan monitoring infrastruktur IT.</p>
                </div>

                <div class="bg-white border border-gray-100 rounded-xl shadow-lg p-6 sm:p-8 transition duration-300 hover:shadow-xl hover:-translate-y-1">
                    <div class="w-12 h-12 rounded-full bg-brand-red-light flex items-center justify-center mb-6">
                        <i data-feather="server" class="text-brand-red"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-2">Praktikum Mahasiswa</h3>
                    <p class="text-sm text-brand-subtext">Praktikum terkait administrasi jaringan, konfigurasi router/switch, serta simulasi keamanan siber menggunakan perangkat lunak pendukung.</p>
                </div>

                <div class="bg-white border border-gray-100 rounded-xl shadow-lg p-6 sm:p-8 transition duration-300 hover:shadow-xl hover:-translate-y-1">
                    <div class="w-12 h-12 rounded-full bg-brand-red-light flex items-center justify-center mb-6">
                        <i data-feather="message-circle" class="text-brand-red"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-2">Workshop & Seminar</h3>
                    <p class="text-sm text-brand-subtext">Kegiatan workshop, seminar, serta pelatihan internal mengenai keamanan jaringan, ethical hacking, dan monitoring infrastruktur IT.</p>
                </div>

            </div>
        </div>
    </section>

    <section class="pb-16 sm:pb-24">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">

            <div class="text-center max-w-3xl mx-auto mb-12">
                <span class="text-sm font-semibold text-brand-red uppercase tracking-wide">SERING DITANYAKAN</span>
                <h2 class="mt-2 text-3xl font-bold tracking-tight text-gray-900 sm:text-4xl">
                    Layanan Konsultatif
                </h2>
                <p class="mt-4 text-lg text-brand-subtext">
                    Lorem ipsum dolor sit amet consectetur adipisicing elit. Ipsam minima voluptate aperiam commodi accusantium nobis in, illum ipsa sint officia.
                </p>
            </div>

            <div class="max-w-3xl mx-auto border-t border-gray-200">
                
                <div class="accordion-item border-b border-gray-200">
                    <button class="accordion-header flex justify-between items-center w-full py-5 text-left">
                        <span class="text-lg font-medium text-brand-red">Bagaimana cara membuat penelitian yang berkualitas?</span>
                        <i data-feather="chevron-down" class="icon text-gray-500 transition-transform duration-300 rotate-180"></i>
                    </button>
                    <div class="accordion-content overflow-hidden transition-all duration-500 ease-in-out max-h-96">
                        <p class="pb-5 pr-10 text-base text-brand-subtext">Lorem ipsum dolor sit amet consectetur adipisicing elit. Ipsam minima voluptate aperiam commodi accusantium nobis in, illum ipsa sint officia.</p>
                    </div>
                </div>

                <div class="accordion-item border-b border-gray-200">
                    <button class="accordion-header flex justify-between items-center w-full py-5 text-left">
                        <span class="text-lg font-medium text-gray-900">Apakah masyarakat bebas bisa mengunggah penelitian mereka disini?</span>
                        <i data-feather="chevron-down" class="icon text-gray-500 transition-transform duration-300"></i>
                    </button>
                    <div class="accordion-content overflow-hidden transition-all duration-500 ease-in-out max-h-0">
                        <p class="pb-5 pr-10 text-base text-brand-subtext">Lorem ipsum dolor sit amet consectetur adipisicing elit. Ipsam minima voluptate aperiam commodi accusantium nobis in, illum ipsa sint officia.</p>
                    </div>
                </div>

                <div class="accordion-item border-b border-gray-200">
                    <button class="accordion-header flex justify-between items-center w-full py-5 text-left">
                        <span class="text-lg font-medium text-gray-900">Apakah ada batasan topik penelitian yang dapat dikonsultasikan?</span>
                        <i data-feather="chevron-down" class="icon text-gray-500 transition-transform duration-300"></i>
                    </button>
                    <div class="accordion-content overflow-hidden transition-all duration-500 ease-in-out max-h-0">
                        <p class="pb-5 pr-10 text-base text-brand-subtext">Lorem ipsum dolor sit amet consectetur adipisicing elit. Ipsam minima voluptate aperiam commodi accusantium nobis in, illum ipsa sint officia.</p>
                    </div>
                </div>

                <div class="accordion-item border-b border-gray-200">
                    <button class="accordion-header flex justify-between items-center w-full py-5 text-left">
                        <span class="text-lg font-medium text-gray-900">Bagaimana cara menjadwalkan sesi konsultasi?</span>
                        <i data-feather="chevron-down" class="icon text-gray-500 transition-transform duration-300"></i>
                    </button>
                    <div class="accordion-content overflow-hidden transition-all duration-500 ease-in-out max-h-0">
                        <p class="pb-5 pr-10 text-base text-brand-subtext">Lorem ipsum dolor sit amet consectetur adipisicing elit. Ipsam minima voluptate aperiam commodi accusantium nobis in, illum ipsa sint officia.</p>
                    </div>
                </div>

            </div>
        </div>
    </section>


    <script>
        // Inisialisasi Feather Icons
        feather.replace();

        // Logika Accordion
        const accordionHeaders = document.querySelectorAll(".accordion-header");

        accordionHeaders.forEach(header => {
            header.addEventListener("click", function() {
                const content = this.nextElementSibling;
                const icon = this.querySelector('.icon');
                const span = this.querySelector('span');
                
                // Cek apakah item yang diklik sudah terbuka
                const isOpen = !content.classList.contains('max-h-0');

                // 1. Tutup semua item lain
                document.querySelectorAll('.accordion-content').forEach(c => {
                    if (c !== content) {
                        c.classList.add('max-h-0');
                        c.classList.remove('max-h-96'); // Atur ke 0
                        
                        // Reset ikon & warna teks header lain
                        const otherHeader = c.previousElementSibling;
                        otherHeader.querySelector('.icon').classList.remove('rotate-180');
                        otherHeader.querySelector('span').classList.remove('text-brand-red');
                        otherHeader.querySelector('span').classList.add('text-gray-900');
                    }
                });

                // 2. Buka/Tutup item yang diklik
                if (isOpen) {
                    content.classList.add('max-h-0');
                    content.classList.remove('max-h-96');
                    icon.classList.remove('rotate-180');
                    span.classList.remove('text-brand-red');
                    span.classList.add('text-gray-900');
                } else {
                    content.classList.remove('max-h-0');
                    content.classList.add('max-h-96'); // Atur ke nilai yg cukup
                    icon.classList.add('rotate-180');
                    span.classList.add('text-brand-red');
                    span.classList.remove('text-gray-900');
                }
            });
        });
    </script>

    <?php
        require_once __DIR__ . '/../includes/footer.php';
    ?>
</body>
</html>