<?php

namespace Tests;

use PDO;
use Phinx\Config\Config;
use Phinx\Migration\Manager;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Output\NullOutput;

class DatabaseTestCase extends TestCase
{
    /**
     * @var PDO
     */
    protected $pdo;

    /**
     * Summary of manager
     * @var Manager
     */
    private $manager;

    /**
     * Summary of setUp
     * @return void
     */
    public function setUp(): void
    {
        // PDO
        $this->pdo = new PDO(
            dsn: 'sqlite::memory:',
            username: null,
            password: null,
            options: [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
            ]
        );

        // APP
        $configArray = require 'phinx.php';
        $configArray['environments']['test'] = [
            'adapter' => 'sqlite',
            'connection' => $this->pdo
        ];
        $config = new Config($configArray);
        $manager = new Manager(
            config: $config,
            input: new StringInput(' '),
            output: new NullOutput()
        );
        $manager->migrate('test');
        $this->manager = $manager;
        $this->pdo->setAttribute(
            attribute: PDO::ATTR_DEFAULT_FETCH_MODE,
            value: PDO::FETCH_OBJ
        );
    }

    /**
     * Summary of seedDatabase
     * @return void
     */
    public function seedDatabase()
    {
        $this->pdo->setAttribute(
            attribute: PDO::ATTR_DEFAULT_FETCH_MODE,
            value: PDO::FETCH_BOTH
        );
        $this->manager->seed('test');
        $this->pdo->setAttribute(
            attribute: PDO::ATTR_DEFAULT_FETCH_MODE,
            value: PDO::FETCH_OBJ
        );
    }
}