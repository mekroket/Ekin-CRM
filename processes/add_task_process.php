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

    $title = $_POST['title'] ?? '';
    $project_id = $_POST['project_id'] ?? '';
    $status = $_POST['status'] ?? 'Yapılacak';

    if (empty($title) || empty($project_id)) {
        echo json_encode(['success' => false, 'error' => 'Eksik bilgi']);
        exit;
    }

    if ($status === 'Bekliyor') {
        $status = 'Yapılacak';
    }

    try {
        $stmt = $pdo->prepare("INSERT INTO tasks (title, project_id, status) VALUES (?, ?, ?)");
        $result = $stmt->execute([$title, $project_id, $status]);

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