<?php
session_start();

// Oturumu ve çerezi temizle
session_unset();
session_destroy();
setcookie("user_id", "", time() - 3600, "/", "", true, true);

// Kullanıcıyı çıkış sayfasına yönlendir
header("Location: login");
exit();
?>