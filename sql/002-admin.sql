ALTER TABLE `users`
    ADD `role` ENUM ('user', 'admin') DEFAULT 'user';

UPDATE `users`
SET `role` = 'admin'
LIMIT 1;
