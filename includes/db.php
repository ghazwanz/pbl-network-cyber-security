<?php
// File: includes/db.php

$host;
$port; // Port default PostgreSQL
$dbname; // Ganti nama database Anda
$user; // Ganti dengan username PostgreSQL Anda
$password; // Ganti dengan password Anda
// --------------------------

// Membuat connection string
$conn_string = "host=$host port=$port dbname=$dbname user=$user password=$password";

// Mencoba terhubung ke database
$db_conn = pg_connect($conn_string);

// Cek status koneksi
if (!$db_conn) {
    // Jika koneksi gagal, hentikan skrip.
    // Di mode produksi, error ini sebaiknya dicatat (log)
    // dan tampilkan halaman error yang ramah pengguna.
    die("Error: Koneksi ke database PostgreSQL gagal. " . pg_last_error());
}

// Set encoding klien ke UTF8 (Best practice)
pg_set_client_encoding($db_conn, "UTF8");

// Jika berhasil, variabel $db_conn (resource) siap digunakan
// oleh file lain yang meng-include file ini.
?>