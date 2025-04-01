<?php
namespace Framework\Database;

use ArrayAccess;
use Exception;
use Iterator;
use PDO;

class Query
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
     * @var string
     */
    private $group;

    /**
     * @var string
     */
    private $order;

    /**
     * @var array
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

    /**
     * Summary of all
     * @return QueryResult
     */
    public function all(): QueryResult
    {
        return new QueryResult(
            records: $this->execute()->fetchAll(PDO::FETCH_ASSOC),
            entity: $this->entity
        );
    }

    /**
     * @param string $table
     * @param mixed $alias
     * @return Query
     */
    public function from(string $table, ?string $alias = null): self
    {
        $alias ? $this->from[$alias] = $table : $this->from[] = $table;
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
     * @param string $field
     * @return Query
     */
    public function group(string $field): self
    {
        $this->group = $field;
        return $this;
    }

    /**
     * @param string $field
     * @return Query
     */
    public function order(string $field): self
    {
        $this->order = $field;
        return $this;
    }

    /**
     * @param int[] $limit
     * @return Query
     */
    public function limit(int ...$limit): self
    {
        $this->limit = $limit;
        return $this;
    }

    /**
     * Summary of count
     */
    public function count()
    {
        $this->select("COUNT(id)");
        return $statement = $this
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

    /**
     * Summary of __tostring
     * @return string
     */
    public function __tostring()
    {
        $parts = ['SELECT'];
        $this->select ?
            $parts[] = join(', ', $this->select)
            :
            $parts[] = '*';
        $parts[] = 'FROM';
        $parts[] = $this->buildFrom();
        if (!empty($this->where)) {
            $parts[] = 'WHERE';
            $parts[] = '(' . join(') AND (', $this->where) . ')';
        }
        if ($this->group) {
            $parts[] = "GROUP BY $this->group";
        }
        if ($this->order) {
            $parts[] = "ORDER BY $this->order";
        }
        if ($this->limit) {
            $parts[] = 'LIMIT';
            $parts[] = join(', ', $this->limit);
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
                $from[] = "$value as $key"
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
        if ($this->params) {
            $statement = $this->pDO->prepare($query);
            $statement->execute($this->params);
            return $statement;
        }
        return $this->pDO->query($query);
    }
}
