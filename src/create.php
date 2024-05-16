<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login"); // Kullanıcı girişi yapılmamışsa, giriş sayfasına yönlendir
    exit();
}

include('connect.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $topic_title = $_POST['topic_title'];
    $message = $_POST['message'];

    $topic_title = mysqli_real_escape_string($conn, $topic_title);
    $message = mysqli_real_escape_string($conn, $message);

    $user_id = $_SESSION['user_id'];

    // Kullanıcının adını alın
    $user_query = "SELECT username FROM users WHERE id = '$user_id'";
    $user_result = mysqli_query($conn, $user_query);

    if ($user_result && mysqli_num_rows($user_result) > 0) {
        $user_row = mysqli_fetch_assoc($user_result);
        $username = $user_row['username'];

        // Yeni konuyu ekleyin ve kullanıcının adını ekleyin
        $query = "INSERT INTO topics (topic_title, username) VALUES ('$topic_title', '$username')";
        if (mysqli_query($conn, $query)) {
            $topic_id = mysqli_insert_id($conn);

            // Mesajı yeni konuya ekleyin
            $message_query = "INSERT INTO messages (message, user_id, topic_id) VALUES ('$message', '$user_id', '$topic_id')";
            if (mysqli_query($conn, $message_query)) {
                // Başarılı bir şekilde konu ve mesaj oluşturuldu, kullanıcıyı yönlendirin
                header("Location: topic?id=$topic_id");
                exit();
            } else {
                echo "Mesaj eklenirken bir hata oluştu: " . mysqli_error($conn);
            }
        } else {
            echo "Konu eklenirken bir hata oluştu: " . mysqli_error($conn);
        }
    } else {
        echo "Kullanıcı bilgileri alınamadı.";
    }
}

mysqli_close($conn);
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Konu Oluştur</title>
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
                        Konu Oluştur
                    </div>
                    <div class="card-body">
                        <form id="createTopicForm" action="" method="POST">
                            <div class="form-group">
                                <label for="topic_title">Başlık</label>
                                <input type="text" class="form-control" id="topic_title" name="topic_title" required>
                            </div>
                            <div class="form-group">
                                <label for="message">Mesaj</label>
                                <textarea class="form-control" id="message" name="message" rows="4" required></textarea>
                            </div>
                            <button type="submit" class="btn btn-primary">Konu Oluştur</button>
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
        document.getElementById("createTopicForm").addEventListener("submit", function(event) {
            if (formSubmitted) {
                event.preventDefault();
                alert("Form zaten gönderildi, lütfen bekleyin.");
            } else {
                formSubmitted = true;
                setTimeout(function() {
                    formSubmitted = false;
                }, 5000); // 5 saniye beklet
            }
        });
    </script>
</body>
</html>