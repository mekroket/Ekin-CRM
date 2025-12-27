<?php
session_start();
require_once '../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_SESSION['user_id'])) {
        http_response_code(403);
        exit('Yetkisiz erişim');
    }

    $title = $_POST['title'];
    $project_id = $_POST['project_id'];
    $status = $_POST['status'];

    // Şemada 'Bekliyor' yok, 'Yapılacak' var. Eğer 'Bekliyor' gelirse 'Yapılacak' olarak kaydet.
    if ($status === 'Bekliyor') {
        $status = 'Yapılacak';
    }

    // tasks tablosunda description ve assigned_to sütunları yok.
    // Sadece title, project_id, status ekliyoruz.
    $stmt = $pdo->prepare("INSERT INTO tasks (title, project_id, status) VALUES (?, ?, ?)");
    $result = $stmt->execute([$title, $project_id, $status]);

    if ($result) {
        echo json_encode(['success' => true]);
    } else {
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => 'Veritabanı hatası']);
    }
}
?>