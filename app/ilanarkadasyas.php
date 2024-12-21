<?php
require_once 'config.php';  // Veritabanı bağlantısı için config dosyası

try {
    // PDO ile veritabanı bağlantısı kur
    $pdo = new PDO("mysql:host=$dbhost;dbname=$dbname;charset=utf8", $dbuser, $dbpass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Sorguyu hazırla ve çalıştır
    $sql = "SELECT * FROM ilanyasaraligi";
    $stmt = $pdo->query($sql);
    $genders = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // JSON formatında başarılı sonucu döndür
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($genders, JSON_UNESCAPED_UNICODE);
} catch (PDOException $e) {
    // Hata durumunda JSON formatında hata mesajı döndür
    http_response_code(500); // Internal Server Error
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['error' => $e->getMessage()], JSON_UNESCAPED_UNICODE);
}
?>
