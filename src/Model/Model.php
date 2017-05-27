<?php

namespace Shulha\Framework\Model;

use Pixie\QueryBuilder\QueryBuilderHandler;
use Shulha\Framework\Database\DBOContract;
use Shulha\Framework\DI\Service;

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
    public $table;

    /**
     * Last Inserted Id
     *
     * @var
     */
    public $id;

    /**
     * QueryBuilder instance
     *
     * @var QueryBuilderHandler
     */
    public $qb;

    /**
     * @var DBOContract
     */
    public $dbo;

    /**
     * @var Data container
     */
    protected $rowData;

    /**
     * Model constructor.
     * @param DBOContract $dbo
     */
    public function __construct(DBOContract $dbo)
    {
        $this->dbo = $dbo;
        $this->qb = $dbo->queryBuilder;
    }

    /**
     * Get all of the models from the database.
     *
     * @return array
     */
    public function all(): array
    {
//        return $this->qb->table($this->table)->setFetchMode(\PDO::FETCH_CLASS, get_class($this), [$this->dbo])->get();
        return $this->qb->table($this->table)->get();
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
        return $this->qb->table($this->table)->setFetchMode(\PDO::FETCH_CLASS, get_class($this), [$this->dbo])->find($id);
//        return $this->qb->table($this->table)->find($id);

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

        $this->id = $this->qb->pdo()->lastInsertId();
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

    /**
     * Create new entity
     */
    public function create(): self
    {
        return Service::get('injector')->make(get_class($this));
    }

    /**
     * @param $varname
     * @return null
     */
    public function __get($varname)
    {
        return isset($this->rowData[$varname]) ? $this->rowData[$varname] : null;
    }

    /**
     * @param $varname
     */
    public function __set($varname, $value)
    {
        $this->rowData[$varname] = $value;
    }

    public function save()
    {
        $this->insert(array_keys($this->rowData), $this->rowData);
    }
}