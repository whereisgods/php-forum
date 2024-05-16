<?php
// "admin_cookie" çerezi kontrolü
if(isset($_COOKIE['admin_cookie'])) {
    // "admin_cookie" çerezi varsa, kullanıcıyı yönlendir
    header("Location: panel"); // Yönlendirilecek sayfanın URL'sini buraya ekleyin
    exit; // İşlemi sonlandır
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Girişi</title>
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
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">Admin Girişi</div>
                    <div class="card-body">
<?php

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    include("../connect.php");
    
    $username = $_POST["username"];
    $password = $_POST["password"];
    
    $sql = "SELECT * FROM admin WHERE username = '$username'";
    $result = $conn->query($sql);
    
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        
        // Veritabanından alınan hash ile kullanıcının girdiği şifreyi karşılaştır
        if (password_verify($password, $row["password"])) {
            // Kullanıcı girişi başarılı
            $cookie_name = "admin_cookie";
            $cookie_value = "admin";
            $expiry_time = time() + (30 * 24 * 60 * 60); // 30 gün
            setcookie($cookie_name, $cookie_value, $expiry_time, "/");
            header("Location: panel"); // Admin paneline yönlendirme
        } else {
            // Kullanıcı adı veya şifre hatalı
            echo '<div class="alert alert-danger">Kullanıcı adı veya şifre hatalı.</div>';
        }
    } else {
        // Kullanıcı adı hatalı
        echo '<div class="alert alert-danger">Kullanıcı adı hatalı.</div>';
    }
    
    $conn->close();
}
?>
                        <form method="POST">
                            <div class="mb-3">
                                <label for="username" class="form-label">Kullanıcı Adı:</label>
                                <input type="text" class="form-control" id="username" name="username" required>
                            </div>
                            <div class="mb-3">
                                <label for="password" class="form-label">Şifre:</label>
                                <input type="password" class="form-control" id="password" name="password" required>
                            </div>
                            <button type="submit" class="btn btn-primary">Giriş Yap</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>