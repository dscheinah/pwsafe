<?php
namespace App\Model;

use Sx\Data\BackendException;
use Sx\Utility\LogInterface;

/**
 * This class represents the domain functionality to handle group entries.
 *
 * @package App\Model
 */
class GroupRepo extends RepoAbstract
{
    /**
     * The storage for the real database queries.
     *
     * @var GroupStorage
     */
    private $storage;

    /**
     * Creates the domain repository with the required storage.
     *
     * @param LogInterface $logger
     * @param GroupStorage $storage
     */
    public function __construct(LogInterface $logger, GroupStorage $storage)
    {
        parent::__construct($logger);
        $this->storage = $storage;
    }

    /**
     * Loads a group for the given ID.
     *
     * @param int $id
     *
     * @return array
     * @throws RepoException
     */
    public function getGroup(int $id): array
    {
        try {
            $category = $this->storage->fetchGroup($id);
        } catch (BackendException $e) {
            $this->logger->log($e->getMessage());
            throw new RepoException('Beim Laden der Gruppe ist ein Fehler aufgetreten.', 501);
        }
        return $category;
    }

    /**
     * Loads all groups.
     *
     * @return array
     * @throws RepoException
     */
    public function getGroups(): array
    {
        // All entries are returned so there is no need to keep the Generator here.
        try {
            return iterator_to_array($this->storage->fetchGroups());
        } catch (BackendException $e) {
            $this->logger->log($e->getMessage());
            throw new RepoException('Beim Laden der Gruppen ist ein Fehler aufgetreten.', 501);
        }
    }

    /**
     * Saves a group. It will be inserted if no id is given in data.
     * It returns the ID on success to be able to load the data afterwards.
     *
     * @param array $data
     *
     * @return int
     * @throws RepoException
     */
    public function saveGroup(array $data): int
    {
        $id = $data['id'] ?? 0;
        if (!$id) {
            try {
                return $this->storage->insertGroup($data);
            } catch (BackendException $e) {
                $this->logger->log($e->getMessage());
                throw new RepoException('Die Gruppe konnte nicht angelegt werden.', 501);
            }
        }
        try {
            $this->storage->updateGroup($id, $data);
        } catch (BackendException $e) {
            $this->logger->log($e->getMessage());
            throw new RepoException('Die Gruppe konnte nicht aktualisiert werden.', 501);
        }
        return $id;
    }

    /**
     * Deletes the given group.
     *
     * @param int $id
     *
     * @return bool
     */
    public function deleteGroup(int $id): bool
    {
        try {
            return (bool)$this->storage->deleteGroup($id);
        } catch (BackendException $e) {
            $this->logger->log($e->getMessage());
            return false;
        }
    }
}
