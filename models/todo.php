<?php

class Todo
{
    public function __construct(
        public int $id,
        public string $title,
        public bool $completed,
        public bool $archived
    ) {}
}
