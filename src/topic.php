<?php
session_start();

include('connect.php');

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $topic_id = $_GET['id'];

    // Konuyu veritabanından al
    $query = "SELECT topics.topic_title, messages.message, messages.message_date, users.username
              FROM topics
              JOIN messages ON topics.id = messages.topic_id
              JOIN users ON messages.user_id = users.id
              WHERE topics.id = $topic_id
              ORDER BY messages.message_date";
    $result = mysqli_query($conn, $query);

    if ($result && mysqli_num_rows($result) > 0) {
        $topic = mysqli_fetch_assoc($result);
        
        // Diğer mesajları al
        $messages = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $messages[] = $row;
        }

        mysqli_close($conn);
    } else {
        // Belirtilen konu bulunamadı
        mysqli_close($conn);
        header("Location: ./"); // Forum ana sayfasına yönlendir
        exit();
    }
} else {
    // Geçersiz konu kimliği
    mysqli_close($conn);
    header("Location: ./"); // Forum ana sayfasına yönlendir
    exit();
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $topic['topic_title']; ?></title>
    <!-- Bootstrap CSS dosyasını ekleyin -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        .message {
            border: 1px solid #ccc;
            margin: 10px 0;
            padding: 10px;
        }

        .message .message-content {
            font-size: 18px;
        }

        .message .message-date {
            font-size: 14px;
            color: #999;
        }

        .reply-button {
            margin-top: 20px;
        }

        .footer {
            margin-top: 50px;
        }
    </style>
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
                <h1><?php echo $topic['topic_title']; ?></h1>
                <p><?php echo $topic['message_date']; ?></p>
                <div class="message">
                    <div class="message-content"><?php echo $topic['message']; ?></div>
                    <div class="message-date"><?php echo htmlspecialchars($topic['username']); ?> - <?php echo $topic['message_date']; ?></div>
                </div>

                <?php
                if (isset($_SESSION['user_id'])) {
                    echo '<a href="reply?topic_id=' . $topic_id . '" class="btn btn-primary reply-button">Cevapla</a>';
                }
                ?>

                <hr>

                <?php
                if (!empty($messages)) {
                    foreach ($messages as $message) {
                        echo '<div class="message">';
                        echo '<div class="message-content">' . $message['message'] . '</div>';
                        echo '<div class="message-date">' . htmlspecialchars($message['username']) . ' - ' . $message['message_date'] . '</div>';
                        echo '</div>';
                    }
                } else {
                    echo '<p>Bu konu hakkında henüz mesaj yok.</p>';
                }
                ?>
            </div>
        </div>
    </div>
    <div class="footer"></div> <!-- Sayfanın altında bir boşluk bırakmak için -->
    <!-- Bootstrap JS dosyasını ekleyin -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>