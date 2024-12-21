<?php
header('Content-Type: application/json');
require_once 'config.php';
require_once 'jwt.php'; // JWT işlemleri için
//require_once 'appcontrol.php';

$hata = 0;
$mesaj = "";
$sonuc = 0;
$token = "";
$userId = 0;
$displayname = "";

$email = GuvenliPostAl('email');
$displayname = GuvenliPostAl('displayname');
$password = GuvenliPostAl('password');


if ($email == "") {
    $hata = 1;
    $mesaj = $mesaj . "Bir E-Posta Adresi Yazınız \n";
}
// mail kontrol
if (!filter_var($email, FILTER_VALIDATE_EMAIL) && $email != "") {
    $hata = 1;
    $mesaj = $mesaj . "Lütfen Geçerli bir e-posta adresi giriniz! \n";
}
// displayname kontrol
if (strlen($displayname) < 3) {
    $hata = 1;
    $mesaj = $mesaj . "Ad Soyad Alanı En Az 3 Karakter Olmalıdır. \n";
}
// password kontrol
if (strlen($password) < 6) {
    $hata = 1;
    $mesaj = $mesaj . "Şifre En Az 6 Karakter Olmalıdır. \n";
}
if ($hata == 0) {
    $mailvarmisorgu = $baglanti->prepare("SELECT email FROM users WHERE email=?");
    $mailvarmisorgu->execute(array($email));
    $mailvarmi = $mailvarmisorgu->rowCount();
    if ($mailvarmi > 0) {
        $mesaj = $mesaj . "Bu E-Posta Adresi Zaten Kayıtlı. Zaten üye iseniz giriş yapınız. \n";
        $hata = 1;
    }
}

if ($hata == 0) {
    $password = password_hash($password, PASSWORD_DEFAULT);
    $query = $baglanti->prepare("INSERT INTO users SET 
        email=:email,
        displayname=:displayname,
        password=:password
        ");
    $insert = $query->execute(
        array(
            "email" => $email,
            "displayname" => $displayname,
            "password" => $password
        )
    );
    if (!$query) {
        $sonuc = 0;
        $mesaj = "Kayıt Başarısız Oldu \n Lütfen Daha Sonra Tekrar Deneyiniz";

    } else {
        $userId = $baglanti->lastInsertId(); // Son eklenen kullanıcının ID'si
        $token = createJWT($userId);
        $sonuc = 1;
        $mesaj = "Kayıt Başarılı \n Giriş Yapapılıyor...";
    }
}
$results = array(
    'sonuc' => "$sonuc",
    'mesaj' => "$mesaj",
    'token' => $token,
    'displayname' => $displayname,
);
echo json_encode($results);
$baglanti = null;
?>