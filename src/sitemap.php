<?php
// Site domaini
$site_domain = "http://example.com";

// Veritabanı bağlantısı için gerekli dosyayı dahil et
include 'connect.php';

// Sitemap dosyasını oluştur
$xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
$xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";

// Statik sayfaları ekle
$static_pages = array(
    "login.php",
    "register.php",
    "create.php"
);

foreach ($static_pages as $page) {
    $xml .= "\t<url>\n";
    $xml .= "\t\t<loc>$site_domain/$page</loc>\n";
    $xml .= "\t\t<lastmod>" . date("Y-m-d") . "</lastmod>\n"; // Bugünün tarihini kullan
    $xml .= "\t</url>\n";
}

// Topic tablosundan veri çek
$sql = "SELECT id, topic_date FROM topics";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $id = $row["id"];
        $topic_date = date("Y-m-d", strtotime($row["topic_date"]));
        // Sitemap'e URL'leri ekle
        $xml .= "\t<url>\n";
        $xml .= "\t\t<loc>$site_domain/topic?id=$id</loc>\n";
        $xml .= "\t\t<lastmod>$topic_date</lastmod>\n";
        $xml .= "\t</url>\n";
    }
} else {
    echo "Topic tablosunda kayıt bulunamadı.";
}

// Sitemap dosyasını kapat
$xml .= "</urlset>";

// Header'ı XML olarak ayarla
header('Content-type: application/xml');

// Sitemap'i ekrana yazdır
echo $xml;

// Veritabanı bağlantısını kapat
$conn->close();
