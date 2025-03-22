<?php

namespace Framework\Database;

use Framework\Database\Exception\RecordNotFoundException;
use Framework\Database\PaginatedQuery;
use Pagerfanta\Pagerfanta;
use PDO;

class Table
{
    /**
     * @var string Nom de la table en BDD
     */
    protected $table;

    /**
     * @var string|null Nom de la table en BDD
     */
    protected $entity;

    /**
     * @var PDO
     */
    protected $pdo;
    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Execute une requète et récupère le premier résultat
     *
     * @param string $query
     * @param array $params
     * @throws \Framework\Database\Exception\RecordNotFoundException
     */
    protected function fetchOrFail(string $query, array $params = [])
    {
        $query = $this->pdo->prepare(query: $query);
        $query->execute(params: $params);
        if ($this->entity) {
            $query->setFetchMode(PDO::FETCH_CLASS, $this->entity);
        }
        $record = $query->fetch();
        if ($record === false) {
            throw new RecordNotFoundException();
        }
        return $record;
    }

    /**
     * Summary of findPaginated
     * @param int $perPage
     * @return Pagerfanta
     */
    public function findPaginated(int $perPage, int $currentPage): Pagerfanta
    {
        $query = new PaginatedQuery(
            pdo: $this->pdo,
            query: $this->paginationQuery(),
            countQuery: "SELECT COUNT(id) FROM $this->table",
            entity: $this->entity
        );
        return new Pagerfanta($query)
            ->setMaxPerPage($perPage)
            ->setCurrentPage($currentPage);
    }

    /**
     * Retourne la requête pour la pagination
     *
     * @return string
     */
    protected function paginationQuery(): string
    {
        return "SELECT * FROM  $this->table";
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
     * Retourne un élément par son id
     *
     * @param integer $id
     * @return mixed
     * @throws RecordNotFoundException
     */
    public function find(int $id): mixed
    {
        $query = $this->pdo->prepare(query: "SELECT * FROM $this->table  WHERE id = ?;");
        $query->execute(params: [$id]);
        if ($this->entity) {
            $query->setFetchMode(PDO::FETCH_CLASS, $this->entity);
        }
        $record = $query->fetch();
        if ($record === false) {
            throw new RecordNotFoundException();
        }
        return $record;
    }

    /**
     * Retourne tous les enregistrements d'une table
     *
     * @return array
     * @throws RecordNotFoundException
     */
    public function findAll(): array
    {
        $query = $this->pdo
            ->query("SELECT * FROM $this->table");
        if ($this->entity) {
            $query->setFetchMode(PDO::FETCH_CLASS, $this->entity);
        } else {
            $query->setFetchMode(PDO::FETCH_OBJ);
        }
        return $query->fetchAll();
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
        return $this->fetchOrFail(
            query: "SELECT * FROM $this->table WHERE $field = ?",
            params: [$value]
        );
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
        return $this->fetchColumn("SELECT COUNT(id) FROM $this->table");
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

    /**
     * Retourne la première colonne
     *
     * @param mixed $query
     * @param mixed $params
     */
    private function fetchColumn($query, $params = [])
    {
        $query = $this->pdo->prepare($query);
        $query->execute($params);
        if ($this->entity) {
            $query->setFetchMode(PDO::FETCH_CLASS, $this->entity);
        }
        return $query->fetchColumn();
    }
}
