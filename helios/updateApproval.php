<?php
// Veritabanı bağlantısı ve CORS yapılandırması
require_once '../app/config.php'; // Veritabanı bağlantısı için config dosyası
require_once '../cors.php'; // CORS başlıklarını kontrol etmek için CORS dosyası

// POST verilerini güvenli bir şekilde al
$id = GuvenliPostAl('id');
$newStatus = GuvenliPostAl('onay_durumu'); // Gelen parametreye uygun isimlendirme

// Başlangıç durumlarını tanımla
$success = false;
$message = "";

// POST verilerinin eksik olup olmadığını kontrol et
if ($id === null || $newStatus === null) {
    $message = "Eksik bilgi gönderildi.";
} else {
    try {
        // Durumu güncellemek için SQL sorgusunu hazırla ve çalıştır
        $query = $baglanti->prepare("UPDATE users SET onay_durumu = :newStatus WHERE id = :id");
        $query->bindParam(':id', $id, PDO::PARAM_INT);
        $query->bindParam(':newStatus', $newStatus, PDO::PARAM_STR);
        $query->execute();

        // Sorgudan etkilenen satır sayısını kontrol et
        if ($query->rowCount() > 0) {
            $success = true;
            $message = $newStatus === "1" ? "Kullanıcı başarıyla onaylandı." : "Kullanıcı onayı başarıyla kaldırıldı.";
        } else {
            $message = "Kullanıcı durumu güncellenemedi. ID'yi kontrol edin.";
        }
    } catch (PDOException $e) {
        // Hata durumunda hata mesajını yakala ve döndür
        $message = "Hata: " . $e->getMessage();
    }
}

// JSON formatında sonucu döndür
echo json_encode([
    'success' => $success,
    'message' => $message,
]);

// Veritabanı bağlantısını kapat
$baglanti = null;
?>
