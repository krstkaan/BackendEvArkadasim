<?php
header('Content-Type: application/json');
require_once 'config.php';  // Veritabanı bağlantısı için config dosyası
error_reporting(E_ALL);
ini_set('display_errors', 1);

$hata = 0;
$sonuc = 0;
$mesaj = "";
$hatalar = [];
$results = array();

$title = GuvenliPostAl('title');
$description = GuvenliPostAl('description');
$rent = floatval(GuvenliPostAl('rent'));
$size = intval(GuvenliPostAl('size'));
$cinsiyet = intval(GuvenliPostAl('cinsiyet'));
$yasaraligi = intval(GuvenliPostAl('yasaraligi'));
$isitmaturu = intval(GuvenliPostAl('isitmaturu'));
$esya = intval(GuvenliPostAl('esya'));
$binayasi = intval(GuvenliPostAl('binayasi'));
$dairetipi = intval(GuvenliPostAl('dairetipi'));
$selectedIl = intval(GuvenliPostAl('selectedIl'));
$selectedIlce = intval(GuvenliPostAl('selectedIlce'));
$selectedMahalle = intval(GuvenliPostAl('selectedMahalle'));
$userID = intval(GuvenliPostAl('userID'));

// Görseller için değişkenler
$imageurl1 = "";
$imageurl2 = "";
$imageurl3 = "";

// Görsel yükleme işlemleri
$allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/jpg'];

if (isset($_FILES['image1']) && $_FILES['image1']['error'] === UPLOAD_ERR_OK) {
    $image1 = $_FILES['image1'];
    $fileType = mime_content_type($image1['tmp_name']);
    if (in_array($fileType, $allowedTypes)) {
        $imageurl1 = 'ilanimages/' . uniqid() . '_' . basename($image1['name']);
        move_uploaded_file($image1['tmp_name'], $imageurl1);
    } else {
        $hatalar[] = "Geçersiz dosya formatı (image1). Yalnızca JPEG, PNG ve GIF formatlarına izin verilmektedir.";
    }
}

if (isset($_FILES['image2']) && $_FILES['image2']['error'] === UPLOAD_ERR_OK) {
    $image2 = $_FILES['image2'];
    $fileType = mime_content_type($image2['tmp_name']);
    if (in_array($fileType, $allowedTypes)) {
        $imageurl2 = 'ilanimages/' . uniqid() . '_' . basename($image2['name']);
        move_uploaded_file($image2['tmp_name'], $imageurl2);
    } else {
        $hatalar[] = "Geçersiz dosya formatı (image2). Yalnızca JPEG, PNG ve GIF formatlarına izin verilmektedir.";
    }
}

if (isset($_FILES['image3']) && $_FILES['image3']['error'] === UPLOAD_ERR_OK) {
    $image3 = $_FILES['image3'];
    $fileType = mime_content_type($image3['tmp_name']);
    if (in_array($fileType, $allowedTypes)) {
        $imageurl3 = 'ilanimages/' . uniqid() . '_' . basename($image3['name']);
        move_uploaded_file($image3['tmp_name'], $imageurl3);
    } else {
        $hatalar[] = "Geçersiz dosya formatı (image3). Yalnızca JPEG, PNG ve GIF formatlarına izin verilmektedir.";
    }
}

// Doğrulama
if (strlen($title) < 3) {
    $hatalar[] = "Başlık en az 3 karakter olmalıdır.";
}
if (strlen($description) < 10) {
    $hatalar[] = "Açıklama en az 10 karakter olmalıdır.";
}
if ($rent <= 0) {
    $hatalar[] = "Geçerli bir kira bedeli giriniz.";
}
if ($size <= 0) {
    $hatalar[] = "Geçerli bir metrekare değeri giriniz.";
}
if ($selectedIl === 0 || $selectedIlce === 0 || $selectedMahalle === 0) {
    $hatalar[] = "İl, ilçe ve mahalle seçimi zorunludur.";
}

// Hatalar varsa yanıt dön
if (count($hatalar) > 0) {
    $results = array(
        'sonuc' => 0,
        'mesaj' => implode("\n", $hatalar),
    );
    echo json_encode($results);
    exit;
}

// Eğer hata yoksa veritabanına kaydet
try {
    $query = $baglanti->prepare("
        INSERT INTO ilanlar 
        (title, description, rent, size, cinsiyet, yasaraligi, isitmaturu, esya, binayasi, dairetipi, selectedIl, selectedIlce, selectedMahalle, userID, imageurl1, imageurl2, imageurl3) 
        VALUES 
        (:title, :description, :rent, :size, :cinsiyet, :yasaraligi, :isitmaturu, :esya, :binayasi, :dairetipi, :selectedIl, :selectedIlce, :selectedMahalle, :userID, :imageurl1, :imageurl2, :imageurl3)
    ");
    $query->bindParam(':title', $title);
    $query->bindParam(':description', $description);
    $query->bindParam(':rent', $rent);
    $query->bindParam(':size', $size);
    $query->bindParam(':cinsiyet', $cinsiyet);
    $query->bindParam(':yasaraligi', $yasaraligi);
    $query->bindParam(':isitmaturu', $isitmaturu);
    $query->bindParam(':esya', $esya);
    $query->bindParam(':binayasi', $binayasi);
    $query->bindParam(':dairetipi', $dairetipi);
    $query->bindParam(':selectedIl', $selectedIl);
    $query->bindParam(':selectedIlce', $selectedIlce);
    $query->bindParam(':selectedMahalle', $selectedMahalle);
    $query->bindParam(':userID', $userID);
    $query->bindParam(':imageurl1', $imageurl1);
    $query->bindParam(':imageurl2', $imageurl2);
    $query->bindParam(':imageurl3', $imageurl3);
    $query->execute();

    $sonuc = 1;
    $mesaj = "İlanınız onaylandıktan sonra yayınlanacaktır.";
} catch (PDOException $e) {
    $sonuc = 0;
    $mesaj = "Hata: " . $e->getMessage();
}

// Sonuçları JSON olarak dön
$results = array(
    'sonuc' => $sonuc,
    'mesaj' => $mesaj,
);
echo json_encode($results);

$baglanti = null; // Bağlantıyı kapat
?>
