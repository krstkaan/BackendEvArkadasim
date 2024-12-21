<?php
header('Content-Type: application/json');
require_once '../vendor/autoload.php'; // Composer autoload dosyası
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
require_once 'config.php'; // Veritabanı bağlantısı
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$hata = 0;
$mesaj = "";
$sonuc = 0;

// Güvenli POST verilerini al
$ilanid = GuvenliPostAl('ilanid');
$token = GuvenliPostAl('token');
$userId = null; // Başlangıçta null olarak tanımlayın

if (!$ilanid) {
    $hata = 1;
    $mesaj .= "İlan ID'si eksik.\n";
}

if (!$token) {
    $hata = 1;
    $mesaj .= "Token eksik.\n";
}

if ($hata == 0) {
    try {
        // Token doğrulama
        $decoded = JWT::decode($token, new Key($_ENV['JWT_SECRET'], 'HS256'));
        if (!isset($decoded->userId)) {
            throw new Exception("Token geçerli, ancak 'userId' eksik.");
        }
        $userId = $decoded->userId;

        // Favori kontrolü
        $sorgu = $baglanti->prepare("SELECT id FROM userfavorites WHERE userId = :userId AND ilanid = :ilanid");
        $sorgu->execute(['userId' => $userId, 'ilanid' => $ilanid]);
        $favoriVarMi = $sorgu->rowCount();

        if ($favoriVarMi > 0) {
            // Favori varsa sil
            $silSorgu = $baglanti->prepare("DELETE FROM userfavorites WHERE userId = :userId AND ilanid = :ilanid");
            if ($silSorgu->execute(['userId' => $userId, 'ilanid' => $ilanid])) {
                $sonuc = 1;
                $mesaj = "Favoriden kaldırıldı.";
            } else {
                $hata = 1;
                $mesaj = "Favoriden kaldırılırken bir hata oluştu.";
            }
        } else {
            // Favori ekle
            $ekleSorgu = $baglanti->prepare("INSERT INTO userfavorites (userId, ilanid) VALUES (:userId, :ilanid)");
            if ($ekleSorgu->execute(['userId' => $userId, 'ilanid' => $ilanid])) {
                $sonuc = 1;
                $mesaj = "Favorilere eklendi.";
            } else {
                $hata = 1;
                $mesaj = "Favorilere eklenirken bir hata oluştu.";
            }
        }
    } catch (Exception $e) {
        $hata = 1;
        $mesaj = "Token doğrulama hatası: " . $e->getMessage();
    }
}

// JSON yanıtı döndür
echo json_encode([
    'sonuc' => $sonuc,
    'mesaj' => $mesaj,
]);

$baglanti = null;
?>