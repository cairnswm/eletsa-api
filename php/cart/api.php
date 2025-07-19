<?php

include_once dirname(__FILE__) . "/../corsheaders.php";
include_once dirname(__FILE__) . "/../gapiv2/dbconn.php";
include_once dirname(__FILE__) . "/../gapiv2/v2apicore.php";
include_once dirname(__FILE__) . "/../utils.php";
include_once dirname(__FILE__) . "/../security/security.config.php";
include_once dirname(__FILE__) . "/cartconfig.php";

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

$user = getUserFromToken($token);
if (isset($user)) {
    $userid = $user->id;
} else {
    $userid = null;
}

// Define the configurations
$config = $cartconfig;

runAPI($config);

function getCart($config) {
    $user_id = $config["where"]["id"];
    $items = [];
    $sql = "
        SELECT
            ci.ticket_type_id AS ticket_id,
            tt.event_id,
            e.title AS event_name,
            tt.name AS ticket_name,
            ci.price,
            ci.quantity,
            (ci.price * ci.quantity) AS total_price_per_item
        FROM cart_items ci
        JOIN ticket_types tt ON ci.ticket_type_id = tt.id
        JOIN events e ON tt.event_id = e.id
        WHERE ci.user_id = ?
    ";
    $result = executeSQL($sql, [$user_id]);
    $items = $result;

    $sqlTotal = "
        SELECT SUM(ci.price * ci.quantity) AS cart_total
        FROM cart_items ci
        WHERE ci.user_id = ?
    ";
    $resultTotal = executeSQL($sqlTotal, [$user_id]);
    $cart_total = 0;
    if (isset($resultTotal[0])) {
        $cart_total = $resultTotal[0]['cart_total'] ?? 0;
    }

    return [
        'items' => $items,
        'cart_total' => $cart_total
    ];
}
