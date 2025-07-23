<?php

$ticketsconfig = [
    "post" => [
        "tickets" => "getTickets"
    ]
];

function getTickets($config)
{
    global $userid;
    $sql = "SELECT
    e.title AS event_title,
    e.start_datetime,
    e.location_name,
    e.location_latitude,
    e.location_longitude,
    tt.name AS ticket_type,
    tt.price,
    t.quantity,
    t.ticket_code,
    t.assigned_at,
    t.used
FROM tickets t
JOIN events e ON t.event_id = e.id
JOIN ticket_types tt ON t.ticket_type_id = tt.id
WHERE t.user_id = ?
ORDER BY e.start_datetime ASC;
";
  $result = executeSQL($sql, [$userid]);
  return $result;
}
