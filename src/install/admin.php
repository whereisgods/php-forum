<?php
include '../connect.php';

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    if (empty($username) || empty($password)) {
        $errors[] = "Kullanıcı adı ve şifre alanları boş bırakılamaz.";
    } else {
        // Parolayı hashle
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        // Admin hesabını oluştur
        $sql = "INSERT INTO `admin` (`username`, `password`) VALUES ('$username', '$hashedPassword')";

        if ($conn->query($sql) === TRUE) {
            // Admin hesabı başarıyla oluşturuldu

            // Dosyaları sil
            $filesToDelete = ['index.php', 'sql.php', 'admin.php'];
            foreach ($filesToDelete as $file) {
                if (file_exists($file)) {
                    unlink($file); // Dosyayı sil
                }
            }
            
            $errors[] = 'İşlemler tamam, artık dosyalar başarıyla silindi. <a href="../">Ana sayfaya dön</a>.';
        } else {
            $errors[] = "Admin hesabını oluştururken hata oluştu: " . $conn->error;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Oluştur</title>
    <style>
        .form-group {
            width: 350px;
        }
        </style>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container">
        <a class="navbar-brand" href="../">Ana Sayfa</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav">
                <li class="nav-item">
                     <a class="nav-link" href="../register">Kayıt ol</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="../login">Giriş yap</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="../create">Konu oluştur</a>
                </li>
            </ul>
        </div>
    </div>
</nav>
<div class="container mt-5">
    <div class="card">
        <div class="card-body">
            <h1 class="card-title">Admin Oluştur</h1>
            
            <?php foreach ($errors as $error): ?>
                <div class="alert alert-danger"><?= $error ?></div>
            <?php endforeach; ?>

            <form method="POST">
                <div class="form-group">
                    <label for="username">Kullanıcı Adı:</label>
                    <input type="text" class="form-control" name="username" required>
                </div>
                <div class="form-group">
                    <label for="password">Şifre:</label>
                    <input type="password" class="form-control" name="password" required>
                </div>
                <button type="submit" class="btn btn-primary">Oluştur</button>
            </form>
        </div>
    </div>
</div>
</body>
</html>