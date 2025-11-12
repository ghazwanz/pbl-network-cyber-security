# Website Laboratorium (NCS) - Sistem Manajemen Konten

Proyek ini adalah pengembangan website laboratorium yang dinamis, dilengkapi dengan Sistem Manajemen Konten (CMS) kustom untuk mengelola semua aspek website, mulai dari profil, galeri, hingga arsip penelitian.

Proyek ini dibuat sebagai bagian dari *user requirement* yang spesifik, dengan fokus pada relasi data antara pengelola lab dan arsip ilmiah mereka.

---

## ✨ Fitur Utama

### Sisi Publik (Pengunjung)
* **Beranda:** Halaman etalase yang menampilkan *highlight* konten terbaru (agenda, galeri, arsip) dan informasi sekilas tentang profil lab.
* **Profil:** Halaman statis (dikelola admin) yang berisi Visi Misi, Sejarah, Struktur Organisasi, dan Logo.
* **Galeri:** Menampilkan dua bagian: Agenda (akan datang) dan Galeri/Kegiatan (sudah lewat).
* **Arsip:** Menampilkan arsip dokumen (PDF) yang dapat diunduh, dikategorikan sebagai "Penelitian" dan "Pengabdian".
* **Layanan:** Menampilkan layanan lab (Sarana Prasarana & Konsultatif).
* **Profil Pengelola:** Halaman dinamis yang menampilkan daftar pengelola lab, dan halaman detail untuk setiap pengelola yang secara otomatis menampilkan publikasi/arsip yang mereka tulis.

### Sisi Admin (Backend)
* Login admin yang aman menggunakan *session*.
* Dashboard terpusat.
* CRUD (Create, Read, Update, Delete) penuh untuk mengelola konten:
    * Galeri
    * Agenda
    * Links
    * Profil Pengelola (termasuk upload foto)
    * Layanan lab (konten sarana prasarana dan konsultatif)
    * Arsip (termasuk upload PDF)
* **Fitur Relasional:** Kemampuan untuk menautkan satu/lebih Pengelola ke satu/lebih Arsip saat mengunggah dokumen.

---

## 🛠️ Tumpukan Teknologi (Tech Stack)

* **Backend:** PHP Native Modular Sederhana
* **Database:** PostgreSQL
* **Frontend (Styling):** CDN Tailwind CSS
* **Frontend (Interaksi):** CDN jQuery
* **Frontend (Animasi):** CDN AOS (Animate On Scroll)

---

## 🏛️ Struktur Database (Skema)

Database ini menggunakan 7 tabel utama untuk mengelola konten dan relasi:

1.  `users` (Data login admin)
2.  `sarana` (Data konten sarana dan prasarana yang dimiliki oleh laboratorium)
3.  `konsultatif` (Data yang berisi kritik dan saran seperti testimonial laboratorium)
4.  `galeri` (Data kegiatan/foto memiliki tipe agenda untuk yang akan datang dan kegiatan yang telah berlalu)
5.  `pengelola` (Data profil pengelola lab)
6.  `arsip` (Data dokumen penelitian/pengabdian, termasuk path PDF)
7.  `arsip_pengelola` (Tabel *Junction* Many-to-Many yang menghubungkan `arsip` dan `pengelola`)

Pada tiap daftar tabel yang tersedia buat rincian kolom yang diperlukan berdasarkan kebutuhannya
---

1.  **Setup Database:**
    * Buat database baru di server Anda (misal: `db_lab_ncs_pbl`).
    * buat file `schema.sql` yang tersedia ke dalam database Anda sebagai skrip untuk menjalankan proses pembuatan skema tabel.