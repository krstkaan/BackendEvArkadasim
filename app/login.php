<?php
header('Content-Type: application/json');
require_once 'config.php';  // Veritabanı bağlantısı için config dosyası
require_once 'jwt.php';     // JWT oluşturma ve doğrulama fonksiyonlarını içeren dosyas
//require_once 'appcontrol.php';  // Application kontrol dosyası
$email = GuvenliPostAl('email');
$password = GuvenliPostAl('password');
$sonuc = 0;
$mesaj = "";
$userId = 0;
$displayname = "";
$photoURL = "";
$token = "";
$hata = 0;

if ($email == "") {
    $hata = 1;
    $mesaj = $mesaj . "Bir E-Posta Adresi Yazınız \n";
}
if ($password == "") {
    $hata = 1;
    $mesaj = $mesaj . "Bir Şifre Yazınız \n";
}
if ($hata == 0) {
    // Kullanıcı bilgilerini veritabanından kontrol ediyoruz
    $query = $baglanti->prepare("SELECT * FROM users WHERE email = :email");
    $query->bindParam(':email', $email);
    $query->execute();
    $user = $query->fetch(PDO::FETCH_ASSOC);
    if ($user) {
        if ($user['onay_durumu'] == 0) {
            $mesaj = "Hesabınız onaylanmamış. Lütfen onaylanmasını bekleyiniz.";
            $sonuc = 0;
            $token = "";
        } elseif (password_verify($password, $user['password'])) {
            // Kullanıcı doğru ise JWT token oluşturuyoruz
            $token = createJWT($user['id']);  // JWT fonksiyonunda userId'yi kullanarak token oluşturuyoruz
            $sonuc = 1;
            $mesaj = "Giriş Başarılı";
            $userId = $user['id'];
            $displayname = $user['displayName'];
            //$photoURL = $user['photoURL'];
        } else {
            $mesaj = "Kullanıcı adı veya şifre hatalı";
            $sonuc = 0;
            $token = "";
        }
    } else {
        $mesaj = "Kullanıcı adı veya şifre hatalı";
        $sonuc = 0;
        $token = "";
    }
}
$results = array(
    'sonuc' => "$sonuc",
    'mesaj' => "$mesaj",
    'token' => $token,
    'userId' => "$userId",
    'displayname' => $displayname,
    //'photoURL' => $photoURL
);
echo json_encode($results);
$baglanti = null;
?>
