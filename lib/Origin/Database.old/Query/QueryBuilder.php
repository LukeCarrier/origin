<?php

/**
 * Database query generator.
 *
 * Origin.
 *
 * @author Luke Carrier <luke@cfx.me>
 * @copyright 2014 Luke Carrier
 * @license Proprietary; all rights reserved
 */

namespace Origin\Database\Dbal\Query;

/**
 * Select query generator.
 */
class QueryBuilder {
    /**
     * Query type: insert.
     *
     * @var string
     */
    const TYPE_INSERT = 'insert';

    /**
     * Query type: select.
     *
     * @var string
     */
    const TYPE_SELECT = 'select';

    /**
     * Names of the columns to be selected.
     *
     * @var string[]
     */
    protected $column_names = [];

    /**
     * Primary table name.
     *
     * @var string
     */
    protected $table;

    /**
     * The type of the query being generated. One of the TYPE_* constants.
     *
     * @var string
     */
    protected $type;

    /**
     * Set the table to insert values into.
     *
     * @param string $table_name The name of the table.
     *
     * @return void
     */
    public function insertInto($table_name) {
        $this->type  = static::TYPE_INSERT;
        $this->table = $table_name;
    }

    /**
     * Add a column (or array of columns) to the list to select.
     *
     * @param string|string[] $column_names The name of the colums.
     *
     * @return void
     */
    public function select($column_names) {
        $this->type = static::TYPE_SELECT;

        if (!is_array($column_names)) {
            $column_names = array($column_names);
        }

        $this->column_names += $column_names;
    }

    /**
     * Generate the SQL for the query.
     *
     * @return string
     */
    public function toSql() {}

    /**
     * @override
     */
    public function __toString() {
        return $this->toSql();
    }
}
