<?php

declare(strict_types=1);

require_once 'sqlitedb.php';
require_once 'migrations.php';
require_once 'services/TodosService.php';

$database = SQLiteDB::getInstance();
$todosService = new TodosService($database);

try {
    $database->exec($createTodosTableQuery);
} catch (PDOException $e) {
    die("Error creating table: {$e->getMessage()}");
}

$path = 'todos.json';
$file = file_get_contents(filename: $path);
$data = json_decode(json: $file);
$todos = array_map(
    callback: function ($item): Todo {
        $todo = new Todo();
        $now = date('Y-m-d H:i:s');

        $todo->id = (int)$item->id;
        $todo->title = $item->title;
        $todo->isCompleted = (bool)$item->completed;
        $todo->isArchived = (bool)$item->archived;
        $todo->lastUpdated = $now;
        $todo->createdAt = $now;

        return $todo;
    },
    array: $data
);

foreach ($todos as $todo) {
    $todosService->create($todo->title);
}

die(json_encode($todosService->all() ?? [], JSON_PRETTY_PRINT));
