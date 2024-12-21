<?php
require_once '../vendor/autoload.php'; // Composer'ın autoload dosyası
use \Firebase\JWT\JWT;
use \Firebase\JWT\Key;
use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(dirname(dirname(__DIR__)));
$dotenv->load();  // .env dosyasındaki değişkenleri yükler

function createJWT($userId) {
    $key = $_ENV['JWT_SECRET'];  // Key .env dosyasından alınıyor
    $payload = [
        'iss' => 'roomiefies.com',  // Token'ın kim tarafından oluşturulduğunu belirtir
        'iat' => time(),                      // Token'ın oluşturulma zamanı 
       // 'exp' => time() + (60 * 60 * 24),     // Token 24 saat geçerli olacak
        'userId' => $userId                   // Kullanıcının ID'si
    ];

    return JWT::encode($payload, $key, 'HS256'); // HS256 algoritması ile token oluşturuluyor
}

function verifyJWT($token) {
    $key =$_ENV['JWT_SECRET'];   // Key .env dosyasından alınıyor
    try {
        return JWT::decode($token, new Key($key, 'HS256'));  // Token'ı çözme ve doğrulama işlemi
    } catch (Exception $e) {
        return false;  // Token geçerli değilse false döndür
    }
}
