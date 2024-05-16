<?php
session_start();
$quote = '';

if (!isset($_COOKIE["admin_cookie"])) {
    header("Location: ../"); // Kullanıcı girişi yoksa giriş sayfasına yönlendirme
    exit();
}

include('../../connect.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_user'])) {
    $user_id = $_POST['user_id'];
    
    // Kullanıcıyı silme işlemi
    $query = "DELETE FROM users WHERE id = '$user_id'";
    mysqli_query($conn, $query);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_topic'])) {
    $topic_id = $_POST['topic_id'];
    
    // Konuyu silme işlemi
    $query = "DELETE FROM topics WHERE id = '$topic_id'";
    mysqli_query($conn, $query);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_message'])) {
    $message_id = $_POST['message_id'];
    
    // Mesajı silme işlemi
    $query = "DELETE FROM messages WHERE id = '$message_id'";
    mysqli_query($conn, $query);
}

// Kullanıcıları, Konuları ve Mesajları Veritabanından Alın
$query_users = "SELECT * FROM users";
$result_users = mysqli_query($conn, $query_users);

$query_topics = "SELECT * FROM topics";
$result_topics = mysqli_query($conn, $query_topics);

$query_messages = "SELECT * FROM messages";
$result_messages = mysqli_query($conn, $query_messages);

mysqli_close($conn);
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Paneli - <?php echo file_get_contents('../../site.txt'); ?></title>
    <link rel="icon" href="<?php echo file_get_contents('../../favicon.txt'); ?>">
    <!-- Bootstrap CSS dosyasını ekleyin -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container">
        <a class="navbar-brand" href="../../">Ana Sayfa</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" href="../../register">Kayıt ol</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="../../login">Giriş yap</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="../../create">Konu oluştur</a>
                </li>
            </ul>
        </div>
    </div>
</nav>
    <div class="container mt-5">
        <div class="row">
            <div class="col-md-4 mb-4">
                <div class="card">
                    <div class="card-header">
                        <h1>Kullanıcılar</h1>
                    </div>
                    <div class="card-body">
                        <ul class="list-group">
                            <?php while ($user = mysqli_fetch_assoc($result_users)) : ?>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <?php echo $user['username']; ?>
                                    <form method="POST" action="">
                                        <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                        <button type="submit" name="delete_user" class="btn btn-danger btn-sm">Sil</button>
                                    </form>
                                </li>
                            <?php endwhile; ?>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="card">
                    <div class="card-header">
                        <h1>Konular</h1>
                    </div>
                    <div class="card-body">
                        <ul class="list-group">
                            <?php while ($topic = mysqli_fetch_assoc($result_topics)) : ?>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <?php echo $topic['topic_title']; ?>
                                    <form method="POST" action="">
                                        <input type="hidden" name="topic_id" value="<?php echo $topic['id']; ?>">
                                        <button type="submit" name="delete_topic" class="btn btn-danger btn-sm">Sil</button>
                                    </form>
                                </li>
                            <?php endwhile; ?>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="card">
                    <div class="card-header">
                        <h1>Mesajlar</h1>
                    </div>
                    <div class="card-body">
                        <ul class="list-group">
                            <?php while ($message = mysqli_fetch_assoc($result_messages)) : ?>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <?php echo $message['message']; ?>
                                    <form method="POST" action="">
                                        <input type="hidden" name="message_id" value="<?php echo $message['id']; ?>">
                                        <button type="submit" name="delete_message" class="btn btn-danger btn-sm">Sil</button>
                                    </form>
                                </li>
                            <?php endwhile; ?>
                        </ul>
                    </div>
                </div>
            </div>
<div class="col-md-4 mb-4">
    <div class="card">
        <div class="card-header">
            <h1>Site düzenleme</h1>
        </div>
        <div class="card-body">
            <form method="post" action="">
                <div class="form-group">
                    <label for="siteName">Site Adı:</label>
                    <input type="text" class="form-control" id="siteName" name="siteName" placeholder="Yeni site adı">
                </div>
                <div class="form-group">
                    <label for="faviconName">Favicon Adı:</label>
                    <input type="text" class="form-control" id="faviconName" name="faviconName" placeholder="Yeni favicon adı">
                </div>
                <button type="submit" class="btn btn-primary">Kaydet</button>
            </form>
            <br>
            <h3>Şu Anki Durum:</h3>
            <?php
            if ($_SERVER["REQUEST_METHOD"] == "POST") {
                // Form verilerini al
                $siteAdi = $_POST["siteName"] ?? "";
                $faviconAdi = $_POST["faviconName"] ?? "";

                // site.txt dosyasını düzenleme
                ($siteFile = fopen("../../site.txt", "w")) or die("site.txt dosyası açılamadı!");
                fwrite($siteFile, $siteAdi);
                fclose($siteFile);

                // favicon.txt dosyasını düzenleme
                ($faviconFile = fopen("../../favicon.txt", "w")) or die("favicon.txt dosyası açılamadı!");
                fwrite($faviconFile, $faviconAdi);
                fclose($faviconFile);
            }

            // Şu anki durumu göster
            echo "<p><strong>Site Adı:</strong> " . file_get_contents("../../site.txt") . "</p>";
            echo "<p><strong>Favicon:</strong> " . file_get_contents("../../favicon.txt") . "</p>";
            ?>
        </div>
    </div>
