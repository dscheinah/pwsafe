<?php
namespace Sx\Data;

interface BackendInterface
{

    public function connect(): void;

    public function prepare(string $statement);

    public function execute($resource, array $params = []): int;

    public function fetch($resource, array $params = []): \Generator;

    public function insert($resource, array $params = []): int;
}
