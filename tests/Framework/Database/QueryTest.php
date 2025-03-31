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
            expected: 'SELECT * FROM posts as p WHERE (a = :a OR b = :b) AND (c = :c)',
            actual: (string) $query
        );
        $this->assertEquals(
            expected: 'SELECT * FROM posts as p WHERE (a = :a OR b = :b) AND (c = :c)',
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
            ->order('name')
        ;
        $this->assertEquals(
            expected: 'SELECT name, category_id FROM posts GROUP BY name ORDER BY name',
            actual: $query->__tostring()
        );
    }

    public function testLimit()
    {
        $query = new Query()
            ->select('name', 'category_id')
            ->from('posts')
            ->group('name')
            ->order('name')
            ->limit(5, 15)
        ;
        $this->assertEquals(
            expected: 'SELECT name, category_id FROM posts GROUP BY name ORDER BY name LIMIT 5, 15',
            actual: $query->__tostring()
        );
    }
}