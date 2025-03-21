<?php

namespace Tests\Blog\Table;

use App\Blog\Entity\Post;
use App\Blog\Table\PostTable;
use Framework\Database\Exception\RecordNotFoundException;
use PDO;
use Tests\DatabaseTestCase;

class PostTableTest extends DatabaseTestCase
{
    /**
     * @var PDO
     */
    protected $pdo;

    /**
     * @var PostTable
     */
    private $postTable;

    private $app;

    public function setUp(): void
    {
        parent::setUp();
        $pdo = $this->getPDO();
        $this->migrateDatabase($pdo);
        $this->postTable = new PostTable($pdo);
    }

    public function testFind()
    {
        $this->seedDatabase($this->postTable->getPDO());
        $post = $this->postTable->find(1);
        $this->assertInstanceOf(Post::class, $post);
    }

    public function testNotFound()
    {
        $this->expectException(RecordNotFoundException::class);
        $post = $this->postTable->find(2500);
    }

    public function testUpdate()
    {
        $this->seedDatabase($this->postTable->getPDO());
        $this->postTable->update(1, [
            'name' => "Hello",
            'slug' => "demo"
        ]);
        $post = $this->postTable->find(1);
        $this->assertEquals('Hello', $post->name);
        $this->assertEquals('demo', $post->slug);
    }

    public function testInsert()
    {
        $this->postTable->insert(['name' => "Yolo", "slug" => "demo"]);
        $post = $this->postTable->find(1);
        $this->assertEquals("Yolo", $post->name);
        $this->assertEquals("demo", $post->slug);
    }

    public function testDelete()
    {
        $pdo = $this->postTable->getPDO();
        $this->postTable->insert(['name' => 'Yolo', 'slug' => 'slugger']);
        $this->postTable->insert(['name' => 'Yolo2', 'slug' => 'slugger2']);
        $count = $pdo->query('SELECT COUNT(id) FROM posts')->fetchColumn();
        $this->assertEquals(2, (int) $count);
        $this->postTable->delete($pdo->lastInsertId());
        $count = $pdo->query('SELECT COUNT(id) FROM posts')->fetchColumn();
        $this->assertEquals(1, (int) $count);
    }
}