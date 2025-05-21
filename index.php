<?php

require_once 'models/todo.php';
require_once 'services/TodosService.php';

$todosService = new TodosService();

die(print_r(value: $todosService->all()));
