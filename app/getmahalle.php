<?php
require_once 'config.php';  // Veritabanı bağlantısı için config dosyası
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");
try {
    // PDO ile veritabanı bağlantısı kur
    $pdo = new PDO("mysql:host=$dbhost;dbname=$dbname;charset=utf8", $dbuser, $dbpass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // Gelen UstID parametresini kontrol et
    if (!isset($_GET['UstID'])) {
        // Eğer UstID gönderilmemişse hata döndür
        http_response_code(400); // Bad Request
        echo json_encode(['error' => 'UstID parametresi gerekli.'], JSON_UNESCAPED_UNICODE);
        exit;
    }
    $ustID = $_GET['UstID'];
    // Sorguyu hazırla
    $sql = "SELECT * FROM ililcemahalle WHERE UstID = :UstID";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':UstID', $ustID, PDO::PARAM_INT);
    // Sorguyu çalıştır
    $stmt->execute();
    // Veriyi çek ve JSON formatına dönüştür
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
} catch (PDOException $e) {
    // Hata durumunda JSON formatında hata mesajı döndür
    http_response_code(500); // Internal Server Error
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['error' => $e->getMessage()], JSON_UNESCAPED_UNICODE);
}
?>