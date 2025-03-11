<?php
// includes/config.php
try {
    $host = 'localhost';
    $dbname = 'elitewatch';
    $username = 'root';
    $password = '';
    
    $koneksi = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $koneksi->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    echo "Koneksi Database Gagal: " . $e->getMessage();
    die();
}
?>