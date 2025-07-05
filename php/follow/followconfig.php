<?php

$followconfig = [
    // List users that a given user follows: GET /user/{id}/follows
    "user" => [
        "tablename" => "followers",
        "key" => "id",
        "select" => [
            "id",
            "follower_user_id",
            "followed_user_id"
        ],
        "subkeys" => [
            "followed" => [
                "tablename" => "followers",
                "key" => "followed_user_id",
                "select" => [
                    "id",
                    "follower_user_id",
                    "followed_user_id"
                ]
            ],
            "follows" => [
                "tablename" => "followers",
                "key" => "follower_user_id",
                "select" => [
                    "id",
                    "follower_user_id",
                    "followed_user_id"
                ]
            ]
        ]
    ],
    "followers" => [
        "tablename" => "followers",
        "key" => "id",
        "select" => [
            "id",
            "follower_user_id",
            "followed_user_id",
            "created_at",
            "modified_at"
        ],
        "create" => [
            "follower_user_id",
            "followed_user_id"
        ],
        "delete" => true,
        // No update allowed for follower record (optional)
        "update" => false,
    ],

    "post" => [
        // Special POST endpoints can go here
    ]
];
