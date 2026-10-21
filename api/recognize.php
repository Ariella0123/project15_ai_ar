<?php
declare(strict_types=1);

header('Content-Type: application/json');
require_once __DIR__ . '/../includes/ObjectRecognizer.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['ok' => false, 'error' => 'Invalid HTTP method']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);
$rawImage = $input['image'] ?? '';

if (empty($rawImage)) {
    echo json_encode(['ok' => false, 'error' => 'Image data missing']);
    exit;
}

// 清理 Base64 前缀
if (preg_match('/^data:image\/(\w+);base64,/', $rawImage, $type)) {
    $rawImage = substr($rawImage, strpos($rawImage, ',') + 1);
}

$recognizer = new ObjectRecognizer();
$response = $recognizer->processImage($rawImage);

echo json_encode($response);