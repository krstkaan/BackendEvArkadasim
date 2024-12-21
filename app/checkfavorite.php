<?php
header('Content-Type: application/json');
require_once '../vendor/autoload.php'; // Composer autoload dosyası
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
require_once 'config.php'; // Veritabanı bağlantısı

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$ilanid = GuvenliPostAl('ilanid');
$token = GuvenliPostAl('token');
$isFavorite = false;

if ($ilanid && $token) {
    try {
        $decoded = JWT::decode($token, new Key($_ENV['JWT_SECRET'], 'HS256'));
        $userId = $decoded->userId;

        // Favori kontrolü
        $sorgu = $baglanti->prepare("SELECT id FROM userfavorites WHERE userId = :userId AND ilanid = :ilanid");
        $sorgu->execute(['userId' => $userId, 'ilanid' => $ilanid]);

        if ($sorgu->rowCount() > 0) {
            $isFavorite = true;
        }
    } catch (Exception $e) {
        echo json_encode(["error" => "Token doğrulama hatası: " . $e->getMessage()]);
        exit();
    }
}
else {
    echo json_encode(["error" => "Eksik parametreler"]);
    exit();
}

// JSON yanıtı
echo json_encode(["isFavorite" => $isFavorite]);
$baglanti = null;
?>
