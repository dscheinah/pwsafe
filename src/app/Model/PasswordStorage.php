<?php
namespace App\Model;

use Sx\Data\BackendException;
use Sx\Data\BackendInterface;
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
     * The key to be used to de- and encrypt shared passwords.
     *
     * @var string
     */
    private $shareKey;

    /**
     * Creates the storage with the backend and the encryption key for shared passwords.
     *
     * @param BackendInterface $backend
     * @param string           $shareKey
     */
    public function __construct(BackendInterface $backend, string $shareKey)
    {
        parent::__construct($backend);
        $this->shareKey = $shareKey;
    }

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
            SELECT  p.`id`, `category_id`, p.`name`, `url`, p.`user_id` = ? AS own,
                AES_DECRYPT(p.`user`, IF (p.`shared`, @shared, @key)) AS `user`,
                AES_DECRYPT(p.`email`, IF (p.`shared`, @shared, @key)) AS `email`,
                AES_DECRYPT(p.`password`, IF (p.`shared`, @shared, @key)) AS `password`,
                AES_DECRYPT(p.`notice`, IF (p.`shared`, @shared, @key)) AS `notice`,
                c.`name` AS category_name
            FROM `passwords` p 
            LEFT JOIN `categories` c ON p.`category_id` = c.`id`
            WHERE (p.`user_id` = ? OR p.`id` IN (%s)) AND p.`id` = ?;
        ';
        // Return first result or empty array which is falsify.
        return $this->fetch(
            sprintf($sql, $this->getSubSelect()),
            [
                $user,
                $user,
                $user,
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
                SELECT `id`, `name`, `url`, `user_id` = ? AS own
                FROM `passwords` 
                WHERE (`user_id` = ? OR `id` IN (%s)) AND (`name` LIKE ? OR `url` LIKE ?)
                ORDER BY IF(`name` LIKE ?, 0, 1), `name`, `url`;
            ';
            // Search for substring. Do not use sprintf here since % is ugly.
            $searchTerm = "%$term%";
            $params = [
                $user,
                $user,
                $user,
                $user,
                $searchTerm,
                $searchTerm,
                $searchTerm,
            ];
        } else {
            $sql = '
                SELECT `id`, `name`, `url`, `user_id` = ? AS own
                FROM `passwords` 
                WHERE `user_id` = ? OR `id` IN (%s) 
                ORDER BY `name`, `url`;
            ';
            $params = [
                $user,
                $user,
                $user,
                $user,
            ];
        }
        yield from $this->fetch(sprintf($sql, $this->getSubSelect()), $params);
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
                SELECT `id`, `name`, `url`, `user_id` = ? AS own
                FROM `passwords` 
                WHERE (`user_id` = ? OR `id` IN (%s)) AND (`name` LIKE ? OR `url` LIKE ?) AND `category_id` <=> ?
                ORDER BY IF(`name` LIKE ?, 0, 1), `name`, `url`;
            ';
            // Search for substring. Do not use sprintf here since % is ugly.
            $searchTerm = "%$term%";
            $params = [
                $user,
                $user,
                $user,
                $user,
                $searchTerm,
                $searchTerm,
                $category,
                $searchTerm,
            ];
        } else {
            $sql = '
                SELECT `id`, `name`, `url`, `user_id` = ? AS own
                FROM `passwords` 
                WHERE (`user_id` = ? OR `id` IN (%s)) AND `category_id` <=> ?
                ORDER BY `name`, `url`;
            ';
            $params = [
                $user,
                $user,
                $user,
                $user,
                $category,
            ];
        }
        yield from $this->fetch(sprintf($sql, $this->getSubSelect()), $params);
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
            INSERT INTO `passwords` (
                `user_id`, 
                `category_id`, 
                `name`, 
                `url`, 
                `user`, 
                `email`, 
                `password`,
                `notice`,
                `shared`
            )
            VALUES (
                ?, 
                ?, 
                ?, 
                ?, 
                AES_ENCRYPT(?, IF (?, @shared, @key)), 
                AES_ENCRYPT(?, IF (?, @shared, @key)), 
                AES_ENCRYPT(?, IF (?, @shared, @key)), 
                AES_ENCRYPT(?, IF (?, @shared, @key)),
                ?
            );
        ';
        return $this->insert(
            $sql,
            [
                $user,
                $data['category_id'] ?? null,
                $data['name'] ?? '',
                $data['url'] ?? '',
                $data['user'] ?? '',
                $data['shared'] ?? 0,
                $data['email'] ?? '',
                $data['shared'] ?? 0,
                $data['password'] ?? '',
                $data['shared'] ?? 0,
                $data['notice'] ?? '',
                $data['shared'] ?? 0,
                $data['shared'] ?? 0,
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
                `user` = AES_ENCRYPT(?, IF (?, @shared, @key)),
                `email` = AES_ENCRYPT(?, IF (?, @shared, @key)),
                `password` = AES_ENCRYPT(?, IF (?, @shared, @key)),
                `notice` = AES_ENCRYPT(?, IF (?, @shared, @key)),
                `shared` = ?
            WHERE `user_id` = ? AND `id` = ?;
        ';
        $this->execute(
            $sql,
            [
                $data['category_id'] ?? null,
                $data['name'] ?? '',
                $data['url'] ?? '',
                $data['user'] ?? '',
                $data['shared'] ?? 0,
                $data['email'] ?? '',
                $data['shared'] ?? 0,
                $data['password'] ?? '',
                $data['shared'] ?? 0,
                $data['notice'] ?? '',
                $data['shared'] ?? 0,
                $data['shared'] ?? 0,
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
     * Fetches the user ID of a password from the given category which does not belong to the given user.
     *
     * @param int $categoryId
     * @param int $user
     *
     * @return int
     * @throws BackendException
     */
    public function fetchSharedUserIdForCategory(int $categoryId, int $user): int
    {
        $sql = 'SELECT `user_id` FROM `passwords` WHERE `category_id` = ? AND `user_id` <> ?;';
        // Return the first results ID or zero.
        return $this->fetch($sql, [$categoryId, $user])->current()['user_id'] ?? 0;
    }

    /**
     * Fetches the user IDs the password is shared to.
     *
     * @param int $id
     *
     * @return array
     * @throws BackendException
     */
    public function fetchUserIds(int $id): array
    {
        $sql = 'SELECT `user_id` FROM `passwords_x_users` WHERE `password_id` = ?;';
        $ids = [];
        foreach ($this->fetch($sql, [$id]) as $user) {
            $ids[] = $user['user_id'];
        }
        return $ids;
    }

    /**
     * Removes the share assignment for the given users from the given password.
     *
     * @param int   $id
     * @param array $users
     *
     * @throws BackendException
     */
    public function removeUsers(int $id, array $users): void
    {
        if (!$users) {
            return;
        }
        $sql = sprintf(
            'DELETE FROM `passwords_x_users` WHERE `password_id` = @id AND `user_id` IN (%s);',
            implode(', ', array_pad([], count($users), '?'))
        );
        $this->execute('SET @id = ?;', [$id]);
        $this->execute($sql, $users);
    }

    /**
     * Adds the share assignment for the given users to the given password.
     *
     * @param int   $id
     * @param array $users
     *
     * @throws BackendException
     */
    public function assignUsers(int $id, array $users): void
    {
        if (!$users) {
            return;
        }
        $sql = sprintf(
            'INSERT INTO `passwords_x_users` (`password_id`, `user_id`) VALUES %s;',
            implode(', ', array_pad([], count($users), '(@id, ?)'))
        );
        $this->execute('SET @id = ?;', [$id]);
        $this->execute($sql, $users);
    }

    /**
     * Fetches the group IDs the password is shared to.
     *
     * @param int $id
     *
     * @return array
     * @throws BackendException
     */
    public function fetchGroupIds(int $id): array
    {
        $sql = 'SELECT `group_id` FROM `passwords_x_groups` WHERE `password_id` = ?;';
        $ids = [];
        foreach ($this->fetch($sql, [$id]) as $group) {
            $ids[] = $group['group_id'];
        }
        return $ids;
    }

    /**
     * Removes the share assignment for the given groups from the given password.
     *
     * @param int   $id
     * @param array $groups
     *
     * @throws BackendException
     */
    public function removeGroups(int $id, array $groups): void
    {
        if (!$groups) {
            return;
        }
        $sql = sprintf(
            'DELETE FROM `passwords_x_groups` WHERE `password_id` = @id AND `group_id` IN (%s);',
            implode(', ', array_pad([], count($groups), '?'))
        );
        $this->execute('SET @id = ?;', [$id]);
        $this->execute($sql, $groups);
    }

    /**
     * Adds the share assignment for the given groups to the given password.
     *
     * @param int   $id
     * @param array $groups
     *
     * @throws BackendException
     */
    public function assignGroups(int $id, array $groups): void
    {
        if (!$groups) {
            return;
        }
        $sql = sprintf(
            'INSERT INTO `passwords_x_groups` (`password_id`, `group_id`) VALUES %s;',
            implode(', ', array_pad([], count($groups), '(@id, ?)'))
        );
        $this->execute('SET @id = ?;', [$id]);
        $this->execute($sql, $groups);
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
        $this->execute('SET @key = ?;', [$key]);
        // Also bind the shared key.
        $this->execute('SET @shared = ?;', [$this->shareKey]);
    }

    /**
     * Returns the sub select to query for shared passwords.
     * To use it, the users ID must be bound twice as a parameter.
     *
     * @return string
     */
    private function getSubSelect(): string
    {
        return '
            SELECT `password_id` 
            FROM `passwords_x_users` 
            WHERE `user_id` = ?

            UNION

            SELECT pxg.`password_id`
            FROM `passwords_x_groups` pxg
            INNER JOIN `groups_x_users` gxu ON gxu.`group_id` = pxg.`group_id`
            WHERE gxu.`user_id` = ?
        ';
    }
}
