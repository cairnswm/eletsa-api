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
        "tickets" => "getTickets",
        "scan" => "markTicketUsed"
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

function markTicketUsed($config)
{
    $ticketCode = $config['ticket_code'] ?? null;
    $eventCode = $config['event_code'] ?? null;

    if (!$ticketCode || !$eventCode) {
        return [$config, ['error' => 'Missing ticket_code or event_code']];
    }

    // Get event id from event code
    $eventSql = "SELECT id FROM events WHERE code = ?";
    $event = gapiExecuteSQL($eventSql, [$eventCode]);
    if (!$event || count($event) === 0) {
        return [$config, ['error' => 'Invalid event code']];
    }
    $eventId = $event[0]['id'];
    $wasUsedAt = "";

    // Fetch ticket info
    $sql = "SELECT id, user_id, quantity, used, used_at FROM tickets WHERE ticket_code = ? AND event_id = ?";
    $ticket = gapiExecuteSQL($sql, [$ticketCode, $eventId]);

    if (!$ticket || count($ticket) === 0) {
        return [$config, ['error' => 'Invalid ticket or event']];
    }

    $ticketInfo = $ticket[0];
    $wasUsedBefore = $ticketInfo['used'];
    if ($wasUsedBefore == 1) {
        var_dump($ticketInfo);
        $wasUsedAt = $ticketInfo["used_at"];
    }


    // Mark as used if not already
    if (!$wasUsedBefore) {
        $updateSql = "UPDATE tickets SET used = 1, used_at = NOW() WHERE ticket_code = ? AND event_id = ?";
        gapiExecuteSQL($updateSql, [$ticketCode, $eventId]);
    }

    return [
        [
            'event_code' => $eventCode,
            'ticket_code' => $ticketCode,
            'user_id' => $ticketInfo['user_id'],
            'quantity' => $ticketInfo['quantity'],
            'was_used_before' => $wasUsedBefore,
            'was_used_at' => $wasUsedAt
        ]
    ];
}