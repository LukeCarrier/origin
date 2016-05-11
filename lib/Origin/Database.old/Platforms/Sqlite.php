<?php

/**
 * DBAL platform interface.
 *
 * Origin.
 *
 * @author Luke Carrier <luke@carrier.im>
 * @copyright 2014 Luke Carrier
 * @license Proprietary; all rights reserved
 */

namespace Origin\Database\Dbal\Platforms;

use Origin\Database\Dbal\APlatform,
    Origin\Database\Dbal\IPlatform,
    Origin\Database\Dbal\Drivers\PdoSqlite as Driver,
    Origin\Database\Dbal\Schema\Column,
    Origin\Database\Dbal\Schema\Constraint,
    origin\Util\ArrayUtil;

/**
 * SQLite query platform.
 */
class Sqlite extends APlatform implements IPlatform {
    /**
     * Regular expression pre fragment to match columns with a given option.
     *
     * @var string
     */
    const REGEX_COLUMNOPTION_PRE = '/[^,\(]*\s+';

    /**
     * Regular expression post fragment to match columns with a given option.
     *
     * @var string
     */
    const REGEX_COLUMNOPTION_POST = '\s+[^,\)]*/';

    /**
     * Regular expression to locate a column name in a single column definition.
     *
     * @var string
     */
    const REGEX_COLUMNNAME = '/\"?([a-zA-Z]*)\"?/';

    /**
     * Regular expression to match an SQLite type and length.
     *
     * @var string
     */
    const REGEX_TYPE = '/^([A-Z]+)\(([0-9]+)\)$/';

    /**
     * Reverse type map.
     *
     * This array is used when reverse engineering tables into schema objects.
     *
     * @see __construct()
     */
    protected $reverse_type_map = [];

    /**
     * Map of column types to SQLite data types.
     *
     * Because of the extensive use of constants, this variable is not static.
     * Despite this, its value should not be altered at runtime.
     *
     * @var array<int, string>
     * @see __construct()
     */
    protected $type_map;

    /**
     * Initialiser.
     *
     * Initialises the type map.
     *
     * @override
     */
    public function __construct() {
        /**
         * Mapping of DBAL types to native types and their default options.
         *
         * @var array<string, <string, mixed>>
         */
        $this->type_map = [
            Column::TYPE_BINARY    => ['BLOB',    []],
            Column::TYPE_BOOLEAN   => ['INTEGER', []],
            Column::TYPE_FLOAT     => ['REAL',    []],
            Column::TYPE_INTEGER   => ['INTEGER', []],
            Column::TYPE_STRING    => ['TEXT',    []],
            Column::TYPE_TIMESTAMP => ['INTEGER', [
                'length' => 10,
            ]],
        ];

        foreach ($this->type_map as $dbal_type => $platform_type) {
            if (!array_key_exists($platform_type[0], $this->reverse_type_map)) {
                $this->reverse_type_map[$platform_type[0]] = array();
            }

            $this->reverse_type_map[$platform_type[0]][] = $dbal_type;
        }

        parent::__construct();
    }

    /**
     * @override
     */
    public function getTableDeltaSql($table_delta) {
        // @todo write me and remove all the debug
        xdebug_print_function_stack('\origin\database\platforms\Sqlite::getTableDeltaSql');
        var_dump($this->reverse_type_map);
        var_dump($table_delta);
        die("functionality not yet implemented!\n");
    }

    /**
     * @override
     *
     * Also handles constraint definitions which can be inlined into column
     * definitions. All remaining constraints will be identified and generated
     * for inclusion in the column definition list, separate from their
     * corresponding columns.
     */
    public function getColumnDefinitionSql($column) {
        $name        = $this->prepareColumnName($column->getName());
        $type        = $column->getType();
        $options     = $column->getOptions();
        $constraints = $column->getConstraints();

        list($native_type, $default_options) = $this->type_map[$type];

        if (array_key_exists('length', $options)
                && (!array_key_exists('auto_increment', $options)
                        || !$options['auto_increment'])) {
            $native_type .= "({$options['length']})";
        }

        foreach ($constraints as $constraint) {
            switch ($constraint->getType()) {
                case Constraint::TYPE_PRIMARYKEY:
                    $native_type .= ' PRIMARY KEY';

                    if (array_key_exists('auto_increment', $options)
                            && $options['auto_increment']) {
                        $native_type .= ' AUTOINCREMENT';
                    }

                    break;

                case Constraint::TYPE_NOTNULL:
                    $native_type .= ' NOT NULL';
                    break;

                case Constraint::TYPE_UNIQUE:
                    $native_type .= ' UNIQUE';
                    break;
            }
        }

        return "{$name} {$native_type}";
    }

