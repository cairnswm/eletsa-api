<?php
include_once __DIR__ . "/../corsheaders.php";

function insertActivityComment($activityId, $userId, $content, $parentCommentId = null)
{
  $sql = "INSERT INTO activity_comments (activity_id, user_id, content, parent_comment_id) VALUES (?, ?, ?, ?)";
  $params = [$activityId, $userId, $content, $parentCommentId];
  $stmt = executeSQL($sql, $params);
  return $stmt->insert_id;
}

function insertActivityCommentReaction($commentId, $userId, $reactionType = 'like')
{
  $sql = "INSERT INTO activity_comment_reactions (comment_id, user_id, reaction_type) VALUES (?, ?, ?)";
  $params = [$commentId, $userId, $reactionType];
  $stmt = executeSQL($sql, $params);
  return $stmt->insert_id;
}

function insertActivityReaction($activityId, $userId, $reactionType = 'like')
{
  $sql = "INSERT INTO activity_reactions (activity_id, user_id, reaction_type) VALUES (?, ?, ?)";
  $params = [$activityId, $userId, $reactionType];
  $stmt = executeSQL($sql, $params);
  return $stmt->insert_id;
}

function insertActivityTemplate($activityType, $languageCode = 'en', $template, $example = null)
{
  $sql = "INSERT INTO activity_templates (activity_type, language_code, template, example) VALUES (?, ?, ?, ?)";
  $params = [$activityType, $languageCode, $template, $example];
  $stmt = executeSQL($sql, $params);
  return $stmt->insert_id;
}

function getActivityTemplateId($activityType, $languageCode = 'en')
{
  $sql = "SELECT id FROM activity_templates WHERE activity_type = ? AND language_code = ? LIMIT 1";
  $params = [$activityType, $languageCode];
  $rows = executeSQL($sql, $params);
  return isset($rows[0]['id']) ? $rows[0]['id'] : null;
}

function insertUserActivityFeed($userId, $activityType, $referenceId1 = null, $referenceId2 = null, $metadata = null, $templateId = null)
{
  if ($templateId === null) {
    $templateId = getActivityTemplateId($activityType, 'en');
  }

  $sql = "INSERT INTO user_activity_feed (user_id, activity_type, reference_id_1, reference_id_2, metadata, template_id) VALUES (?, ?, ?, ?, ?, ?)";
  $params = [$userId, $activityType, $referenceId1, $referenceId2, $metadata, $templateId];
  $stmt = executeSQL($sql, $params);
  return $stmt->insert_id;
}

/*
Understanding reference_id_1 and reference_id_2
In the user_activity_feed table, the fields reference_id_1 and reference_id_2 are used to link each activity to relevant data in other tables, depending on the activity_type. This flexible structure allows the system to store different combinations of related information for each type of user action, without needing a different schema per activity.

Field	Type	Description
reference_id_1	INT	The primary related record ID for the activity
reference_id_2	INT	The secondary related record ID, if needed

📌 Reference Mapping by activity_type
activity_type	        reference_id_1	                   reference_id_2
event_created	        event_id (from events)	           NULL
event_reviewed	      event_id (from events)	           review_id (from reviews)
user_followed	        followed_user_id (integer user ID) NULL
ticket_purchased	    event_id (from events)	           ticket_type_id (from ticket_types)
achievement_unlocked	achievement_id (from achievements) NULL
*/

function insertActivityEventCreated($userId, $eventId, $metadata = null)
{
  return insertUserActivityFeed($userId, 'event_created', $eventId, null, $metadata, $templateId);
}

function insertActivityEventReviewed($userId, $eventId, $reviewId, $metadata = null)
{
  return insertUserActivityFeed($userId, 'event_reviewed', $eventId, $reviewId, $metadata, $templateId);
}

function insertActivityUserFollowed($userId, $followedUserId, $metadata = null)
{
  return insertUserActivityFeed($userId, 'user_followed', $followedUserId, null, $metadata, $templateId);
}

function insertActivityTicketPurchased($userId, $eventId, $ticketTypeId, $metadata = null)
{
  return insertUserActivityFeed($userId, 'ticket_purchased', $eventId, $ticketTypeId, $metadata, $templateId);
}

function insertActivityAchievementUnlocked($userId, $achievementId, $metadata = null)
{
  return insertUserActivityFeed($userId, 'achievement_unlocked', $achievementId, null, $metadata, $templateId);
}

function getUserActivityFeed($config)
{
  $limit = 50;
  $userId = $config["where"]["user_id"] ?? null;
  $sql = "
    SELECT af.*, at.template AS template_text,
CASE WHEN af.activity_type = 'user_followed' THEN af.reference_id_1 ELSE NULL END AS followed_user_id,
CASE WHEN af.activity_type IN ('event_created', 'ticket_purchased') THEN e.title
     WHEN af.activity_type = 'event_reviewed' THEN e2.title
     ELSE NULL
END AS event_title,
CASE WHEN af.activity_type IN ('event_created', 'ticket_purchased') THEN e.start_datetime
     WHEN af.activity_type = 'event_reviewed' THEN e2.start_datetime
     ELSE NULL
END AS event_date,
r.rating AS review_rating,
LEFT(r.review, 100) AS review_snippet,
tt.name AS ticket_type_name,
t.quantity AS ticket_quantity,
COALESCE(rt.total_reactions, 0) AS total_reactions,
rt.reaction_breakdown,
COALESCE(cm.total_comments, 0) AS total_comments
FROM (
  SELECT af.*
  FROM followers f
  JOIN user_activity_feed af ON af.user_id = f.followed_user_id
  WHERE f.follower_user_id = ?
  ORDER BY af.created_at DESC
  LIMIT 50
) af
LEFT JOIN activity_templates at ON af.template_id = at.id AND at.language_code = 'en'
LEFT JOIN events e ON af.activity_type IN ('event_created', 'ticket_purchased') AND e.id = af.reference_id_1
LEFT JOIN events e2 ON af.activity_type = 'event_reviewed' AND e2.id = af.reference_id_1
LEFT JOIN reviews r ON af.activity_type = 'event_reviewed' AND r.id = af.reference_id_2
LEFT JOIN tickets t ON af.activity_type = 'ticket_purchased' AND t.id = af.reference_id_2
LEFT JOIN ticket_types tt ON af.activity_type = 'ticket_purchased' AND tt.id = t.ticket_type_id
LEFT JOIN (
  SELECT ar.activity_id,
         COUNT(*) AS total_reactions,
         JSON_OBJECTAGG(ar.reaction_type, cnt) AS reaction_breakdown
  FROM (
    SELECT activity_id, reaction_type, COUNT(*) AS cnt
    FROM activity_reactions
    GROUP BY activity_id, reaction_type
  ) ar
  GROUP BY ar.activity_id
) rt ON rt.activity_id = af.id
LEFT JOIN (
  SELECT activity_id, COUNT(*) AS total_comments
  FROM activity_comments
  WHERE is_visible = 1
  GROUP BY activity_id
) cm ON cm.activity_id = af.id;
  ";
  $params = [$userId];
  return executeSQL($sql, $params, ['reaction_breakdown', 'metadata']);
}