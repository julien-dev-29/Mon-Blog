<?php
namespace Tests\Framework\Database;

use Framework\Database\Query;
use PHPUnit\Framework\TestCase;
use Tests\DatabaseTestCase;

class QueryTest extends DatabaseTestCase
{
    public function testSimpleQuery()
    {
        $query = new Query()->from('posts')->select('name');
        $this->assertEquals(
            expected: 'SELECT name FROM posts',
            actual: (string) $query
        );
    }

    public function testWithWhere()
    {
        $query = new Query()
            ->from('posts', 'p')
            ->where('a = :a OR b = :b', 'c = :c');
        $query2 = new Query()
            ->from('posts', 'p')
            ->where('a = :a OR b = :b')
            ->where('c = :c');
        $this->assertEquals(
            expected: 'SELECT * FROM posts p WHERE (a = :a OR b = :b) AND (c = :c)',
            actual: (string) $query
        );
        $this->assertEquals(
            expected: 'SELECT * FROM posts p WHERE (a = :a OR b = :b) AND (c = :c)',
            actual: (string) $query2
        );
    }

    public function testFetchAll()
    {
        $pdo = $this->getPDO();
        $this->migrateDatabase($pdo);
        $this->seedDatabase($pdo);
        $posts = new Query($pdo)->from('posts', 'p')->count();
        $this->assertEquals(100, $posts);
    }

    public function testFetchAllWithParams()
    {
        $pdo = $this->getPDO();
        $this->migrateDatabase($pdo);
        $this->seedDatabase($pdo);
        $posts = new Query($pdo)
            ->from('posts', 'p')
            ->where('p.id < :number')
            ->params([
                'number' => 30
            ])
            ->count();
        $this->assertEquals(expected: 29, actual: $posts);
    }

    public function testHydrateEntity()
    {
        $pdo = $this->getPDO();
        $this->migrateDatabase($pdo);
        $this->seedDatabase($pdo);
        $posts = new Query($pdo)
            ->from('posts', 'p')
            ->into(Demo::class)
            ->fetchAll();
        $this->assertEquals(
            expected: 'demo',
            actual: substr($posts[0]->getSlug(), -4)
        );
    }

    public function testLazyHydrate()
    {
        $pdo = $this->getPDO();
        $this->migrateDatabase($pdo);
        $this->seedDatabase($pdo);
        $posts = new Query($pdo)
            ->from('posts', 'p')
            ->into(Demo::class)
            ->fetchAll();
        $post = $posts[0];
        $post2 = $posts[0];
        $this->assertSame($post, $post2);
    }

    public function testGroupBy()
    {
        $query = new Query()
            ->select('name', 'category_id')
            ->from('posts')
            ->group('name')
        ;
        $this->assertEquals(
            expected: 'SELECT name, category_id FROM posts GROUP BY name',
            actual: $query->__tostring()
        );
    }

    public function testOrderBy()
    {
        $query = new Query()
            ->select('name', 'category_id')
            ->from('posts')
            ->group('name')
            ->order('id ASC')
            ->order('name DESC')
        ;
        $this->assertEquals(
            expected: 'SELECT name, category_id FROM posts GROUP BY name ORDER BY id ASC, name DESC',
            actual: $query->__tostring()
        );
    }

    public function testLimit()
    {
        $query = new Query()
            ->select('name', 'category_id')
            ->from('posts')
            ->group('name')
            ->limit(10, 5)
        ;
        $this->assertEquals(
            expected: 'SELECT name, category_id FROM posts GROUP BY name LIMIT 5, 10',
            actual: $query->__tostring()
        );
    }


    public function testJoinQuery()
    {
        $query = new Query()
            ->from('posts', 'p')
            ->select('name')
            ->join('categories c', 'c.id = p.category_id')
            ->join('categories c2', 'c2.id = p.category_id', 'inner');
        $this->assertEquals("SELECT name FROM posts p " .
            "LEFT JOIN categories c ON c.id = p.category_id " .
            "INNER JOIN categories c2 " .
            "ON c2.id = p.category_id", $query->__tostring());
    }
}