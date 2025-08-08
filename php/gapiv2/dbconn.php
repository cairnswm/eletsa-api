<?php

include_once __DIR__ . "/../eletsaconfig.php";

// Create a global $gapiconn exists for the MySQL connection
global $gapiconn;
$gapiconn = new mysqli($eletsaconfig["server"], $eletsaconfig["username"], $eletsaconfig["password"], $eletsaconfig["database"]);

if ($gapiconn->connect_error) {
    die("Connection failed: " . $gapiconn->connect_error);
}

/**
 * Execute an SQL query with prepared statements
 * 
 * @param string $sql The SQL query with placeholders
 * @param array $params The parameters to bind to the query
 * @return mysqli_stmt The executed statement object
 * */
function gapiExecuteSQL($sql, $params = [], $json = []) {
    global $gapiconn;

    $stmt = $gapiconn->prepare($sql);

    if (!$stmt) {
        throw new Exception("Error preparing statement: " . $gapiconn->error);
    }

    if (!empty($params)) {
        $types = '';
        foreach ($params as $param) {
            if (is_int($param)) {
                $types .= 'i';
            } elseif (is_float($param)) {
                $types .= 'd';
            } elseif (is_string($param)) {
                $types .= 's';
            } else {
                $types .= 'b';
            }
        }
        $bindParams = array($types);
        foreach ($params as $key => $value) {
            $bindParams[] = &$params[$key];
        }
        call_user_func_array(array($stmt, 'bind_param'), $bindParams);
    }

    if (!$stmt->execute()) {
        $stmt->close();
        throw new Exception("Error executing statement: " . $stmt->error);
    }

    $result = $stmt->get_result();
    $rows = [];
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            foreach ($json as $field) {
                if (isset($row[$field])) {
                    $decoded = json_decode($row[$field], true);
                    if (json_last_error() === JSON_ERROR_NONE) {
                        $row[$field] = $decoded;
                    }
                }
            }
            $rows[] = $row;
        }
        $result->free();
    }
    $stmt->close();
    return $rows;
}
