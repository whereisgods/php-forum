<?php
session_start();

// Parsedown kütüphanesini dahil edin
require 'Parsedown.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login");
    exit();
}

include('connect.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $message = $_POST['message'];
    $topic_id = $_POST['topic_id'];

    // Markdown'dan HTML'e dönüştür
    $parsedown = new Parsedown();
    $message = $parsedown->text($message);

    $message = mysqli_real_escape_string($conn, $message);
    $user_id = $_SESSION['user_id'];
    
    // Kullanıcının adını veritabanından alın
    $user_query = "SELECT username FROM users WHERE id = '$user_id'";
    $user_result = mysqli_query($conn, $user_query);
    $user_row = mysqli_fetch_assoc($user_result);
    $username = $user_row['username'];

    // Yeni mesajı ekleyin
    $query = "INSERT INTO messages (message, user_id, topic_id, username) VALUES ('$message', '$user_id', '$topic_id', '$username')";
    if (mysqli_query($conn, $query)) {
        header("Location: topic.php?id=$topic_id");
        exit();
    } else {
        echo "Mesaj eklenirken bir hata oluştu: " . mysqli_error($conn);
    }
}

mysqli_close($conn);
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cevap Yaz - <?php echo file_get_contents('site.txt'); ?></title>
    <link rel="icon" href="<?php echo file_get_contents('favicon.txt'); ?>">
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
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        Cevap Yaz
                    </div>
                    <div class="card-body">
                        <?php
                        if (isset($_GET['topic_id']) && is_numeric($_GET['topic_id'])) {
                            $topic_id = $_GET['topic_id'];
                        } else {
                            header("Location: ../"); // Geçersiz konu kimliği, forum ana sayfasına yönlendir
                            exit();
                        }
                        ?>

                        <form id="replyForm" action="" method="POST">
                            <input type="hidden" name="topic_id" value="<?php echo $topic_id; ?>">
                            <div class="form-group">
                                <label for="message">Mesaj</label>
                                <textarea class="form-control" id="message" name="message" rows="4" required></textarea>
                            </div>
                            <button type="submit" class="btn btn-primary" id="submitButton">Cevap Yaz</button>
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
        document.getElementById("replyForm").addEventListener("submit", function(event) {
            if (formSubmitted) {
                event.preventDefault();
                alert("Form zaten gönderildi, lütfen bekleyin.");
            } else {
                formSubmitted = true;
                document.getElementById("submitButton").disabled = true;
                setTimeout(function() {
                    formSubmitted = false;
                    document.getElementById("submitButton").disabled = false;
                }, 5000); // 5 saniye beklet
            }
        });
    </script>
</body>
</html>