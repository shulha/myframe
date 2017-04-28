<?php

namespace Shulha\Framework\Model;

/**
 * Class QueryBuilder
 * @package Shulha\Framework\Model
 */
abstract class QueryBuilder
{
    /**
     * @var Query mode. e.g. select, delete, update, etc
     */
    protected $mode = 'select';

    /**
     * @var array   Column names to select
     */
    protected $columns = [];

    /**
     * @var array   Where clause conditions
     */
    protected $conditions = [];

    /**
     * @var string  Table name
     */
    protected $table = '';

    /**
     * @var array
     */
    protected $ordering = [];

    /**
     * @var int Limit
     */
    protected $limit = 0;

    /**
     * @var int Offset
     */
    protected $offset = 0;

    /**
     * All of the available clause operators.
     *
     * @var array
     */
    public $operators = [
        '=', '<', '>', '<=', '>=', '<>', '!=',
        'like', 'like binary', 'not like', 'between', 'ilike',
        '&', '|', '^', '<<', '>>',
        'rlike', 'regexp', 'not regexp',
        '~', '~*', '!~', '!~*', 'similar to',
        'not similar to', 'not ilike', '~~*', '!~~*',
    ];

    /**
     * Select statement
     *
     * @param array $columns
     * @return $this
     */
    public function select($columns = []): self
    {
        $this->mode = 'select';
        $this->columns = $columns;

        return $this;
    }

    /**
     * Delete
     *
     * @return QueryBuilder
     */
    public function delete(): self
    {
        $this->mode = 'delete';

        return $this;
    }

    /**
     * From table
     *
     * @param $table
     * @return $this
     */
    public function from($table): self
    {
        $this->table = $table;

        return $this;
    }

    /**
     * Add Where condition
     *
     * @param array ...$args
     * @return QueryBuilder
     * @throws \Exception
     */
    public function where(...$args): self
    {
        $arg_count = func_num_args();
        if ($arg_count < 2) {
            throw new \Exception('Too few arguments to build condition');
        }

        $column = array_shift($args);

        if ($arg_count == 2) {
            $value = array_shift($args);
            $operator = '=';
        } elseif (!in_array($operator = array_shift($args), $this->operators)) {
            throw new \Exception("Wrong operator in \"where\" condition");
        } else {
            $value = array_shift($args);
        }

        $this->conditions[] = $column . $operator . $value;

        return $this;
    }

    /**
     * Set ordering
     *
     * @param $column
     * @param string $direction
     * @return QueryBuilder
     */
    public function orderBy($column, $direction = 'asc'): self
    {
        $this->ordering[$column] = $direction;

        return $this;
    }

    /**
     * Limit
     *
     * @return QueryBuilder
     */
    public function limit($limit, $offset = 0): self
    {
        $this->limit = $limit;
        $this->offset = $offset ?? $this->offset;

        return $this;
    }

    /**
     * Build query SQL statement
     */
    protected function build(): string
    {
        switch ($this->mode) {
            case 'delete':
                $sql = 'DELETE FROM ' . $this->table .
                    $this->buildWhere();
                break;

            case 'select':
            default:
                $sql = 'SELECT ' . $this->buildColumns() .
                    ' FROM ' . $this->table .
                    $this->buildWhere() .
                    $this->buildOrder() .
                    $this->buildLimit();
        }

        return $sql;
    }

    /**
     * Build where sql
     */
    protected function buildWhere(): string
    {
        $sql = '';
        if (!empty($this->conditions)) {
            $sql = ' WHERE ' . implode(' AND ', $this->conditions);
        }

        return $sql;
    }

    /**
     * Build columns to select
     */
    protected function buildColumns(): string
    {
        if (!empty($this->columns)) {
            $sql = implode(', ', $this->columns);
        } else {
            $sql = '*';
        }

        return $sql;
    }

    /**
     * Build order clause
     *
     * @return string
     */
    protected function buildOrder(): string
    {
        $sql = '';

        if (!empty($this->ordering)) {
            $sub = array_map(function ($key, $value) {
                return $key . ' ' . strtoupper($value);
            }, $this->ordering);
            $sql = ' ORDER BY ' . implode(', ', $sub);
        }

        return $sql;
    }

    /**
     * Build limit clause
     *
     * @return string
     */
    protected function buildLimit(): string
    {
        $sql = '';
        if ($this->limit) {
            $sql = ' LIMIT ' . ($this->offset ? (int)$this->offset . ',' : '') . (int)$this->limit;
        }

        return $sql;
    }
}