<?php

namespace App\Blog\Table;

use App\Blog\Entity\Post;
use App\Blog\Table\CategoryTable;
use Framework\Database\Query;
use Framework\Database\Table;

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

    public function findAll(): Query
    {
        $category = new CategoryTable($this->pdo);
        return $this->createQuery()
            ->join($category->getTable() . ' c', 'c.id = p.category_id')
            ->select('p.*, c.name as category_name, c.slug as category_slug')
            ->from('posts', 'p')
            ->order('p.created_at DESC');
    }

    public function findPublic(): Query
    {
        return $this->findAll()
            ->where('p.published = 1')
            ->where('p.created_at < NOW()');
    }

    public function findPublicWithCategory(int $id): Query
    {
        return $this->findPublic()->where("p.category_id = $id");
    }

    public function findWithCategory(int $post_id): Post
    {
        return $this->findPublic()
            ->where("p.id = $post_id")
            ->fetch();
    }
}
