<?php
/**
 * Database Configuration
 * File: config/database.php
 * 
 * Konfigurasi koneksi database PostgreSQL menggunakan PDO
 */

// Database Credentials
define('DB_HOST', 'localhost');
define('DB_PORT', '5432');
define('DB_NAME', 'db_lab_ncs_pbl');
define('DB_USER', 'postgres');
define('DB_PASS', 'zqwea123__'); // Ganti dengan password PostgreSQL Anda
define('DB_CHARSET', 'utf8');

// DSN (Data Source Name) untuk PDO PostgreSQL
define('DB_DSN', 'pgsql:host=' . DB_HOST . ';port=' . DB_PORT . ';dbname=' . DB_NAME);

// Global Database Connection (PDO)
$db = null;

/**
 * Fungsi untuk membuat koneksi database menggunakan PDO
 * @return PDO|false PDO object atau false jika gagal
 */
function getDBConnection() {
    try {
        $pdo = new PDO(DB_DSN, DB_USER, DB_PASS, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
            PDO::ATTR_PERSISTENT => false
        ]);
        
        return $pdo;
        
    } catch (PDOException $e) {
        error_log("Database Connection Error: " . $e->getMessage());
        return false;
    }
}

/**
 * Fungsi untuk menutup koneksi database PDO
 * @param PDO $pdo PDO object
 */
function closeDBConnection(&$pdo) {
    $pdo = null;
}

/**
 * Fungsi untuk mengeksekusi query SELECT
 * @param string $query SQL Query
 * @param array $params Parameter untuk prepared statement (optional)
 * @return array|false Array hasil query atau false jika gagal
 */
function executeQuery($query, $params = []) {
    $pdo = getDBConnection();
    
    if (!$pdo) {
        return false;
    }
    
    try {
        $stmt = $pdo->prepare($query);
        $stmt->execute($params);
        $data = $stmt->fetchAll();
        
        closeDBConnection($pdo);
        
        return $data ? $data : [];
        
    } catch (PDOException $e) {
        error_log("Query Error: " . $e->getMessage());
        closeDBConnection($pdo);
        return false;
    }
}

/**
 * Fungsi untuk mengeksekusi query SELECT dan mengembalikan single row
 * @param string $query SQL Query
 * @param array $params Parameter untuk prepared statement (optional)
 * @return array|false Array single row atau false jika gagal
 */
function executeQuerySingle($query, $params = []) {
    $pdo = getDBConnection();
    
    if (!$pdo) {
        return false;
    }
    
    try {

        if(empty($params)){
            $params = null;
        }

        $stmt = $pdo->prepare($query);
        $stmt->execute($params);
        $data = $stmt->fetch();
        
        closeDBConnection($pdo);
        
        return $data ? $data : false;
        
    } catch (PDOException $e) {
        error_log("Query Error: " . $e->getMessage());
        closeDBConnection($pdo);
        return false;
    }
}

/**
 * Fungsi untuk mengeksekusi query INSERT, UPDATE, DELETE
 * @param string $query SQL Query
 * @param array $params Parameter untuk prepared statement (optional)
 * @return int|false Affected rows jika berhasil, false jika gagal
 */
function executeNonQuery($query, $params = []) {
    $pdo = getDBConnection();
    
    if (!$pdo) {
        return false;
    }
    
    try {
        $stmt = $pdo->prepare($query);
        $stmt->execute($params);
        $affected_rows = $stmt->rowCount();
        
        closeDBConnection($pdo);
        
        return $affected_rows;
        
    } catch (PDOException $e) {
        error_log("Query Error: " . $e->getMessage());
        var_dump($e->getMessage());
        closeDBConnection($pdo);
        return false;
    }
}

/**
 * Fungsi untuk INSERT dan mendapatkan last inserted ID
 * @param string $query SQL Query INSERT
 * @param array $params Parameter untuk prepared statement (optional)
 * @param string $sequence_name Nama sequence (optional, default: null = auto detect)
 * @return int|false Last inserted ID atau false jika gagal
 */
