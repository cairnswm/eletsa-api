<?php
// Display all PHP errors
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include_once './config.php';
require_once __DIR__ . '/../utils.php';
include_once "../corsheaders.php";
include_once dirname(__FILE__)."/../cairnsgames/permissionfunctions.php";

$appid = getAppId();
if (!$appid) {
    http_response_code(404);
    echo json_encode(['error' => 'App ID not found. Please provide a valid app_id.']);
    exit;
}
$log = [];
$error = [];

$timezone = new DateTimeZone("Africa/Johannesburg"); // SAST timezone
$DateTime = new DateTime("now", $timezone);

$host = $_SERVER['HTTP_HOST'];

$referer = $_SERVER['HTTP_REFERER'] ?? null;

if ($referer) {
    $parsedUrl = parse_url($referer);
    $hostWithPort = $parsedUrl['host'] ?? null;

    // Remove the port if present
    $referer = explode(':', $hostWithPort)[0] ?? null;
}
$domain = $referer ?? $host;

// Do not remove these comments
//    $returnURL = "https://cairnsgames.co.za/php/payweb3/return.php";
// $notifyURL = "https://cairnsgames.co.za/php/payweb3/notify.php";
$missing = [];

$returnURL = getSecretOrProperty("returnURL", null, $domain);
if (!isset($returnURL) || empty($returnURL)) {
    $missing[] = 'Return URL';
}

$notifyURL = getSecretOrProperty("notifyURL", null, $domain);
if (!isset($notifyURL) || empty($notifyURL)) {
    $missing[] = 'Notify URL';
}

$paygateSecret = getSecretOrProperty("PaygateSecret", null, $domain);
if (!isset($paygateSecret) || empty($paygateSecret)) {
    $paygateSecret = $PAYGATE_SECRET;
    if (empty($paygateSecret)) {
        $missing[] = 'Paygate Secret';
    }
}

$paygateid = getSecretOrProperty("PaygateId", null, $domain);
if (!isset($paygateid) || empty($paygateid)) {
    $paygateid = $PAYGATE_ID_DEFAULT;
    if (empty($paygateid)) {
        $missing[] = 'Paygate ID';
    }
}

if (count($missing) > 0) {
    http_response_code(404);
    echo json_encode([
        'error' => 'Missing required configuration values.',
        'missing' => $missing
    ]);
    exit;
}

$log[]=  "Paygate ID: $paygateid";
$log[] = "Paygate Secret: $paygateSecret";
$log[] = "Host: $host";
$log[] = "Domain: $domain";
$log[] = "Return URL: $returnURL";
$log[] = "Notify URL: $notifyURL";
$log[] = "App ID: $appid";

$order_id = $_GET['order_id'];
$encryptionKey = $paygateSecret;

$order_details_sql = "SELECT * FROM orders WHERE id = ?";
$order_details = executeQuery($order_details_sql, [$order_id]);

$order = [];

if (empty($order_details)) {
    $order['currency'] = 'ZAR';
    $order['final_amount'] = 100;
} else {
    $order = $order_details[0];
}


$log[] = "order details:" . json_encode($order_details);

$currency = isset($order['currency']) && !empty($order['currency']) ? $order['currency'] : 'ZAR';
$amount = $order['final_amount'];

$data = array(
    'PAYGATE_ID' => $paygateid,
    'REFERENCE' => $order_id, // Use order_id as the reference
    'AMOUNT' => $amount * 100, // cents
    'CURRENCY' => $currency,
    'RETURN_URL' => $returnURL,
    'TRANSACTION_DATE' => $DateTime->format('Y-m-d H:i:s'),
    'LOCALE' => 'en-za',
    'COUNTRY' => 'ZAF',
    'EMAIL' => 'customer@paygate.co.za',
    'NOTIFY_URL' => $notifyURL,
);

$log[] = "Payment Data: " . json_encode($data);
$checksum = md5(implode('', $data) . $encryptionKey);

$data['CHECKSUM'] = $checksum;
$log[]= "Payment Details: ". json_encode($data);
$log[]=  "Encryption Key: ". $encryptionKey;
$log[]=  "CHECKSUM: ". $checksum;

$fieldsString = http_build_query($data);

// Execute cURL request using the new function
$result = executeCurlRequest('https://secure.paygate.co.za/payweb3/initiate.trans', 'POST', $fieldsString);

$log[]=  "RESPONSE: ". $result;



$log[] = "Payweb Response:" . json_encode($result);

executeQuery("INSERT INTO payment_logs (status_code, request_payload, response_body) VALUES (?, ?, ?)", ['initiate.php', json_encode($data), json_encode($result)]);

// Process the response
parse_str($result, $response); // Parse the query string response
if (!isset($response['CHECKSUM'])) {
    $response['CHECKSUM'] = '';
    $error[] = "Checksum not found in response";
}
$eccode = $response['CHECKSUM'];

$payment_id = null;
$eccode = null;

if (isset($response['PAY_REQUEST_ID'])) {
    $payment_id = $response['PAY_REQUEST_ID'];
    $eccode = $response['CHECKSUM'];
    $status = "pending";
    $source = "paygate";

    // Insert payment tracking data
    $sql = "INSERT INTO payment_progress (order_id, payment_source, payment_id, eccode, status) VALUES (?, ?, ?, ?,?)";
    executeQuery($sql, [$order_id, $source, $payment_id, $eccode, $status]);
}

$out = [
    'payment_id' => $payment_id,
    'checksum' => $eccode
];
$out['debug'] = $log;
if (count($error)) {
    $out['error'] = $error;
}


echo json_encode($out);
