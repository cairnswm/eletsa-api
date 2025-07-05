<?php

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
    "event" => [
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
        ],
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
