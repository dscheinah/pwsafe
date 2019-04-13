CREATE TABLE `groups`
(
    `id`   INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(255)
) ENGINE = INNODB;

CREATE TABLE `groups_x_users`
(
    `group_id` INT UNSIGNED NOT NULL,
    `user_id`  INT UNSIGNED NOT NULL,
    FOREIGN KEY (`group_id`) REFERENCES `groups` (`id`) ON DELETE CASCADE,
    FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE = INNODB;
