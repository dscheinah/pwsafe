<?php
namespace App\Model;

use Sx\Data\BackendException;
use Sx\Utility\LogInterface;

/**
 * This class represents the domain functionality to handle category entries.
 * Since the domain and database do not differ much, it is mostly a proxy to the storage with some input validation.
 *
 * @package App\Model
 */
class CategoryRepo extends RepoAbstract
{
    /**
     * The storage for the real database queries.
     *
     * @var CategoryStorage
     */
    private $categoryStorage;

    /**
     * The storage for the passwords is needed to give the category to the next user if shared.
     *
     * @var PasswordStorage
     */
    private $passwordStorage;

    /**
     * The currently logged in clients user ID.
     *
     * @var int
     */
    private $user = 0;

    /**
     * Creates the domain repository with the required storage.
     * Before the functions can be used also setUser must be called.
     *
     * @param LogInterface    $logger
     * @param CategoryStorage $storage
     * @param PasswordStorage $userStorage
     */
    public function __construct(
        LogInterface $logger,
        CategoryStorage $storage,
        PasswordStorage $userStorage
    ) {
        parent::__construct($logger);
        $this->categoryStorage = $storage;
        $this->passwordStorage = $userStorage;
    }

    /**
     * Sets the user ID of the currently logged in client.
     * With this it is validated that a client can only access his own categories.
     *
     * @param int $id
     */
    public function setUser(int $id): void
    {
        $this->user = $id;
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
     * Loads a category of the set user for the given ID.
     *
     * @param int $id
     *
     * @return array
     * @throws RepoException
     */
    public function getCategory(int $id): array
    {
        try {
            $category = $this->categoryStorage->fetchCategory($this->getUser(), $id);
        } catch (BackendException $e) {
            $this->logger->log($e->getMessage());
            throw new RepoException('Beim Laden der Kategorie ist ein Fehler aufgetreten.', 501);
        }
        return $category;
    }

    /**
     * Loads all categories for the set user.
     *
     * @return array
     * @throws RepoException
     */
    public function getCategories(): array
    {
        // All entries are returned so there is no need to keep the Generator here.
        try {
            return iterator_to_array($this->categoryStorage->fetchCategories($this->getUser()));
        } catch (BackendException $e) {
            $this->logger->log($e->getMessage());
            throw new RepoException('Beim Laden der Kategorien ist ein Fehler aufgetreten.', 501);
        }
    }

    /**
     * Saves a category for the set user. It will be inserted if no id is given in data.
     * It returns the ID on success to be able to load the data afterwards.
     *
     * @param array $data
     *
     * @return int
     * @throws RepoException
     */
    public function saveCategory(array $data): int
    {
        $id = $data['id'] ?? 0;
        if (!$id) {
            try {
                return $this->categoryStorage->insertCategory($this->getUser(), $data);
            } catch (BackendException $e) {
                $this->logger->log($e->getMessage());
                throw new RepoException('Die Kategorie konnte nicht angelegt werden.', 501);
            }
        }
        try {
            $this->categoryStorage->updateCategory($this->getUser(), $id, $data);
        } catch (BackendException $e) {
            $this->logger->log($e->getMessage());
            throw new RepoException('Die Kategorie konnte nicht aktualisiert werden.', 501);
        }
        return $id;
    }

    /**
     * Deletes the given category for the set user.
     *
     * @param int $id
     *
     * @return bool
     */
    public function deleteCategory(int $id): bool
    {
        try {
            $currentUserId = $this->getUser();
            // If the category is used by another user, transfer it to the user instead of deleting.
            $userId = $this->passwordStorage->fetchSharedUserIdForCategory($id, $currentUserId);
            if ($userId) {
                $this->categoryStorage->updateUserForCategory($id, $currentUserId, $userId);
                return true;
            }
            return (bool)$this->categoryStorage->deleteCategory($currentUserId, $id);
        } catch (BackendException $e) {
            $this->logger->log($e->getMessage());
            return false;
        }
    }
}
