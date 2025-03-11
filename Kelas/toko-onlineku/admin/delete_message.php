<?php
// File: delete_message.php
require_once '../includes/config.php';
header('Content-Type: application/json');

// Ambil JSON data
$json = file_get_contents('php://input');
$data = json_decode($json, true);

try {
    if (!isset($data['message_id'])) {
        throw new Exception('Message ID not provided');
    }

    $message_id = (int)$data['message_id'];

    // Verifikasi message exists sebelum delete
    $check_query = "SELECT message_id FROM messages WHERE message_id = :message_id";
    $check_stmt = $koneksi->prepare($check_query);
    $check_stmt->bindParam(':message_id', $message_id, PDO::PARAM_INT);
    $check_stmt->execute();

    if ($check_stmt->rowCount() === 0) {
        throw new Exception('Message not found');
    }

    // Lakukan delete
    $delete_query = "DELETE FROM messages WHERE message_id = :message_id";
    $delete_stmt = $koneksi->prepare($delete_query);
    $delete_stmt->bindParam(':message_id', $message_id, PDO::PARAM_INT);

    if ($delete_stmt->execute()) {
        echo json_encode([
            'success' => true,
            'message' => 'Message deleted successfully'
        ]);
    } else {
        throw new Exception('Failed to delete message');
    }
} catch (Exception $e) {
    error_log("Delete message error: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
