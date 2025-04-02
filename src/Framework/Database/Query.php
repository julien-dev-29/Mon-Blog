<?php
namespace Framework\Database;

use ArrayAccess;
use Exception;
use Framework\Database\Exception\RecordNotFoundException;
use Iterator;
use IteratorAggregate;
use Pagerfanta\Pagerfanta;
use PDO;

class Query implements IteratorAggregate
{
    /**
     * @var array
     */
    private $select;

    /**
     * @var string
     */
    private $from;

    /**
     * @var array
     */
    private $where = [];

    /**
     * @var array
     */
    private $join;

    /**
     * @var string
     */
    private $group;

    /**
     * @var array
     */
    private $order;

    /**
     * @var string
     */
    private $limit;

    /**
     * @var array
     */
    private $params;

    /**
     * @var string
     */
    private $entity;

    /**
     * @var PDO
     */
    private $pDO;


    /**
     * @param mixed $pDO
     */
    public function __construct(?PDO $pDO = null)
    {
        $this->pDO = $pDO;
    }


    public function fetch()
    {
        $record = $this->execute()->fetch(PDO::FETCH_ASSOC);
        if ($record === false) {
            return false;
        }
        if ($this->entity) {
            return Hydrator::hydrate($record, $this->entity);
        }
        return $record;
    }

    /**
     * Retourne un résultat ou une exception
     * @throws \Framework\Database\Exception\RecordNotFoundException
     */
    public function fetchOrFail()
    {
        $record = $this->fetch();
        if ($record == false) {
            throw new RecordNotFoundException();
        }
        return $record;
    }

    /**
     * Summary of all
     * @return QueryResult
     */
    public function fetchAll(): QueryResult
    {
        return new QueryResult(
            records: $this->execute()->fetchAll(PDO::FETCH_ASSOC),
            entity: $this->entity
        );
    }

    public function paginate(int $perpage, int $currentPage)
    {
        $paginator = new PaginatedQuery($this);
        return new Pagerfanta($paginator)
            ->setMaxPerPage($perpage)
            ->setCurrentPage($currentPage);
    }

    /**
     * @param string $table
     * @param mixed $alias
     * @return Query
     */
    public function from(string $table, ?string $alias = null): self
    {
        if ($alias) {
            $this->from[$table] = $alias;
        } else {
            $this->from[] = $table;
        }
        return $this;
    }

    /**
     * @param string[] $fields
     * @return Query
     */
    public function select(string ...$fields): self
    {
        $this->select = $fields;
        return $this;
    }

    /**
     * @param string[] $condition
     * @return Query
     */
    public function where(string ...$condition): self
    {
        $this->where = array_merge($this->where, $condition);
        return $this;
    }

    /**
     * Ajoute une liaison
     * @param string $table
     * @param string $condition
     * @param ?string $type
     * @return static
     */
    public function join(
        string $table,
        string $condition,
        ?string $type = 'left'
    ): self {
        $this->join[$type][] = [$table, $condition];
        return $this;
    }

    /**
     * @param string $field
     * @return Query
     */
    public function group(string $field): self
    {
        $this->group = $field;
        return $this;
    }

    /**
     * Spécifie l'ordre dans la requète
     * @param string $orders
     * @return Query
     */
    public function order(string $order): self
    {
        $this->order[] = $order;
        return $this;
    }

    /**
     * Spécifie la limite
     * @param int $length
     * @param int $offset
     * @return Query
     */
    public function limit(int $length, int $offset): self
    {
        $this->limit = "$offset, $length";
        return $this;
    }

    /**
     * Summary of count
     */
    public function count()
    {
        $query = clone $this;
        $table = current($this->from);
        return $query->select("COUNT($table.id)")
            ->execute()
            ->fetchColumn();
    }

    /**
     * Summary of params
     * @param array $params
     * @return static
     */
    public function params(array $params)
    {
        $this->params = $params;
        return $this;
    }

    /**
     * Summary of into
     * @param string $entity
     * @return Query
     */
    public function into(string $entity): self
    {
        $this->entity = $entity;
        return $this;
    }

    public function getIterator(): \Traversable
    {
        return $this->fetchAll();
    }

    /**
     * Summary of __tostring
     * @return string
     */
    public function __tostring(): string
    {
        $parts = ['SELECT'];
        $this->select ?
            $parts[] = join(', ', $this->select)
            :
            $parts[] = '*';
        $parts[] = 'FROM';
        $parts[] = $this->buildFrom();
        if (!empty($this->join)) {
            foreach ($this->join as $type => $joins) {
                foreach ($joins as [$table, $condition]) {
                    $parts[] = strtoupper($type) . ' JOIN' . " $table ON $condition";
                }
            }
        }
        if (!empty($this->where)) {
            $parts[] = 'WHERE';
            $parts[] = '(' . join(') AND (', $this->where) . ')';
        }
        if ($this->group) {
            $parts[] = "GROUP BY $this->group";
        }
        if ($this->order) {
            $parts[] = "ORDER BY " . join(', ', $this->order);
        }
        if ($this->limit) {
            $parts[] = "LIMIT $this->limit";
        }
        return join(' ', $parts);
    }

    /**
     * Summary of buildFrom
     * @return string
     */
    private function buildFrom()
    {
        $from = [];
        foreach ($this->from as $key => $value) {
            is_string($key) ?
                $from[] = "$key $value"
                :
                $from[] = $value;
        }
        return join(', ', $from);
    }

    /**
     * @return bool|\PDOStatement
     */
    private function execute()
    {
        $query = $this->__tostring();
        if (!empty($this->params)) {
            $statement = $this->pDO->prepare($query);
            $statement->execute($this->params);
            return $statement;
        }
        return $this->pDO->query($query);
    }
}
