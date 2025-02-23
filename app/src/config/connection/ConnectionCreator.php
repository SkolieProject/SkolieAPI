<?php

namespace Minuz\SkolieAPI\config\connection;


class ConnectionCreator
{
    public static function connect(): \PDO
    {
        $dsn = sprintf(
            '%s:host=%s;port=%s;dbname=%s;charset=utf8',
            $_ENV['DB_CONNECTION'],
            $_ENV['DB_HOST'],
            $_ENV['DB_PORT'],
            $_ENV['DB_DATABASE'],
        );

        return new \PDO($dsn, $_ENV['DB_USER'], $_ENV['DB_PASS']);
    }
}
