<?php
namespace App\Model;

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
     * Selects an user by the given ID. Retrieves decrypted key and email and therefore requires the password.
     * If a wrong password is given the columns will have NULL values in the result.
     * Data is cached for ID and user name.
     *
     * @param int    $id
     * @param string $password
     *
     * @return array
     * @throws \Sx\Data\BackendException
     */
    public function fetchUserById(int $id, string $password): array
    {
        // Use cache if available.
        if (isset($this->cacheById[$id])) {
            return $this->cacheById[$id];
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
        $data = $this->fetch(
            $sql,
            [
                $id,
            ]
        )->current() ?: [];
        // Store cache for both fetch functions.
        $this->cacheById[$id] = $data;
        $this->cacheByUser[$data['user'] ?? ''] = $data;
        return $data;
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
     * @throws \Sx\Data\BackendException
     */
    public function fetchUserByUser(string $user, string $password): array
    {
        // Use cache if available.
        if (isset($this->cacheByUser[$user])) {
            return $this->cacheByUser[$user];
        }
        // Afterwards the password is available as an SQL variable.
        $this->preparePassword($password);
        $sql = '
            SELECT `id`, `user`, `password`, 
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
        $this->cacheByUser[$user] = $data;
        $this->cacheById[$data['id'] ?? 0] = $data;
        return $data;
    }

    /**
     * Updates data for the given user and use the given password to encrypt the key and email.
     * Only updates available columns from data.
     *
     * @param int    $id
     * @param string $password
     * @param array  $data
     *
     * @throws \Sx\Data\BackendException
     */
    public function updateUser(int $id, string $password, array $data): void
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
        $this->cacheById[$id] = $data;
        $this->cacheByUser[$data['user'] ?? ''] = $data;
    }

    /**
     * Use the password as de-/ encryption key. Hash it as recommended by MySQL. Since the backend only supports one
     * statement per execute, store an SQL variable to reduce bounded parameters in queries.
     *
     * @param string $password
     *
     * @throws \Sx\Data\BackendException
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
