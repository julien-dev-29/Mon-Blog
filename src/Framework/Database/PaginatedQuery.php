<?php
namespace Framework\Database;

use Pagerfanta\Adapter\AdapterInterface;
use PDO;

/**
 * Traite la pagination
 */
class PaginatedQuery implements AdapterInterface
{
    /**
     * @var PDO
     */
    private $pdo;

    /**
     * @var Query
     */
    private $query;

    /**
     * Summary of __construct
     * @param \PDO $pdo
     * @param string $query
     * @param string $countQuery
     * @param string|null $entity
     */
    public function __construct(Query $query)
    {
        $this->query = $query;
    }

    /**
     * Retourne le nombre de résultat
     * @return int
     */
    public function getNbResults(): int
    {
        return $this->query->count();
    }

    /**
     * @param int $offset
     * @param int $length
     * @return \Traversable
     */
    public function getSlice(int $offset, int $length): QueryResult
    {
        $query = clone $this->query;
        return $query->limit($length, $offset)->fetchAll();
    }
}
