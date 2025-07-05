<?php


include_once "./eletsaconfig.php";
global $conn;

function getConnection() {
    global $conn;
    
    if (!isset($conn)) {
        global $eletsaconfig;
        $conn = new mysqli($eletsaconfig["server"], $eletsaconfig["username"], $eletsaconfig["password"], $eletsaconfig["database"]);

        if ($conn->connect_error) {
            throw new Exception("Connection failed: " . $conn->connect_error);
        }
    }
    
    return $conn;
}

function executeSQL($sql, $params = [], $json = []) {
    $conn = getConnection();
    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        throw new Exception("Query preparation failed: " . $conn->error);
    }

    if (!empty($params)) {
        $types = str_repeat('s', count($params));
        $stmt->bind_param($types, ...$params);
    }

    if (!$stmt->execute()) {
        throw new Exception("Query execution failed: " . $stmt->error);
    }

    // Check if this is a SELECT statement
    if (stripos(trim($sql), 'select') === 0) {
        $result = $stmt->get_result();
        $rows = [];
        while ($row = $result->fetch_assoc()) {
            foreach ($json as $field) {
                if (isset($row[$field])) {
                    $decoded = json_decode($row[$field], true);
                    if (json_last_error() === JSON_ERROR_NONE) {
                        $row[$field] = $decoded;
                    } else {
                        $row[$field] = new stdClass();
                    }
                }
            }
            $rows[] = $row;
        }
        $stmt->close();
        return $rows;
    }

    return $stmt;
}

