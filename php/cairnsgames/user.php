<?php
include_once "../corsheaders.php";
include_once "./dbutils.php";
include_once "../utils.php";

$app_id = getAppId();

// Extract user ID from the request URI (e.g., user.php/123)
$uri = $_SERVER['REQUEST_URI'];
$parts = explode('/', $uri);
$user_id_str = end($parts);

// Remove query string if present
$user_id_str = explode('?', $user_id_str)[0];

if (!is_numeric($user_id_str)) {
  http_response_code(400);
  echo json_encode(['error' => 'Invalid or missing user id']);
  exit;
}

$_GET['id'] = $user_id_str;

$user_id = intval($_GET['id']);

try {
  $sql = "SELECT id, app_id, username, firstname, lastname, avatar, active FROM `user` WHERE id = ? AND app_id = ?";
  $params = [$user_id, $app_id];
  $result = PrepareExecSQL($sql, "is", $params);

  if (!empty($result)) {
    echo json_encode($result[0]); // Return the first record
  } else {
    http_response_code(404);
    echo json_encode(['error' => 'User not found']);
  }
} catch (Exception $e) {
  http_response_code(500);
  echo json_encode(['error' => 'Internal server error', 'details' => $e->getMessage()]);
}