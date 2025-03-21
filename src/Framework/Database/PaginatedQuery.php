<?php

namespace Framework\Database;

use Pagerfanta\Adapter\AdapterInterface;
use PDO;

class PaginatedQuery implements AdapterInterface
{
    /**
     * @var PDO
     */
    private $pdo;

    /**
     * @var string
     */
    private $query;

    /**
     * @var string
     */
    private $countQuery;

    /**
     * @var string|null
     */
    private $entity;

    /**
     * @var array
     */
    private $params;

    /**
     * Summary of __construct
     * @param \PDO $pdo
     * @param string $query
     * @param string $countQuery
     * @param string|null $entity
     */
    public function __construct(
        PDO $pdo,
        string $query,
        string $countQuery,
        ?string $entity,
        array $params = []
    ) {
        $this->pdo = $pdo;
        $this->query = $query;
        $this->countQuery = $countQuery;
        $this->entity = $entity;
        $this->params = $params;
    }

    /**
     * @return int
     */
    public function getNbResults(): int
    {
        if (!empty($this->params)) {
            $query = $this->pdo->prepare($this->countQuery);
            $query->execute($this->params);
            return $query->fetchColumn();
        }
        return $this->pdo->query($this->countQuery)->fetchColumn();
    }

    /**
     * @param int $offset
     * @param int $length
     * @return array
     */
    public function getSlice(int $offset, int $length): array|\Traversable
    {
        $query = $this->pdo->prepare(query: "$this->query LIMIT :offset, :length;");
        foreach ($this->params as $key => $param) {
            $query->bindParam($key, $param);
        }
        $query->bindParam('offset', $offset, PDO::PARAM_INT);
        $query->bindParam('length', $length, PDO::PARAM_INT);
        if ($this->entity) {
            $query->setFetchMode(PDO::FETCH_CLASS, $this->entity);
        }
        $query->execute();
        return $query->fetchAll();
    }
}
