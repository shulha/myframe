<?php

namespace Shulha\Framework\Model;

use Shulha\Framework\Database\DBOContract;

/**
 * Class Model
 * @package Shulha\Framework\Model
 */
abstract class Model
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
     * QueryBuilder instance
     *
     * @var QueryBuilderHandler
     */
    protected $queryBuilder;

    /**
     * Model constructor.
     * @param DBOContract $dbo
     */
    public function __construct(DBOContract $dbo)
    {
        $this->dbo = $dbo;
        $this->queryBuilder = $dbo->queryBuilder;
    }

    /**
     * Get all of the models from the database.
     *
     * @return array
     */
    public function all(): array
    {
        $query = $this->queryBuilder->table($this->table);
        return $query->get();
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
        return $this->queryBuilder->table($this->table)->find($id);
    }

    /**
     * Insert the model in the database.
     *
     * @param array $columns
     * @param array $values
     */
    public function insert(array $columns = [], array $values = [])
    {
        $data = array_combine($columns, $values);
        $this->queryBuilder->table($this->table)->insert($data);
    }

    /**
     * Update the model in the database.
     *
     * @param int $id
     * @param array $columns
     * @param array $values
     */
    public function update(int $id, array $columns = [], array $values = [])
    {
        $data = array_combine($columns, $values);
        $this->queryBuilder->table($this->table)->where('id', $id)->update($data);
    }

    /**
     * Delete the model from the database.
     *
     * @param $id
     */
    public function delete(int $id)
    {
        $this->queryBuilder->table($this->table)->where('id', $id)->delete();
    }
}