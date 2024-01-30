<?php

namespace App\Class;

class Database
{
    private static $connection;

    public function __construct()
    {
    }

    public static function getConnection()
    {
        if (self::$connection) {
            return self::$connection;
        }
        try {
            self::$connection = new \PDO('mysql:host=localhost;dbname=solid-blog;charset=utf8', 'root', '');
        } catch (\PDOException $e) {
            die('Erreur : ' . $e->getMessage());
        }
        return self::$connection;
    }
}
