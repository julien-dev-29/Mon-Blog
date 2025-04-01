<?php
namespace Framework\Database;

use ArrayAccess;
use Exception;
use Iterator;

class QueryResult implements ArrayAccess, Iterator
{

    /**
     * @var array
     */
    private $records;

    /**
     * @var int
     */
    private $index = 0;

    /**
     * @var string
     */
    private $entity;
    
    /**
     * @var array
     */
    private $hydratedRecords = [];

    /**
     * Summary of __construct
     * @param array $records
     * @param mixed $entity
     */
    public function __construct(array $records, ?string $entity = null)
    {
        $this->records = $records;
        $this->entity = $entity;
    }

    /**
     * Summary of get
     * @param int $index
     * @return object|string
     */
    public function get(int $index)
    {
        if ($this->entity) {
            if (!isset($this->hydratedRecords[$index])) {
                $this->hydratedRecords[$index] = Hydrator::hydrate($this->records[$index], $this->entity);
            }
            return $this->hydratedRecords[$index];
        }
        return $this->entity;
    }

    /**
     * Summary of offsetExists
     * @param mixed $offset
     * @return bool
     */
    public function offsetExists(mixed $offset): bool
    {
        return isset($this->records[$offset]);
    }

    /**
     * Summary of offsetGet
     * @param mixed $offset
     * @return mixed
     */
    public function offsetGet(mixed $offset): mixed
    {
        return $this->get($offset);
    }

    /**
     * Summary of offsetSet
     * @param mixed $offset
     * @param mixed $value
     * @throws \Exception
     * @return never
     */
    public function offsetSet(mixed $offset, mixed $value): void
    {
        throw new Exception("Can't alter records !");
    }

    /**
     * Summary of offsetUnset
     * @param mixed $offset
     * @throws \Exception
     * @return never
     */
    public function offsetUnset(mixed $offset): void
    {
        throw new Exception("Can't alter records !");
    }

    /**
     * Summary of current
     * @return mixed
     */
    public function current(): mixed
    {
        return $this->get($this->index);
    }

    /**
     * Summary of key
     * @return int
     */
    public function key(): mixed
    {
        return $this->index;
    }

    /**
     * Summary of next
     * @return void
     */
    public function next(): void
    {
        $this->index++;
    }

    /**
     * Summary of rewind
     * @return void
     */
    public function rewind(): void
    {
        $this->index = 0;
    }

    /**
     * Summary of valid
     * @return bool
     */
    public function valid(): bool
    {
        return isset($this->records[$this->index]);
    }
}