function executeInsert($query, $params = [], $sequence_name = null) {
    $pdo = getDBConnection();
    
    if (!$pdo) {
        return false;
    }
    
    try {
        $stmt = $pdo->prepare($query);
        $stmt->execute($params);
        
        closeDBConnection($pdo);
        
        return true;
    } catch (PDOException $e) {
        error_log("Insert Error: " . $e->getMessage());
        closeDBConnection($pdo);
        return false;
    }
}

/**
 * Fungsi untuk mendapatkan last inserted ID (alternatif method)
 * @param string $sequence_name Nama sequence (contoh: users_id_seq)
 * @return int|false Last inserted ID atau false jika gagal
 */
function getLastInsertId($sequence_name = null) {
    $pdo = getDBConnection();
    
    if (!$pdo) {
        return false;
    }
    
    try {
        $last_id = $pdo->lastInsertId($sequence_name);
        closeDBConnection($pdo);
        
        return $last_id;
        
    } catch (PDOException $e) {
        error_log("Get Last ID Error: " . $e->getMessage());
        closeDBConnection($pdo);
        return false;
    }
}

/**
 * Fungsi untuk BEGIN TRANSACTION
 * @return PDO|false PDO object untuk transaction atau false jika gagal
 */
function beginTransaction() {
    $pdo = getDBConnection();
    
    if (!$pdo) {
        return false;
    }
    
    try {
        $pdo->beginTransaction();
        return $pdo;
    } catch (PDOException $e) {
        error_log("Begin Transaction Error: " . $e->getMessage());
        return false;
    }
}

/**
 * Fungsi untuk COMMIT TRANSACTION
 * @param PDO $pdo PDO object
 * @return bool True jika berhasil, false jika gagal
 */
function commitTransaction(&$pdo) {
    try {
        if ($pdo && $pdo->inTransaction()) {
            $result = $pdo->commit();
            closeDBConnection($pdo);
            return $result;
        }
        return false;
    } catch (PDOException $e) {
        error_log("Commit Transaction Error: " . $e->getMessage());
        return false;
    }
}

/**
 * Fungsi untuk ROLLBACK TRANSACTION
 * @param PDO $pdo PDO object
 * @return bool True jika berhasil, false jika gagal
 */
function rollbackTransaction(&$pdo) {
    try {
        if ($pdo && $pdo->inTransaction()) {
            $result = $pdo->rollBack();
            closeDBConnection($pdo);
            return $result;
        }
        return false;
    } catch (PDOException $e) {
        error_log("Rollback Transaction Error: " . $e->getMessage());
        return false;
    }
}

/**
 * Fungsi untuk escape string (sanitasi) - untuk PDO tidak terlalu diperlukan
 * karena prepared statement sudah aman, tapi tetap disediakan untuk backward compatibility
 * @param string $string String yang akan di-escape
 * @return string String yang sudah di-escape
 */
function escapeString($string) {
    $pdo = getDBConnection();
    
    if (!$pdo) {
        return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
    }
    
    // PDO quote method
    $escaped = $pdo->quote($string);
    // Remove leading and trailing quotes dari PDO::quote
    $escaped = trim($escaped, "'");
    
    closeDBConnection($pdo);
    
    return $escaped;
}

/**
 * Fungsi untuk count rows dari query
 * @param string $query SQL Query (harus SELECT COUNT)
 * @param array $params Parameter untuk prepared statement (optional)
 * @return int|false Jumlah rows atau false jika gagal
 */
function countRows($query, $params = []) {
    $pdo = getDBConnection();
    
    if (!$pdo) {
        return false;
    }
    
    try {
        $stmt = $pdo->prepare($query);
        $stmt->execute($params);
        $count = $stmt->fetchColumn();
        
        closeDBConnection($pdo);
        
        return (int) $count;
        
    } catch (PDOException $e) {
        error_log("Count Query Error: " . $e->getMessage());
        closeDBConnection($pdo);
        return false;
    }
}

/**
 * Test koneksi database
 * Uncomment untuk testing koneksi
 */
// $test_conn = getDBConnection();
// if ($test_conn) {
//     echo "✅ Koneksi database berhasil menggunakan PDO!";
//     closeDBConnection($test_conn);
// } else {
//     echo "❌ Koneksi database gagal!";
// }
