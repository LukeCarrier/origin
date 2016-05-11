<?php

/**
 * DBAL error.
 *
 * Origin.
 *
 * @author Luke Carrier <luke@carrier.im>
 * @copyright 2014 Luke Carrier
 * @license Proprietary; all rights reserved
 */

namespace Origin\Database\Dbal\Errors;
use Exception;

/**
 * No such column exists.
 */
class NoSuchColumn extends Exception {
	/**
	 * Column name.
	 *
	 * @var string
	 */
	protected $column_name;

	/**
	 * Initialiser.
	 *
	 * @param string $column_name The name of the nonexistent column.
	 */
	public function __construct($column_name) {
		$this->column_name = $column_name;

		parent::__construct("Column '{$column_name}' does not exist");
	}

	/**
	 * Get the column name.
	 *
	 * @return string The name of the nonexistent column.
	 */
	public function getColumnName() {
		return $this->column_name;
	}
}
