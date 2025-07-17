<?php

$organizersconfig = [
    "organizer" => [
        "tablename" => "organizers",
        "key" => "user_id",
        "select" => [
            "id",
            "user_id",
            "is_verified",
            "verification_method",
            "social_proof_links",
            "payout_eligible",
            "events_hosted",
            "tickets_sold",
            "positive_reviews",
            "quick_payout_eligibility",
            "badges",
            "created_at",
            "modified_at"
        ],
        "create" => [
            "user_id",
            "is_verified",
            "verification_method",
            "social_proof_links",
            "payout_eligible",
            "events_hosted",
            "tickets_sold",
            "positive_reviews",
            "quick_payout_eligibility",
            "badges"
        ],
        "update" => [
            "is_verified",
            "verification_method",
            "social_proof_links",
            "payout_eligible",
            "events_hosted",
            "tickets_sold",
            "positive_reviews",
            "quick_payout_eligibility",
            "badges"
        ],
        "delete" => true,

        // Subkeys represent related tables linked by organizer_id and only support GET/select operations
        "subkeys" => [            
            "events" => [
                "tablename" => "events",
                "key" => "organizer_id",
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
                ],
            "payout_requests" => [
                "tablename" => "payout_requests",
                "key" => "organizer_id",
                "select" => [
                    "id",
                    "organizer_id",
                    "event_id",
                    "requested_amount",
                    "status",
                    "payout_date",
                    "created_at",
                    "modified_at"
                ],
            ],
            "payouts" => [
                "tablename" => "payouts",
                "key" => "organizer_id",
                "select" => [
                    "id",
                    "organizer_id",
                    "payout_amount",
                    "payout_fee",
                    "payout_status",
                    "payout_request_date",
                    "payout_processed_date",
                    "payout_method",
                    "payout_reference",
                    "created_at",
                    "modified_at"
                ],
            ],
            "transactions" => [
                "tablename" => "transactions",
                "key" => "organizer_id",
                "select" => [
                    "id",
                    "user_id",
                    "organizer_id",
                    "related_ticket_id",
                    "related_payout_id",
                    "transaction_type",
                    "amount",
                    "currency",
                    "payment_provider",
                    "payment_reference",
                    "status",
                    "transaction_date",
                    "created_at",
                    "modified_at"
                ],
            ],
        ],
    ],

    // Top-level configurations to allow create, update, delete on related tables independently

    "payout_requests" => [
        "tablename" => "payout_requests",
        "key" => "id",
        "select" => [
            "id",
            "organizer_id",
            "event_id",
            "requested_amount",
            "status",
            "payout_date",
            "created_at",
            "modified_at"
        ],
        "create" => [
            "organizer_id",
            "event_id",
            "requested_amount",
            "status",
            "payout_date"
        ],
        "update" => [
            "status",
            "payout_date"
        ],
        "delete" => true,
    ],

    "payouts" => [
        "tablename" => "payouts",
        "key" => "id",
        "select" => [
            "id",
            "organizer_id",
            "payout_amount",
            "payout_fee",
            "payout_status",
            "payout_request_date",
            "payout_processed_date",
            "payout_method",
            "payout_reference",
            "created_at",
            "modified_at"
        ],
        "create" => [
            "organizer_id",
            "payout_amount",
            "payout_fee",
            "payout_status",
            "payout_request_date",
            "payout_processed_date",
            "payout_method",
            "payout_reference"
        ],
        "update" => [
            "payout_status",
            "payout_processed_date",
            "payout_method",
            "payout_reference"
        ],
        "delete" => true,
    ],

    "transactions" => [
        "tablename" => "transactions",
        "key" => "id",
        "select" => [
            "id",
            "user_id",
            "organizer_id",
            "related_ticket_id",
            "related_payout_id",
            "transaction_type",
            "amount",
            "currency",
            "payment_provider",
            "payment_reference",
            "status",
            "transaction_date",
            "created_at",
            "modified_at"
        ],
        "create" => [
            "user_id",
            "organizer_id",
            "related_ticket_id",
            "related_payout_id",
            "transaction_type",
            "amount",
            "currency",
            "payment_provider",
            "payment_reference",
            "status",
            "transaction_date"
        ],
        "update" => [
            "status"
        ],
        "delete" => true,
    ],

    "post" => [
        // Add any special POST endpoints here if needed
    ]
];
