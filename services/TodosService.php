<?php

declare(strict_types=1);

require_once 'models/todo.php';

class TodosService
{
    public function __construct(public PDO $database) {}

    /**
     * Create a new todo.
     *
     * @param string $title
     * @return int Inserted todo ID
     */
    public function create(string $title): int
    {
        $query = "INSERT INTO todos (title) VALUES (:title)";
        $pdostmt = $this->database->prepare($query);

        $pdostmt->bindValue(':title', $title, PDO::PARAM_STR);
        $pdostmt->execute();

        return (int)$this->database->lastInsertId();
    }

    /**
     * Get a todo by ID.
     *
     * @param int $id
     * @return Todo|null Associative array of todo or null if not found
     */
    public function find(int $id): ?Todo
    {
        $query = "SELECT * FROM todos WHERE id = :id";
        $pdostmt = $this->database->prepare($query);

        $pdostmt->bindValue(':id', $id, PDO::PARAM_INT);
        $pdostmt->setFetchMode(PDO::FETCH_CLASS, Todo::class);
        $pdostmt->execute();

        $row = $pdostmt->fetch(PDO::FETCH_ASSOC);

        if ($row === false) {
            return null;
        }

        $todo = new Todo();

        $todo->id = (int)$row['id'];
        $todo->title = $row['title'];
        $todo->isCompleted = (bool)$row['is_completed'];
        $todo->isArchived = (bool)$row['is_archived'];
        $todo->lastUpdated = $row['last_updated'];
        $todo->createdAt = $row['created_at'];

        return $todo;
    }

    /**
     * Get all todos.
     *
     * @return Todo[] Array of todos (each as associative array)
     */
    public function all(): ?array
    {
        $query = "SELECT * FROM todos ORDER BY created_at DESC";
        $pdostmt = $this->database->query($query);
        $rows = $pdostmt->fetchAll(PDO::FETCH_ASSOC);

        if (empty($rows)) {
            return null;
        }

        $todos = [];

        foreach ($rows as $row) {
            $todo = new Todo();
            $todo->id = (int)$row['id'];
            $todo->title = $row['title'];
            $todo->isCompleted = (bool)$row['is_completed'];
            $todo->isArchived = (bool)$row['is_archived'];
            $todo->lastUpdated = $row['last_updated'];
            $todo->createdAt = $row['created_at'];

            $todos[] = $todo;
        }

        return $todos;
    }

    /**
     * Update a todo by ID.
     *
     * @param int $id
     * @param string|null $title
     * @param bool|null $isCompleted
     * @param bool|null $isArchived
     * @return bool True if updated, false otherwise
     */
    public function update(int $id, ?string $title = null, ?bool $isCompleted = null, ?bool $isArchived = null): bool
    {
        $fields = [];
        $params = [':id' => $id];

        if ($title !== null) {
            $fields[] = 'title = :title';
            $params[':title'] = $title;
        }
        if ($isCompleted !== null) {
            $fields[] = 'is_completed = :is_completed';
            $params[':is_completed'] = $isCompleted ? 1 : 0;
        }
        if ($isArchived !== null) {
            $fields[] = 'is_archived = :is_archived';
            $params[':is_archived'] = $isArchived ? 1 : 0;
        }

        if (empty($fields)) {
            // Nothing to update
            return false;
        }

        // Update last_updated timestamp to current time
        $fields[] = 'last_updated = CURRENT_TIMESTAMP';

        $query = "UPDATE todos SET " . implode(', ', $fields) . " WHERE id = :id";
        $pdostmt = $this->database->prepare($query);

        foreach ($params as $key => $value) {
            $paramType = is_int($value) ? PDO::PARAM_INT : PDO::PARAM_STR;
            $pdostmt->bindValue($key, $value, $paramType);
        }

        return $pdostmt->execute();
    }

    /**
     * Delete a todo by ID.
     *
     * @param int $id
     * @return bool True if deleted, false otherwise
     */
    public function delete(int $id): bool
    {
        $query = "DELETE FROM todos WHERE id = :id";
        $pdostmt = $this->database->prepare($query);

        $pdostmt->bindValue(':id', $id, PDO::PARAM_INT);

        return $pdostmt->execute();
    }
}
