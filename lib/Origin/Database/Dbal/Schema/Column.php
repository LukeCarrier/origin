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
 * Schema column.
 *
 * This class represents a column in a database table.
 */
class Column {
    /**
     * Binary type.
     *
     * @var string
     */
    const TYPE_BINARY = 'binary';

    /**
     * Boolean type.
     *
     * @var string
     */
    const TYPE_BOOLEAN = 'boolean';

    /**
     * Float type.
     *
     * @var string
     */
    const TYPE_FLOAT = 'float';

    /**
     * Integer type.
     *
     * @var string
     */
    const TYPE_INTEGER = 'integer';

    /**
     * String type.
     *
     * @var string
     */
    const TYPE_STRING = 'string';

    /**
     * Timestamp type.
     *
     * @var string
     */
    const TYPE_TIMESTAMP = 'timestamp';

    /**
     * Type array.
     *
     * @var array <String>
     * @see initialise()
     */
    protected static $types;

    /**
     * Column constraints.
     *
     * @var \Origin\Database\Dbal\Schema\Constraint[]
     */
    protected $constraints;

    /**
     * Column name.
     *
     * @var string
     */
    protected $name;

    /**
     * Column options.
     *
     * @var array
     */
    protected $options;

    /**
     * Column type.
     *
     * @var string
     */
    protected $type;

    /**
     * Initialiser.
     *
     * Note the complexity: when retrofitting a schema in reverse, we're unable
     * to ascertain the DBAL type of the column -- the mapping is not one to
     * one. In this instance, we accept an array of possible type values and
     * account for these differences when performing similarity tests.
     *
     * @param string $name The name of the column.
     * @param string $type The column type; it's advisable to use the TYPE_
     *        constants.
     * @param array $options An array of options associated with the column
     *        type.
     */
    public function __construct($name, $type, $options=array(),
                                $constraints=array()) {
        static::initialise();

        $this->setName($name);
        $this->setType($type);
        $this->setOptions($options);
        $this->setConstraints($constraints);
    }

    /**
     * Static initialiser.
     *
     * Initialises the column types array.
     *
     * @return void
     */
    protected static function initialise() {
        if (static::$types === NULL) {
            static::$types = [
                static::TYPE_BINARY,
                static::TYPE_BOOLEAN,
                static::TYPE_INTEGER,
                static::TYPE_STRING,
                static::TYPE_TIMESTAMP,
            ];
        }
    }

    /**
     * Get the column's constraints.
     *
     * @return \Origin\Database\Dbal\Schema\Constraint[] The column's
     *                                                   constraints.
     */
    public function getConstraints() {
        return $this->constraints;
    }

    /**
     * Get the column's name.
     *
     * @return string The column's name.
     *
     * @return void
     */
    public function getName() {
        return $this->name;
    }

    /**
     * Get the value of a specific option.
     *
     * @param string $name The name of the option.
     *
     * @return mixed The value of the option.
     */
    public function getOption($name) {
        return $this->options[$name];
    }

    /**
     * Get the column's options.
     *
     * @return string[] The column's options.
     */
    public function getOptions() {
        return $this->options;
    }

    /**
     * Get the column's type.
     *
     * @return string The column's type.
     */
    public function getType() {
        return $this->type;
    }

    public function hasOption($name) {
        return array_key_exists($name, $this->options);
    }

    /**
     * Check for differences between this and another column object.
     *
     * @param \Origin\Database\Dbal\IPlatform     $platform     The driver
     *                                                          object to use
     *                                                          for native type
     *                                                          conversion.
     * @param \Origin\Database\Dbal\Schema\Column $other_column The other
     *                                                          column.
     *
     * @return boolean True if the two columns are identical, else false.
     */
    public function identicalTo($platform, $other_column) {
        $this_type  = $platform->getDriverColumnType($this->getType());
        $other_type = $platform->getDriverColumnType($other_column->getType());

        $this_options  = $this->getOptions();
        $other_options = $other_column->getOptions();

        $this_constraints  = $this->getConstraints();
        $other_constraints = $other_column->getConstraints();

        if ($this->getName() !== $other_column->getName()
                || $this_type !== $other_type) {
            return false;
        }

        if (count($this_options) !== count($other_options)
                || count($this_constraints) !== count($other_constraints)) {
            return false;
        }

        foreach ($this_options as $name => $value) {
            if ($other_options[$name] !== $value) {
                return false;
            }
        }

        foreach ($this_constraints as $this_constraint) {
            foreach ($other_constraints as $other_constraint) {
                if ($this_constraint->identicalTo($platform, $other_constraint)) {
                    continue 2;
                }
            }

            return false;
        }

        return true;
    }

    /**
     * Set the column's constraints.
     *
     * @param \Origin\Database\Dbal\Schema\Constraint[] $constraints The column's
     *                                                               constraints.
     *
     * @return void
     */
    public function setConstraints($constraints) {
        foreach ($constraints as $constraint) {
            $constraint->setColumnNames(array($this->getName()));
        }

        $this->constraints = $constraints;
    }

    /**
     * Set the column's name.
     *
     * @param string $name The column's name.
     *
     * @return void
     */
    public function setName($name) {
        $this->name = $name;
    }

    /**
     * Set the value of an option.
     *
     * @param string $name  The name of the option to set.
     * @param mixed  $value The option's new value.
     *
     * @return void
     */
    public function setOption($name, $value) {
        $this->options[$name] = $value;
    }

    /**
     * Set the column's options.
     *
     * @param array $options The column's options.
     *
     * @return void
     */
    public function setOptions($options) {
        $this->options = $options;
    }

    /**
     * Set the column's type.
     *
     * @param integer|integer[] $type The column's type.
     *
     * @return void
     */
    public function setType($type) {
        if (!is_array($type)) {
            $type = array($type);
        }

        foreach ($type as $single_type) {
            if (!in_array($single_type, static::$types)) {
                throw new \Exception('invalid type');
            }
        }

        if (count($type) === 1) {
            $this->type = $type[0];
        } else {
            $this->type = $type;
        }
    }

    public function unsetOption($name) {
        unset($this->options[$name]);
    }
}
