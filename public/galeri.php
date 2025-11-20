<?php
// Include file header (menyediakan <!DOCTYPE, <head>, <body>, <main>, dan <style> CSS)
require_once __DIR__ . '/../includes/header.php';

// --- Data Kegiatan ---
$limit = 9;
$kegiatan_list = [
    ['judul' => 'Workshop Forensik Digital', 'tag' => 'AGENDA'],
    ['judul' => 'Kelas Penetration Testing', 'tag' => 'AGENDA'],
    ['judul' => 'Pelatihan Cyber Range', 'tag' => 'AGENDA'],
    ['judul' => 'Eksperimen Serangan DDoS', 'tag' => 'AGENDA'],
    ['judul' => 'Workshop Analisis Malware', 'tag' => 'KEGIATAN'],
    ['judul' => 'Bootcamp Ethical Hacking', 'tag' => 'KEGIATAN'],
    ['judul' => 'Penyelenggaraan Perlombaan CTF', 'tag' => 'KEGIATAN'],
    ['judul' => 'Simulasi Phishing Attack', 'tag' => 'KEGIATAN'],
    ['judul' => 'Workshop Cyber Awareness', 'tag' => 'KEGIATAN'],
];

$total_kegiatan = count($kegiatan_list); // Total data: 12
$total_pages = ceil($total_kegiatan / $limit); // Total halaman: ceil(12 / 9) = 2

// Dapatkan halaman saat ini dari URL. Default ke 1 jika tidak ada.
$current_page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int) $_GET['page'] : 1;
// Batasi halaman yang sah
if ($current_page < 1)
    $current_page = 1;
if ($current_page > $total_pages)
    $current_page = $total_pages;

// Hitung offset (posisi awal data untuk halaman ini)
$offset = ($current_page - 1) * $limit;

