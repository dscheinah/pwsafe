CREATE TABLE `categories`
(
  `id`      INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT UNSIGNED NOT NULL,
  `name`    VARCHAR(255),
  FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE = INNODB;

ALTER TABLE `passwords`
  ADD `category_id` INT UNSIGNED,
  ADD FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`);
