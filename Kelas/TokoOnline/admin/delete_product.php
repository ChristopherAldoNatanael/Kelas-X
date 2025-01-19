<?php
session_start();
include '../db/connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $product_id = $_POST['product_id'];

    // Siapkan query untuk menghapus produk dari database
    $stmt = $conn->prepare("DELETE FROM products WHERE id = ?");
    $stmt->bind_param("i", $product_id);

    if ($stmt->execute()) {
        header("Location: home.php"); // Redirect setelah penghapusan
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
}
