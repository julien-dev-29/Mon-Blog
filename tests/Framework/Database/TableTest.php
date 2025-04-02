<?php

namespace Tests\Framework\Database;

use Framework\Database\Table;
use PDO;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use stdClass;

class TableTest extends TestCase
{
    /**
     * @var Table
     */
    private $table;

    /**
     * @return void
     */
    public function setUp(): void
    {
        $pdo = new PDO('sqlite::memory:', null, null, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ
        ]);
        $pdo->exec('CREATE TABLE test (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        name VARCHAR(255))');
        $this->table = new Table($pdo);
        $reflection = new ReflectionClass($this->table);
        $property = $reflection->getProperty('table');
        $property->setAccessible(true);
        $property->setValue($this->table, 'test');
    }

    public function testFind()
    {
        $this->table->getPDO()->exec('INSERT INTO test (name) VALUES ("a1")');
        $this->table->getPDO()->exec('INSERT INTO test (name) VALUES ("a2")');
        $test = $this->table->find(1);
        $this->assertInstanceOf(stdClass::class, $test);
        $this->assertEquals('a1', $test->name);
        $test2 = $this->table->find(2);
        $this->assertInstanceOf(stdClass::class, $test2);
        $this->assertEquals('a2', $test2->name);
    }

    public function testFindList()
    {
        $this->table->getPDO()->exec('INSERT INTO test (name) VALUES ("a1")');
        $this->table->getPDO()->exec('INSERT INTO test (name) VALUES ("a2")');
        $this->assertEquals(['1' => 'a1', '2' => 'a2'], $this->table->findList());
    }

    public function testExists()
    {
        $this->table->getPDO()->exec('INSERT INTO test (name) VALUES ("a1")');
        $this->table->getPDO()->exec('INSERT INTO test (name) VALUES ("a2")');
        $this->table->getPDO()->exec('INSERT INTO test (name) VALUES ("a3")');
        $this->assertTrue($this->table->exists(1));
        $this->assertTrue($this->table->exists(2));
        $this->assertTrue($this->table->exists(3));
    }
    public function testFindAll()
    {
        $this->table->getPDO()->exec('INSERT INTO test (name) VALUES ("a1")');
        $this->table->getPDO()->exec('INSERT INTO test (name) VALUES ("a2")');
        $this->table->getPDO()->exec('INSERT INTO test (name) VALUES ("a3")');
        $categories = $this->table->findAll()->fetchAll();
        $this->assertCount(3, $categories);
        $this->assertInstanceOf(stdClass::class, $categories[0]);
        $this->assertEquals('a1', $categories[0]->name);
        $this->assertEquals('a2', $categories[1]->name);
    }

    public function testFindBy()
    {
        $this->table->getPDO()->exec('INSERT INTO test (name) VALUES ("a1")');
        $this->table->getPDO()->exec('INSERT INTO test (name) VALUES ("a2")');
        $this->table->getPDO()->exec('INSERT INTO test (name) VALUES ("a1")');
        $category = $this->table->findBy('name', 'a1');
        $this->assertInstanceOf(stdClass::class, $category);
        $this->assertEquals(1, (int) $category->id);
    }

    public function testCount()
    {
        $this->table->getPDO()->exec('INSERT INTO test (name) VALUES ("a1")');
        $this->table->getPDO()->exec('INSERT INTO test (name) VALUES ("a2")');
        $this->table->getPDO()->exec('INSERT INTO test (name) VALUES ("a1")');
        $this->assertEquals(3, $this->table->count());
    }
}