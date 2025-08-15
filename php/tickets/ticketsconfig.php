<?php

include_once __DIR__ . "/../activity/activity_functions.php";

$ticketsconfig = [
    "review" => [
        "tablename" => "reviews",
        "key" => "id",
        "select" => ["id", "user_id", "event_id", "rating", "review"],
        "create" => ["user_id", "event_id", "rating", "review"],
        "aftercreate" => "afterCreateReview"
    ],
    "attendance" => [
        "tablename" => "tickets",
        "key" => "ticket_code",
        "select" => ["ticket_code", "used"],
        "update" => ["used"]
    ],
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
    e.end_datetime,
    e.location_name,
    e.location_latitude,
    e.location_longitude,
    tt.name AS ticket_type,
    tt.price,
    t.quantity,
    t.ticket_code,
    t.assigned_at,
    t.used,
    r.rating,
    r.review
FROM tickets t
JOIN events e ON t.event_id = e.id
JOIN ticket_types tt ON t.ticket_type_id = tt.id
LEFT JOIN reviews r ON r.event_id = e.id AND r.user_id = t.user_id
WHERE t.user_id = ?
ORDER BY e.start_datetime ASC;
";
  $result = gapiExecuteSQL($sql, [$userid]);
  return $result;
}

function afterCreateReview($config, $data, $new_record)
{
    $record = $new_record[0];
    // Insert into user activity feed
    insertUserActivityFeed($record['user_id'], 'event_reviewed', $record['event_id'], $record['id'], [
        'rating' => $record['rating'],
        'review' => $record['review']
    ]);
    return [$config, $data];
}