    /**
     * @override
     */
    public function getColumnNativeType($column) {
        return $this->type_map[$column->getType()][0];
    }

    /**
     * @override
     *
     * Note that, as per SQLite's inlining of certain constraint types, we
     * skip over many constraints here.
     */
    public function getConstraintDefinitionSql($constraint) {
        switch ($constraint->getType()) {
            default:
                return;
        }

        return "{$native_type}({$column_names})";
    }

    /**
     * Get a regular expression for a given column option.
     *
     * @var string $option The column option.
     * @return string The generated regular expression pattern.
     */
    private function getColumnOptionRegex($option) {
        return static::REGEX_COLUMNOPTION_PRE . $option
             . static::REGEX_COLUMNOPTION_POST;
    }

    /**
     * @override
     */
    public function getDriverColumnType($column_type) {
        // @todo do we need to double check the caller isn't passing us dumb data?
        if (is_array($column_type)) {
            $column_type = current($column_type);
        }

        return $this->type_map[$column_type][0];
    }

    /**
     * @override
     *
     * Necessary to implement DDL hack to circumvent prepared statements.
     */
    public function getInsertSql($table_name, $table_columns, $driver=null,
                                 $values=null) {
        if ($driver === null || $values === null) {
            return parent::getInsertSql($table_name, $table_columns, $values);
        }

        if (count($table_columns) !== count($values)) {
            throw new \Exception('pls recount');
        }

        $table_name = $this->prepareTableName($table_name);
        $column_list = implode(",\n    ", array_map(array($this, 'prepareColumnName'),
                                                    $table_columns));
        $quoted_values = ArrayUtil::mapWithArgs([$this, 'typedQuote'],
                                                [$driver], $values);
        $value_list = "\n    " . implode(",\n    ", $quoted_values);

        return "INSERT INTO {$table_name} (\n    {$column_list}\n)\nVALUES ({$value_list}\n)";
    }

    /**
     * @override
     */
    public function getTableColumns($driver, $table_name) {
        $pragma_statement = $driver->query('PRAGMA table_info('
                          .     $this->prepareTableName($table_name)
                          . ')');

        $ai_sql = 'SELECT ' . $this->prepareColumnName('sql') . "\n"
                . 'FROM ' . $this->prepareTableName('sqlite_master') . "\n"
                . 'WHERE ' . $this->prepareColumnName('type') . ' = :type' . "\n"
                .     'AND ' . $this->prepareColumnName('name') . ' = :table';
        $ai_statement = $driver->prepare($ai_sql);
        $ai_statement->execute([
            ':type'  => 'table',
            ':table' => $table_name,
        ]);
        $create_table_sql = $ai_statement->fetchColumn();
        $ai_statement->closeCursor();

        $ai_column = NULL;
        $ai_result = preg_match($this->getColumnOptionRegex('AUTOINCREMENT'),
                                $create_table_sql, $ai_matches);
        if ($ai_result) {
            $create_fragment = trim($ai_matches[0]);
            $ai_result = preg_match(static::REGEX_COLUMNNAME, $create_fragment,
                                    $ai_matches);
            $ai_column = $ai_matches[1];
        }

        $table_columns = [];
        while ($column = $pragma_statement->fetchObject()) {
            $options = [];

            if (preg_match(static::REGEX_TYPE, $column->type, $matches)) {
                $type = $matches[1];
                $options['length'] = $matches[2];
            } else {
                $type = $column->type;
            }
            $dbal_type = $this->reverse_type_map[$type];

            if ($column->name === $ai_column) {
                $options['auto_increment'] = true;
            }

            $constraints = array();
            if ($column->notnull) {
                $constraints[] = new Constraint(Constraint::TYPE_NOTNULL);
            }
            if ($column->pk) {
                $constraints[] = new Constraint(Constraint::TYPE_PRIMARYKEY);
            }

            $table_columns[$column->name] = new Column($column->name,
                                                       $dbal_type, $options,
                                                       $constraints);
        }

        return $table_columns;
    }

    /**
     * @override
     */
    public function getTableConstraintsSql($table_name) {
        return '@todo';
    }

    /**
     * @override
     */
    public function getTableExistsSql() {
        return <<<SQL
SELECT COUNT(*) AS count
FROM sqlite_master
WHERE type = 'table'
AND name = ?
SQL;
    }

    /**
     * @override
     */
    public function normaliseColumnAttributes($column) {
        if ($column->hasOption('length')) {
            $column->unsetOption('length');
        }

        return $column;
    }

    /**
     * @override
     */
    public function prepareColumnName($column_name) {
        return "\"{$column_name}\"";
    }

    /**
     * @override
     */
    public function prepareTableName($table_name) {
        return "[{$table_name}]";
    }
}
