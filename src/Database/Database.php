<?php

namespace Shulha\Framework\Database;

use PDO;
use Shulha\Framework\DI\Service;

/**
 * Class Database
 * @package Shulha\Framework\Database
 */
class Database implements DBOContract
{
    /**
     * @var PDO
     */
    protected $connection;

    /**
     * The default fetch mode of the connection.
     *
     * @var int
     */
    protected $fetchMode = PDO::FETCH_OBJ;

    /**
     * Generic constructor.
     */
    public function __construct()
    {
        try {
            $this->connection = Service::get('injector')->make('PDO');
            $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (\PDOException $e) {
            throw $e;
        }
    }

    /**
     * Magic call
     *
     * @param $method
     * @param $args
     * @return mixed
     */
    public function __call($method, $args)
    {
        return call_user_func_array([$this->connection, $method], $args);
    }

    /**
     * Query method
     * @param $statement
     * @return \PDOStatement
     */
    public function query($statement)
    {
        return $this->connection->query($statement, $this->fetchMode);
    }
}