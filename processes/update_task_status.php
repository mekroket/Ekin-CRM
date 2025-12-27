<?php
ini_set('display_errors', 0);
header('Content-Type: application/json');

session_start();
require_once '../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_SESSION['user_id'])) {
        http_response_code(403);
        echo json_encode(['success' => false, 'error' => 'Yetkisiz erişim']);
        exit;
    }

    $id = $_POST['id'] ?? '';
    $status = $_POST['status'] ?? '';

    if (empty($id) || empty($status)) {
        echo json_encode(['success' => false, 'error' => 'Eksik bilgi']);
        exit;
    }

    try {
        $stmt = $pdo->prepare("UPDATE tasks SET status = ? WHERE id = ?");
        $result = $stmt->execute([$status, $id]);

        if ($result) {
            echo json_encode(['success' => true]);
        } else {
            http_response_code(500);
            echo json_encode(['success' => false, 'error' => 'Veritabanı hatası']);
        }
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => 'Veritabanı hatası: ' . $e->getMessage()]);
    }
}
?>