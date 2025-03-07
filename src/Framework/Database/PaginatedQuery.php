<?php

namespace Framework\Database;

use Pagerfanta\Adapter\AdapterInterface;
use PDO;

class PaginatedQuery implements AdapterInterface
{
    private $pdo;
    private $query;
    private $countQuery;
    private $entity;
    
    /**
     * Summary of __construct
     * @param \PDO $pdo
     * @param string $query
     * @param string $countQuery
     * @param string $entity
     */
    public function __construct(PDO $pdo, string $query, string $countQuery, string $entity)
    {
        $this->pdo = $pdo;
        $this->query = $query;
        $this->countQuery = $countQuery;
        $this->entity = $entity;
    }
    public function getNbResults(): int
    {
        return $this->pdo->query($this->countQuery)->fetchColumn();
    }
    public function getSlice(int $offset, int $length): array|\Traversable
    {
        $statement = $this->pdo->prepare(query: "$this->query LIMIT :offset, :length;");
        $statement->bindParam('offset', $offset, PDO::PARAM_INT);
        $statement->bindParam('length', $length, PDO::PARAM_INT);
        $statement->setFetchMode(PDO::FETCH_CLASS, $this->entity);
        $statement->execute();
        return $statement->fetchAll();
    }
}
