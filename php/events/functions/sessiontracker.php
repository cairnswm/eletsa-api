<?php

function sessionTracker() {
  global $gapiconn;
  $app_id = getParam("app", 0);
  $date = getParam("date", 0);
  $ip = getParam("ip", 0);

  $sql = "SELECT * FROM events WHERE ip_address = ?
AND application_id = ?
AND event_date = ?";

$prevRow = null;

    $stmt = $gapiconn->prepare($sql);
    $stmt->bind_param('sis', $ip, $app_id, $date);
    $stmt->execute();
    $result = $stmt->get_result();
    $rows = [];
    while ($row = $result->fetch_assoc()) {
        $row["duration"] = 0;
        if ($prevRow) {
            $row["duration"] = strtotime($row["created_at"]) - strtotime($prevRow["created_at"]);
        }
        $rows[] = $row;
        $prevRow = $row;
    }
    $stmt->close();

  return $rows;

}