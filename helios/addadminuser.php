<?php
header('Content-Type: application/json');
require_once '../app/config.php'; // Veritabanı bağlantısı ve güvenlik fonksiyonları
require_once '../app/jwt.php'; // JWT işlemleri için kullanılabilir
require_once '../cors.php'; // CORS başlıklarını kontrol etmek için

$hata = 0;
$mesaj = "";
$sonuc = 0;
$userId = 0;
$displayname = "";

// Kullanıcıdan gelen verileri al
$email = GuvenliPostAl('email');
$displayname = GuvenliPostAl('displayname');
$password = GuvenliPostAl('password');

// E-posta doğrulama
if ($email == "") {
    $hata = 1;
    $mesaj .= "Bir E-Posta Adresi Yazınız. \n";
}
if (!filter_var($email, FILTER_VALIDATE_EMAIL) && $email != "") {
    $hata = 1;
    $mesaj .= "Lütfen Geçerli bir e-posta adresi giriniz! \n";
}

// Display name doğrulama
if (strlen($displayname) < 3) {
    $hata = 1;
    $mesaj .= "Ad Soyad Alanı En Az 3 Karakter Olmalıdır. \n";
}

// Şifre doğrulama
if (strlen($password) < 6) {
    $hata = 1;
    $mesaj .= "Şifre En Az 6 Karakter Olmalıdır. \n";
}

// E-posta benzersizliği kontrol et
if ($hata == 0) {
    $mailvarmisorgu = $baglanti->prepare("SELECT email FROM roomiefi_vrtbn.adminuser WHERE email=?");
    $mailvarmisorgu->execute([$email]);
    $mailvarmi = $mailvarmisorgu->rowCount();
    if ($mailvarmi > 0) {
        $mesaj .= "Bu E-Posta Adresi Zaten Kayıtlı. \n";
        $hata = 1;
    }
}

// Veritabanına ekleme
if ($hata == 0) {
    $password = password_hash($password, PASSWORD_DEFAULT); // Şifreyi hashle
    $query = $baglanti->prepare("
        INSERT INTO roomiefi_vrtbn.adminuser (email, displayname, password)
        VALUES (:email, :displayname, :password)
    ");
    $insert = $query->execute([
        ':email' => $email,
        ':displayname' => $displayname,
        ':password' => $password
    ]);

    if (!$insert) {
        $sonuc = 0;
        $mesaj = "Kayıt Başarısız Oldu. Lütfen Daha Sonra Tekrar Deneyiniz.";
    } else {
        $userId = $baglanti->lastInsertId(); // Son eklenen kullanıcının ID'si
        $sonuc = 1;
        $mesaj = "Yeni yönetici başarıyla eklendi.";
    }
}

// JSON sonuç döndür
$results = [
    'sonuc' => $sonuc,
    'mesaj' => $mesaj,
    'userId' => $sonuc === 1 ? $userId : null
];

echo json_encode($results);
$baglanti = null;
