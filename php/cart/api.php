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
            ci.id as cart_item_id,
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
    $result = gapiExecuteSQL($sql, [$user_id]);
    $items = $result;

    $sqlTotal = "
        SELECT SUM(ci.price * ci.quantity) AS cart_total
        FROM cart_items ci
        WHERE ci.user_id = ?
    ";
    $resultTotal = gapiExecuteSQL($sqlTotal, [$user_id]);
    $cart_total = 0;
    if (isset($resultTotal[0])) {
        $cart_total = $resultTotal[0]['cart_total'] ?? 0;
    }

    return [
        'items' => $items,
        'cart_total' => $cart_total
    ];
}


function cartToOrder() {
    global $user;

    if (!isset($user)) {
        http_response_code(401);
        return ["error" => true, "message" => "Unauthorized"];
    }

    $userId = $user->id;

    // Fetch cart items for the user
    $cartItemsSql = "SELECT ticket_type_id, quantity, price FROM cart_items WHERE user_id = ?";
    $cartItems = gapiExecuteSQL($cartItemsSql, [$userId]);

    if (empty($cartItems)) {
        return ["error" => true, "message" => "Cart is empty"];
    }

    // Calculate total and final amounts
    $totalAmount = 0;
    foreach ($cartItems as $item) {
        $totalAmount += $item["quantity"] * $item["price"];
    }
    $finalAmount = $totalAmount; // Apply any discounts or adjustments here if needed

    // Create a new order
    $createOrderSql = "INSERT INTO orders (user_id, total_amount, final_amount, created_at, modified_at) VALUES (?, ?, ?, NOW(), NOW())";
    gapiExecuteSQL($createOrderSql, [$userId, $totalAmount, $finalAmount]);

    // Get the newly created order ID
    $orderIdSql = "SELECT LAST_INSERT_ID() AS order_id";
    $orderIdResult = gapiExecuteSQL($orderIdSql);
    $orderId = $orderIdResult[0]["order_id"];

    // Create order items from cart items
    foreach ($cartItems as $item) {
        $createOrderItemSql = "INSERT INTO order_items (order_id, ticket_type_id, quantity, price, created_at, modified_at) VALUES (?, ?, ?, ?, NOW(), NOW())";
        gapiExecuteSQL($createOrderItemSql, [$orderId, $item["ticket_type_id"], $item["quantity"], $item["price"]]);
    }

    // Clear the user's cart
    $clearCartSql = "DELETE FROM cart_items WHERE user_id = ?";
    gapiExecuteSQL($clearCartSql, [$userId]);

    return ["success" => true, "message" => "Order created successfully", "order_id" => $orderId];
}
