<?php

include_once "../corsheaders.php";

header('Content-Type: application/json');

$input = json_decode(file_get_contents('php://input'), true);
$response = [
    'success' => false,
    'files' => [],
    'errors' => []
];

if (!is_array($input) || !isset($input['userId'], $input['images']) || !is_array($input['images'])) {
    $response['errors'][] = 'Invalid input JSON. Ensure userId and images (base 64 array) are provided.';
    echo json_encode($response);
    exit;
}

$userId = $input['userId'];
$images = $input['images'];

if (!is_numeric($userId)) {
    $response['errors'][] = 'Invalid userId it is expected to be numeric.';
    echo json_encode($response);
    exit;
}

$uploadDir = __DIR__ . "/../../uploads/$userId/";
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0755, true);
}

foreach ($images as $key => $base64data) {
    if (!preg_match('/^(?:data:)?image\/(\w+);base64,/', $base64data, $matches)) {
        $format = '';
        if (preg_match('/^([^;]+);base64,/', $base64data, $formatMatch)) {
            $format = $formatMatch[1];
        } else {
            $format = 'unknown';
        }
        $response['errors'][] = "$key: Invalid image format. Received: $format";
        continue;
    }

    $ext = $matches[1];
    $base64 = substr($base64data, strpos($base64data, ',') + 1);
    $decodedData = base64_decode($base64);

    if ($decodedData === false) {
        $response['errors'][] = "$key: Failed to decode base64.";
        continue;
    }

    $filename = "$userId-" . uniqid("img_", true) . ".$ext";
    $filepath = $uploadDir . $filename;

    if (file_put_contents($filepath, $decodedData)) {
        $response['files'][] = [
            'name' => $filename,
            'size' => strlen($decodedData),
            'url' => "/uploads/$userId/$filename"
        ];
    } else {
        $response['errors'][] = "$key: Failed to save file.";
    }
}

$response['success'] = count($response['files']) > 0;
echo json_encode($response);
