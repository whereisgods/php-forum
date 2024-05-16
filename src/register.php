<?php
// Veritabanı bağlantısını yapılandırma
include('connect.php');

// Hata dizisi
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Kullanıcı adı ve şifre alınır
    $username = $_POST['username'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // SQL enjeksiyonundan koruma için kullanıcı girdileri temizlenir
    $username = mysqli_real_escape_string($conn, $username);
    $password = mysqli_real_escape_string($conn, $password);
    $confirm_password = mysqli_real_escape_string($conn, $confirm_password);

    // Alanların boş olup olmadığını kontrol edin
    if (empty($username) || empty($password) || empty($confirm_password)) {
        $errors[] = "Tüm alanları doldurun.";
    }

    // Şifrelerin eşleşip eşleşmediğini kontrol edin
    if ($password !== $confirm_password) {
        $errors[] = "Şifreler eşleşmiyor.";
    }

    // Hatalar yoksa, kullanıcıyı veritabanına ekleyin
    if (count($errors) === 0) {
        // Kullanıcı adının benzersiz olup olmadığını kontrol edin
        $query = "SELECT * FROM users WHERE username='$username'";
        $result = mysqli_query($conn, $query);

        if (mysqli_num_rows($result) > 0) {
            $errors[] = "Bu kullanıcı adı zaten kullanılıyor. Lütfen farklı bir kullanıcı adı seçin.";
        } else {
            // Şifreyi hashleyin ve veritabanına ekleyin
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $insert_query = "INSERT INTO users (username, password) VALUES ('$username', '$hashed_password')";

            if (mysqli_query($conn, $insert_query)) {
                // Kayıt başarılı, kullanıcıyı oturumla işaretleyin
                $_SESSION['user_id'] = mysqli_insert_id($conn);

                // Kullanıcıyı hoş geldiniz sayfasına yönlendirin
                header("Location: ./");
                exit();
            } else {
                $errors[] = "Kayıt sırasında bir hata oluştu: " . mysqli_error($conn);
            }
        }
    }
}

// Veritabanı bağlantısını kapatma
mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kayıt Ol</title>
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
                        Kayıt Ol
                    </div>
                    <div class="card-body">
                        <?php
                        if (count($errors) > 0) {
                            foreach ($errors as $error) {
                                echo '<div class="alert alert-danger mb-3">' . $error . '</div>';
                            }
                        }
                        ?>

                        <form id="registerForm" action="" method="POST">
                            <div class="form-group">
                                <label for="username">Kullanıcı Adı</label>
                                <input type="text" class="form-control" id="username" name="username" required>
                            </div>
                            <div class="form-group">
                                <label for="password">Şifre</label>
                                <input type="password" class="form-control" id="password" name="password" required>
                            </div>
                            <div class="form-group">
                                <label for="confirm_password">Şifre Tekrarı</label>
                                <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                            </div>
                            <button type="submit" class="btn btn-primary">Kayıt Ol</button>
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
    <script>
    // Formun birden fazla gönderilmesini engellemek için spam koruması ekle
    var formSubmitted = false;
    document.getElementById("registerForm").addEventListener("submit", function(event) {
        if (formSubmitted) {
            event.preventDefault();
            alert("Form zaten gönderildi, lütfen bekleyin.");
        } else {
            formSubmitted = true;
            setTimeout(function() {
                formSubmitted = false;
            }, 10000); // 10 saniye beklet
        }
    });
</script>

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
