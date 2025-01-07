<?php
header('Content-Type: application/json');
require_once 'config.php';
require_once 'jwt.php';

$email = GuvenliPostAl('email');
$password = GuvenliPostAl('password');
$sonuc = 0;
$mesaj = "";
$userId = 0;
$displayname = "";
$token = "";
$hata = 0;
$birthdate = ""; // Başlangıç değeri olarak boş string

if ($email == "") {
    $hata = 1;
    $mesaj = $mesaj . "Bir E-Posta Adresi Yazınız \n";
}
if ($password == "") {
    $hata = 1;
    $mesaj = $mesaj . "Bir Şifre Yazınız \n";
}
if ($hata == 0) {
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
            $token = createJWT($user['id']);
            $sonuc = 1;
            $mesaj = "Giriş Başarılı";
            $userId = $user['id'];
            $displayname = $user['displayName'];
            
            // birthdate değeri null ise boş string yap
             $birthdate = $user['birthdate'] ? $user['birthdate'] : "";

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
    'birthdate' => $birthdate,
);
echo json_encode($results);
$baglanti = null;
?>