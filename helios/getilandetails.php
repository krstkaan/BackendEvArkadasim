<?php
require_once '../app/config.php'; // Veritabanı bağlantısı için config dosyası
require_once '../cors.php'; // CORS başlıklarını kontrol etmek için CORS dosyasını dahil ediyoruz


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
    ilanlar.userID, 
    users.displayName, 
    ilanlar.description, 
    ilangendertypes.title AS cinsiyet, 
    ilanyasaraligi.aralik AS yasaraligi, 
    isitma_tipi.tip_adi AS isitmaturu, 

    
    esya_durumu.durum_adi AS esya, 
    ilanbinayas.binayas AS binayasi, 
    daire_tipi.tip_adi AS dairetipi, 
    ilanlar.imageurl2, 
    ilanlar.imageurl3
FROM 
    ilanlar
INNER JOIN users ON ilanlar.userID = users.id
LEFT JOIN ilangendertypes ON ilanlar.cinsiyet = ilangendertypes.id
LEFT JOIN ilanyasaraligi ON ilanlar.yasaraligi = ilanyasaraligi.id
LEFT JOIN isitma_tipi ON ilanlar.isitmaturu = isitma_tipi.id
LEFT JOIN esya_durumu ON ilanlar.esya = esya_durumu.id
LEFT JOIN ilanbinayas ON ilanlar.binayasi = ilanbinayas.id
LEFT JOIN daire_tipi ON ilanlar.dairetipi = daire_tipi.id
WHERE 
    ilanlar.id = :id;
    ";

    $stmt = $baglanti->prepare($query);
    $stmt->execute([":id" => $id]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($result) {
        echo json_encode($result);
    } else {
        echo json_encode(["error" => "İlan bulunamadı"]);
    }
} catch (PDOException $e) {
    echo json_encode(["error" => $e->getMessage()]);
}
