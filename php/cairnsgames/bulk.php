<?php
include_once "../corsheaders.php";
include_once "./dbutils.php";
include_once "../utils.php";

$app_id = getAppId();

// Check if the request method is POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

// Parse the incoming JSON payload
$input = json_decode(file_get_contents('php://input'), true);

if (!isset($input['users']) || !is_array($input['users'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid or missing users array']);
    exit;
}

$user_ids = array_filter($input['users'], 'is_numeric');

if (empty($user_ids)) {
    http_response_code(400);
    echo json_encode(['error' => 'No valid user IDs provided']);
    exit;
}

try {
    $placeholders = implode(',', array_fill(0, count($user_ids), '?'));
    $sql = "SELECT id, app_id, username, firstname, lastname, avatar, active FROM `user` WHERE id IN ($placeholders) AND app_id = ?";
    $params = array_merge($user_ids, [$app_id]);
    $result = PrepareExecSQL($sql, str_repeat('i', count($user_ids)) . 's', $params);

    if (!empty($result)) {
        echo json_encode($result); // Return all matching records
    } else {
        http_response_code(404);
        echo json_encode(['error' => 'No users found']);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Internal server error', 'details' => $e->getMessage()]);
}
