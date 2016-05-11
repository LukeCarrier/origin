<?php

/**
 * DBAL schema library.
 *
 * Origin.
 *
 * @author Luke Carrier <luke@carrier.im>
 * @copyright 2014 Luke Carrier
 * @license Proprietary; all rights reserved
 */

namespace Origin\Database\Dbal\Schema;
use Origin\Database\Dbal\Errors\NoSuchColumn;

/**
 * DBAL schema table.
 *
 * This class represents a database table.
 */
class Table {
    /**
     * Table columns.
     *
     * @var \origin\database\schema\Column[]
     */
    protected $columns;

    /**
     * The name of the table.
     *
     * @var string
     */
    protected $name;

    /**
     * The name of the column representing the table's primary key.
     *
     * @var string
     */
    protected $primary_key;

    /**
     * Initialiser.
     *
     * @param string $name The name of the table.
     * @param \origin\database\schema\Column[] $columns The columns to define
     *        the table with.
     */
    public function __construct($name, $columns=array()) {
        $this->setName($name);

        foreach ($columns as $column) {
            $this->addColumn($column);
        }
    }

    /**
     * Add a column to the table.
     *
     * @param \origin\database\schema\Column $column The column to add.
     */
    public function addColumn($column) {
        $this->columns[$column->getName()] = $column;
    }

    /**
     * Get a column object by its name.
     *
     * @param string $column_name The name of the column to retrieve.
     * @return \origin\database\schema\Column The column object.
     * @throws \origin\database\errors\NoSuchColumn Thrown when no column with
     *         the given name can be found.
     */
    public function getColumn($column_name) {
        if (!$this->hasColumn($column_name)) {
            throw new NoSuchColumn($column_name);
        }

        return $this->columns[$column_name];
    }

    /**
     * Get all of the table's columns.
     *
     * @return \origin\database\schema\Column[] All of the table's columns.
     */
    public function getColumns() {
        return $this->columns;
    }

    /**
     * Get all of the table's constraints.
     *
     * @return \origin\database\schema\Constraint[] All of the table's
     *         constraints.
     */
    public function getConstraints() {
        $constraints = array();

        foreach ($this->getColumns() as $column) {
            $constraints = array_merge($constraints, $column->getConstraints());
        }

        return $constraints;
    }

    /**
     * Get the table's name.
     *
     * @return string The name of the table.
     */
    public function getName() {
        return $this->name;
    }

    /**
     * Get the name of the table's primary key.
     *
     * @return string The name of the table's primary key.
     */
    public function getPrimaryKeyName() {
        return $this->primary_key;
    }

    /**
     * Get the column object for the table's primary key.
     *
     * @return \origin\database\schema\Column The primary key's column object. 
     */
    public function getPrimaryKeyColumn() {
        return $this->getColumn($this->getPrimaryKeyName());
    }

    /**
     * Check whether the table has a column with the given name.
     *
     * @param string $column_name The name of the column to look up.
     * @return boolean True if the column exists, false otherwise.
     */
    public function hasColumn($column_name) {
        return array_key_exists($column_name, $this->columns);
    }

    /**
     * Remove a column by its name.
     *
     * @param string $column_name The name of the column to remove.
     */
    public function removeColumn($column_name) {
        unset($this->columns[$column_name]);
    }

    /**
     * Set the name of the table name.
     *
     * @param string $name The new name of the table.
     */
    public function setName($name) {
        $this->name = $name;
    }

    /**
     * Set the primary key column by its name.
     *
     * @param string $column_name The name of the column to set as the primary
     *        key.
     */
    public function setPrimaryKey($column_name) {
        $this->primary_key = $column_name;
    }

    /**
     * Set a primary key column.
     *
     * A shorthand means of adding a new column to the table and setting it as
     * the table's primary key.
     *
     * @param \origin\database\schema\Column $column The new primary key column.
     */
    public function setPrimaryKeyColumn($column) {
        $this->addColumn($column);
        $this->setPrimaryKey($column->getName());
    }
}
