<?php

require_once 'config.php';  // Veritabanı bağlantısı için config dosyası
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");



try {

    // PDO ile veritabanı bağlantısı kur

    $pdo = new PDO("mysql:host=$dbhost;dbname=$dbname;charset=utf8mb4", $dbuser, $dbpass);

    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);



    // Sorguyu hazırla ve çalıştır

    $sql = "SELECT * FROM ililcemahalle WHERE UstID = 0";

    $stmt = $pdo->prepare($sql);

    $stmt->execute();



    // Veriyi çek ve JSON formatına dönüştür

    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

    header('Content-Type: application/json; charset=utf-8');

    echo json_encode($data, JSON_UNESCAPED_UNICODE); // Türkçe karakterlerin düzgün gösterimi

} catch (PDOException $e) {

    // Hata durumunda JSON formatında hata mesajı döndür

    header('Content-Type: application/json; charset=utf-8');

    echo json_encode(['error' => $e->getMessage()], JSON_UNESCAPED_UNICODE);

}

