<?php
header('Content-Type: application/json');
require_once 'config.php';
require_once 'jwt.php';

// JSON girişini manuel olarak işleme
$inputJSON = file_get_contents('php://input');
$input = json_decode($inputJSON, true);
$token = isset($input['token']) ? $input['token'] : null;

if (!$token) {
    echo json_encode(['error' => 'Token is missing']);
    exit;
}

$decoded = verifyJWT($token);

if ($decoded) {
    $userId = $decoded->userId;
    $query = $baglanti->prepare("SELECT * FROM users WHERE id = :id");
    $query->bindParam(':id', $userId);
    $query->execute();
    $user = $query->fetch(PDO::FETCH_ASSOC);
    if ($user) {
        echo json_encode(['isAuth' => true]);
    } else {
        echo json_encode(['isAuth' => false]);
    }
} else {
    echo json_encode(['isAuth' => false]);
}
$baglanti = null;
?>
