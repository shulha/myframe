<?php

namespace Shulha\Framework\Model;

use Pixie\QueryBuilder\QueryBuilderHandler;

/**
 * Class Model
 * @package Shulha\Framework\Model
 */
abstract class Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table;

    /**
     * QueryBuilder instance
     *
     * @var QueryBuilderHandler
     */
    protected $qb;

    /**
     * Model constructor.
     *
     * @param QueryBuilderHandler $qb
     */
    public function __construct(QueryBuilderHandler $qb)
    {
        $this->qb = $qb;
    }

    /**
     * Get all of the models from the database.
     *
     * @return array
     */
    public function all(): array
    {
        $query = $this->qb->table($this->table);

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
        return $this->qb->table($this->table)->find($id);

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

        $this->qb->table($this->table)->insert($data);
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

        $this->qb->table($this->table)->where('id', $id)->update($data);
    }

    /**
     * Delete the model from the database.
     *
     * @param $id
     */
    public function delete(int $id)
    {
        $this->qb->table($this->table)->where('id', $id)->delete();
    }


}