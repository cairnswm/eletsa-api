-- --------------------------------------------------------
-- Host:                         cairns.co.za
-- Server version:               10.6.22-MariaDB-cll-lve - MariaDB Server
-- Server OS:                    Linux
-- HeidiSQL Version:             12.4.0.6659
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

-- Dumping structure for table cairnsco_eletsa.achievements
CREATE TABLE IF NOT EXISTS `achievements` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `achievement_type` varchar(100) NOT NULL,
  `progress` int(11) NOT NULL DEFAULT 0,
  `progress_goal` int(11) NOT NULL DEFAULT 1,
  `level` int(11) NOT NULL DEFAULT 1,
  `points_earned` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `modified_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `achievement_type` (`achievement_type`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- Data exporting was unselected.

-- Dumping structure for table cairnsco_eletsa.cart_items
CREATE TABLE IF NOT EXISTS `cart_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `ticket_type_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 1,
  `price` int(11) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `modified_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `ticket_type_id` (`ticket_type_id`),
  KEY `cart_id` (`user_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- Data exporting was unselected.

-- Dumping structure for table cairnsco_eletsa.comments
CREATE TABLE IF NOT EXISTS `comments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `event_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `content` text NOT NULL,
  `parent_comment_id` int(11) DEFAULT NULL,
  `likes` int(11) NOT NULL DEFAULT 0,
  `is_moderated` tinyint(1) NOT NULL DEFAULT 0,
  `is_visible` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `modified_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `event_id` (`event_id`),
  KEY `user_id` (`user_id`),
  KEY `parent_comment_id` (`parent_comment_id`),
  KEY `is_visible` (`is_visible`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- Data exporting was unselected.

-- Dumping structure for table cairnsco_eletsa.events
CREATE TABLE IF NOT EXISTS `events` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `organizer_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `category` varchar(100) DEFAULT NULL,
  `tags` text DEFAULT NULL,
  `location_name` varchar(255) DEFAULT NULL,
  `location_latitude` decimal(10,7) DEFAULT NULL,
  `location_longitude` decimal(10,7) DEFAULT NULL,
  `start_datetime` datetime NOT NULL,
  `end_datetime` datetime DEFAULT NULL,
  `max_attendees` int(11) DEFAULT NULL,
  `status` enum('draft','published','cancelled','completed') NOT NULL DEFAULT 'draft',
  `images` text DEFAULT NULL,
  `videos` text DEFAULT NULL,
  `popularity_score` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `modified_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `organizer_id` (`organizer_id`),
  KEY `start_datetime` (`start_datetime`),
  KEY `category` (`category`),
  KEY `status` (`status`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- Data exporting was unselected.

-- Dumping structure for table cairnsco_eletsa.followers
CREATE TABLE IF NOT EXISTS `followers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `follower_user_id` int(11) NOT NULL,
  `followed_user_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `modified_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `follower_user_id` (`follower_user_id`),
  KEY `followed_user_id` (`followed_user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- Data exporting was unselected.

-- Dumping structure for table cairnsco_eletsa.monthly_statements
CREATE TABLE IF NOT EXISTS `monthly_statements` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `organizer_id` int(11) NOT NULL,
  `statement_year` int(11) NOT NULL,
  `statement_month` int(11) NOT NULL,
  `total_ticket_sales` decimal(10,2) NOT NULL DEFAULT 0.00,
  `total_refunds` decimal(10,2) NOT NULL DEFAULT 0.00,
  `total_payouts` decimal(10,2) NOT NULL DEFAULT 0.00,
  `total_fees` decimal(10,2) NOT NULL DEFAULT 0.00,
  `statement_generated_at` datetime NOT NULL,
  `statement_data` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `modified_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `organizer_month_year` (`organizer_id`,`statement_year`,`statement_month`),
  KEY `statement_generated_at` (`statement_generated_at`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- Data exporting was unselected.

-- Dumping structure for table cairnsco_eletsa.notifications
CREATE TABLE IF NOT EXISTS `notifications` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `type` enum('new_event_by_followed_organizer','comment_on_event','review_on_event','new_follower','message_received','payout_status_update','achievement_unlocked') NOT NULL,
  `reference_id` int(11) DEFAULT NULL,
  `is_read` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `modified_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `type` (`type`),
  KEY `is_read` (`is_read`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- Data exporting was unselected.

-- Dumping structure for table cairnsco_eletsa.orders
CREATE TABLE IF NOT EXISTS `orders` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `status` enum('pending','paid','failed','cancelled') NOT NULL DEFAULT 'pending',
  `promo_code_id` int(11) DEFAULT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `discount_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `final_amount` decimal(10,2) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `modified_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `status` (`status`),
  KEY `promo_code_id` (`promo_code_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- Data exporting was unselected.

-- Dumping structure for table cairnsco_eletsa.order_items
CREATE TABLE IF NOT EXISTS `order_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order_id` int(11) NOT NULL,
  `ticket_type_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `price_paid` decimal(10,2) NOT NULL,
  `purchase_datetime` datetime NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `modified_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `order_id` (`order_id`),
  KEY `ticket_type_id` (`ticket_type_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- Data exporting was unselected.

-- Dumping structure for table cairnsco_eletsa.organizers
CREATE TABLE IF NOT EXISTS `organizers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `is_verified` tinyint(1) NOT NULL DEFAULT 0,
  `verification_method` enum('none','manual','social_proof','3_events') NOT NULL DEFAULT 'none',
  `social_proof_links` text DEFAULT NULL,
  `payout_eligible` tinyint(1) NOT NULL DEFAULT 0,
  `events_hosted` int(11) NOT NULL DEFAULT 0,
  `tickets_sold` int(11) NOT NULL DEFAULT 0,
  `positive_reviews` int(11) NOT NULL DEFAULT 0,
  `quick_payout_eligibility` tinyint(1) NOT NULL DEFAULT 0,
  `badges` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `modified_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `is_verified` (`is_verified`),
  KEY `verification_method` (`verification_method`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- Data exporting was unselected.

-- Dumping structure for table cairnsco_eletsa.payments
CREATE TABLE IF NOT EXISTS `payments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order_id` int(11) NOT NULL,
  `payment_gateway` varchar(50) NOT NULL,
  `payment_gateway_reference` varchar(255) DEFAULT NULL,
  `status` enum('pending','completed','failed') NOT NULL DEFAULT 'pending',
  `amount` decimal(10,2) NOT NULL,
  `currency` varchar(10) NOT NULL DEFAULT 'USD',
  `payment_datetime` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `modified_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `order_id` (`order_id`),
  KEY `status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- Data exporting was unselected.

-- Dumping structure for table cairnsco_eletsa.payouts
CREATE TABLE IF NOT EXISTS `payouts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `organizer_id` int(11) NOT NULL,
  `payout_amount` decimal(10,2) NOT NULL,
  `payout_fee` decimal(10,2) NOT NULL DEFAULT 0.00,
  `payout_status` enum('pending','approved','paid','failed','cancelled') NOT NULL DEFAULT 'pending',
  `payout_request_date` datetime NOT NULL,
  `payout_processed_date` datetime DEFAULT NULL,
  `payout_method` varchar(50) DEFAULT NULL,
  `payout_reference` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `modified_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `organizer_id` (`organizer_id`),
  KEY `payout_status` (`payout_status`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- Data exporting was unselected.

-- Dumping structure for table cairnsco_eletsa.payout_requests
CREATE TABLE IF NOT EXISTS `payout_requests` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `organizer_id` int(11) NOT NULL,
  `event_id` int(11) DEFAULT NULL,
  `requested_amount` decimal(10,2) NOT NULL,
  `status` enum('pending','approved','paid','rejected') NOT NULL DEFAULT 'pending',
  `payout_date` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `modified_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `organizer_id` (`organizer_id`),
  KEY `status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- Data exporting was unselected.

-- Dumping structure for table cairnsco_eletsa.promo_codes
CREATE TABLE IF NOT EXISTS `promo_codes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `code` varchar(50) NOT NULL,
  `event_id` int(11) DEFAULT NULL,
  `discount_type` enum('percentage','fixed_amount') NOT NULL,
  `discount_value` decimal(10,2) NOT NULL,
  `usage_limit` int(11) DEFAULT NULL,
  `used_count` int(11) NOT NULL DEFAULT 0,
  `start_date` datetime DEFAULT NULL,
  `end_date` datetime DEFAULT NULL,
  `active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `modified_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `code` (`code`),
  KEY `event_id` (`event_id`),
  KEY `active` (`active`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- Data exporting was unselected.

-- Dumping structure for table cairnsco_eletsa.refunds
CREATE TABLE IF NOT EXISTS `refunds` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ticket_id` int(11) NOT NULL,
  `transaction_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `organizer_id` int(11) NOT NULL,
  `refund_amount` decimal(10,2) NOT NULL,
  `refund_reason` text DEFAULT NULL,
  `refund_status` enum('requested','approved','rejected','processed') NOT NULL DEFAULT 'requested',
  `refund_request_date` datetime NOT NULL,
  `refund_processed_date` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `modified_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `ticket_id` (`ticket_id`),
  KEY `transaction_id` (`transaction_id`),
  KEY `user_id` (`user_id`),
  KEY `organizer_id` (`organizer_id`),
  KEY `refund_status` (`refund_status`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- Data exporting was unselected.

-- Dumping structure for table cairnsco_eletsa.reviews
CREATE TABLE IF NOT EXISTS `reviews` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `event_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `rating` tinyint(4) NOT NULL CHECK (`rating` >= 1 and `rating` <= 5),
  `review` text DEFAULT NULL,
  `likes` int(11) NOT NULL DEFAULT 0,
  `is_testimonial` tinyint(1) NOT NULL DEFAULT 0,
  `is_moderated` tinyint(1) NOT NULL DEFAULT 0,
  `is_visible` tinyint(1) NOT NULL DEFAULT 1,
  `organizer_response` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `modified_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `event_id` (`event_id`),
  KEY `user_id` (`user_id`),
  KEY `is_testimonial` (`is_testimonial`),
  KEY `is_visible` (`is_visible`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- Data exporting was unselected.

-- Dumping structure for table cairnsco_eletsa.tickets
CREATE TABLE IF NOT EXISTS `tickets` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order_item_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `ticket_code` varchar(100) NOT NULL,
  `assigned_at` datetime NOT NULL,
  `used` tinyint(1) NOT NULL DEFAULT 0,
  `used_at` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `modified_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `order_item_id` (`order_item_id`),
  KEY `user_id` (`user_id`),
  KEY `ticket_code` (`ticket_code`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- Data exporting was unselected.

-- Dumping structure for table cairnsco_eletsa.tickets_old
CREATE TABLE IF NOT EXISTS `tickets_old` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ticket_type_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `purchase_datetime` datetime NOT NULL,
  `price_paid` decimal(10,2) NOT NULL,
  `payment_status` enum('pending','paid','refunded','cancelled') NOT NULL DEFAULT 'pending',
  `payout_status` enum('not_paid','partial','paid') NOT NULL DEFAULT 'not_paid',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `modified_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `ticket_type_id` (`ticket_type_id`),
  KEY `user_id` (`user_id`),
  KEY `purchase_datetime` (`purchase_datetime`),
  KEY `payment_status` (`payment_status`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- Data exporting was unselected.

-- Dumping structure for table cairnsco_eletsa.ticket_types
CREATE TABLE IF NOT EXISTS `ticket_types` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `event_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `quantity` int(11) DEFAULT NULL,
  `quantity_sold` int(11) NOT NULL DEFAULT 0,
  `refundable` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `modified_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `event_id` (`event_id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- Data exporting was unselected.

-- Dumping structure for table cairnsco_eletsa.transactions
CREATE TABLE IF NOT EXISTS `transactions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `organizer_id` int(11) DEFAULT NULL,
  `related_ticket_id` int(11) DEFAULT NULL,
  `related_payout_id` int(11) DEFAULT NULL,
  `transaction_type` enum('ticket_purchase','ticket_refund','payout','fee') NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `currency` varchar(10) NOT NULL DEFAULT 'ZAR',
  `payment_provider` varchar(50) NOT NULL DEFAULT 'Payweb',
  `payment_reference` varchar(255) DEFAULT NULL,
  `status` enum('pending','completed','failed','refunded','cancelled') NOT NULL DEFAULT 'pending',
  `transaction_date` datetime NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `modified_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `organizer_id` (`organizer_id`),
  KEY `transaction_type` (`transaction_type`),
  KEY `status` (`status`),
  KEY `transaction_date` (`transaction_date`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- Dumping structure for table cairnsco_eletsa.activity_comments
CREATE TABLE IF NOT EXISTS `activity_comments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `activity_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `content` text NOT NULL,
  `parent_comment_id` int(11) DEFAULT NULL,
  `likes` int(11) NOT NULL DEFAULT 0,
  `is_moderated` tinyint(1) NOT NULL DEFAULT 0,
  `is_visible` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `modified_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `activity_id` (`activity_id`),
  KEY `user_id` (`user_id`),
  KEY `parent_comment_id` (`parent_comment_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- Data exporting was unselected.

-- Dumping structure for table cairnsco_eletsa.activity_comment_reactions
CREATE TABLE IF NOT EXISTS `activity_comment_reactions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `comment_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `reaction_type` enum('like','love','laugh','wow','sad','angry') NOT NULL DEFAULT 'like',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_comment_reaction` (`comment_id`,`user_id`),
  KEY `comment_id` (`comment_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- Data exporting was unselected.

-- Dumping structure for table cairnsco_eletsa.activity_reactions
CREATE TABLE IF NOT EXISTS `activity_reactions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `activity_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `reaction_type` enum('like','love','laugh','wow','sad','angry') NOT NULL DEFAULT 'like',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_reaction` (`activity_id`,`user_id`),
  KEY `activity_id` (`activity_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- Data exporting was unselected.

-- Dumping structure for table cairnsco_eletsa.activity_templates
CREATE TABLE IF NOT EXISTS `activity_templates` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `activity_type` enum('event_created','event_reviewed','user_followed','ticket_purchased','achievement_unlocked') NOT NULL,
  `language_code` varchar(10) NOT NULL DEFAULT 'en',
  `template` text NOT NULL,
  `example` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `modified_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `type_language` (`activity_type`,`language_code`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=31 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- Data exporting was unselected.

-- Dumping structure for table cairnsco_eletsa.user_activity_feed
CREATE TABLE IF NOT EXISTS `user_activity_feed` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `activity_type` enum('event_created','event_reviewed','user_followed','ticket_purchased','achievement_unlocked') NOT NULL,
  `reference_id_1` int(11) DEFAULT NULL,
  `reference_id_2` int(11) DEFAULT NULL,
  `metadata` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `template_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `activity_type` (`activity_type`),
  KEY `created_at` (`created_at`),
  KEY `template_id` (`template_id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- Data exporting was unselected.

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
