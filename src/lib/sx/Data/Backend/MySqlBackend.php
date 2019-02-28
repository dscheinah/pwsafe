<?php
namespace Sx\Data\Backend;

use Sx\Data\BackendInterface;
use Sx\Data\BackendException;

class MySqlBackend implements BackendInterface
{

    private $options = [
        'server' => null,
        'user' => null,
        'password' => null,
        'database' => null,
        'port' => null,
        'socket' => null
    ];

    private $mysqli;

    public function __construct(array $options = [])
    {
        foreach (array_keys($this->options) as $key) {
            if (isset($options[$key])) {
                $this->options[$key] = $options[$key];
            }
        }
    }

    public function connect(): void
    {
        if ($this->mysqli) {
            if (! $this->mysqli->ping()) {
                throw new BackendException('connection to mysql was lost: ' . $this->mysqli->error, $this->mysqli->errno);
            }
            return;
        }
        $this->mysqli = new \mysqli(...array_values($this->options));
        if ($this->mysqli->connect_errno) {
            throw new BackendException('error connection to mysql: ' . $this->mysqli->connect_error, $this->mysqli->connect_errno);
        }
        $this->mysqli->query('SET NAMES utf8;');
        $this->mysqli->query('SET CHARACTER SET utf8;');
        $this->mysqli->set_charset('utf8');
    }

    public function prepare(string $statement): \mysqli_stmt
    {
        $resource = $this->mysqli->prepare($statement);
        if (! $resource) {
            throw new BackendException('error preparing statement: ' . $this->mysqli->error, $this->mysqli->errno);
        }
        return $resource;
    }

    public function execute($resource, array $params = []): int
    {
        if (! $resource instanceof \mysqli_stmt) {
            throw new BackendException('only mysql_stmt are supported for queries', 500);
        }
        $types = '';
        foreach ($params as $param) {
            $type = gettype($param);
            switch ($type) {
                case 'boolean':
                case 'integer':
                    $types .= 'i';
                    break;
                case 'double':
                    $types .= 'd';
                    break;
                case 'string':
                    $types .= 's';
                    break;
                default:
                    throw new BackendException('unsupported param type: ' . $type, 422);
            }
        }
        if (! $resource->bind_param($types, ...$params)) {
            throw new BackendException('error binding parameter', $resource->errno);
        }
        if (! $resource->execute()) {
            throw new BackendException('error executing: ' . $resource->error, $resource->errno);
        }
        return $resource->affected_rows;
    }

    public function fetch($resource, array $params = []): \Generator
    {
        $this->execute($resource, $params);
        /** @var \mysqli_stmt $resource */
        $result = $resource->get_result();
        if (! $result) {
            throw new BackendException('error getting result: ' . $resource->error, $resource->errno);
        }
        $count = $result->num_rows;
        for ($no = 0; $no < $count; $no ++) {
            $result->data_seek($no);
            yield $result->fetch_assoc();
        }
    }

    public function insert($resource, array $params = []): int
    {
        $this->execute($resource, $params);
        /** @var \mysqli_stmt $resource */
        return $resource->insert_id;
    }
}
