<?php
namespace Sx\Data;

class Storage implements StorageInterface
{

    protected $backend;

    private $statements = [];

    public function __construct(BackendInterface $backend)
    {
        $this->backend = $backend;
    }

    public function execute(string $statement, array $params = []): int
    {
        $this->backend->connect();
        $resource = $this->getResource($statement);
        return $this->backend->execute($resource, $params);
    }

    public function fetch(string $statement, array $params = []): \Generator
    {
        $this->backend->connect();
        $resource = $this->getResource($statement);
        foreach ($this->backend->fetch($resource, $params) as $result) {
            yield $result;
        }
    }

    public function insert(string $statement, array $params = []): int
    {
        $this->backend->connect();
        $resource = $this->getResource($statement);
        return $this->backend->insert($resource, $params);
    }

    private function getResource($statement)
    {
        if (isset($this->statements[$statement])) {
            return $this->statements[$statement];
        }
        return $this->statements[$statement] = $this->backend->prepare($statement);
    }
}
