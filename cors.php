<?php
// İzin verilen domainleri listeleyin
$allowed_origins = [
    "http://localhost:3000",
    "http://192.168.1.106:3000",
    "https://roomiefies.com/",
    "https://www.roomiefies.com/"
];

// Gelen isteğin Origin başlığını alın
if (isset($_SERVER['HTTP_ORIGIN']) && in_array($_SERVER['HTTP_ORIGIN'], $allowed_origins)) {
    // İzin verilen domainlerden biriyle eşleşiyorsa, Access-Control-Allow-Origin başlığını bu domainle ayarlayın
    header("Access-Control-Allow-Origin: " . $_SERVER['HTTP_ORIGIN']);
}

header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header('Content-Type: application/json'); // JSON formatında yanıt verir

// Eğer OPTIONS isteğiyle gelen bir preflight kontrolü varsa, yanıt verip çıkın
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit();
}
?>