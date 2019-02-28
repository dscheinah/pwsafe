<?php
namespace App\Model;

use Sx\Data\Storage;

class PasswordStorage extends Storage
{

    public function fetchPassword(string $key, int $user, int $id): array
    {
        $this->prepareKey($key);
        $sql = '
            SELECT  `id`, `name`, `url`,
                AES_DECRYPT(`user`, @key) AS `user`,
                AES_DECRYPT(`email`, @key) AS `email`,
                AES_DECRYPT(`password`, @key) AS `password`,
                AES_DECRYPT(`notice`, @key) AS `notice`
            FROM `passwords` WHERE `user_id` = ? AND `id` = ?;
        ';
        return $this->fetch($sql, [
            $user,
            $id
        ])->current() ?: [];
    }

    public function fetchPasswords(int $user): \Generator
    {
        $sql = '
            SELECT `id`, `name`, `url` FROM `passwords` WHERE `user_id` = ?;
        ';
        return $this->fetch($sql, [
            $user
        ]);
    }

    public function insertPassword(string $key, int $user, array $data): int
    {
        $this->prepareKey($key);
        $sql = '
            INSERT INTO `passwords`(`user_id`, `name`, `url`, `user`, `email`, `password`, `notice`)
            VALUES(?, ?, ?, AES_ENCRYPT(?, @key), AES_ENCRYPT(?, @key), AES_ENCRYPT(?, @key), AES_ENCRYPT(?, @key));
        ';
        return $this->insert($sql, [
            $user,
            $data['name'] ?? '',
            $data['url'] ?? '',
            $data['user'] ?? '',
            $data['email'] ?? '',
            $data['password'] ?? '',
            $data['notice'] ?? ''
        ]);
    }

    public function updatePassword(string $key, int $user, int $id, array $data): void
    {
        $this->prepareKey($key);
        $sql = '
            UPDATE `passwords`
            SET `name` = ?,
                `url` = ?,
                `user` = AES_ENCRYPT(?, @key),
                `email` = AES_ENCRYPT(?, @key),
                `password` = AES_ENCRYPT(?, @key),
                `notice` = AES_ENCRYPT(?, @key)
            WHERE `user_id` = ? AND `id` = ?;
        ';
        $this->execute($sql, [
            $data['name'] ?? '',
            $data['url'] ?? '',
            $data['user'] ?? '',
            $data['email'] ?? '',
            $data['password'] ?? '',
            $data['notice'] ?? '',
            $user,
            $id
        ]);
    }

    public function deletePassword(int $user, int $id): int
    {
        $sql = '
            DELETE FROM `passwords` WHERE `user_id` = ? AND `id` = ?;
        ';
        return $this->execute($sql, [
            $user,
            $id
        ]);
    }

    private function prepareKey(string $key): void
    {
        $this->execute('SET @key = ?;', [
            $key
        ]);
    }
}