// Ambil subset data (slice) untuk halaman saat ini
$kegiatan_halaman = array_slice($kegiatan_list, $offset, $limit);
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Galeri Kegiatan - Contoh Web</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

    <style>
        /* --- Base Styles & Variables --- */
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        :root {
            --primary-color: #000000ff;
            /* Hitam */
            --secondary-color: #ff6600;
            /* Oranye */
            --bg-light: #f8fcff;
            /* Biru muda */
            --text-dark: #333;
            --text-light: #666;
        }

        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #000000ff;
            background-color: #F8FCFF;
            /* Latar Belakang Biru Muda */
        }

        .container {
            width: 90%;
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px 0;
        }

        .text-center {
            text-align: center;
        }

        .section {
            padding: 40px 0;
        }

        /* --- Navigation Bar (Dummy/Placeholder) --- */
        .navbar {
            background-color: white;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
            /* Ini perlu diisi dengan struktur navigasi lengkap */
        }

        /* ... CSS Navigasi lainnya ... */

        /* --- Header Galeri --- */
        .header-galeri {
            padding-top: 80px;
            padding-bottom: 30px;
        }

        .tagline {
            color: var(--secondary-color);
            font-size: 0.9em;
            margin-bottom: 10px;
            /* Gaya Oval */
            display: inline-block;
            background-color: #fff0e6;
            padding: 8px 20px;
            border-radius: 50px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
        }

        .tagline i {
            margin-right: 5px;
        }

        h1 {
            font-size: 2.5em;
            color: var(--primary-color);
            margin-bottom: 20px;
            font-weight: bold;
        }

        .header-galeri .desc {
            max-width: 700px;
            margin: 0 auto;
            color: var(--text-light);
        }

        /* --- Filter Section --- */
        .filter-section {
            padding: 20px 0 60px 0;
        }

        .filter-container {
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            gap: 30px;
        }

        .search-box,
        .kategori-box {
            flex: 1;
        }

        .kategori-box {
            max-width: 250px;
        }

        label {
            display: block;
            font-weight: bold;
            margin-bottom: 5px;
            color: var(--text-dark);
        }

        .input-group {
            position: relative;
            display: flex;
            align-items: center;
            border: 1px solid #ddd;
            border-radius: 6px;
            padding: 5px 10px;
            background-color: white;
        }

        .input-group i {
            color: var(--text-light);
            margin-right: 10px;
        }

        #search-input {
            border: none;
            padding: 10px 0;
            width: 100%;
            font-size: 1em;
            outline: none;
        }

        .select-group {
            padding: 0;
        }

        #kategori-select {
            appearance: none;
            -webkit-appearance: none;
            -moz-appearance: none;
            border: none;
            padding: 15px;
            width: 100%;
            font-size: 1em;
            background: transparent;
            cursor: pointer;
            outline: none;
        }

        .select-group i {s
            pointer-events: none;
            position: absolute;
            right: 10px;
        }

        /* --- Grid Kegiatan & Cards --- */
        .kegiatan-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 30px;
            margin-bottom: 50px;
        }

        .kegiatan-card {
            background: white;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
            overflow: hidden;
            transition: transform 0.3s;
        }

        .kegiatan-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 15px rgba(0, 0, 0, 0.1);
        }

        .card-image {
            width: 100%;
            height: 180px;
            object-fit: cover;
            background-color: #f0f4f7;
        }

        .card-content {
            padding: 20px;
        }

        .card-tag {
            display: inline-block;
            font-size: 0.7em;
            padding: 5px 10px;
            /* Jaga radius tag agar tetap kecil */
            border-radius: 3px;
            margin-bottom: 10px;
            text-transform: uppercase;
        }

        /* Warna Tag AGENDA */
        .card-tag.agenda {
            background-color: #d7e4faff;
            border-radius: 50px;
            color: #0077cc;
        }

        /* Warna Tag KEGIATAN */
        .card-tag.kegiatan {
            background-color: #ffe6e6;
            border-radius: 50px;
            color: #ff6600;
        }

        .kegiatan-card h3 {
            font-size: 1.1em;
            color: #000000ff;
        }

        /* Judul link harus diatur warnanya agar hitam */
        .kegiatan-card h3 a {
            color: #000000ff;
            /* Judul menjadi hitam */
            text-decoration: none;
            /* Hilangkan garis bawah */
        }

        .kegiatan-card h3 a:hover {
            text-decoration: underline;
        }

        /* --- Paginasi --- */
        .paginasi-container {
            display: flex;
            justify-content: center;
            gap: 10px;
            padding: 20px 0;
            margin-bottom: 50px;
        }

        .page-link {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 40px;
            height: 40px;
            border-radius: 5px;
            text-decoration: none;
            color: var(--text-dark);
            border: 1px solid #ddd;
            transition: background-color 0.2s, color 0.2s;
        }

        .page-link:hover:not(.active):not(.disabled) {
            background-color: #eee;
        }

        .page-link.active {
            background-color: var(--secondary-color);
            color: white;
            border-color: var(--secondary-color);
        }

        .page-link.disabled {
            opacity: 0.5;
            pointer-events: none;
        }


        /* --- Responsiveness --- */
        @media (max-width: 992px) {
            .kegiatan-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 768px) {
            .navbar ul {
                display: none;
            }

            .filter-container {
                flex-direction: column;
                align-items: stretch;
            }

            .kategori-box {
                max-width: 100%;
            }

            .kegiatan-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>

<section class="section header-galeri">
    <div class="container text-center">
        <p class="tagline"><i class="fas fa-plus-circle"></i> INOVASI TEKNOLOGI KEKINIAN</p>
        <h1>Galeri</h1>
        <p class="desc">
            Lorem ipsum dolor sit, amet consectetur adipisicing elit. Ipsam minima
            voluptate aperiam commodi accusantium nobis in. Etum ipsa sint officia.
        </p>
    </div>
</section>

<section class="section filter-section">
    <div class="container filter-container">
        <div class="search-box">
            <label for="search-input">Cari Kegiatan</label>
            <div class="input-group">
                <i class="fas fa-search"></i>
                <input type="text" id="search-input" placeholder="Cari berdasarkan judul atau deskripsi...">
            </div>
        </div>
        <div class="kategori-box">
            <label for="kategori-select">Kategori</label>
            <div class="input-group select-group">
                <select id="kategori-select">
                    <option>Semua Kategori</option>
                    <option>Workshop</option>
                    <option>Pelatihan</option>
                    <option>Kompetisi</option>
                </select>
                <i class="fas fa-chevron-down"></i>
            </div>
        </div>
    </div>
</section>

<section class="section grid-kegiatan">
    <div class="container kegiatan-grid">
        <?php
        // Looping MENGGUNAKAN DATA HALAMAN SAAT INI
        foreach ($kegiatan_halaman as $kegiatan) {
            $tag_class = strtolower($kegiatan['tag']);
            echo "
            <div class='kegiatan-card'>
                <img src='../assets/img/no-image.png' alt='Ilustrasi Kegiatan' class='card-image'>
                <div class='card-content'>
                    <span class='card-tag {$tag_class}'>{$kegiatan['tag']}</span>
                    <h3><a href='#'>{$kegiatan['judul']}</a></h3>
                </div>
            </div>";
        }
        ?>
    </div>
</section>

<div class="container paginasi-container">
    <?php
    // Link ke Halaman Sebelumnya
    $prev_page = $current_page - 1;
    $prev_disabled = ($current_page == 1) ? 'disabled' : '';
    echo "<a href='?page={$prev_page}' class='page-link {$prev_disabled}'><i class='fas fa-chevron-left'></i></a>";

    // Link Halaman 1, 2, 3, dst
    for ($i = 1; $i <= $total_pages; $i++) {
        $active_class = ($i == $current_page) ? 'active' : '';
        echo "<a href='?page={$i}' class='page-link {$active_class}'>{$i}</a>";
    }

    // Link ke Halaman Selanjutnya
    $next_page = $current_page + 1;
    $next_disabled = ($current_page == $total_pages) ? 'disabled' : '';
    echo "<a href='?page={$next_page}' class='page-link {$next_disabled}'><i class='fas fa-chevron-right'></i></a>";
    ?>
</div>

<?php
// Include file footer (menyediakan </main>, </body>, </html>)
require_once __DIR__ . '/../includes/footer.php';
?>