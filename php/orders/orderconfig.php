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
            "orders" => [
                "tablename" => "orders",
                "key" => "user_id",
                "select" => "getOrders"
            ]
        ]
    ],
    "orders" => [
        "tablename" => "orders",
        "key" => "id",
        "select" => [
            "id",
            "user_id",
            "promo_code_id",
            "created_at",
            "modified_at"
        ],
        "create" => [
            "user_id",
            "promo_code_id"
        ],
        "update" => [
            "promo_code_id"
        ],
        "delete" => true,
        "subkeys" => [
            "items" => [
                "tablename" => "order_items",
                "key" => "order_id",
                "select" => [
                    "id",
                    "order_id",
                    "ticket_type_id",
                    "quantity",
                    "price",
                    "created_at",
                    "modified_at"
                ]
            ]
        ]
    ],
    "order_items" => [
        "tablename" => "order_items",
        "key" => "id",
        "select" => [
            "id",
            "order_id",
            "ticket_type_id",
            "quantity",
            "price",
            "created_at",
            "modified_at"
        ],
        "create" => [
            "order_id",
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
    "post" => [
    ]
];


