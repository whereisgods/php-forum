<?php
// connect.php dosyasını kontrol et
if (filesize('connect.php') === 0) {
    // connect.php dosyası boşsa install.php sayfasına yönlendir
    header('Location: install');
    exit;
}

session_start();

include('connect.php');

$user_id = null;

if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
} elseif (isset($_COOKIE['user_id'])) {
    $user_id = $_COOKIE['user_id'];
}

$username = ""; // Varsayılan kullanıcı adı

if ($user_id !== null) {
    $query = "SELECT * FROM users WHERE id = '$user_id'";
    $result = mysqli_query($conn, $query);

    if ($result && mysqli_num_rows($result) > 0) {
        $user = mysqli_fetch_assoc($result);
        htmlspecialchars($username = $user['username']);
    }
}

// Konu Başlıklarını Al
$query = "SELECT * FROM topics ORDER BY topic_date DESC LIMIT 20";
$result = mysqli_query($conn, $query);

function formatElapsedTime($timestamp) {
    $now = time();
    $elapsed = $now - $timestamp;

    if ($elapsed < 60) {
        return $elapsed . ' saniye önce';
    } elseif ($elapsed < 3600) {
        $minutes = floor($elapsed / 60);
        return $minutes . ' dakika önce';
    } elseif ($elapsed < 86400) {
        $hours = floor($elapsed / 3600);
        return $hours . ' saat önce';
    } elseif ($elapsed < 2592000) {
        $days = floor($elapsed / 86400);
        return $days . ' gün önce';
    } elseif ($elapsed < 31536000) {
        $months = floor($elapsed / 2592000);
        return $months . ' ay önce';
    } else {
        $years = floor($elapsed / 31536000);
        return $years . ' yıl önce';
    }
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hoş Geldiniz</title>
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
                    <?php
                    if ($username !== "") {
                        echo "Hoş Geldiniz, " . $username . "!";
                    } else {
                        echo "Hoş Geldiniz!";
                    }
                    ?>
                </div>
                <div class="card-body">
                    <?php
                    if ($username !== "") {
                        echo '<a href="logout" class="btn btn-danger">Çıkış Yap</a><br><br>';
                    } else {
                        echo '<p>Kayıt olmadan sınırlı erişime sahipsiniz. Lütfen kayıt olun veya giriş yapın.</p>';
                    }
                    ?>

                    <!-- Konu Başlıkları -->
                    <?php
                    while ($row = mysqli_fetch_assoc($result)) {
                        // Tarih bilgisini hesaplayın (örneğin, $row['topic_date'] tarih olmalıdır).
                        $topicDate = strtotime($row['topic_date']);
                        $formattedDate = formatElapsedTime($topicDate);

                        echo '<div class="card mb-3">';
                        echo '<div class="card-body">';
                        echo '<h5 class="card-title"><a href="topic?id=' . $row['id'] . '">' . htmlspecialchars($row['topic_title']) . '</a></h5>';
                        echo '<p class="card-text">Konu oluşturuldu: ' . $formattedDate . '</p>';
                        echo '</div>';
                        echo '</div>';
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Bootstrap JS dosyasını ekleyin -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<br>
</body>
</html>