</div>
<div class="col-md-4">
    <div class="card">
        <div class="card-header">
            <h1>Google Key düzenleme</h1>
        </div>
        <div class="card-body">
            <form method="post" action="">
                <div class="form-group">
                    <label for="newKey">Yeni Anahtar:</label>
                    <input type="text" class="form-control" id="newKey" name="newKey" placeholder="Yeni anahtar">
                </div>
                <button type="submit" class="btn btn-primary">Key'i Güncelle</button>
            </form>
            <br>
            <h3>Şu Anki Durum:</h3>
            <?php
            if ($_SERVER["REQUEST_METHOD"] == "POST") {
                // Yeni anahtarı al
                $newKey = $_POST["newKey"] ?? "";

                // key.txt dosyasını düzenleme
                ($keyFile = fopen("../../key.txt", "w")) or die("key.txt dosyası açılamadı!");
                fwrite($keyFile, $newKey);
                fclose($keyFile);
                echo "<p><strong>Key güncellendi:</strong> $newKey</p>";
            } else {
                // Şu anki durumu göster
                echo "<p><strong>Key:</strong> " . file_get_contents("../../key.txt") . "</p>";
            }
            ?>
        </div>
    </div>
</div>
        </div>
        <br>
    <p>Eğer admin iseniz her durumda her sayfaya erişebilirsiniz.</p>
     <p id="rastgeleSoz"></p>
         </div>
  <script>
    var sozler = [
      "Hayatta en hakiki mürşit ilimdir.",
      "Okumak, düşünmenin merdivenidir.",
      "Bir kitap, bin dost demektir.",
      "Kitaplar insanları özgürleştirir.",
      "Sözler kısa, bilgi sonsuzdur.",
      "Bir sayfa okuyan, bir dünyayı gezmiş gibidir.",
      "Kendi portremi resmediyorum çünkü çoğunlukla yalnızım, çünkü en iyi tanıdığım insanım.",
      "Yaşam, en güçlüleri bile baş aşağı getirebilir.",
      "Babam cennetten buğday çalmış. Eğer ben cennete düşersem, bütün cenneti çalarım.",
      "Yorgunsak eğer, bu daha önceden çok bir yolu yürüdüğümüzden değilmidir? insanın yeryüzünde verilecek bir savaşı olduğu doğruysa, o doygunluk duygusu ve başın yanıp tutuşması, uzun süredir mücadele ettiğimizin göstergesi değil midir?",
      "Nereye gideceğini bilmiyorsan hangi yoldan gittiğinin hiçbir önemli yoktur.",
      "Yer ve gök boyunca, bir ben yüceler yücesiyim.",
      "Olana isyan etmektense onu sevmek en iyisidir.",
      "Işık hızına yakın bir hızdaki 1 gr'lık iğne dünyaya çarpacak olursa inanılmaz şeyler olsada en kısa anlatımıyla 50 megaton yani çar bombası gücünde bir patlama yaratırdı. Yani iğne ölüm olurdu. 😂",
      "Bazen hiçbir şey yapmamak şifalı olandır.",
      "En uç çözümler, en uç hastalıklar için çok uygundur.",
      "Esasında hayatta iki şey vardır: Bilim ve şahsi düşünceler. İlki bilgiye yol açar, ikincisi cehalete.",
      "Hastalıklar için iki şeyi alışkanlık haline getir: Yardım et veya en azından zararlı olanı yapma.",
      "Her şey doğaya karşı çıkıyor.",
      "Yaşam kısa, bilim uzundur; kriz kısacık, deneyim tehlikeli ve karar zordur.",
      "Şuana kadar yaşamış tüm canlıların yalnızca %6'sını biliyoruz %1 şuan yaşayan türler %5 fosiller ile öğrendiğimiz türler.",
      "Ne en güçlü olan tür hayatta kalır, ne de en zeki olan; değişime en çok adapte olabilendir, hayatta kalan.",
      "Yaşam: organizasyon ve iç aktivite.",
      "Benim bir dinim yok ve bazen bütün dinlerin denizin dibini boylamasını istiyorum. Hükümetini ayakta tutmak için dini kullanmaya gerek duyanlar zayıf yöneticilerdir. Âdetâ halkı bir kapana kıstırırlar. Benim halkım demokrasi ilkelerini, gerçeğin emirlerini ve bilimin öğretilerini öğrenecektir. Batıl inançlardan vazgeçilmelidir. İsteyen istediği gibi ibadet edebilir. Herkes kendi vicdanının sesini dinler. Ama bu davranış ne sağduyulu mantıkla çelişmeli ne de başkalarının özgürlüğüne karşı çıkmasına yol açmalıdır.",
      "Bir kahraman veya bir tanrı değil, bir insan."
    ];
    function rastgeleSozGoster() {
      var indeks = Math.floor(Math.random() * sozler.length);
      document.getElementById("rastgeleSoz").textContent = sozler[indeks];
    }
    window.onload = rastgeleSozGoster;
  </script>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>