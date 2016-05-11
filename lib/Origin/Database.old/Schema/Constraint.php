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

/**
 * Schema constraint.
 *
 * This class represents a constraint in a database table.
 */
class Constraint {
    /**
     * Not null constraint.
     *
     * @var string
     */
    const TYPE_NOTNULL = 'not_null';

    /**
     * Primary key constraint.
     *
     * @var string
     */
    const TYPE_PRIMARYKEY = 'primary_key';

    /**
     * Not unique constraint.
     *
     * @var string
     */
    const TYPE_UNIQUE = 'unique';

    /**
     * Names of the affected columns.
     *
     * These should exist within the parent table object. Foreign key
     * constraints are not *yet* supported.
     *
     * @var string[]
     */
    protected $column_names;

    /**
     * Constraint type.
     *
     * One of the TYPE_* constants.
     *
     * @var string
     */
    protected $type;

    /**
     * Initialiser.
     *
     * @param string   $type         The constraint type; one of the TYPE_*
     *                               constants.
     * @param string[] $column_names The names of the columns within the parent
     *                               table the constraint concerns.
     */
    public function __construct($type, $column_names=array()) {
        $this->setType($type);
        $this->setColumnNames($column_names);
    }

    /**
     * Get the names of the affected columns.
     *
     * @return string[]
     */
    public function getColumnNames() {
        return $this->column_names;
    }

    /**
     * Get the type of the constraint.
     *
     * @return string One of the TYPE_* constraints.
     */
    public function getType() {
        return $this->type;
    }

    /**
     * Perform a likeness check on two constraints.
     *
     * @param \Origin\Database\Database\IPlatform $platform
     * @param \Origin\Database\Dbal\Schema\Constraint $other_constraint The constraint to test against.
     *
     * @return boolean Whether or not the two constraints are considered alike.
     */
    public function identicalTo($platform, $other_constraint) {
        $this_type  = $this->getType();
        $other_type = $other_constraint->getType();

        $this_column_names  = $this->getColumnNames();
        $other_column_names = $other_constraint->getColumnNames();

        if ($this_type !== $other_type
                || count($this_column_names) !== count($other_column_names)) {
            return false;
        }

        foreach ($this_column_names as $column_name) {
            if (!in_array($column_name, $other_column_names)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Set the names of the columns the constraint affects.
     *
     * @param string[] $column_names The names of the affected columns.
     *
     * @return void
     */
    public function setColumnNames($column_names) {
        $this->column_names = $column_names;
    }

    /**
     * Set the type of the constraint.
     *
     * @param string $type The type of the constraint; one of the TYPE_*
     *                     constants.
     *
     * @return void
     */
    public function setType($type) {
        $this->type = $type;
    }
}
