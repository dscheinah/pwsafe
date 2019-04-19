CREATE TABLE `passwords_x_users`
(
  `password_id` INT UNSIGNED NOT NULL,
  `user_id`     INT UNSIGNED NOT NULL,
  FOREIGN KEY (`password_id`) REFERENCES `passwords` (`id`) ON DELETE CASCADE,
  FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE = INNODB;

CREATE TABLE `passwords_x_groups`
(
  `password_id` INT UNSIGNED NOT NULL,
  `group_id`    INT UNSIGNED NOT NULL,
  FOREIGN KEY (`password_id`) REFERENCES `passwords` (`id`) ON DELETE CASCADE,
  FOREIGN KEY (`group_id`) REFERENCES `groups` (`id`) ON DELETE CASCADE
) ENGINE = INNODB;

ALTER TABLE `passwords`
  ADD `shared` BOOLEAN;
