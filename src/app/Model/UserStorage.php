<?php
namespace App\Model;

use Sx\Data\Storage;

class UserStorage extends Storage
{

    private $cacheById = [];

    private $cacheByUser = [];

    public function fetchUserById(int $id, string $password): array
    {
        if (isset($this->cacheById[$id])) {
            return $this->cacheById[$id];
        }

        $this->execute('SET @password = ?', [
            hash('sha256', $password)
        ]);
        $sql = '
            SELECT `id`, `user`, `password`, AES_DECRYPT(`email`, @password) AS `email`, AES_DECRYPT(`key`, @password) AS `key`
            FROM `users`
            WHERE `id` = ?;
        ';
        $data = $this->fetch($sql, [
            $id
        ])->current() ?: [];

        $this->cacheById[$id] = $data;
        $this->cacheByUser[$data['user'] ?? ''] = $data;
        return $data;
    }

    public function fetchUserByUser(string $user, string $password): array
    {
        if (isset($this->cacheByUser[$user])) {
            return $this->cacheByUser[$user];
        }

        $this->execute('SET @password = ?', [
            hash('sha256', $password)
        ]);
        $sql = '
            SELECT `id`, `user`, `password`, AES_DECRYPT(`email`, @password) AS `email`, AES_DECRYPT(`key`, @password) AS `key`
            FROM `users`
            WHERE `user` = ?;
        ';
        $data = $this->fetch($sql, [
            $user
        ])->current() ?: [];

        $this->cacheByUser[$user] = $data;
        $this->cacheById[$data['id'] ?? 0] = $data;
        return $data;
    }

    public function updateUser(int $id, string $password, array $data): void
    {
        $this->execute('SET @password = ?', [
            hash('sha256', $password)
        ]);
        $sql = '
            UPDATE `users`
            SET `user` = ?, `password` = ?, `email` = AES_ENCRYPT(?, @password), `key` = AES_ENCRYPT(?, @password)
            WHERE `id` = ?;
        ';
        $this->execute($sql, [
            $data['user'],
            $data['password'] ?? '',
            $data['email'] ?? '',
            $data['key'],
            $id
        ]);
        $this->cacheById[$id] = $data;
        $this->cacheByUser[$data['user'] ?? ''] = $data;
    }
}
