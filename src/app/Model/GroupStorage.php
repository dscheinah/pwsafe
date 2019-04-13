<?php
namespace App\Model;

use Sx\Data\BackendException;
use Sx\Data\Storage;

/**
 * The database storage to handle queries for groups.
 * This currently uses no SQL abstraction but hard coded SQL and therefore requires to be used with a MySQL adapter.
 *
 * @package App\Model
 */
class GroupStorage extends Storage
{
    /**
     * Fetches one group with the given ID.
     *
     * @param int $id
     *
     * @return array
     * @throws BackendException
     */
    public function fetchGroup(int $id): array
    {
        $sql = 'SELECT `id`, `name` FROM `groups` WHERE `id` = ?;';
        // Return first result or empty array which is falsify.
        return $this->fetch($sql, [$id])->current() ?: [];
    }

    /**
     * Loads all groups.
     *
     * @return \Generator
     * @throws BackendException
     */
    public function fetchGroups(): \Generator
    {
        $sql = 'SELECT `id`, `name` FROM `groups` ORDER BY `name`;';
        yield from $this->fetch($sql);
    }

    /**
     * Inserts a new group and returns the auto generated ID on success.
     *
     * @param array $data
     *
     * @return int
     * @throws BackendException
     */
    public function insertGroup(array $data): int
    {
        $sql = 'INSERT INTO `groups` (`name`) VALUES (?);';
        return $this->insert($sql, [$data['name'] ?? '']);
    }

    /**
     * Updates the data of the given group.
     *
     * @param int   $id
     * @param array $data
     *
     * @throws BackendException
     */
    public function updateGroup(int $id, array $data): void
    {
        $sql = 'UPDATE `groups` SET `name` = ? WHERE `id` = ?;';
        $this->execute($sql, [$data['name'] ?? '', $id]);
    }

    /**
     * Deletes the given group and returns the number of deleted rows to indicate success.
     *
     * @param int $id
     *
     * @return int
     * @throws BackendException
     */
    public function deleteGroup(int $id): int
    {
        $sql = 'DELETE FROM `groups` WHERE `id` = ?;';
        return $this->execute($sql, [$id]);
    }

    /**
     * Fetches the groups for the given user. Only the IDs are needed by the UserRepo.
     *
     * @param int $user
     *
     * @return \Generator
     * @throws BackendException
     */
    public function fetchForUser(int $user): \Generator
    {
        $sql = 'SELECT `group_id` AS id FROM `groups_x_users` WHERE `user_id` = ?;';
        yield from $this->fetch($sql, [$user]);
    }

    /**
     * Removes the given users from the given groups by deleting from the relation table.
     *
     * @param int   $user
     * @param int[] $groups
     *
     * @throws BackendException
     */
    public function removeUser(int $user, array $groups): void
    {
        if (!$groups) {
            return;
        }
        $sql = sprintf(
            'DELETE FROM `groups_x_users` WHERE `user_id` = @user AND `group_id` IN (%s);',
            implode(', ', array_pad([], count($groups), '?'))
        );
        $this->execute('SET @user = ?;', [$user]);
        $this->execute($sql, $groups);
    }

    /**
     * Assigns the given users to the given groups by inserting into the relation table.
     *
     * @param int   $user
     * @param int[] $groups
     *
     * @throws BackendException
     */
    public function assignUser(int $user, array $groups): void {
        if (!$groups) {
            return;
        }
        $sql = sprintf(
            'INSERT INTO `groups_x_users` (`user_id`, `group_id`) VALUES %s;',
            implode(', ', array_pad([], count($groups), '(@user, ?)'))
        );
        $this->execute('SET @user = ?;', [$user]);
        $this->execute($sql, $groups);
    }
}
