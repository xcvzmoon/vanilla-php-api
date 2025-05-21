<?php

class TodosService
{
    private array $todos = [];

    public function __construct()
    {
        $path = 'todos.json';
        $file = file_get_contents(filename: $path);
        $data = json_decode(json: $file);

        $this->todos = array_map(
            callback: fn($todo): Todo => new Todo(
                id: $todo->id,
                title: $todo->title,
                completed: $todo->completed,
                archived: $todo->archived
            ),
            array: $data
        );
    }

    public function all(): array
    {
        return $this->todos;
    }

    public function find(int $id): ?Todo
    {
        return array_filter(
            array: $this->todos,
            callback: fn(Todo $todo): bool => $todo->id === $id
        )[0] ?? null;
    }

    public function create(string $title): Todo
    {
        $todo = new Todo(
            id: count(value: $this->todos) + 1,
            title: $title,
            completed: false,
            archived: false
        );

        $this->todos[] = $todo;

        return $todo;
    }

    public function update(Todo $todo): Todo
    {
        $this->todos = array_map(
            callback: fn(Todo $item): Todo => $item->id === $todo->id ? $todo : $item,
            array: $this->todos
        );

        return $todo;
    }

    public function archive(int $id): void
    {
        $todo = $this->find(id: $id);

        if ($todo) {
            $todo->archived = true;
            $this->update(todo: $todo);
        }
    }
}
