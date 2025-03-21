<?php

namespace App\Blog\Table;

use App\Blog\Entity\Post;
use Framework\Database\PaginatedQuery;
use Framework\Database\Table;
use Pagerfanta\Pagerfanta;

class PostTable extends Table
{
    /**
     * @var string
     */
    protected $entity = Post::class;

    /**
     * @var string
     */
    protected $table = 'posts';

    /**
     * Summary of findPaginatedPublic
     * @param int $perPage
     * @param int $currentPage
     * @return Pagerfanta
     */
    public function findPaginatedPublic(int $perPage, int $currentPage): Pagerfanta
    {
        $query = new PaginatedQuery(
            pdo: $this->pdo,
            query: "SELECT p.*, c.name category_name, c.slug category_slug  
            FROM posts p 
            LEFT JOIN categories c ON p.category_id = c.id 
            ORDER BY created_at DESC",
            countQuery: "SELECT COUNT(id) FROM $this->table",
            entity: $this->entity
        );
        return new Pagerfanta($query)
            ->setMaxPerPage($perPage)
            ->setCurrentPage($currentPage);
    }

    public function findPaginatedPublicByCategory(int $perPage, int $currentPage, int $categoryID): Pagerfanta
    {
        $query = new PaginatedQuery(
            pdo: $this->pdo,
            query: "SELECT p.*, c.name category_name, c.slug category_slug  
            FROM posts p 
            LEFT JOIN categories c ON p.category_id = c.id 
            WHERE p.category_id = :category
            ORDER BY created_at DESC",
            countQuery: "SELECT COUNT(id) FROM $this->table WHERE category_id = :category",
            entity: $this->entity,
            params: ['category' => $categoryID]
        );
        return new Pagerfanta($query)
            ->setMaxPerPage($perPage)
            ->setCurrentPage($currentPage);
    }

    public function findWithCategory(int $id)
    {
        return $this->fetchOrFail(
            query: "SELECT p.*, c.name category_name, c.slug category_slug
                    FROM posts p LEFT JOIN categories c
                    ON p.category_id = c.id
                    WHERE p.id = ?",
            params: [$id]
        );
    }

    /**
     * @return string
     */
    public function paginationQuery(): string
    {
        return "SELECT p.id, p.name, c.name category_name
        FROM $this->table p
        LEFT JOIN categories c ON p.category_id = c.id
        ORDER BY created_at DESC";
    }
}
