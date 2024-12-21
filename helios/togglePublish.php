<?php
require_once '../app/config.php'; // Veritabanı bağlantısı için config dosyası
require_once '../cors.php'; // CORS başlıklarını kontrol etmek için CORS dosyası

$id = GuvenliPostAl('id'); // POST verisinden güvenli şekilde 'id' al
$newStatus = GuvenliPostAl('newStatus'); // POST verisinden güvenli şekilde 'newStatus' al

$success = false;
$mesaj = "";

if ($id === null || $newStatus === null) {
    $mesaj = "Eksik bilgi gönderildi.";
} else {
    try {
        // İlan durumunu güncelleme sorgusu
        $query = $baglanti->prepare("UPDATE ilanlar SET onay_durumu = :newStatus WHERE id = :id");
        $query->bindParam(':id', $id, PDO::PARAM_INT);
        $query->bindParam(':newStatus', $newStatus, PDO::PARAM_STR);
        $query->execute();

        if ($query->rowCount() > 0) {
            $success = true;
            $mesaj = $newStatus === "1" ? "İlan başarıyla yayınlandı." : "İlan başarıyla yayından kaldırıldı.";
        } else {
            $mesaj = "İlan durumu güncellenemedi. ID'yi kontrol edin.";
        }
    } catch (PDOException $e) {
        $mesaj = "Hata: " . $e->getMessage();
    }
}

// JSON formatında sonuç döndür
echo json_encode([
    'success' => $success,
    'mesaj' => $mesaj,
]);

$baglanti = null;
?>
