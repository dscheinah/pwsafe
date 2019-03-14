<?php
namespace App\Model;

use Sx\Data\Storage;

/**
 * The database storage to handle queries for categories.
 * This currently uses no SQL abstraction but hard coded SQL and therefore requires to be used with a MySQL adapter.
 *
 * @package App\Model
 */
class CategoryStorage extends Storage
{
    /**
     * Fetches one category of the given user with the given ID.
     *
     * @param int $user
     * @param int $id
     *
     * @return array
     * @throws \Sx\Data\BackendException
     */
    public function fetchCategory(int $user, int $id): array
    {
        $sql = 'SELECT `id`, `name` FROM `categories` WHERE `user_id` = ? AND `id` = ?;';
        // Return first result or empty array which is falsify.
        return $this->fetch($sql, [$user, $id])->current() ?: [];
    }

    /**
     * Loads all categories of the given user.
     *
     * @param int $user
     *
     * @return \Generator
     * @throws \Sx\Data\BackendException
     */
    public function fetchCategories(int $user): \Generator
    {
        $sql = 'SELECT `id`, `name` FROM `categories` WHERE `user_id` = ? ORDER BY `name`;';
        yield from $this->fetch($sql, [$user]);
    }

    /**
     * Inserts a new category and returns the auto generated ID on success.
     *
     * @param int   $user
     * @param array $data
     *
     * @return int
     * @throws \Sx\Data\BackendException
     */
    public function insertCategory(int $user, array $data): int
    {
        $sql = 'INSERT INTO `categories` (`user_id`, `name`) VALUES (?, ?);';
        return $this->insert($sql, [$user, $data['name'] ?? '']);
    }

    /**
     * Updates the data of the given category.
     *
     * @param int   $user
     * @param int   $id
     * @param array $data
     *
     * @throws \Sx\Data\BackendException
     */
    public function updateCategory(int $user, int $id, array $data): void
    {
        $sql = 'UPDATE `categories` SET `name` = ? WHERE `user_id` = ? AND `id` = ?;';
        $this->execute($sql, [$data['name'] ?? '', $user, $id]);
    }

    /**
     * Deletes the given category and returns the number of deleted rows to indicate success.
     *
     * @param int $user
     * @param int $id
     *
     * @return int
     * @throws \Sx\Data\BackendException
     */
    public function deleteCategory(int $user, int $id): int
    {
        $sql = 'DELETE FROM `categories` WHERE `user_id` = ? AND `id` = ?;';
        return $this->execute($sql, [$user, $id]);
    }
}
