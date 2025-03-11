<?php

namespace App\Blog\Table;

use App\Blog\Entity\Post;
use Framework\Database\PaginatedQuery;
use Pagerfanta\Pagerfanta;
use PDO;

class PostTable
{
    /**
     * @var PDO
     */
    private $pdo;
    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Summary of findPaginated
     * @param int $perPage
     * @return Pagerfanta
     */
    public function findPaginated(int $perPage, int $currentPage): Pagerfanta
    {
        $query = new PaginatedQuery(
            pdo: $this->pdo,
            query: 'SELECT * FROM posts ORDER BY created_at DESC',
            countQuery: 'SELECT COUNT(id) FROM posts',
            entity: Post::class
        );
        return new Pagerfanta($query)
            ->setMaxPerPage($perPage)
            ->setCurrentPage($currentPage);
    }

    /**
     * Trouve un article par son id
     *
     * @return Post|null
     */
    public function find(int $id): ?Post
    {
        $query = $this->pdo->prepare(query: 'SELECT * FROM posts WHERE id = ?;');
        $query->execute(params: [$id]);
        $query->setFetchMode(PDO::FETCH_CLASS, Post::class);
        return $query->fetch() ?: null;
    }

    /**
     * Met à jour un article
     * @param int $id
     * @param array $fields
     * @return bool
     */
    public function update(int $id, array $params): bool
    {
        $fieldQuery = $this->buildFieldQuery($params);
        $params['id'] = $id;
        $statement = $this->pdo->prepare("UPDATE posts SET $fieldQuery WHERE id = :id");
        return $statement->execute($params);
    }

    /**
     * Insere un article en base de données
     * @param int $id
     * @param array $params
     * @return bool
     */
    public function insert(array $params): bool
    {
        $fields = array_keys($params);
        $values = array_map(function ($field) {
            return ":$field";
        }, $fields);
        $statement = $this->pdo->prepare("INSERT INTO posts 
            (" . join(',', $fields) . ") 
            VALUES (" . join(',', $values) . ")");
        return $statement->execute($params);
    }

    /**
     * Supprime un article de la base de données
     * @param int $id
     * @return bool
     */
    public function delete(int $id)
    {
        $statement = $this->pdo->prepare('DELETE FROM posts WHERE id = ?');
        return $statement->execute([$id]);
    }

    /**
     * Summary of buildFieldQuery
     * @param array $params
     * @return string
     */
    private function buildFieldQuery(array $params)
    {
        return join(', ', array_map(function ($field) {
            return "$field = :$field";
        }, array_keys($params)));
    }
}
