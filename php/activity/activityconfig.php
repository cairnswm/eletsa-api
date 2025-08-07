<?php

include_once __DIR__ . "/activity_functions.php";

$activityconfig = [
    "user" => [
        "tablename" => "user_activity_feed",
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
            "wall" => [
                "tablename" => "user_activity_feed",
                "key" => "user_id",
                "select" => "getUserActivityFeed",
            ]
        ]
    ],
    "activityComment" => [
        "tablename" => "activity_comments",
        "key" => "id",
        "select" => [
            "id",
            "activity_id",
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
            "activity_id",
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
    "activityCommentReaction" => [
        "tablename" => "activity_comment_reactions",
        "key" => "id",
        "select" => [
            "id",
            "comment_id",
            "user_id",
            "reaction_type",
            "created_at"
        ],
        "create" => [
            "comment_id",
            "user_id",
            "reaction_type"
        ],
        "update" => [
            "reaction_type"
        ],
        "delete" => true,
    ],
    "activityReaction" => [
        "tablename" => "activity_reactions",
        "key" => "id",
        "select" => [
            "id",
            "activity_id",
            "user_id",
            "reaction_type",
            "created_at"
        ],
        "create" => [
            "activity_id",
            "user_id",
            "reaction_type"
        ],
        "update" => [
            "reaction_type"
        ],
        "delete" => true,
    ],
    "activityTemplate" => [
        "tablename" => "activity_templates",
        "key" => "id",
        "select" => [
            "id",
            "activity_type",
            "language_code",
            "template",
            "example",
            "created_at",
            "modified_at"
        ],
        "create" => [
            "activity_type",
            "language_code",
            "template",
            "example"
        ],
        "update" => [
            "template",
            "example"
        ],
        "delete" => true,
    ],
    "userActivityFeed" => [
        "tablename" => "user_activity_feed",
        "key" => "id",
        "select" => [
            "id",
            "user_id",
            "activity_type",
            "reference_id_1",
            "reference_id_2",
            "metadata",
            "created_at",
            "template_id"
        ],
        "create" => false,
        "update" => false,
        "delete" => true,
        "subkeys" => [
            "comments" => [
                "tablename" => "activity_comments",
                "key" => "activity_id",
                "select" => [
                    "id",
                    "activity_id",
                    "user_id",
                    "content",
                    "parent_comment_id",
                    "likes",
                    "is_moderated",
                    "is_visible",
                    "created_at",
                    "modified_at"
                ],
            ],
            "reactions" => [
                "tablename" => "activity_reactions",
                "key" => "activity_id",
                "select" => [
                    "id",
                    "activity_id",
                    "user_id",
                    "reaction_type",
                    "created_at"
                ],
            ]
        ]
    ],
    "post" => [
        // Special POST endpoints can go here
    ]
];
