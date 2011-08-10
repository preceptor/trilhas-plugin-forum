CREATE TABLE IF NOT EXISTS `forum` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) NOT NULL,
  `classroom_id` bigint(20) NOT NULL,
  `title` varchar(255) DEFAULT NULL,
  `description` text NOT NULL,
  `begin` date NOT NULL,
  `end` date DEFAULT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `classroom_id` (`classroom_id`)
);

CREATE TABLE IF NOT EXISTS `forum_access` (
  `user_id` bigint(20) NOT NULL,
  `forum_id` bigint(20) NOT NULL,
  `updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`user_id`,`forum_id`),
  KEY `forum_id` (`forum_id`)
);

CREATE TABLE IF NOT EXISTS `forum_reply` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `forum_id` bigint(20) DEFAULT NULL,
  `forum_reply_id` bigint(20) DEFAULT NULL,
  `user_id` bigint(20) NOT NULL,
  `description` text NOT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `forum_id` (`forum_id`),
  KEY `user_id` (`user_id`),
  KEY `forum_reply_id` (`forum_reply_id`)
);

CREATE TABLE IF NOT EXISTS `forum_subscribe` (
  `user_id` bigint(20) NOT NULL,
  `forum_id` bigint(20) NOT NULL,
  PRIMARY KEY (`user_id`,`forum_id`)
);


ALTER TABLE `forum`
  ADD CONSTRAINT `forum_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`),
  ADD CONSTRAINT `forum_ibfk_2` FOREIGN KEY (`classroom_id`) REFERENCES `classroom` (`id`);

ALTER TABLE `forum_access`
  ADD CONSTRAINT `forum_access_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`),
  ADD CONSTRAINT `forum_access_ibfk_2` FOREIGN KEY (`forum_id`) REFERENCES `forum` (`id`);

ALTER TABLE `forum_reply`
  ADD CONSTRAINT `forum_reply_ibfk_1` FOREIGN KEY (`forum_id`) REFERENCES `forum` (`id`),
  ADD CONSTRAINT `forum_reply_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`),
  ADD CONSTRAINT `forum_reply_ibfk_3` FOREIGN KEY (`forum_reply_id`) REFERENCES `forum_reply` (`id`);