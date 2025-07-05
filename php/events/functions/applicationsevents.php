<?php

function getMostActiveCountries($config, $id)
{
    global $gapiconn;
    $query = "SELECT 
    c.country_name,
    c.country_code,
    COUNT(DISTINCT CASE WHEN e.event_date >= CURDATE() - INTERVAL 30 DAY THEN e.ip_address END) AS ip_last_30_days,
    COUNT(DISTINCT CASE WHEN e.event_date >= CURDATE() - INTERVAL 7 DAY THEN e.ip_address END) AS ip_last_7_days,
    COUNT(DISTINCT CASE WHEN e.event_date = CURDATE() - INTERVAL 1 DAY THEN e.ip_address END) AS ip_yesterday,
    COUNT(DISTINCT CASE WHEN e.event_date = CURDATE() THEN e.ip_address END) AS ip_today
FROM events e
JOIN ip_geolocation_cache geo
  ON e.ip_address = geo.ip_address
JOIN apps_countries c
  ON geo.country = c.country_code
WHERE 
    e.application_id = ? AND
    e.event_date >= CURDATE() - INTERVAL 30 DAY
GROUP BY c.country_code, c.country_name
ORDER BY ip_last_30_days DESC;";
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

function getApplicationsForUser($config, $id)
{
    global $gapiconn;

    $query = "SELECT 
    a.id AS id,
    a.name,
    a.description,
    a.api_key,
    COUNT(e.id) AS totalVisits,
    COUNT(DISTINCT CASE WHEN e.event_date = CURDATE() THEN e.id END) AS eventsToday,
    COUNT(DISTINCT e.ip_address) AS uniqueVisitors,
    COUNT(DISTINCT CASE WHEN e.event_date = CURDATE() THEN e.ip_address END) AS visitsToday,
    COUNT(DISTINCT CASE WHEN e.event_date = CURDATE() - INTERVAL 1 DAY THEN e.ip_address END) AS visitsYesterday,
    COUNT(DISTINCT CASE WHEN e.event_date >= CURDATE() - INTERVAL 7 DAY THEN e.ip_address END) AS visitsTheWeek,
             SEC_TO_TIME(AVG(TIMESTAMPDIFF(SECOND, e.created_at, e.modified_at))) AS avgSession,
             Max(e.modified_at) AS lastUpdated,
    a.created_at 
FROM applications a
LEFT JOIN application_users au ON a.id = au.application_id
LEFT JOIN events e ON e.application_id = a.id
WHERE a.user_id = ? OR au.user_id = ?
GROUP BY a.id;";
    $stmt = $gapiconn->prepare($query);
    $stmt->bind_param('ss', $config['where']['user_id'], $config['where']['user_id']);
    $stmt->execute();
    $result = $stmt->get_result();
    $rows = [];
    while ($row = $result->fetch_assoc()) {
        $rows[] = $row;
    }
    $stmt->close();

    return $rows;
}

function getEvents($config, $id)
{
    global $gapiconn;
    $query = "SELECT 
    e.id,
    e.application_id,
    e.page,
    e.item_id,
    e.type,
    e.ip_address,
    e.event_date,
    e.modified_at,
    geo.country AS country_code,
    c.country_name
FROM events e
LEFT JOIN ip_geolocation_cache geo
  ON e.ip_address = geo.ip_address
LEFT JOIN apps_countries c
  ON geo.country = c.country_code
WHERE e.application_id = ?
ORDER BY e.modified_at DESC
LIMIT 100;";
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
function getSiteEvents($config, $id)
{
    global $gapiconn;
    $query = "SELECT
            application_id applicationId,
            COUNT(*) AS totalVisits,
            COUNT(DISTINCT ip_address) AS uniqueVisitors,
            MAX(event_date) AS lastUpdated,
             SEC_TO_TIME(AVG(TIMESTAMPDIFF(SECOND, created_at, modified_at))) AS avgSession
        FROM events
        WHERE application_id = ?
        GROUP BY application_id;";
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