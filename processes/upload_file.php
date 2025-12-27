<?php
session_start();
require_once '../includes/db.php';

if (!isset($_SESSION['user_id'])) {
    die(json_encode(['success' => false, 'message' => 'Oturum açmanız gerekiyor.']));
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die(json_encode(['success' => false, 'message' => 'Geçersiz istek.']));
}

$project_id = $_POST['project_id'] ?? null;
if (!$project_id) {
    die(json_encode(['success' => false, 'message' => 'Proje ID eksik.']));
}

if (!isset($_FILES['file']) || $_FILES['file']['error'] !== UPLOAD_ERR_OK) {
    die(json_encode(['success' => false, 'message' => 'Dosya yükleme hatası.']));
}

$file = $_FILES['file'];
$filename = $file['name'];
$tmp_name = $file['tmp_name'];

// Dosya uzantısını kontrol et (Güvenlik)
$allowed_extensions = ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'jpg', 'jpeg', 'png', 'zip', 'rar'];
$extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

if (!in_array($extension, $allowed_extensions)) {
    die(json_encode(['success' => false, 'message' => 'Bu dosya türüne izin verilmiyor.']));
}

// Dosya ismini benzersiz yap
$new_filename = uniqid() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '', $filename);
$upload_dir = '../uploads/project_files/';

if (!is_dir($upload_dir)) {
    mkdir($upload_dir, 0777, true);
}

$destination = $upload_dir . $new_filename;

if (move_uploaded_file($tmp_name, $destination)) {
    // Veritabanına kaydet
    $stmt = $pdo->prepare("INSERT INTO project_files (project_id, filename, filepath) VALUES (?, ?, ?)");
    if ($stmt->execute([$project_id, $filename, 'uploads/project_files/' . $new_filename])) {
        echo json_encode(['success' => true, 'message' => 'Dosya başarıyla yüklendi.']);
    } else {
        unlink($destination); // Veritabanı hatası olursa dosyayı sil
        echo json_encode(['success' => false, 'message' => 'Veritabanı hatası.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Dosya taşınamadı.']);
}
?>