
<?php
require_once '../app/config.php'; // Veritabanı bağlantısı için config dosyası
require_once '../cors.php'; // CORS başlıklarını kontrol etmek için CORS dosyasını dahil ediyoruz

try {
    $query = "
        SELECT ilanlar.id, ilanlar.title, ilanlar.imageurl1, ilanlar.rent, ilanlar.userID, users.displayName , ilanlar.onay_durumu
        FROM ilanlar 
        INNER JOIN users ON ilanlar.userID = users.id
        WHERE ilanlar.imageurl1 IS NOT NULL
    ";
    $statement = $baglanti->prepare($query);
    $statement->execute();

    $result = $statement->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($result);
} catch (PDOException $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
