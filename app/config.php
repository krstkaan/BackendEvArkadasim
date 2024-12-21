<?php 
require_once '../vendor/autoload.php'; // Composer'ın autoload dosyası
use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(dirname(dirname(__DIR__)));
$dotenv->load();  // .env dosyasındaki değişkenleri yükler

if (!isset($_SESSION)) {
    session_start();
}
ini_set('display_errors', '1');
date_default_timezone_set('Europe/Istanbul');
setlocale(LC_TIME, 'tr_TR');

$dbhost = $_ENV['DBHOST']; 
$dbname = $_ENV['DBNAME']; 
$dbuser = $_ENV['DBUSER'];
$dbpass = $_ENV['DBPASS'];

$dsn = 'mysql:host=' . $dbhost . ';dbname=' . $dbname . ";charset=utf8mb4";

try {
    $baglanti = new PDO($dsn, $dbuser, $dbpass);
} catch (PDOException $e) {
    echo 'Bağlantı Sağlanamadı: ' . $e->getMessage();
    exit; // Hata durumunda devam etme
}

$baglanti->query("SET NAMES 'utf8mb4'");
$baglanti->query("SET CHARACTER SET utf8mb4");
$baglanti->query("SET COLLATION_CONNECTION = 'utf8mb4_general_ci'");
$baglanti->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
$baglanti->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

function Guvenlik($data)
{
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    return $data;
}

// JSON verisini al
$data = json_decode(file_get_contents("php://input"), true);

 function GuvenliJsonAl($postKey) {
     global $data; // JSON verisini kullan
     if (isset($data[$postKey])) {
         return Guvenlik($data[$postKey]);
     }
     return null; // Eğer JSON verisi yoksa null döner
 }
function GuvenliPostAl($postKey) {
    if (isset($_POST[$postKey])) {
        return Guvenlik($_POST[$postKey]);
    }
    return null; // Eğer post verisi yoksa null döner
}


