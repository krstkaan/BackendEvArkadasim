<?php
header("Content-Type: application/json");
include "config.php";

try {
    // İlan ID'sini al
    $id = GuvenliPostAl("id");

    if (!$id) {
        echo json_encode(["error" => "ID parametresi gerekli"]);
        exit();
    }

    // Sorgu oluştur
    $query = "
SELECT 
    ilanlar.id, 
    ilanlar.title, 
    ilanlar.imageurl1, 
    ilanlar.rent, 
    ilanlar.size,
    ilanlar.userID, 
    users.displayName, 
    ilanlar.description, 
    ilanlar.cinsiyet, 
    ilanlar.yasaraligi, 
    ilanlar.isitmaturu, 
    ilanlar.esya, 
    ilanlar.binayasi, 
    ilanlar.dairetipi, 
    ilanlar.selectedIl,
    ilanlar.selectedIlce,
    ilanlar.selectedMahalle,
    ilanlar.imageurl2, 
    ilanlar.imageurl3
FROM 
    ilanlar
INNER JOIN users ON ilanlar.userID = users.id
WHERE 
    ilanlar.id = :id;
    ";

    $stmt = $baglanti->prepare($query);
    $stmt->execute([":id" => $id]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($result) {
        echo json_encode([$result]); // Sonucu dizi içinde döndür
    } else {
        echo json_encode(["error" => "İlan bulunamadı"]);
    }
} catch (PDOException $e) {
    echo json_encode(["error" => $e->getMessage()]);
}