CREATE TABLE `users`
(
  `id`       INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `user`     VARCHAR(255) NOT NULL,
  # Saves a hash of the password.
  `password` VARCHAR(255),
  # These BINARY columns are encrypted by the plain text password. The key is used for password data encryption.
  # To allow password changes without re-encrypting all entries the key is added as an in between layer.
  `key`      BINARY(48),
  `email`    VARBINARY(255)
) ENGINE = INNODB;

CREATE TABLE `passwords`
(
  `id`       INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `user_id`  INT UNSIGNED NOT NULL,
  `name`     VARCHAR(255),
  `url`      VARCHAR(255),
  # All BLOB and BINARY columns are encrypted by the key.
  `user`     VARBINARY(255),
  `email`    VARBINARY(255),
  `password` VARBINARY(255),
  `notice`   BLOB,
  FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE = INNODB;

# Create a default user for first login. Users without password can login with any password.
INSERT INTO `users`(`user`)
VALUES ('default');
