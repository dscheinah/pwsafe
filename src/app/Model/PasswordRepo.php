<?php
namespace App\Model;

use Sx\Data\RepoInterface;

class PasswordRepo implements RepoInterface
{

    private $storage;

    private $user = 0;

    private $key = '';

    public function __construct(PasswordStorage $storage)
    {
        $this->storage = $storage;
    }

    public function setUser(int $id): void
    {
        $this->user = $id;
    }

    public function setKey(string $key): void
    {
        $this->key = $key;
    }

    public function getPassword(int $id): array
    {
        $this->validate();
        return $this->storage->fetchPassword($this->key, $this->user, $id);
    }

    public function getPasswords(): array
    {
        $this->validate();
        return iterator_to_array($this->storage->fetchPasswords($this->user));
    }

    public function savePassword(array $data): int
    {
        $this->validate();
        $id = $data['id'] ?? 0;
        if (! $id) {
            return $this->storage->insertPassword($this->key, $this->user, $data);
        }
        $this->storage->updatePassword($this->key, $this->user, $id, $data);
        return $id;
    }

    public function deletePassword(int $id): bool
    {
        $this->validate();
        return (bool) $this->storage->deletePassword($this->user, $id);
    }

    private function validate(): void
    {
        if (! $this->user || ! $this->key) {
            throw new RepoException('user and key need to be available to retrieve passwords');
        }
    }
}
