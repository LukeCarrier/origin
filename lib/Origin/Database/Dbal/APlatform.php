<?php

/**
 * DBAL generator interface.
 *
 * Origin.
 *
 * @author Luke Carrier <luke@carrier.im>
 * @copyright 2014 Luke Carrier
 * @license Proprietary; all rights reserved
 */

namespace Origin\Database\Dbal;

use PDO;

/**
 * Abstract generator library.
 *
 * Contains generic query fragments that are mostly cross-RDBMS. These may be
 * overridden in child classes to add further support for RDBMS-specific
 * functionality and performance and stability enhancements.
 */
class APlatform {
    /**
     * Dummy initialiser.
     *
     * Must be implemented for the IPlatform interface, and some platforms lack
     * initialisation code.
     */
    public function __construct() {
    }

    /**
     * @override
     */
    public function getColumnDefinitionListSql($columns) {
        $definition_list = '';

        foreach ($columns as $column) {
            $definition_list .= "\n    {$this->getColumnDefinitionSql($column)},";
        }

        return substr($definition_list, 0, -1);
    }

    /**
     * @override
     */
    public function getConstraintDefinitionListSql($constraints) {
        $definition_list = '';

        foreach ($constraints as $constraint) {
            $definition = $this->getConstraintDefinitionSql($constraint);
            if ($definition !== NULL) {
                $definition_list .= "\n    {$definition},";
            }
        }

        return substr($definition_list, 0, -1);
    }

    /**
     * @override
     */
    public function getCreateTableSql($table) {
        $name        = $this->prepareTableName($table->getName());
        $columns     = $this->getColumnDefinitionListSql($table->getColumns());
        $constraints = $this->getConstraintDefinitionListSql($table->getConstraints());

        if (strlen($constraints) > 0) {
            $constraints .= "\n";
        }

        return "CREATE TABLE {$name} ({$columns}\n)";
    }

    /**
     * @override
     */
    public function getDriverParamType($object) {
        switch (gettype($object)) {
            case 'boolean': return PDO::PARAM_BOOL;
            case 'double':
            case 'integer': return PDO::PARAM_INT;
            default:        return PDO::PARAM_STR;
        }
    }

    /**
     * @override
     */
    public function getInsertSql($table_name, $column_names, $driver=null,
                                 $values=null) {
        if ($driver !== null || $values !== null) {
            throw new \Exception('@todo pop a message in here before release');
        }

        $table_name = $this->prepareTableName($table_name);
        $column_list = implode(",\n    ", array_map(array($this, 'prepareColumnName'),
                                                    $column_names));
        $value_list = substr(str_repeat("\n    ?,", count($column_names)), 0, -1);
 
        return "INSERT INTO {$table_name} (\n    {$column_list}\n)\nVALUES ({$value_list}\n)";
    }

    /**
     * Do a type-sensitive quote.
     *
     * @param \origin\database\IDriver $driver
     * @param mixed                    $value
     *
     * @return string
     */
    public function typedQuote($driver, $value) {
        return $driver->quote($value, $this->getDriverParamType($value));
    }
}
