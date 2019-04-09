<?php
namespace App\Model;

use Sx\Data\BackendException;
use Sx\Utility\LogInterface;

/**
 * A class to represent the domain of the user. It is used for login and profile updates.
 *
 * @package App\Model
 */
class UserRepo extends RepoAbstract
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
     * @param LogInterface $logger
     * @param UserStorage  $storage
     */
    public function __construct(LogInterface $logger, UserStorage $storage)
    {
        parent::__construct($logger);
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
        try {
            $data = $this->storage->fetchUserByUser($user, $password);
        } catch (BackendException $e) {
            $this->logger->log($e->getMessage());
            return false;
        }
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
     * @throws \RuntimeException
     */
    public function getUserForLogin(string $user, string $password): array
    {
        try {
            $data = $this->storage->fetchUserByUser($user, $password);
            // The initial user without a password can be accessed with every password,
            // but must be loaded with an empty one to correctly decrypt the data.
            if ($data && !$data['password']) {
                $password = '';
                $data = $this->storage->fetchUserByUser($user, '');
            }
        } catch (BackendException $e) {
            $this->logger->log($e->getMessage());
            throw new RepoException('Der angegebene Benutzer konnte nicht geladen werden.', 501);
        }
        if (!$data) {
            throw new RepoException('Der angegebene Benutzer konnte nicht gefunden werden.', 422);
        }
        // If the data does not contain a valid key (e.g. a newly created user) a new one must be created.
        if (!($data['key'] ?? '')) {
            try {
                // This creates a 256bit key for MySQL AES with random printable characters.
                $key = '';
                for ($i = 0; $i < 32; $i++) {
                    $key .= \chr(random_int(0x21, 0x7E));
                }
            } catch (\Exception $e) {
                throw new \RuntimeException('error creating key: ' . $e->getMessage(), $e->getCode(), $e);
            }
            $data['key'] = $key;
            // Give the password to encrypt the key.
            try {
                $this->storage->updateProfile($data['id'], $password, $data);
            } catch (BackendException $e) {
                $this->logger->log($e->getMessage());
                throw new RepoException('Der Benutzer konnte nicht aktualisiert werden.', 422);
            }
        }
        return $data;
    }

    /**
     * Fetches the data for a given user. Only the unencrypted data is available.
     * This is used for administrative edits.
     *
     * @param int $id
     *
     * @return array
     * @throws RepoException
     */
    public function getUser(int $id): array
    {
        try {
            $data = $this->storage->fetchUserById($id);
        } catch (BackendException $e) {
            $this->logger->log($e->getMessage());
            throw new RepoException('Der angegebene Benutzer konnte nicht geladen werden.', 501);
        }
        if (!$data) {
            throw new RepoException('Der angegebene Benutzer konnte nicht gefunden werden.', 422);
        }
        return $data;
    }

    /**
     * Fetches the basic data for all users.
     * This is used for administrative edits.
     *
     * @return array
     * @throws RepoException
     */
    public function getUsers(): array
    {
        // All entries are returned so there is no need to keep the Generator here.
        try {
            return iterator_to_array($this->storage->fetchUsers());
        } catch (BackendException $e) {
            $this->logger->log($e->getMessage());
            throw new RepoException('Beim Laden der Benutzer ist ein Fehler aufgetreten.', 501);
        }
    }

    /**
     * Saves the data for a user.
     * This is used for administrative edits.
     *
     * @param array $data
     *
     * @return int
     * @throws RepoException
     */
    public function saveUser(array $data): int
    {
        $id = $data['id'] ?? 0;
        if (!$id) {
            if (!isset($data['password'])) {
                throw new RepoException('Es muss ein Passwort angegeben werden.', 422);
            }
            $password = $data['password'];
            // Do not save plain text passwords but use a password hash.
            $data['password'] = password_hash($password, PASSWORD_DEFAULT);
            try {
                return $this->storage->insertUser($data, $password);
            } catch (BackendException $e) {
                $this->logger->log($e->getMessage());
                throw new RepoException('Der Benutzer konnte nicht angelegt werden.', 501);
            }
        }
        try {
            $this->storage->updateUser($id, $data);
        } catch (BackendException $e) {
            $this->logger->log($e->getMessage());
            throw new RepoException('Der Benutzer konnte nicht aktualisiert werden.', 501);
        }
        return $id;
    }

    /**
     * Deletes the user.
     * This is used for administrative edits.
     *
     * @param int $id
     *
     * @return bool
     */
    public function deleteUser(int $id): bool
    {
        try {
            return (bool)$this->storage->deleteUser($id);
        } catch (BackendException $e) {
            $this->logger->log($e->getMessage());
            return false;
        }
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
    public function saveProfile(int $id, array $data): array
    {
        $password = $data['password'] ?? '';
        // Validate the password, first retrieve it to get the current (unchanged) user name for validation.
        try {
            $user = $this->storage->fetchCompleteUserById($id, $password);
        } catch (BackendException $e) {
            $this->logger->log($e->getMessage());
            throw new RepoException('Beim Laden des Profils ist ein Fehler aufgetreten.', 501);
        }
        if (!$user) {
            throw new RepoException('Das Profil konnte nicht gefunden werden.', 501);
        }
        // This function will also retrieve the user from the storage. Assume the storage caches data.
        if (!$this->checkPassword($user['user'], $password)) {
            throw new RepoException('Das eingegebene Passwort ist nicht korrekt.', 422);
        }
        // Check if new passwords match.
        $changedPassword = $data['new_password'] ?? '';
        if ($changedPassword) {
            if ($changedPassword !== ($data['confirm_password'] ?? '')) {
                throw new RepoException('Die eingegebenen neuen Passwörter stimmen nicht überein.', 422);
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
        try {
            $this->storage->updateProfile($id, $password, $user);
        } catch (BackendException $e) {
            $this->logger->log($e->getMessage());
            throw new RepoException('Das Profil konnte nicht aktualisiert werden.', 501);
        }
        return $user;
    }

    /**
     * Checks if the given user is an administrator.
     *
     * @param int $id
     *
     * @return bool
     */
    public function isAdmin(int $id): bool
    {
        try {
            $user = $this->getUser($id);
        } catch (RepoException $e) {
            return false;
        }
        $role = $user['role'] ?? '';
        return $role === 'admin';
    }
}
