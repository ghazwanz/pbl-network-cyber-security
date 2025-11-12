-- ============================================
-- DATABASE SCHEMA: Website Laboratorium (NCS)
-- Database: db_lab_ncs_pbl
-- DBMS: PostgreSQL
-- Created: November 10, 2025
-- ============================================

-- Drop tables if exists (untuk development)
DROP TABLE IF EXISTS arsip_pengelola CASCADE;
DROP TABLE IF EXISTS arsip CASCADE;
DROP TABLE IF EXISTS pengelola CASCADE;
DROP TABLE IF EXISTS galeri CASCADE;
DROP TABLE IF EXISTS konsultatif CASCADE;
DROP TABLE IF EXISTS sarana CASCADE;
DROP TABLE IF EXISTS profil_lab CASCADE;
DROP TABLE IF EXISTS users CASCADE;

-- ============================================
-- TABLE: users
-- Deskripsi: Data login admin
-- ============================================
CREATE TABLE users (
    id SERIAL PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL, -- akan di-hash (md5/bcrypt)
    nama_lengkap VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    role VARCHAR(20) DEFAULT 'admin', -- admin, superadmin (optional)
    is_active BOOLEAN DEFAULT true,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Insert default admin
INSERT INTO users (username, password, nama_lengkap, email, role) 
VALUES ('admin', MD5('admin123'), 'Administrator', 'admin@lab.ncs.ac.id', 'admin');

-- ============================================
-- TABLE: profil_lab
-- Deskripsi: Data profil laboratorium (single record)
-- ============================================
CREATE TABLE profil_lab (
    id SERIAL PRIMARY KEY,
    nama_lab VARCHAR(200) NOT NULL,
    logo_path VARCHAR(255),
    visi TEXT,
    misi TEXT,
    sejarah TEXT,
    struktur_organisasi TEXT, -- bisa berisi HTML atau plain text
    alamat TEXT,
    no_telepon VARCHAR(20),
    email VARCHAR(100),
    social_media_links TEXT, -- JSON format: {"facebook":"url","instagram":"url"}
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Insert default profil lab
INSERT INTO profil_lab (nama_lab, visi, misi) 
VALUES ('Laboratorium NCS', 'Menjadi laboratorium terdepan dalam penelitian', 'Melaksanakan penelitian berkualitas');

-- ============================================
-- TABLE: sarana
-- Deskripsi: Data sarana dan prasarana lab
-- ============================================
CREATE TABLE sarana (
    id SERIAL PRIMARY KEY,
    nama_sarana VARCHAR(200) NOT NULL,
    deskripsi TEXT,
    gambar_path VARCHAR(255),
    spesifikasi TEXT,
    jumlah INTEGER DEFAULT 1,
    kondisi VARCHAR(50) DEFAULT 'Baik', -- Baik, Rusak Ringan, Rusak Berat
    is_active BOOLEAN DEFAULT true,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ============================================
-- TABLE: konsultatif
-- Deskripsi: Data testimonial/kritik saran
-- ============================================
CREATE TABLE konsultatif (
    id SERIAL PRIMARY KEY,
    nama_pengisi VARCHAR(100) NOT NULL,
    instansi VARCHAR(150),
    email VARCHAR(100),
    no_telepon VARCHAR(20),
    pesan TEXT NOT NULL,
    rating INTEGER CHECK (rating >= 1 AND rating <= 5), -- 1-5 stars
    is_approved BOOLEAN DEFAULT false, -- untuk moderasi
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ============================================
-- TABLE: galeri
-- Deskripsi: Data kegiatan/agenda (foto)
-- ============================================
CREATE TABLE galeri (
    id SERIAL PRIMARY KEY,
    judul VARCHAR(200) NOT NULL,
    deskripsi TEXT,
    gambar_path VARCHAR(255) NOT NULL,
    tipe VARCHAR(20) NOT NULL CHECK (tipe IN ('agenda', 'kegiatan')), -- agenda = akan datang, kegiatan = sudah lewat
    tanggal_kegiatan DATE NOT NULL,
    lokasi VARCHAR(200),
    is_featured BOOLEAN DEFAULT false, -- untuk highlight di homepage
    is_active BOOLEAN DEFAULT true,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Index untuk optimasi query berdasarkan tipe dan tanggal
CREATE INDEX idx_galeri_tipe ON galeri(tipe);
CREATE INDEX idx_galeri_tanggal ON galeri(tanggal_kegiatan);

-- ============================================
-- TABLE: pengelola
-- Deskripsi: Data profil pengelola lab
-- ============================================
CREATE TABLE pengelola (
    id SERIAL PRIMARY KEY,
    nama_lengkap VARCHAR(100) NOT NULL,
    nip_nidn VARCHAR(30) UNIQUE,
    jabatan VARCHAR(100) NOT NULL, -- Kepala Lab, Teknisi, Peneliti, dll
    pendidikan_terakhir VARCHAR(50), -- S1, S2, S3
    bidang_keahlian VARCHAR(200),
    bio TEXT,
    foto_path VARCHAR(255),
    email VARCHAR(100),
    no_telepon VARCHAR(20),
    urutan_tampil INTEGER DEFAULT 999, -- untuk sorting di halaman publik
    is_active BOOLEAN DEFAULT true,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Index untuk optimasi sorting
CREATE INDEX idx_pengelola_urutan ON pengelola(urutan_tampil);

-- ============================================
-- TABLE: arsip
-- Deskripsi: Data dokumen penelitian/pengabdian
-- ============================================
CREATE TABLE arsip (
    id SERIAL PRIMARY KEY,
    judul VARCHAR(255) NOT NULL,
    kategori VARCHAR(20) NOT NULL CHECK (kategori IN ('penelitian', 'pengabdian')),
    abstrak TEXT,
    tahun_publikasi INTEGER NOT NULL,
    penerbit VARCHAR(200), -- nama jurnal, conference, dll
    file_pdf_path VARCHAR(255) NOT NULL,
    file_size_kb INTEGER, -- ukuran file dalam KB
    jumlah_download INTEGER DEFAULT 0,
    keywords VARCHAR(255), -- kata kunci dipisah koma
    doi VARCHAR(100), -- Digital Object Identifier (optional)
    is_featured BOOLEAN DEFAULT false, -- untuk highlight di homepage
    is_active BOOLEAN DEFAULT true,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Index untuk optimasi query
CREATE INDEX idx_arsip_kategori ON arsip(kategori);
CREATE INDEX idx_arsip_tahun ON arsip(tahun_publikasi);

-- ============================================
-- TABLE: arsip_pengelola (Junction Table)
-- Deskripsi: Relasi Many-to-Many antara arsip dan pengelola
-- ============================================
CREATE TABLE arsip_pengelola (
    id SERIAL PRIMARY KEY,
    arsip_id INTEGER NOT NULL,
    pengelola_id INTEGER NOT NULL,
    urutan_penulis INTEGER DEFAULT 1, -- urutan author (1=penulis pertama, 2=penulis kedua, dst)
    peran VARCHAR(50) DEFAULT 'Penulis', -- Penulis, Kontributor, Editor, dll
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    -- Foreign Keys
    CONSTRAINT fk_arsip
        FOREIGN KEY (arsip_id) 
        REFERENCES arsip(id) 
        ON DELETE CASCADE
        ON UPDATE CASCADE,
    
    CONSTRAINT fk_pengelola
        FOREIGN KEY (pengelola_id) 
        REFERENCES pengelola(id) 
        ON DELETE CASCADE
        ON UPDATE CASCADE,
    
    -- Unique constraint untuk mencegah duplikasi relasi
    CONSTRAINT unique_arsip_pengelola 
        UNIQUE (arsip_id, pengelola_id)
);

-- Index untuk optimasi query join
CREATE INDEX idx_arsip_pengelola_arsip ON arsip_pengelola(arsip_id);
CREATE INDEX idx_arsip_pengelola_pengelola ON arsip_pengelola(pengelola_id);

-- ============================================
-- SAMPLE DATA untuk Development/Testing
-- ============================================

-- Sample Pengelola
INSERT INTO pengelola (nama_lengkap, nip_nidn, jabatan, pendidikan_terakhir, bidang_keahlian, email, urutan_tampil) 
VALUES 
    ('Dr. Ahmad Santoso, S.Kom., M.Kom', '0123456789', 'Kepala Laboratorium', 'S3', 'Computer Networks & Security', 'ahmad.santoso@lab.ncs.ac.id', 1),
    ('Budi Rahardjo, S.T., M.T.', '0987654321', 'Teknisi Senior', 'S2', 'Network Infrastructure', 'budi.rahardjo@lab.ncs.ac.id', 2),
    ('Dr. Citra Dewi, S.Si., M.Kom', '1122334455', 'Peneliti', 'S3', 'Cybersecurity & Cryptography', 'citra.dewi@lab.ncs.ac.id', 3);

-- Sample Sarana
INSERT INTO sarana (nama_sarana, deskripsi, spesifikasi, jumlah, kondisi) 
VALUES 
    ('Server Rack 42U', 'Server rack untuk infrastruktur jaringan', 'Standard 19 inch, 42U height', 5, 'Baik'),
    ('Cisco Router 2911', 'Router untuk praktikum jaringan', 'Cisco 2911 with Security Bundle', 10, 'Baik'),
    ('Network Analyzer', 'Alat analisis traffic jaringan', 'Wireshark Hardware Edition', 3, 'Baik');

-- Sample Galeri (Agenda)
INSERT INTO galeri (judul, deskripsi, gambar_path, tipe, tanggal_kegiatan, lokasi, is_featured) 
VALUES 
    ('Workshop Cybersecurity 2025', 'Workshop tentang keamanan siber untuk mahasiswa', '/uploads/galeri/workshop-cyber.jpg', 'agenda', '2025-12-15', 'Lab NCS Lantai 3', true),
    ('Seminar Nasional Jaringan Komputer', 'Seminar nasional dengan tema Network Infrastructure', '/uploads/galeri/seminar-nasional.jpg', 'agenda', '2025-11-25', 'Auditorium Utama', true);

-- Sample Galeri (Kegiatan)
INSERT INTO galeri (judul, deskripsi, gambar_path, tipe, tanggal_kegiatan, lokasi) 
VALUES 
    ('Praktikum Routing & Switching', 'Kegiatan praktikum mahasiswa semester 5', '/uploads/galeri/praktikum-routing.jpg', 'kegiatan', '2025-10-20', 'Lab NCS', false),
    ('Kunjungan Industri ke Data Center', 'Mahasiswa berkunjung ke data center perusahaan telekomunikasi', '/uploads/galeri/kunjungan-industri.jpg', 'kegiatan', '2025-09-15', 'Jakarta', false);

-- Sample Arsip
INSERT INTO arsip (judul, kategori, abstrak, tahun_publikasi, penerbit, file_pdf_path, keywords, is_featured) 
VALUES 
    ('Implementasi Intrusion Detection System Menggunakan Machine Learning', 'penelitian', 'Penelitian tentang deteksi intrusi menggunakan algoritma machine learning...', 2024, 'Jurnal Teknik Informatika Vol. 12', '/uploads/arsip/ids-ml-2024.pdf', 'IDS, Machine Learning, Cybersecurity', true),
    ('Analisis Kinerja Routing Protocol OSPF dan EIGRP', 'penelitian', 'Perbandingan performa routing protocol pada topologi jaringan skala besar...', 2023, 'Proceeding SNATI 2023', '/uploads/arsip/routing-protocol-2023.pdf', 'OSPF, EIGRP, Routing Protocol', false),
    ('Pengabdian Masyarakat: Pelatihan Keamanan Internet untuk UMKM', 'pengabdian', 'Kegiatan pengabdian masyarakat memberikan pelatihan tentang keamanan internet...', 2024, 'Laporan Pengabdian Masyarakat 2024', '/uploads/arsip/pengabdian-umkm-2024.pdf', 'Internet Security, UMKM, Community Service', false);

-- Sample Relasi Arsip-Pengelola
-- Arsip 1 ditulis oleh Pengelola 1 dan 3
INSERT INTO arsip_pengelola (arsip_id, pengelola_id, urutan_penulis, peran) 
VALUES 
    (1, 1, 1, 'Penulis Utama'),
    (1, 3, 2, 'Penulis Pendamping');

-- Arsip 2 ditulis oleh Pengelola 2
INSERT INTO arsip_pengelola (arsip_id, pengelola_id, urutan_penulis, peran) 
VALUES 
    (2, 2, 1, 'Penulis Utama');

-- Arsip 3 ditulis oleh Pengelola 1, 2, dan 3
INSERT INTO arsip_pengelola (arsip_id, pengelola_id, urutan_penulis, peran) 
VALUES 
    (3, 1, 1, 'Ketua Tim'),
    (3, 2, 2, 'Anggota Tim'),
    (3, 3, 3, 'Anggota Tim');

-- Sample Konsultatif
INSERT INTO konsultatif (nama_pengisi, instansi, email, pesan, rating, is_approved) 
VALUES 
    ('Andi Wijaya', 'PT. Telkom Indonesia', 'andi@telkom.co.id', 'Laboratorium NCS memiliki fasilitas yang sangat lengkap dan modern. Sangat mendukung kegiatan penelitian.', 5, true),
    ('Siti Nurhaliza', 'Universitas Indonesia', 'siti@ui.ac.id', 'Pelayanan konsultasi sangat baik dan membantu dalam penelitian kami.', 4, true);

-- ============================================
-- FUNCTIONS & TRIGGERS (Optional - untuk automation)
-- ============================================

-- Function untuk update timestamp otomatis
CREATE OR REPLACE FUNCTION update_updated_at_column()
RETURNS TRIGGER AS $$
BEGIN
    NEW.updated_at = CURRENT_TIMESTAMP;
    RETURN NEW;
END;
$$ language 'plpgsql';

-- Trigger untuk auto-update updated_at di semua tabel
CREATE TRIGGER update_users_updated_at BEFORE UPDATE ON users 
    FOR EACH ROW EXECUTE FUNCTION update_updated_at_column();

CREATE TRIGGER update_profil_lab_updated_at BEFORE UPDATE ON profil_lab 
    FOR EACH ROW EXECUTE FUNCTION update_updated_at_column();

CREATE TRIGGER update_sarana_updated_at BEFORE UPDATE ON sarana 
    FOR EACH ROW EXECUTE FUNCTION update_updated_at_column();

CREATE TRIGGER update_konsultatif_updated_at BEFORE UPDATE ON konsultatif 
    FOR EACH ROW EXECUTE FUNCTION update_updated_at_column();

CREATE TRIGGER update_galeri_updated_at BEFORE UPDATE ON galeri 
    FOR EACH ROW EXECUTE FUNCTION update_updated_at_column();

CREATE TRIGGER update_pengelola_updated_at BEFORE UPDATE ON pengelola 
    FOR EACH ROW EXECUTE FUNCTION update_updated_at_column();

CREATE TRIGGER update_arsip_updated_at BEFORE UPDATE ON arsip 
    FOR EACH ROW EXECUTE FUNCTION update_updated_at_column();

-- ============================================
-- VIEWS (Optional - untuk kemudahan query)
-- ============================================

-- View untuk menampilkan arsip beserta pengelolanya
CREATE OR REPLACE VIEW view_arsip_with_pengelola AS
SELECT 
    a.id AS arsip_id,
    a.judul,
    a.kategori,
    a.tahun_publikasi,
    a.penerbit,
    a.file_pdf_path,
    a.is_featured,
    a.jumlah_download,
    p.id AS pengelola_id,
    p.nama_lengkap AS nama_pengelola,
    p.jabatan AS jabatan_pengelola,
    ap.urutan_penulis,
    ap.peran
FROM arsip a
LEFT JOIN arsip_pengelola ap ON a.id = ap.arsip_id
LEFT JOIN pengelola p ON ap.pengelola_id = p.id
WHERE a.is_active = true AND p.is_active = true
ORDER BY a.tahun_publikasi DESC, ap.urutan_penulis ASC;

-- View untuk menampilkan pengelola beserta jumlah arsipnya
CREATE OR REPLACE VIEW view_pengelola_with_stats AS
SELECT 
    p.id,
    p.nama_lengkap,
    p.nip_nidn,
    p.jabatan,
    p.bidang_keahlian,
    p.foto_path,
    p.email,
    p.urutan_tampil,
    COUNT(ap.arsip_id) AS jumlah_arsip
FROM pengelola p
LEFT JOIN arsip_pengelola ap ON p.id = ap.pengelola_id
WHERE p.is_active = true
GROUP BY p.id
ORDER BY p.urutan_tampil ASC;

-- ============================================
-- NOTES & DOCUMENTATION
-- ============================================

-- Cara menjalankan script ini:
-- 1. Buat database baru: CREATE DATABASE db_lab_ncs_pbl;
-- 2. Connect ke database: \c db_lab_ncs_pbl
-- 3. Run script ini: \i schema.sql
--
-- Default Admin Login:
-- Username: admin
-- Password: admin123
--
-- Struktur Folder Upload yang direkomendasikan:
-- /uploads/
--   /galeri/       (untuk foto kegiatan/agenda)
--   /pengelola/    (untuk foto profil pengelola)
--   /arsip/        (untuk file PDF arsip)
--   /sarana/       (untuk gambar sarana prasarana)
--   /profil/       (untuk logo lab)

-- ============================================
-- END OF SCHEMA
-- ============================================
