<?php
include 'config.php'; // Bağlantı için config dosyasını dahil et
require_once '../cors.php'; // CORS başlıklarını kontrol etmek için CORS dosyasını dahil ediyoruz

try {
    $offset = isset($_GET['offset']) ? intval($_GET['offset']) : 0;
    $limit = isset($_GET['limit']) ? intval($_GET['limit']) : 4;

    $query = "
        SELECT ilanlar.id, ilanlar.title, ilanlar.imageurl1, ilanlar.rent, ilanlar.userID, users.displayName 
        FROM ilanlar 
        INNER JOIN users ON ilanlar.userID = users.id
        WHERE ilanlar.imageurl1 IS NOT NULL AND ilanlar.onay_durumu = 1
        LIMIT :offset, :limit
    ";
    $statement = $baglanti->prepare($query);
    $statement->bindParam(':offset', $offset, PDO::PARAM_INT);
    $statement->bindParam(':limit', $limit, PDO::PARAM_INT);
    $statement->execute();

    $result = $statement->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($result);
} catch (PDOException $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
