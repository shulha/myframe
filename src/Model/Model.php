<?php

namespace Shulha\Framework\Model;

use Shulha\Framework\Database\DBOContract;

/**
 * Class Model
 * @package Shulha\Framework\Model
 */
abstract class Model extends QueryBuilder
{
    /**
     * @var string
     */
    protected $table = '';

    /**
     * @var DBOContract
     */
    protected $dbo;

    /**
     * Model constructor.
     * @param DBOContract $dbo
     */
    public function __construct(DBOContract $dbo)
    {
        $this->dbo = $dbo;
    }

    /**
     * Get queried records
     *
     * @return array
     */
    public function all(): array
    {
        $sql = $this->select()
            ->from($this->table)
            ->build();

        $pdo_stat = $this->dbo->query($sql);

        // Warning! calls to fetch methods should be wrapped in Adapter!
        return $pdo_stat ? $pdo_stat->fetchAll() : [];
    }

    /**
     * Find single record by ID
     *
     * @param $id
     *
     * @return Object
     */
    public function find($id)
    {
        $sql = $this->select()
            ->from($this->table)
            ->where('id', (int)$id)
            ->limit(1)
            ->build();

        $pdo_stat = $this->dbo->query($sql);

        // Warning! calls to fetch methods should be wrapped in Adapter!
        return $pdo_stat ? $pdo_stat->fetchObject() : null;
    }
}