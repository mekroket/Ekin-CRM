<?php
session_start();
require_once '../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Şimdilik basit bir kontrol, gerçek sistemde password_verify kullanılacak
    // İlk kullanıcıyı oluşturmak için bir kontrol ekleyelim
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        header('Location: ../index.php');
        exit;
    } else {
        // Eğer kullanıcı yoksa ve veritabanı boşsa ilk kullanıcıyı oluştur (Test amaçlı)
        $count = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
        if ($count == 0 && $username === 'admin' && $password === 'admin') {
            $hashed_password = password_hash('admin', PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO users (username, password, email) VALUES (?, ?, ?)");
            $stmt->execute(['admin', $hashed_password, 'admin@example.com']);

            $_SESSION['user_id'] = $pdo->lastInsertId();
            $_SESSION['username'] = 'admin';
            header('Location: ../index.php');
            exit;
        }
        header('Location: ../login.php?error=1');
        exit;
    }
}
?>
