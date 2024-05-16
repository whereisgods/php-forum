<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Başla</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        .form-group {
            width: 500px;
        }
        </style>
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
            <h1 class="card-title">MySQL Bağlantı Bilgileri</h1>
            <?php
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $host = $_POST['host'];
                $db_username = $_POST['db_username'];
                $db_password = $_POST['db_password'];
                $database = $_POST['database'];
                $connect_file = fopen('../connect.php', 'w');
                if ($connect_file) {
                    $content = <<<EOD
<?php
\$host = '$host';
\$db_username = '$db_username';
\$db_password = '$db_password';
\$database = '$database';
\$conn = new mysqli(\$host, \$db_username, \$db_password, \$database);
if (\$conn->connect_error) {
    die("Bağlantı hatası: " . \$conn->connect_error);
}
?>
EOD;
                    if (fwrite($connect_file, $content) === false) {
                        echo '<div class="alert alert-danger">connect.php dosyasına yazılamadı.</div>';
                    } else {
                        fclose($connect_file);
                        include 'sql.php';
                        header('Location: admin.php');
                        exit();
                    }
                } else {
                    echo '<div class="alert alert-danger">connect.php dosyası açılamadı veya yazılamadı.</div>';
                }
            }
            ?>
            <form method="POST">
                <div class="form-group">
                    <label for="host">Host:</label>
                    <input type="text" class="form-control" name="host" required>
                </div>
                <div class="form-group">
                    <label for="username">Kullanıcı Adı:</label>
                    <input type="text" class="form-control" name="db_username" required>
                </div>
                <div class="form-group">
                    <label for="password">Şifre:</label>
                    <input type="password" class="form-control" name="db_password">
                </div>
                <div class="form-group">
                    <label for="database">Veritabanı Adı:</label>
                    <input type="text" class="form-control" name="database" required>
                </div>
                <button type="submit" class="btn btn-primary">Devam</button>
            </form>
        </div>
    </div>
</div>
</body>
</html>