<?php
session_start();
require_once '../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_SESSION['user_id'])) {
        http_response_code(403);
        exit('Yetkisiz erişim');
    }

    $title = $_POST['title'];
    $description = $_POST['description'];
    $project_id = $_POST['project_id'];
    $status = 'Bekliyor'; // Varsayılan durum
    $assigned_to = $_SESSION['user_id']; // Şimdilik görevi ekleyen kişiye ata

    $stmt = $pdo->prepare("INSERT INTO tasks (title, description, project_id, status, assigned_to) VALUES (?, ?, ?, ?, ?)");
    $result = $stmt->execute([$title, $description, $project_id, $status, $assigned_to]);

    if ($result) {
        echo json_encode(['success' => true]);
    } else {
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => 'Veritabanı hatası']);
    }
}
?>