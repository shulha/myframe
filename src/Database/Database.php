<?php

namespace Shulha\Framework\Database;

use Shulha\Framework\DI\Injector;
use Shulha\Framework\DI\Service;

/**
 * Class Database
 * @package Shulha\Framework\Database
 */
class Database implements DBOContract
{
    /**
     * @var
     */
    protected $connection;

    /**
     * DB config
     *
     * @var
     */
    protected $config;

    /**
     * @var QueryBuilderHandler
     */
    public $queryBuilder;

    /**
     * Generic constructor.
     */
    public function __construct()
    {
        try {
            $this->config = Injector::$config['db'];
            $this->connection = Service::get('injector')->make('Pixie\Connection',
                [':adapter' => $this->config['driver'], ':adapterConfig' => $this->config]);
            $this->queryBuilder = Service::get('injector')->make('Pixie\QueryBuilder\QueryBuilderHandler');
        } catch (\PDOException $e) {
            throw $e;
        }
    }

}