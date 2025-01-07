<?php
include 'config.php'; // Bağlantı için config dosyasını dahil et
require_once '../cors.php'; // CORS başlıklarını kontrol etmek için CORS dosyasını dahil ediyoruz

try {
    // Uygulamadan gelen userID'yi al
    $userID = GuvenliPostAl('userID');
    error_log("Gelen userID: " . $userID); // userID değerini logla

    $query = "
       SELECT id, title, imageurl1, rent
       FROM ilanlar
       WHERE userID = :userID
       AND onay_durumu = 1
        AND imageurl1 IS NOT NULL 
    ";


    $statement = $baglanti->prepare($query);
    $statement->bindParam(':userID', $userID, PDO::PARAM_INT);
    $statement->execute();

    $result = $statement->fetchAll(PDO::FETCH_ASSOC);
    error_log("Sorgu Sonucu: " . json_encode($result)); // Sorgu sonucunu logla

    echo json_encode($result);

} catch (PDOException $e) {
    error_log("PDO Hatası: " . $e->getMessage());
    echo json_encode(['error' => $e->getMessage()]);
} catch (Exception $e) {
    error_log("Genel Hata: " . $e->getMessage());
    echo json_encode(['error' => $e->getMessage()]);
}
?>