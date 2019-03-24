<?php
namespace App\Model;

use Sx\Data\BackendException;
use Sx\Data\RepoInterface;

/**
 * This class represents the domain functionality to handle password entries.
 * Since the domain and database do not differ much, it is mostly a proxy to the storage with some input validation.
 *
 * @package App\Model
 */
class PasswordRepo implements RepoInterface
{
    /**
     * The storage for the real database queries.
     *
     * @var PasswordStorage
     */
    private $passwordStorage;

    /**
     * The storage for the categories is needed to validate access to the category_id on save.
     *
     * @var CategoryStorage
     */
    private $categoryStorage;

    /**
     * The currently logged in clients user ID.
     *
     * @var int
     */
    private $user = 0;

    /**
     * The key for password de-/ encryption.
     *
     * @var string
     */
    private $key = '';

    /**
     * Creates the instance with the reference to the database storage.
     * Before most of the functions can be used, also setUser and setKey must be called.
     *
     * @param PasswordStorage $passwordStorage
     * @param CategoryStorage $categoryStorage
     */
    public function __construct(PasswordStorage $passwordStorage, CategoryStorage $categoryStorage)
    {
        $this->passwordStorage = $passwordStorage;
        $this->categoryStorage = $categoryStorage;
    }

    /**
     * Sets the user ID of the currently logged in client.
     * With this it is validated that a client can only access his own password.
     *
     * @param int $id
     */
    public function setUser(int $id): void
    {
        $this->user = $id;
    }

    /**
     * Sets the key for de-/ encryption of passwords. Without most of the data is not readable.
     *
     * @param string $key
     */
    public function setKey(string $key): void
    {
        $this->key = $key;
    }

    /**
     * Returns the user from setUser but checks if the setter was called as a validation.
     *
     * @return int
     * @throws \RuntimeException
     */
    public function getUser(): int
    {
        if (!$this->user) {
            throw new \RuntimeException('a user needs to be set');
        }
        return $this->user;
    }

    /**
     * Returns the key from setKey but checks if the setter was used as a validation.
     *
     * @return string
     * @throws \RuntimeException
     */
    public function getKey(): string
    {
        if (!$this->key) {
            throw new \RuntimeException('a key needs to be set');
        }
        return $this->key;
    }

    /**
     * Retrieves an decrypted password by given ID. If no user ID or key is set an exception will be thrown.
     *
     * @param int $id
     *
     * @return array
     * @throws RepoException
     */
    public function getPassword(int $id): array
    {
        try {
            $password = $this->passwordStorage->fetchPassword($this->getKey(), $this->getUser(), $id);
        } catch (BackendException $e) {
            throw new RepoException('Beim Laden des Passworts ist ein Fehler aufgetreten.', 501);
        }
        return $password;
    }

    /**
     * Retrieves all passwords as a simple list of ID, name and url. This is all unencrypted data so no key is needed.
     *
     * @param string $term
     * @param mixed  $category
     *
     * @return array
     * @throws RepoException
     */
    public function getPasswords(string $term = '', $category = ''): array
    {
        // All entries are returned so there is no need to keep the Generator here.
        try {
            $user = $this->getUser();
            if ($category) {
                // If the client explicitly selected "no category" search for passwords without a category.
                if ($category === 'null') {
                    $category = null;
                }
                return iterator_to_array($this->passwordStorage->fetchPasswordsByCategory($user, $category, $term));
            }
            // Search for all passwords independent of the category.
            return iterator_to_array($this->passwordStorage->fetchPasswords($user, $term));
        } catch (BackendException $e) {
            throw new RepoException('Beim Laden der Passwortliste ist ein Fehler aufgetreten.', 501);
        }
    }

    /**
     * Saves data for a password. If an ID is present in the data an existing entry is updated.
     * The data is extended with the user ID and the key is needed to encrypt the data.
     *
     * @param array $data
     *
     * @return int
     * @throws RepoException
     */
    public function savePassword(array $data): int
    {
        $user = $this->getUser();
        if ($data['category_id'] ?? false) {
            try {
                $category = $this->categoryStorage->fetchCategory($user, $data['category_id']);
                if (!$category) {
                    throw new RepoException('Die gewählte Kategorie konnte nicht gefunden werden.', 422);
                }
            } catch (BackendException $e) {
                throw new RepoException('Die gewählte Kategorie konnte nicht geladen werden.', 501);
            }
        } else {
            // Rewrite empty string from empty option to explicit null.
            $data['category_id'] = null;
        }
        $id = $data['id'] ?? 0;
        if (!$id) {
            try {
                return $this->passwordStorage->insertPassword($this->getKey(), $user, $data);
            } catch (BackendException $e) {
                throw new RepoException('Das Passwort konnte nicht angelegt werden.', 501);
            }
        }
        try {
            $this->passwordStorage->updatePassword($this->getKey(), $user, $id, $data);
        } catch (BackendException $e) {
            throw new RepoException('Das Passwort konnte nicht aktualisiert werden.', 501);
        }
        return $id;
    }

    /**
     * Delete the password with the given ID. Only needs the user ID since no de-/ encryption is executed.
     *
     * @param int $id
     *
     * @return bool
     */
    public function deletePassword(int $id): bool
    {
        try {
            return (bool)$this->passwordStorage->deletePassword($this->getUser(), $id);
        } catch (BackendException $e) {
            return false;
        }
    }
}
