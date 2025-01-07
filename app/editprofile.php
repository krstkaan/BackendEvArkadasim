<?php
header('Content-Type: application/json');
require_once 'config.php';
require_once 'jwt.php'; // JWT işlemleri için

$hata = 0;
$mesaj = "";
$sonuc = 0;
$token = "";
$userId = 0;

$email = GuvenliPostAl('email');
$displayname = GuvenliPostAl('displayname');
$birthdate = GuvenliPostAl('birthdate'); // Doğum tarihi alanı


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


if ($hata == 0) {

        $query = $baglanti->prepare("UPDATE users SET 
        displayname=:displayname,
        birthdate=:birthdate
         WHERE email=:email
        ");
    $update = $query->execute(
        array(
            "email" => $email,
            "displayname" => $displayname,
            "birthdate" => $birthdate,
        )
    );
    if (!$query) {
        $sonuc = 0;
        $mesaj = "Profil Güncelleme Başarısız Oldu \n Lütfen Daha Sonra Tekrar Deneyiniz";

    } else {
          $sonuc = 1;
          $mesaj = "Profil Güncelleme Başarılı";
            // Profil güncellemesi başarılı olduğunda JWT token oluşturabiliriz.
          //   $userId = $baglanti->lastInsertId(); // Son eklenen kullanıcının ID'si
           $query = $baglanti->prepare("SELECT id FROM users WHERE email=:email");
           $query->execute(array("email"=>$email));
           $user = $query->fetch(PDO::FETCH_ASSOC);
           $userId = $user['id'];
           $token = createJWT($userId);
      
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