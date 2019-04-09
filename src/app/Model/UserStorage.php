<?php
namespace App\Model;

use Sx\Data\BackendException;
use Sx\Data\Storage;

/**
 * The database storage to handle queries for user data.
 * This currently uses no SQL abstraction but hard coded SQL and therefore requires to be used with a MySQL adapter.
 *
 * @package App\Model
 */
class UserStorage extends Storage
{
    /**
     * Cache for user entries by ID. Users are fetched multiple times by repository.
     *
     * @var array
     */
    private $cacheById = [];

    /**
     * Cache for user entries by user name. Users are fetched multiple times by repository.
     *
     * @var array
     */
    private $cacheByUser = [];

    /**
     * Selects the unencrypted data for a given user.
     *
     * @param int $id
     *
     * @return array
     * @throws BackendException
     */
    public function fetchUserById(int $id): array
    {
        // Do not use the cache here as not needed and no complete data.
        $sql = 'SELECT `id`, `user`, `role` FROM `users` WHERE `id` = ?;';
        // Use the first result or empty array to indicate not found.
        return $this->fetch($sql, [$id])->current() ?: [];
    }

    /**
     * Selects an user by the given ID. Retrieves decrypted key and email and therefore requires the password.
     * If a wrong password is given the columns will have NULL values in the result.
     * Data is cached for ID and user name.
     *
     * @param int    $id
     * @param string $password
     *
     * @return array
     * @throws BackendException
     */
    public function fetchCompleteUserById(int $id, string $password): array
    {
        // Use cache if available.
        if (isset($this->cacheById[$id][$password])) {
            return $this->cacheById[$id][$password];
        }
        // Afterwards the password is available as an SQL variable.
        $this->preparePassword($password);
        $sql = '
            SELECT `id`, `user`, `password`, 
              AES_DECRYPT(`email`, @password) AS `email`, AES_DECRYPT(`key`, @password) AS `key`
            FROM `users`
            WHERE `id` = ?;
        ';
        // Use the first result or empty array to indicate not found.
        $data = $this->fetch($sql, [$id])->current() ?: [];
        // Store cache for both fetch functions.
        $this->cacheById[$id][$password] = $data;
        $this->cacheByUser[$data['user'] ?? ''][$password] = $data;
        return $data;
    }

    /**
     * Selects the unencrypted data for all users.
     *
     * @return \Generator
     * @throws BackendException
     */
    public function fetchUsers(): \Generator
    {
        $sql = 'SELECT `id`, `user` FROM `users` ORDER BY `user`;';
        yield from $this->fetch($sql);
    }

    /**
     * Selects an user by the given name. Retrieves decrypted key and email and therefore requires the password.
     * If a wrong password is given the columns will have NULL values in the result.
     * Data is cached for ID and user name.
     *
     * @param string $user
     * @param string $password
     *
     * @return array
     * @throws BackendException
     */
    public function fetchUserByUser(string $user, string $password): array
    {
        // Use cache if available.
        if (isset($this->cacheByUser[$user][$password])) {
            return $this->cacheByUser[$user][$password];
        }
        // Afterwards the password is available as an SQL variable.
        $this->preparePassword($password);
        $sql = '
            SELECT `id`, `user`, `password`, `role`,
              AES_DECRYPT(`email`, @password) AS `email`, AES_DECRYPT(`key`, @password) AS `key`
            FROM `users`
            WHERE `user` = ?;
        ';
        // Use the first result or an empty array to indicate not found.
        $data = $this->fetch(
            $sql,
            [
                $user,
            ]
        )->current() ?: [];
        // Store cache for both fetch functions.
        $this->cacheByUser[$user][$password] = $data;
        $this->cacheById[$data['id'] ?? 0][$password] = $data;
        return $data;
    }

    /**
     * Inserts a new user and returns the auto increment ID.
     *
     * @param array  $data
     * @param string $password
     *
     * @return int
     * @throws BackendException
     */
    public function insertUser(array $data, string $password): int
    {
        $this->preparePassword($password);
        $sql = '
            INSERT INTO `users` (`user`, `role`, `password`, `email`) 
            VALUES (?, ?, ?, AES_ENCRYPT(?, @password));
        ';
        return $this->insert(
            $sql,
            [
                $data['user'] ?? '',
                $data['role'] ?? null,
                $data['password'] ?? '',
                $data['email'] ?? '',
            ]
        );
    }

    /**
     * Updates the user data.
     *
     * @param int   $id
     * @param array $data
     *
     * @throws BackendException
     */
    public function updateUser(int $id, array $data): void
    {
        $sql = 'UPDATE `users` SET `user` = ?, `role` = ? WHERE `id` = ?;';
        $this->execute($sql, [$data['user'] ?? '', $data['role'] ?? null, $id]);
    }

    /**
     * Updates data for the given user and use the given password to encrypt the key and email.
     * Only updates available columns from data.
     *
     * @param int    $id
     * @param string $password
     * @param array  $data
     *
     * @throws BackendException
     */
    public function updateProfile(int $id, string $password, array $data): void
    {
        // Afterwards the password is available as an SQL variable.
        $this->preparePassword($password);
        $sql = '
            UPDATE `users`
            SET `user` = ?, `password` = ?, `email` = AES_ENCRYPT(?, @password), `key` = AES_ENCRYPT(?, @password)
            WHERE `id` = ?;
        ';
        $this->execute(
            $sql,
            [
                $data['user'],
                // Can be null for initial users if the update only sets a new key.
                $data['password'] ?? '',
                // Is considered optional data and must not be set.
                $data['email'] ?? '',
                $data['key'],
                $id,
            ]
        );
        // Update caches to represent the new data.
        $this->cacheById[$id][$password] = $data;
        $this->cacheByUser[$data['user'] ?? ''][$password] = $data;
    }

    /**
     * Deletes the given user and returns the number of deleted rows to indicate success.
     *
     * @param int $id
     *
     * @return int
     * @throws BackendException
     */
    public function deleteUser(int $id): int
    {
        $sql = 'DELETE FROM `users` WHERE `id` = ?;';
        return $this->execute($sql, [$id]);
    }

    /**
     * Use the password as de-/ encryption key. Hash it as recommended by MySQL. Since the backend only supports one
     * statement per execute, store an SQL variable to reduce bounded parameters in queries.
     *
     * @param string $password
     *
     * @throws BackendException
     */
    private function preparePassword(string $password): void
    {
        $this->execute(
            'SET @password = ?',
            [
                hash('sha256', $password),
            ]
        );
    }
}
