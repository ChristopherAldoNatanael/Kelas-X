<?php

include '../includes/config.php';
// File: stats_calculation.php

try {
    // Query untuk menghitung total pesan
    $query = "SELECT COUNT(*) as total FROM messages";
    $stmt = $koneksi->prepare($query);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $stats['total_messages'] = $result['total'];
    
} catch (PDOException $e) {
    error_log("Error in stats calculation: " . $e->getMessage());
    $stats['total_messages'] = 0;
}
?>