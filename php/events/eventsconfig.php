<?php

include_once __DIR__ . "/../activity/activity_functions.php";

$eventsconfig = [
    "user" => [
        "tablename" => "users",
        "key" => "id",
        "select" => [
            "id",
            "username",
            "email",
            "first_name",
            "last_name",
            "profile_picture",
            "created_at",
            "modified_at"
        ],
        "subkeys" => [
            "events" => [
                "tablename" => "events",
                "key" => "id",
                "select" => [
                    "id",
                    "organizer_id",
                    "title",
                    "description",
                    "category",
                    "tags",
                    "location_name",
                    "location_latitude",
                    "location_longitude",
                    "start_datetime",
                    "end_datetime",
                    "max_attendees",
                    "status",
                    "images",
                    "videos",
                    "popularity_score",
                    "created_at",
                    "modified_at"
                ]
            ]
        ]
    ],
    "scan" => [
        "tablename" => "events",
        "key" => "id",
        "select" => "getEventByCode"
    ],
    "event" => [
        "tablename" => "events",
        "key" => "id",
        "select" => "getEvents",
        "create" => [
            "organizer_id",
            "title",
            "description",
            "category",
            "tags",
            "location_name",
            "location_latitude",
            "location_longitude",
            "start_datetime",
            "end_datetime",
            "max_attendees",
            "status",
            "images",
            "videos"
        ],
        "update" => [
            "title",
            "description",
            "category",
            "tags",
            "location_name",
            "location_latitude",
            "location_longitude",
            "start_datetime",
            "end_datetime",
            "max_attendees",
            "status",
            "images",
            "videos",
            "popularity_score"
        ],
        "delete" => true,
        "aftercreate" => "activityEventCreated",
        "subkeys" => [
            "ticket_types" => [
                "tablename" => "ticket_types",
                "key" => "event_id",
                "select" => [
                    "id",
                    "event_id",
                    "name",
                    "description",
                    "price",
                    "quantity",
                    "quantity_sold",
                    "refundable",
                    "created_at",
                    "modified_at"
                ]
            ],
            "comments" => [
                "tablename" => "comments",
                "key" => "event_id",
                "select" => [
                    "id",
                    "event_id",
                    "user_id",
                    "content",
                    "parent_comment_id",
                    "likes",
                    "is_moderated",
                    "is_visible",
                    "created_at",
                    "modified_at"
                ]
            ],
            "reviews" => [
                "tablename" => "reviews",
                "key" => "event_id",
                "select" => [
                    "id",
                    "user_id",
                    "event_id",
                    "rating",
                    "review", 
                    "created_at"
                ]
            ]
        ],
    ],
    "ticket_type" => [
        "tablename" => "ticket_types",
        "key" => "id",
        "select" => [
            "id",
            "event_id",
            "name",
            "description",
            "price",
            "quantity",
            "quantity_sold",
            "refundable",
            "created_at",
            "modified_at"
        ],
        "create" => [
            "event_id",
            "name",
            "description",
            "price",
            "quantity",
            "refundable"
        ],
        "update" => [
            "name",
            "description",
            "price",
            "quantity",
            "refundable"
        ],
        "delete" => true,
    ],
    "comment" => [
        "tablename" => "comments",
        "key" => "id",
        "select" => [
            "id",
            "event_id",
            "user_id",
            "content",
            "parent_comment_id",
            "likes",
            "is_moderated",
            "is_visible",
            "created_at",
            "modified_at"
        ],
        "create" => [
            "event_id",
            "user_id",
            "content",
            "parent_comment_id"
        ],
        "update" => [
            "content",
            "likes",
            "is_moderated",
            "is_visible"
        ],
        "delete" => true,
    ],
    "post" => [
        // Special POST endpoints can go here
    ]
];


function getEvents($config, $id = null) {
    $sql = "SELECT 
        e.*,
        AVG(r.rating) AS popularity_score
    FROM 
        events e
    JOIN 
        organizers o ON e.organizer_id = o.user_id
    LEFT JOIN 
        reviews r ON r.user_id = o.user_id";
    
    if ($id !== null) {
        $sql .= " WHERE e.id = " . intval($id);
    }
    
    $sql .= " GROUP BY e.id;";

    $result = gapiExecuteSQL($sql);
    return $result;
}

function activityEventCreated($config, $data, $new_record)
{
    $record = $new_record[0];
    insertUserActivityFeed($record['organizer_id'], 'event_created', $record['id'], null, [
        'title' => $record['title'],
        'start_datetime' => $record['start_datetime'],
        'end_datetime' => $record['end_datetime']
    ]);
    // Insert into user activity feed
    return [$config, $data];
}

function getEventByCode($config, $code)
{
    $sql = "SELECT 
    e.id AS event_id,
    e.title,
    e.location_name,
    e.location_latitude,
    e.location_longitude,
    e.start_datetime,
    e.end_datetime,
    e.category,
    e.status,
    COALESCE(SUM(t.quantity), 0) AS tickets_sold,
    COALESCE(SUM(CASE WHEN t.used = 1 THEN t.quantity ELSE 0 END), 0) AS tickets_used
FROM events e
LEFT JOIN tickets t 
    ON t.event_id = e.id
WHERE e.code = ?
GROUP BY 
    e.id, e.title, e.location_name, e.location_latitude, e.location_longitude,
    e.start_datetime, e.end_datetime, e.category, e.status;";

    $result = gapiExecuteSQL($sql, [$code]);
    return $result;
}