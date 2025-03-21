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
    public function getPDO(): PDO
    {
        return new PDO(
            dsn: 'sqlite::memory:',
            username: null,
            password: null,
            options: [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ
            ]
        );
    }

    public function getManager(PDO $pdo): Manager
    {
        $configArray = require 'phinx.php';
        $configArray['environments']['test'] = [
            'adapter' => 'sqlite',
            'connection' => $pdo
        ];
        $config = new Config($configArray);
        return new Manager(
            config: $config,
            input: new StringInput(' '),
            output: new NullOutput()
        );
    }

    /**
     * Summary of seedDatabase
     * @return void
     */
    public function seedDatabase(PDO $pdo)
    {
        $pdo->setAttribute(
            attribute: PDO::ATTR_DEFAULT_FETCH_MODE,
            value: PDO::FETCH_BOTH
        );
        $this->getManager($pdo)->migrate('test');
        $this->getManager($pdo)->seed('test');
        $pdo->setAttribute(
            attribute: PDO::ATTR_DEFAULT_FETCH_MODE,
            value: PDO::FETCH_OBJ
        );
    }

    public function migrateDatabase(PDO $pdo)
    {
        $pdo->setAttribute(
            attribute: PDO::ATTR_DEFAULT_FETCH_MODE,
            value: PDO::FETCH_BOTH
        );
        $this->getManager($pdo)->migrate('test');
        $pdo->setAttribute(
            attribute: PDO::ATTR_DEFAULT_FETCH_MODE,
            value: PDO::FETCH_OBJ
        );
    }
}