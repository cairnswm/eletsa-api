<?php

$PAYREQUESTID = "5CEA1629-858B-8D48-C6DD-FF66B717812C";
$CHECKSUM = "d416087fc3c68b9d183d67060fe28d77";

$PAYGATE_ID_DEFAULT = 10011072130;
$PAYGATE_SECRET = "secret";
$REFERENCE = 'pgtest_123456789';
$pwhost = 'cairns.co.za'; // Database host
$pwuser = 'cairnsco_eletsa'; // Database username
$pwpassword = 'cairnsco_eletsa'; // Database password
$pwdatabase = 'cairnsco_eletsa'; // Database name
$encryptionKey = 'secret';

// Function to execute SQL statements
function executeQuery($sql, $params = []) {
    global $pwhost, $pwuser, $pwpassword, $pwdatabase;

    $conn = new mysqli($pwhost, $pwuser, $pwpassword, $pwdatabase);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $stmt = $conn->prepare($sql);
    if ($params) {
        $stmt->bind_param(str_repeat('s', count($params)), ...$params);
    }

    $stmt->execute();

    if (stripos($sql, 'SELECT') === 0) {
        $meta = $stmt->result_metadata();
        $fields = [];
        $parameters = [];
        while ($field = $meta->fetch_field()) {
            $parameters[] = &$row[$field->name];
            $fields[] = $field->name;
        }
        call_user_func_array([$stmt, 'bind_result'], $parameters);
        $data = [];
        while ($stmt->fetch()) {
            $rowData = [];
            foreach ($row as $key => $val) {
                $rowData[$key] = $val;
            }
            $data[] = $rowData;
        }
        $stmt->close();
        $conn->close();
        return $data;
    }

    if (stripos($sql, 'INSERT') === 0) {
        $insertId = $conn->insert_id;
        $table = '';
        if (preg_match('/INSERT\s+INTO\s+`?([a-zA-Z0-9_]+)`?/i', $sql, $matches)) {
            $table = $matches[1];
        }
        if ($table && $insertId) {
            $stmt->close();
            $selectSql = "SELECT * FROM `$table` WHERE id = ?";
            $stmt2 = $conn->prepare($selectSql);
            $stmt2->bind_param('i', $insertId);
            $stmt2->execute();
            $meta = $stmt2->result_metadata();
            $fields = [];
            $parameters = [];
            while ($field = $meta->fetch_field()) {
                $parameters[] = &$row[$field->name];
                $fields[] = $field->name;
            }
            call_user_func_array([$stmt2, 'bind_result'], $parameters);
            $data = [];
            while ($stmt2->fetch()) {
                $rowData = [];
                foreach ($row as $key => $val) {
                    $rowData[$key] = $val;
                }
                $data[] = $rowData;
            }
            $stmt2->close();
            $conn->close();
            return $data ? $data[0] : null;
        }
    }

    $stmt->close();
    $conn->close();
}

// Function to execute cURL requests
function executeCurlRequest($url, $method = 'GET', $data = null) {
    $ch = curl_init();

    // Set cURL options
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_REFERER, $_SERVER['HTTP_HOST']);
    
    if ($method === 'POST') {
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_NOBODY, false);
        if ($data) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        }
    }

    // Execute cURL request
    $response = curl_exec($ch);

    // Check for errors
    if (curl_errno($ch)) {
        $error_msg = curl_error($ch);
        curl_close($ch);
        die("cURL error: $error_msg");
    }

    curl_close($ch);
    return $response;
}
?>
