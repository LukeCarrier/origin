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

/**
 * DBAL platform interface.
 *
 * Platform libraries contain the bulk of the platform-specific code. They are:
 * - query generators responsible for the construction of RDBMS-specific
 *   components of SQL queries.
 * - abstraction layers for retrieving database schema representations.
 *
 * @todo Continue documentation; API is still unstable.
 */
interface IPlatform {
    /**
     * Initialiser.
     */
    public function __construct();

    /**
     * Get column definition SQL.
     *
     * Given a column schema object, generate the appropriate SQL to create the
     * row.
     *
     * @param Column $column The column object.
     *
     * @return string The resulting SQL fragment.
     */
    public function getColumnDefinitionSql($column);

    /**
     * Get column definition list SQL.
     *
     * Given an array of column schema objects, generate the appropriate SQL to
     * create the rows and format it as a list.
     *
     * @param array<Column> An array of column objects.
     *
     * @return string The resulting SQL fragment.
     */
    public function getColumnDefinitionListSql($columns);

    /**
     * @todo documentation
     */
    public function getColumnNativeType($column);

    /**
     * Get create table SQL.
     *
     * Given a table schema object, generate the appropriate SQL to create it.
     *
     * @param Table $table The table to generate the create statmement for.
     *
     * @return string The resulting SQL query.
     */
    public function getCreateTableSql($table);

    /**
     * Get the driver's internal type for a given column.
     *
     * @param mixed $column_type One of the Column::TYPE_* constants.
     *
     * @return string The driver's internal type for the column typex.
     */
    public function getDriverColumnType($column_type);

    /**
     * Get the driver's parameter type for a given PHP object.
     *
     * @param mixed $object The object to get the type of.
     *
     * @return integer The value of the type's corresponding PDO::PARAM_*
     *                 constant.
     */
    public function getDriverParamType($object);

    /**
     * Get SQL for an insert query.
     *
     * Note: the last two parameters to this function are valid ONLY on database
     * drivers which fail to correctly reload schema state after DDL operations.
     * At the moment, the only known case is that of SQLite. Other RDBMS driver
     * implementations should throw exceptions if these parameters are passed,
     * as they indicate sloppy practice -- developers should be using
     * parameterised queries using the {@link prepare()} method.
     *
     * @param string                   $table_name   The name of the table to
     *                                               insert the record into.
     * @param string[]                 $column_names An array containing the
     *                                               names of the fields we're
     *                                               populating.
     * @param \origin\database\IDriver $driver       For broken databases only;
     *                                               the driver object to
     *                                               prepare (sanitise) values
     *                                               with.
     * @param (integer|string)[]       $values       For broken databases only;
     *                                               an array of values to
     *                                               insert. The order of the
     *                                               values *must* match that of
     *                                               the target columns.
     *
     * @return string
     */
    public function getInsertSql($table_name, $column_names, $driver=null,
                                 $values=null);

    /**
     * Get table columns SQL.
     *
     * Given a table name, retrieve the SQL to ascertain the columns within.
     *
     * @param string                   $table_name The name of the table.
     * @param \origin\database\IDriver $driver     The driver instance to
     *                                             perform the queries on.
     * @return string The generated SQL query.
     */
    public function getTableColumns($driver, $table_name);

    /**
     * Get table constraints SQL.
     *
     * Given a table name, retrieve the SQL to ascertain the constraints within.
     *
     * @param string $table_name The name of the table.
     * @return string The generated SQL query.
     */
    public function getTableConstraintsSql($table_name);

    /**
     * Get the corresponding SQL statements to apply a table delta.
     *
     * @param \origin\database\schema\TableDelta $table_delta The table delta
     *        schema object.
     */
    public function getTableDeltaSql($table_delta);

    /**
     * Get table exists SQL.
     *
     * Generate a query to check for the presence of a database table. You will
     * need to prepare a statement from this query, then bind the table name
     * value on the statement object; it is not handled here.
     */
    public function getTableExistsSql();

    /**
     * Normalise attributes of a column for a given platform.
     *
     * This is necessary for database systems like SQLite that don't implement
     * the SQL specification in its entirety. It's used to strip out unnecessary
     * option values and translate types ready for table deltas.
     *
     * @param \origin\database\schema\Column $column The column object. Note
     *                                               that it will be modified,
     *                                               so pass a cloned instance
     *                                               if you want to keep the old
     *                                               one.
     *
     * @return \origin\database\schema\Column
     * @todo rewrite docs when less drunk
     */
    public function normaliseColumnAttributes($column);

    /**
     * Prepare a column name for use in a query.
     *
     * Validates, escapes and quotes the column name, returning the name on
     * success or raising an exception on failure.
     *
     * @param string $column_name The name of the column.
     * @throws Exception
     */
    public function prepareColumnName($column_name);

    /**
     * Prepare a table name for use in a query.
     *
     * Validates, escapes and quotes the table name, returning the name on
     * success or raising an exception on failure.
     *
     * @param string $table_name The name of the table.
     * @throws Exception
     */
    public function prepareTableName($table_name);
}
