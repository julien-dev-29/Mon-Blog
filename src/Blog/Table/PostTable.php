<?php

namespace App\Blog\Table;

use App\Blog\Entity\Post;
use Framework\Database\PaginatedQuery;
use Kint\Kint;
use Pagerfanta\Pagerfanta;
use PDO;
use stdClass;

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
     *
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
     * @return Post
     */
    public function find(int $id): Post
    {
        $query = $this->pdo->prepare(query: 'SELECT * FROM posts WHERE id = ?;');
        $query->execute(params: [$id]);
        $query->setFetchMode(PDO::FETCH_CLASS, Post::class);
        return $query->fetch();
    }
}
