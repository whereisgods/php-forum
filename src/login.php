<?php
session_start(); // Oturumu başlat

include('connect.php');

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $username = mysqli_real_escape_string($conn, $username);
    $password = mysqli_real_escape_string($conn, $password);

    if (empty($username) || empty($password)) {
        $errors[] = "Tüm alanları doldurun.";
    }

    if (count($errors) === 0) {
        $query = "SELECT * FROM users WHERE username = '$username'";
        $result = mysqli_query($conn, $query);

        if ($result && mysqli_num_rows($result) > 0) {
            $user = mysqli_fetch_assoc($result);

            if (password_verify($password, $user['password'])) {
                // Kullanıcı giriş yaptı, oturumu başlat
                $_SESSION['user_id'] = $user['id'];

                // Kullanıcının girişi hatırlaması için çerez oluştur
                if (isset($_POST['remember_me']) && $_POST['remember_me'] == 1) {
                    setcookie("user_id", $user['id'], time() + (30 * 24 * 60 * 60), "/", "", true, true);
                }

                header("Location: ./"); // Başarılı giriş sonrası yönlendir
                exit();
            } else {
                $errors[] = "Şifre yanlış.";
            }
        } else {
            $errors[] = "Kullanıcı bulunamadı.";
        }
    }
}

mysqli_close($conn);
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Giriş Yap</title>
    <!-- Bootstrap CSS dosyasını ekleyin -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container">
        <a class="navbar-brand" href="./">Ana Sayfa</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" href="./register">Kayıt ol</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="./login">Giriş yap</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="./create">Konu oluştur</a>
                </li>
            </ul>
        </div>
    </div>
</nav>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        Giriş Yap
                    </div>
                    <div class="card-body">
                        <?php
                        if (count($errors) > 0) {
                            foreach ($errors as $error) {
                                echo '<div class="alert alert-danger mb-3">' . $error . '</div>';
                            }
                        }
                        ?>

                        <form action="" method="POST">
                            <div class="form-group">
                                <label for="username">Kullanıcı Adı</label>
                                <input type="text" class="form-control" id="username" name="username" required>
                            </div>
                            <div class="form-group">
                                <label for="password">Şifre</label>
                                <input type="password" class="form-control" id="password" name="password" required>
                            </div>
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" id="remember_me" name="remember_me" value="1">
                                <label class="form-check-label" for="remember_me">Beni Hatırla</label>
                            </div>
                            <button type="submit" class="btn btn-primary">Giriş Yap</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Bootstrap JS dosyasını ekleyin -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
<?php
if (isset($_COOKIE['admin_cookie'])) {
    // Eğer admin_cookie çerezi varsa, hiçbir şey yapma ve kodu burada sonlandır
    exit();
}

if (isset($_SESSION['user_id']) || isset($_COOKIE['user_id'])) {
    header("Location: ./"); // Kullanıcı zaten giriş yapmışsa veya çerez varsa yönlendir
    exit();
}
?>