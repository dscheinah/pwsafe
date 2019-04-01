<?php
namespace App\Model;

use Sx\Data\BackendException;
use Sx\Data\Storage;

/**
 * The database storage to handle queries for passwords.
 * This currently uses no SQL abstraction but hard coded SQL and therefore requires to be used with a MySQL adapter.
 *
 * @package App\Model
 */
class PasswordStorage extends Storage
{
    /**
     * Fetches a password entry with full decrypted data by given key.
     * If an incorrect key is given the corresponding columns get a value of NULL.
     *
     * @param string $key
     * @param int    $user
     * @param int    $id
     *
     * @return array
     * @throws BackendException
     */
    public function fetchPassword(string $key, int $user, int $id): array
    {
        // Afterwards the key is available in SQL as the variable @key.
        $this->prepareKey($key);
        $sql = '
            SELECT  p.`id`, `category_id`, p.`name`, `url`,
                AES_DECRYPT(`user`, @key) AS `user`,
                AES_DECRYPT(`email`, @key) AS `email`,
                AES_DECRYPT(`password`, @key) AS `password`,
                AES_DECRYPT(`notice`, @key) AS `notice`,
                c.`name` AS category_name
            FROM `passwords` p 
            LEFT JOIN `categories` c ON p.`category_id` = c.`id`
            WHERE p.`user_id` = ? AND p.`id` = ?;
        ';
        // Return first result or empty array which is falsify.
        return $this->fetch(
            $sql,
            [
                $user,
                $id,
            ]
        )->current() ?: [];
    }

    /**
     * Fetches all passwords for the given user having no category.
     *
     * @param int    $user
     * @param string $term
     *
     * @return \Generator
     * @throws BackendException
     */
    public function fetchPasswords(int $user, string $term = ''): \Generator
    {
        if ($term) {
            // Add a condition to search for the term. Order results with matching name first by using IF.
            $sql = '
                SELECT `id`, `name`, `url` 
                FROM `passwords` 
                WHERE `user_id` = ? AND (`name` LIKE ? OR `url` LIKE ?)
                ORDER BY IF(`name` LIKE ?, 0, 1), `name`, `url`;
            ';
            // Search for substring. Do not use sprintf here since % is ugly.
            $searchTerm = "%$term%";
            $params = [
                $user,
                $searchTerm,
                $searchTerm,
                $searchTerm,
            ];
        } else {
            $sql = '
                SELECT `id`, `name`, `url` 
                FROM `passwords` 
                WHERE `user_id` = ?
                ORDER BY `name`, `url`;
            ';
            $params = [
                $user,
            ];
        }
        yield from $this->fetch($sql, $params);
    }

    /**
     * Fetches all passwords for the given user and category.
     *
     * @param int    $user
     * @param int    $category
     * @param string $term
     *
     * @return \Generator
     * @throws BackendException
     */
    public function fetchPasswordsByCategory(int $user, int $category = null, string $term = ''): \Generator
    {
        if ($term) {
            // Add a condition to search for the term. Order results with matching name first by using IF.
            $sql = '
                SELECT `id`, `name`, `url` 
                FROM `passwords` 
                WHERE `user_id` = ? AND (`name` LIKE ? OR `url` LIKE ?) AND `category_id` <=> ?
                ORDER BY IF(`name` LIKE ?, 0, 1), `name`, `url`;
            ';
            // Search for substring. Do not use sprintf here since % is ugly.
            $searchTerm = "%$term%";
            $params = [
                $user,
                $searchTerm,
                $searchTerm,
                $category,
                $searchTerm,
            ];
        } else {
            $sql = '
                SELECT `id`, `name`, `url` 
                FROM `passwords` 
                WHERE `user_id` = ? AND `category_id` <=> ?
                ORDER BY `name`, `url`;
            ';
            $params = [
                $user,
                $category,
            ];
        }
        yield from $this->fetch($sql, $params);
    }

    /**
     * Insert a new entry. The key is used for encryption and the user ID is fixed. Only available columns will be used
     * from the given data. It returns the newly created auto increment ID.
     *
     * @param string $key
     * @param int    $user
     * @param array  $data
     *
     * @return int
     * @throws BackendException
     */
    public function insertPassword(string $key, int $user, array $data): int
    {
        // Afterwards the key will we available in SQL as a variable.
        $this->prepareKey($key);
        $sql = '
            INSERT INTO `passwords` (`user_id`, `category_id`, `name`, `url`, `user`, `email`, `password`, `notice`)
            VALUES (?, ?, ?, ?, AES_ENCRYPT(?, @key), AES_ENCRYPT(?, @key), AES_ENCRYPT(?, @key), AES_ENCRYPT(?, @key));
        ';
        return $this->insert(
            $sql,
            [
                $user,
                $data['category_id'] ?? null,
                $data['name'] ?? '',
                $data['url'] ?? '',
                $data['user'] ?? '',
                $data['email'] ?? '',
                $data['password'] ?? '',
                $data['notice'] ?? '',
            ]
        );
    }

    /**
     * Updates the data for a password by the given user and ID. Only available columns will be updated from data.
     * The data is encrypted by the given key.
     * There is no return value since errors are indicated by exceptions.
     *
     * @param string $key
     * @param int    $user
     * @param int    $id
     * @param array  $data
     *
     * @throws BackendException
     */
    public function updatePassword(string $key, int $user, int $id, array $data): void
    {
        // Afterwards the key is available in SQL as a variable.
        $this->prepareKey($key);
        $sql = '
            UPDATE `passwords`
            SET `category_id` = ?,
                `name` = ?,
                `url` = ?,
                `user` = AES_ENCRYPT(?, @key),
                `email` = AES_ENCRYPT(?, @key),
                `password` = AES_ENCRYPT(?, @key),
                `notice` = AES_ENCRYPT(?, @key)
            WHERE `user_id` = ? AND `id` = ?;
        ';
        $this->execute(
            $sql,
            [
                $data['category_id'] ?? null,
                $data['name'] ?? '',
                $data['url'] ?? '',
                $data['user'] ?? '',
                $data['email'] ?? '',
                $data['password'] ?? '',
                $data['notice'] ?? '',
                $user,
                $id,
            ]
        );
    }

    /**
     * Deletes the given password by user and ID. The number of deleted rows is returned to indicate success.
     *
     * @param int $user
     * @param int $id
     *
     * @return int
     * @throws BackendException
     */
    public function deletePassword(int $user, int $id): int
    {
        $sql = '
            DELETE FROM `passwords` WHERE `user_id` = ? AND `id` = ?;
        ';
        return $this->execute(
            $sql,
            [
                $user,
                $id,
            ]
        );
    }

    /**
     * Since the adapter only allows one statement per execution the key must be made available as an extra statement.
     * By using an SQL variable the number of bound parameters is reduced in queries.
     *
     * @param string $key
     *
     * @throws BackendException
     */
    private function prepareKey(string $key): void
    {
        $this->execute(
            'SET @key = ?;',
            [
                $key,
            ]
        );
    }
}
