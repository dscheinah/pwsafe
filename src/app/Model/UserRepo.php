<?php
namespace App\Model;

use Sx\Data\RepoInterface;

class UserRepo implements RepoInterface
{

    private $storage;

    public function __construct(UserStorage $storage)
    {
        $this->storage = $storage;
    }

    public function checkPassword(string $user, string $password): bool
    {
        $user = $this->storage->fetchUserByUser($user, $password);
        if (! $user) {
            return false;
        }
        if ($user['password'] && ! password_verify($password, $user['password'])) {
            return false;
        }
        return true;
    }

    public function getUser(string $user, string $password): array
    {
        $data = $this->storage->fetchUserByUser($user, $password);
        if (! $data) {
            throw new RepoException('error retrieving user', 404);
        }
        if (! ($data['key'] ?? '')) {
            $key = bin2hex(random_bytes(16));
            $data['key'] = $key;
            $this->storage->updateUser($data['id'], $password, $data);
        }
        return $data;
    }

    public function saveUser(int $id, array $data): array
    {
        $password = $data['password'] ?? '';
        $user = $this->storage->fetchUserById($id, $password);
        if (! $user) {
            throw new RepoException('error retrieving user', 404);
        }
        if (! $this->checkPassword($user['user'], $password)) {
            throw new RepoException('provided password is not valid', 403);
        }
        $changedPassword = $data['new_password'] ?? '';
        if ($changedPassword) {
            if ($changedPassword !== ($data['confirm_password'] ?? '')) {
                throw new RepoException('passwords do not match', 422);
            }
            $password = $changedPassword;
        }
        foreach (array_keys($user) as $key) {
            if (array_key_exists($key, $data)) {
                $user[$key] = $data[$key];
            }
        }
        $user['password'] = password_hash($password, PASSWORD_DEFAULT);
        $this->storage->updateUser($id, $password, $user);
        return $user;
    }
}
