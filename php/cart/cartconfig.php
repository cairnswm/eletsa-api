<?php

$cartconfig = [
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
            "cart" => [
                "tablename" => "cart_items",
                "key" => "id",
                "select" => "getCart"
            ]
        ]
    ],
    "item" => [
        "tablename" => "cart_items",
        "key" => "id",
        "select" => [
            "id",
            "user_id",
            "ticket_type_id",
            "quantity",
            "price",
            "created_at",
            "modified_at"
        ],
        "create" => [
            "user_id",
            "ticket_type_id",
            "quantity",
            "price"
        ],
        "update" => [
            "quantity",
            "price"
        ],
        "delete" => true
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
        "carttoorder" => "cartToOrder"
    ]
];
