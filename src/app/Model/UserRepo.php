<?php
namespace App\Model;

use Sx\Data\RepoInterface;

/**
 * A class to represent the domain of the user. It is used for login and profile updates.
 *
 * @package App\Model
 */
class UserRepo implements RepoInterface
{
    /**
     * The storage to access the database.
     *
     * @var UserStorage
     */
    private $storage;

    /**
     * Creates the domain repository with the database storage.
     *
     * @param UserStorage $storage
     */
    public function __construct(UserStorage $storage)
    {
        $this->storage = $storage;
    }

    /**
     * Checks if the given password matches the stored and hashed password for the given user.
     *
     * @param string $user
     * @param string $password
     *
     * @return bool
     */
    public function checkPassword(string $user, string $password): bool
    {
        $data = $this->storage->fetchUserByUser($user, $password);
        // An invalid username is handled as a validation error too.
        if (!$data) {
            return false;
        }
        // Compare the password. For the initial user creation the stored password is empty. This skips the validation.
        if ($data['password'] && !password_verify($password, $data['password'])) {
            return false;
        }
        return true;
    }

    /**
     * Fetches the data for a given user. The plain text password is needed to encrypt the key and email.
     *
     * @param string $user
     * @param string $password
     *
     * @return array
     * @throws RepoException
     */
    public function getUser(string $user, string $password): array
    {
        $data = $this->storage->fetchUserByUser($user, $password);
        if (!$data) {
            throw new RepoException('error retrieving user', 404);
        }
        // If the data does not contain a valid key (e.g. a newly created user) a new one must be created.
        if (!($data['key'] ?? '')) {
            // This creates a 256bit key for MySQL AES with random printable characters.
            try {
                $key = bin2hex(random_bytes(16));
            } catch (\Exception $e) {
                throw new RepoException('error creating key: ' . $e->getMessage(), $e->getCode(), $e);
            }
            $data['key'] = $key;
            // Give the password to encrypt the key.
            $this->storage->updateUser($data['id'], $password, $data);
        }
        return $data;
    }

    /**
     * Updates all data for the given user. There is no insert as it always updates the logged in client.
     * This function validates the given profile data:
     * - there must be the correct current password available in the data
     * - if a new_password is given it must match with the confirm_password
     *
     * @param int   $id
     * @param array $data
     *
     * @return array
     * @throws RepoException
     */
    public function saveUser(int $id, array $data): array
    {
        $password = $data['password'] ?? '';
        // Validate the password, first retrieve it to get the current (unchanged) user name for validation.
        $user = $this->storage->fetchUserById($id, $password);
        if (!$user) {
            throw new RepoException('error retrieving user', 404);
        }
        // This function will also retrieve the user from the storage. Assume the storage caches data.
        if (!$this->checkPassword($user['user'], $password)) {
            throw new RepoException('provided password is not valid', 403);
        }
        // Check if new passwords match.
        $changedPassword = $data['new_password'] ?? '';
        if ($changedPassword) {
            if ($changedPassword !== ($data['confirm_password'] ?? '')) {
                throw new RepoException('passwords do not match', 422);
            }
            // Replace old password with new password.
            $password = $changedPassword;
        }
        foreach (array_keys($user) as $key) {
            if (\array_key_exists($key, $data)) {
                $user[$key] = $data[$key];
            }
        }
        // Do not save plain text passwords but use a password hash.
        $user['password'] = password_hash($password, PASSWORD_DEFAULT);
        $this->storage->updateUser($id, $password, $user);
        return $user;
    }
}
