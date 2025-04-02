<?php

namespace Framework\Database;

use Framework\Database\Exception\RecordNotFoundException;
use PDO;
use stdClass;
use Traversable;

class Table
{
    /**
     * @var string Nom de la table en BDD
     */
    protected $table;

    /**
     * @var string|null Nom de la table en BDD
     */
    protected $entity = stdClass::class;

    /**
     * @var PDO
     */
    protected $pdo;
    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Récupère une liste clé valeur de nos enregistrements
     *
     * @return array
     */
    public function findList(): array
    {
        $results = $this->pdo
            ->query("SELECT id, name FROM $this->table")
            ->fetchAll(PDO::FETCH_NUM);
        $list = [];
        foreach ($results as $result) {
            $list[$result[0]] = $result[1];
        }
        return $list;
    }

    /**
     * Retourne une requête
     * @return Query
     */
    protected function createQuery()
    {
        return new Query($this->pdo)
        ->from($this->table, $this->table[0])
        ->into($this->entity)
        ;
    }

    /**
     * Retourne un élément par son id
     *
     * @param integer $id
     * @return mixed
     * @throws RecordNotFoundException
     */
    public function find(int $id): mixed
    {
        return $this->createQuery()
            ->where("id = :id")
            ->params(["id" => $id])
            ->fetchOrFail();
    }

    /**
     * Retourne tout les enregistrement
     * @return Query
     */
    public function findAll(): Query
    {
        return $this->createQuery();
    }

    /**
     * Retourne un enregistrement par un de ses champ
     *
     * @param string $field
     * @param string $value
     * @throws \Framework\Database\Exception\RecordNotFoundException
     * @return mixed
     */
    public function findBy(string $field, string $value)
    {
        return $this->createQuery()
            ->where("$field = :field")
            ->params(["field" => $value])
            ->fetchOrFail();
    }

    /**
     * Met à jour un élément
     *
     * @param int $id
     * @param array $fields
     * @return bool
     */
    public function update(int $id, array $params): bool
    {
        $fieldQuery = $this->buildFieldQuery($params);
        $params['id'] = $id;
        $query = $this->pdo->prepare("UPDATE $this->table SET $fieldQuery WHERE id = :id");
        return $query->execute($params);
    }

    /**
     * Insere un article en base de données
     *
     * @param int $id
     * @param array $params
     * @return bool
     */
    public function insert(array $params): bool
    {
        $fields = array_keys($params);
        $values = join(', ', array_map(
            callback: fn($field): string => ":$field",
            array: $fields
        ));
        $fields = join(', ', $fields);
        $query = $this->pdo->prepare("INSERT INTO $this->table 
        ($fields) VALUES ($values)");
        return $query->execute($params);
    }

    /**
     * Supprime un enregistremnt de la base de données
     * @param int $id
     * @return bool
     */
    public function delete(int $id)
    {
        $query = $this->pdo->prepare("DELETE FROM $this->table WHERE id = ?");
        return $query->execute([$id]);
    }

    /**
     * Retourne le nombre d'enregistreemnts
     *
     * @return int
     */
    public function count(): int
    {
        return $this->createQuery()->count();
    }

    /**
     * @return string
     */
    public function getEntity(): string
    {
        return $this->entity;
    }

    /**
     * @return string
     */
    public function getTable(): string
    {
        return $this->table;
    }

    /**
     * @return PDO
     */
    public function getPDO(): PDO
    {
        return $this->pdo;
    }

    /**
     * Vérifie qu'un enregistrement existe
     * @param mixed $id
     * @return bool
     */
    public function exists($id)
    {
        $query = $this->pdo->prepare("SELECT id FROM $this->table WHERE id = ?");
        $query->execute([$id]);
        return $query->fetchColumn() !== false;
    }

    /**
     * @param array $params
     * @return string
     */
    private function buildFieldQuery(array $params): string
    {
        return join(separator: ', ', array: array_map(
            callback: fn($field): string => "$field = :$field",
            array: array_keys($params)
        ));
    }
}
