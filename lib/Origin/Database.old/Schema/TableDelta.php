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
 * DBAL schema table delta.
 *
 * This class represents a delta diff between two versions of a database table.
 */
class TableDelta {
    /**
     * Columns marked for addition.
     *
     * @var array<string, Column>
     */
    protected $add_columns = array();

    /**
     * Columns marked for alteration.
     *
     * @var array<string, ColumnDelta>
     */
    protected $alter_columns = array();

    /**
     * Columns marked for deletion.
     *
     * @var array<string, Column>
     */
    protected $drop_columns = array();

    /**
     * Initialiser.
     *
     * @param \origin\database\IPlatform    $platform
     * @param \origin\database\schema\Table $current_table
     * @param \origin\database\schema\Table $new_table
     */
    public function __construct($platform, $current_table, $new_table) {
        $new_columns     = $new_table->getColumns();
        $current_columns = $current_table->getColumns();

        while ($new_column = array_shift($new_columns)) {
            $platform->normaliseColumnAttributes($new_column);
            $column_name = $new_column->getName();

            if (array_key_exists($column_name, $current_columns)) {
                $current_column = $current_columns[$column_name];
                $platform->normaliseColumnAttributes($current_column);

                if (!$current_column->identicalTo($platform, $new_column)) {
                    $this->alter_columns[] = new ColumnDelta($platform,
                                                             $current_column,
                                                             $new_column);
                }
            } else {
                $this->add_columns[] = $new_column;
            }

            unset($current_columns[$column_name]);
        }

        foreach ($current_columns as $current_column) {
            $column_name = $current_column->getName();
            if (!array_key_exists($column_name, $new_columns)) {
                $this->drop_columns[] = $current_column;
            }
        }
    }

    /**
     * Does the delta contain any changes?
     *
     * If it doesn't, we can skip the delta application for extra performance.
     *
     * @return boolean True if changes are contained within the delta, else
     *                 false.
     */
    public function hasChanges() {
        $changes = count($this->add_columns) + count($this->alter_columns)
                 + count($this->drop_columns);

        return $changes > 0;
    }
}
