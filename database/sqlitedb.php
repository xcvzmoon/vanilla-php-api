<?php

declare(strict_types=1);

class SQLiteDB
{
    private static ?PDO $instance = null;

    private function __construct() {}

    private function __clone() {}

    public function __wakeup() {}

    /**
     * Get the singleton PDO instance connected to SQLite database
     * @param string $path Path to the SQLite database file
     * @return PDO
     * @throws PDOException on connection failure
     */
    public static function getInstance(string $path = 'database/database.sqlite'): PDO
    {
        if (self::$instance === null) {
            $dsn = str_starts_with($path, 'file:') ? "sqlite:$path" : "sqlite:$path";

            try {
                self::$instance = new PDO($dsn);
                self::$instance->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                self::$instance->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            } catch (PDOException $e) {
                throw new PDOException("Failed to connect to SQLite database: {$e->getMessage()}");
            }
        }

        return self::$instance;
    }
}
