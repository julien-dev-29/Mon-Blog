<?php
namespace Framework\Database;

use PDO;

class Query
{
    private $select;
    private $from;
    private $where = [];
    private $group;
    private $order;
    private $limit;
    private $params;
    /**
     * @var PDO
     */
    private $pDO;

    public function __construct(?PDO $pDO = null)
    {
        $this->pDO = $pDO;
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

    public function group(string $field): self
    {
        $this->group = $field;
        return $this;
    }

    public function order(string $field): self
    {
        $this->order = $field;
        return $this;
    }

    public function limit(int ...$limit): self
    {
        $this->limit = $limit;
        return $this;
    }

    public function count()
    {
        $this->select("COUNT(id)");
        return $statement = $this
            ->execute()
            ->fetchColumn();
    }

    public function params(array $params)
    {
        $this->params = $params;
        return $this;
    }

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
