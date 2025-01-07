<?php
require_once '../app/config.php'; // Veritabanı bağlantısı için config dosyası
require_once '../cors.php'; // CORS başlıklarını kontrol etmek için CORS dosyasını dahil ediyoruz

 try {
     $query = "
         SELECT id, displayname, email, onay_durumu
         FROM roomiefi_vrtbn.users
     ";
     $statement = $baglanti->prepare($query);
     $statement->execute();

     // Tüm verileri çek ve JSON olarak döndür
     $result = $statement->fetchAll(PDO::FETCH_ASSOC);
     echo json_encode($result);
 } catch (PDOException $e) {
     // Hata durumunda JSON formatında hata mesajı döndür
     echo json_encode(['error' => $e->getMessage()]);
 }
