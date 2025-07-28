<?php

$txconfig = [
    "post" => [
        "transactions" => "getTransactions"
    ]
];

function getTransactions($config)
{
    global $userid;
    $sql = "SELECT
    t.transaction_date,
    tt.name AS ticket_type_name,
    e.title AS event_name,
    ti.quantity,
    t.amount,
    t.payout_amount,
    t.cost_amount,
    t.balance,
    t.id AS transaction_id,
    t.user_id,
    t.order_id,
    t.order_item_id,
    t.ticket_id,
    t.organizer_id
FROM transactions t
LEFT JOIN tickets ti ON t.ticket_id = ti.id
LEFT JOIN ticket_types tt ON ti.ticket_type_id = tt.id
LEFT JOIN events e ON ti.event_id = e.id
WHERE t.organizer_id = ?
ORDER BY t.transaction_date DESC;
";
    $result = executeSQL($sql, [$userid]);
    return $result;
}
