<?php

declare(strict_types=1);

namespace App\Repositories;

use PDO;

abstract class BaseRepository
{
    protected string $table = '';
    protected string $primaryKey = 'id';
    protected PDO $connection;

    public function __construct(PDO $connection)
    {
        $this->connection = $connection;
    }

    public function find($id)
    {
        $statement = $this->connection->prepare("SELECT * FROM {$this->table} WHERE {$this->primaryKey} = :id LIMIT 1");
        $statement->execute(['id' => $id]);

        return $statement->fetch();
    }

    public function all(string $orderBy = 'created_at', string $direction = 'DESC'): array
    {
        $orderBy = $this->sanitizeIdentifier($orderBy, 'created_at');
        $direction = strtoupper($direction) === 'ASC' ? 'ASC' : 'DESC';
        $statement = $this->connection->query("SELECT * FROM {$this->table} ORDER BY {$orderBy} {$direction}");

        return $statement->fetchAll();
    }

    public function paginate(int $page = 1, int $perPage = 15, string $orderBy = 'created_at', string $direction = 'DESC'): array
    {
        $page = max(1, $page);
        $offset = ($page - 1) * $perPage;
        $orderBy = $this->sanitizeIdentifier($orderBy, 'created_at');
        $direction = strtoupper($direction) === 'ASC' ? 'ASC' : 'DESC';

        $total = (int) $this->connection->query("SELECT COUNT(*) FROM {$this->table}")->fetchColumn();
        $statement = $this->connection->prepare("SELECT * FROM {$this->table} ORDER BY {$orderBy} {$direction} LIMIT :limit OFFSET :offset");
        $statement->bindValue(':limit', $perPage, PDO::PARAM_INT);
        $statement->bindValue(':offset', $offset, PDO::PARAM_INT);
        $statement->execute();

        return [
            'data' => $statement->fetchAll(),
            'pagination' => [
                'page' => $page,
                'per_page' => $perPage,
                'total' => $total,
                'pages' => (int) ceil($total / $perPage),
            ],
        ];
    }

    public function where(array $conditions, string $orderBy = 'created_at', string $direction = 'DESC', ?int $limit = null): array
    {
        [$sql, $bindings] = $this->buildWhereClause($conditions);
        $orderBy = $this->sanitizeIdentifier($orderBy, 'created_at');
        $direction = strtoupper($direction) === 'ASC' ? 'ASC' : 'DESC';
        $query = "SELECT * FROM {$this->table}" . $sql . " ORDER BY {$orderBy} {$direction}";

        if ($limit !== null) {
            $query .= ' LIMIT ' . (int) $limit;
        }

        $statement = $this->connection->prepare($query);
        $statement->execute($bindings);

        return $statement->fetchAll();
    }

    public function create(array $data): string
    {
        $columns = [];
        $filteredData = [];

        foreach ($data as $column => $value) {
            $column = $this->sanitizeIdentifier((string) $column, '');

            if ($column === '') {
                continue;
            }

            $columns[] = $column;
            $filteredData[$column] = $value;
        }

        if ($columns === []) {
            throw new \InvalidArgumentException('No valid columns were provided for insert.');
        }

        $data = $filteredData;
        $placeholders = array_map(static fn (string $column): string => ':' . $column, $columns);

        $statement = $this->connection->prepare(sprintf(
            'INSERT INTO %s (%s) VALUES (%s)',
            $this->table,
            implode(', ', $columns),
            implode(', ', $placeholders)
        ));

        foreach ($data as $column => $value) {
            $statement->bindValue(':' . $column, $value);
        }

        $statement->execute();

        return (string) $this->connection->lastInsertId();
    }

    public function update($id, array $data): bool
    {
        $assignments = [];
        $filteredData = ['id' => $id];

        foreach (array_keys($data) as $column) {
            $column = $this->sanitizeIdentifier($column, '');

            if ($column === '') {
                continue;
            }

            $assignments[] = $column . ' = :' . $column;
            $filteredData[$column] = $data[$column];
        }

        if ($assignments === []) {
            throw new \InvalidArgumentException('No valid columns were provided for update.');
        }

        $statement = $this->connection->prepare(sprintf(
            'UPDATE %s SET %s WHERE %s = :id',
            $this->table,
            implode(', ', $assignments),
            $this->primaryKey
        ));

        foreach ($filteredData as $column => $value) {
            $statement->bindValue(':' . $column, $value);
        }

        return $statement->execute();
    }

    public function delete($id): bool
    {
        $statement = $this->connection->prepare("DELETE FROM {$this->table} WHERE {$this->primaryKey} = :id");

        return $statement->execute(['id' => $id]);
    }

    protected function buildWhereClause(array $conditions): array
    {
        if ($conditions === []) {
            return ['', []];
        }

        $clauses = [];
        $bindings = [];

        foreach ($conditions as $column => $value) {
            $column = $this->sanitizeIdentifier((string) $column, '');

            if ($column === '') {
                continue;
            }

            $placeholder = str_replace('.', '_', (string) $column);
            $clauses[] = $column . ' = :' . $placeholder;
            $bindings[$placeholder] = $value;
        }

        return [' WHERE ' . implode(' AND ', $clauses), $bindings];
    }

    protected function sanitizeIdentifier(string $identifier, string $fallback): string
    {
        return preg_match('/^[a-zA-Z0-9_.]+$/', $identifier) === 1 ? $identifier : $fallback;
    }
}
