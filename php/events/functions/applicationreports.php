<?php

function getSiteEventsByDay($config, $id)
{
    global $gapiconn;
    $query = "SELECT
    application_id applicationId,
    DATE(created_at) AS visitDate,
    COUNT(*) AS totalVisits,
    COUNT(DISTINCT ip_address) AS uniqueVisitors,
    MAX(created_at) AS lastUpdated,
     SEC_TO_TIME(AVG(TIMESTAMPDIFF(SECOND, created_at, modified_at))) AS avgSession
FROM events
WHERE application_id = ?
GROUP BY application_id, DATE(created_at)
ORDER BY visitDate;";
    $stmt = $gapiconn->prepare($query);
    $stmt->bind_param('s', $config['where']['application_id']);
    $stmt->execute();
    $result = $stmt->get_result();
    $rows = [];
    while ($row = $result->fetch_assoc()) {
        $rows[] = $row;
    }
    $stmt->close();

    return $rows;
}

function getPageEvents($config, $id)
{
    global $gapiconn;
    $query = "SELECT
            application_id applicationId, page, page title,
            COUNT(*) AS totalVisits,
            COUNT(DISTINCT ip_address) AS uniqueVisitors,
            MAX(event_date) AS lastUpdated
        FROM events
        WHERE type = 'page'
        AND application_id = ?
        GROUP BY application_id, page;";
    $stmt = $gapiconn->prepare($query);
    $stmt->bind_param('s', $config['where']['application_id']);
    $stmt->execute();
    $result = $stmt->get_result();
    $rows = [];
    while ($row = $result->fetch_assoc()) {
        $rows[] = $row;
    }
    $stmt->close();

    return $rows;
}
function getPageEventsByDay($config, $id)
{
    global $gapiconn;
    $query = "SELECT
    application_id applicationId, page, page title,
    DATE(created_at) AS visitDate,
    COUNT(*) AS totalVisits,
    COUNT(DISTINCT ip_address) AS uniqueVisitors,
    MAX(created_at) AS lastUpdated
FROM events
WHERE type = 'page'
  AND application_id = ?
GROUP BY application_id, page, DATE(created_at)
ORDER BY visitDate;";
    $stmt = $gapiconn->prepare($query);
    $stmt->bind_param('s', $config['where']['application_id']);
    $stmt->execute();
    $result = $stmt->get_result();
    $rows = [];
    while ($row = $result->fetch_assoc()) {
        $rows[] = $row;
    }
    $stmt->close();

    return $rows;
}

function getItemEvents($config, $id)
{
    global $gapiconn;
    $query = "SELECT
            application_id applicationId, page, page title, item_id,
            COUNT(*) AS totalVisits,
            COUNT(DISTINCT ip_address) AS uniqueVisitors,
            MAX(event_date) AS lastUpdated
        FROM events
        WHERE type = 'item'
        AND application_id = ?
        GROUP BY application_id, page, item_id;";
    $stmt = $gapiconn->prepare($query);
    $stmt->bind_param('s', $config['where']['application_id']);
    $stmt->execute();
    $result = $stmt->get_result();
    $rows = [];
    while ($row = $result->fetch_assoc()) {
        $rows[] = $row;
    }
    $stmt->close();

    return $rows;
}
function getItemEventsByDay($config, $id)
{
    global $gapiconn;
    $query = "SELECT
    application_id applicationId, page, page title, item_id,
    DATE(created_at) AS visitDate,
    COUNT(*) AS totalVisits,
    COUNT(DISTINCT ip_address) AS uniqueVisitors,
    MAX(created_at) AS lastUpdated
FROM events
WHERE type = 'item'
  AND application_id = ?
GROUP BY application_id, page, item_id, DATE(created_at)
ORDER BY visitDate;";
    $stmt = $gapiconn->prepare($query);
    $stmt->bind_param('s', $config['where']['application_id']);
    $stmt->execute();
    $result = $stmt->get_result();
    $rows = [];
    while ($row = $result->fetch_assoc()) {
        $rows[] = $row;
    }
    $stmt->close();

    return $rows;
}