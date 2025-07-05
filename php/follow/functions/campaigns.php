<?php

function getCampaignsForUser($config, $id)
{
    global $gapiconn;

    $query = "SELECT 
    c.id,
    c.name,
    c.created_at,
    c.application_id,
    a.name AS application_name,
    a.description AS application_description,
    COALESCE(SUM(click_counts.total_clicks), 0) AS total_clicks,
    COALESCE(SUM(click_counts.unique_clicks), 0) AS unique_clicks
FROM campaigns c
LEFT JOIN applications a ON c.application_id = a.id
LEFT JOIN application_users au ON a.id = au.application_id
LEFT JOIN (
    SELECT 
        l.campaign_id,
        COUNT(*) AS total_clicks,
        COUNT(DISTINCT cl.ip_address) AS unique_clicks
    FROM clicks cl
    JOIN links l ON cl.link_id = l.id
    WHERE cl.created_at >= CURDATE() - INTERVAL ? DAY
    GROUP BY l.campaign_id
) AS click_counts ON click_counts.campaign_id = c.id
WHERE a.user_id = ? OR au.user_id = ?
GROUP BY c.id
ORDER BY c.created_at DESC;
";
    $days = 30;
    $stmt = $gapiconn->prepare($query);
    $stmt->bind_param('iss', $days, $config['where']['user_id'], $config['where']['user_id']);
    $stmt->execute();
    $result = $stmt->get_result();
    $rows = [];
    while ($row = $result->fetch_assoc()) {
        $rows[] = $row;
    }
    $stmt->close();

    return $rows;
}
