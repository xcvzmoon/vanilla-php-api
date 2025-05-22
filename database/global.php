<?php

declare(strict_types=1);

function getSqliteVersion(PDO $db): string
{
    $pdostmt = $db->prepare('SELECT sqlite_version()');
    $result = $pdostmt->fetchColumn();

    return "SQLite version: $result";
}

function checkIfTableExists(PDO $db, string $tableName): string
{
    $query = "SELECT name FROM sqlite_master WHERE type='table' AND name = :tableName";

    $pdostmt = $db->prepare($query);

    $pdostmt->bindParam(':tableName', $tableName, PDO::PARAM_STR);
    $pdostmt->execute();

    $result = $pdostmt->fetch(PDO::FETCH_ASSOC);

    return $result
        ? "Table '$tableName' exists in the database."
        : "Table '$tableName' does NOT exist in the database.";
}
