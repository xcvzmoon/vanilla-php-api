<?php

declare(strict_types=1);

require_once 'sqlitedb.php';
require_once 'migrations.php';
require_once 'services/TodosService.php';

$database = SQLiteDB::getInstance();
$todosService = new TodosService($database);

$path = 'todos.json';
$file = file_get_contents(filename: $path);
$data = json_decode(json: $file);
$todos = array_map(
    callback: function ($todo): Todo {
        $todoObj = new Todo();
        $now = date('Y-m-d H:i:s');

        $todoObj->id = (int)$todo->id;
        $todoObj->title = $todo->title;
        $todoObj->isCompleted = (bool)$todo->isCompleted;
        $todoObj->isArchived = (bool)$todo->isArchived;
        $todoObj->lastUpdated = $now;
        $todoObj->createdAt = $now;

        return $todoObj;
    },
    array: $data
);

foreach ($todos as $todo) {
    $todosService->create($todo->title);
}

die(json_encode($todosService->all() ?? [], JSON_PRETTY_PRINT));
