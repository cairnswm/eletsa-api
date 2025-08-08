<?php

include_once dirname(__FILE__) . "/../corsheaders.php";
include_once dirname(__FILE__) . "/../gapiv2/dbconn.php";
include_once dirname(__FILE__) . "/../gapiv2/v2apicore.php";
include_once dirname(__FILE__) . "/../utils.php";
include_once dirname(__FILE__) . "/../security/security.config.php";
include_once dirname(__FILE__) . "/orderconfig.php";

// Get authentication details
$appid = getAppId();
$token = getToken();

// if (validateJwt($token, false) == false) {
//     http_response_code(401);
//     echo json_encode([
//         'error' => true,
//         'message' => 'Unauthorized'
//     ]);
//     die();
// }

var_dump($token);
$user = getUserFromToken($token);
if (isset($user)) {
    $userid = $user->id;
} else {
    $userid = null;
}

// Define the configurations
$config = $cartconfig;

runAPI($config);

function getOrders($config) {
    $userId = $config["where"]["user_id"];
    $sql = "SELECT id, promo_code_id, created_at, modified_at FROM orders WHERE user_id = ?";
    return gapiExecuteSQL($sql, [$userId]);
}
