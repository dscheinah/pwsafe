<?php
namespace Sx\Data;

interface StorageInterface
{

    public function execute(string $statement, array $params = []): int;

    public function fetch(string $statement, array $params = []): \Generator;

    public function insert(string $statement, array $params = []): int;
}
