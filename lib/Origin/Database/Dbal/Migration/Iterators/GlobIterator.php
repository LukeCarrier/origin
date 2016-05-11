<?php

/**
 * Database migration manager.
 *
 * Origin.
 *
 * @author Luke Carrier <luke@cfx.me>
 * @copyright 2014 Luke Carrier
 * @license Proprietary; all rights reserved
 */

namespace Origin\Database\Dbal\Migration\Iterators;

use GlobIterator as NativeGlobIterator,
    Origin\Database\Dbal\Migration\IMigrationIterator;

/**
 * Glob iterator.
 *
 * An extension of PHP's own glob iterator specially tailored for seeking
 * database migrations.
 */
class GlobIterator extends NativeGlobIterator implements IMigrationIterator {
    /**
     * Whether or not to manually include containing files for classes.
     *
     * @var boolean
     */
    protected $manual_include;

    /**
     * Migration class.
     *
     * @var string
     */
    protected $migration_class;

    /**
     * Migration time.
     *
     * @var string
     */
    protected $migration_creation_time;

    /**
     * Migration name.
     *
     * @var string
     */
    protected $migration_name;

    /**
     * Migration namespace.
     *
     * @var string
     */
    protected $migration_namespace = '';

    /**
     * @override
     */
    public function __construct($path, $flags=NULL) {
        parent::__construct($path, $flags);

        $this->setInfoClass(__CLASS__);

        list($this->migration_creation_time, $this->migration_name)
                = explode('_', $this->getBasename('.php'), 2);
        $this->migration_class = "{$this->migration_name}_{$this->migration_creation_time}";
    }

    /**
     * @override
     */
    public function current() {
        $file_info = parent::current();
        $file_info->setMigrationNamespace($this->migration_namespace);

        return $file_info;
    }

    /**
     * Get manual include.
     *
     * @return boolean Whether or not to manually include containing files for
     *                 classes.
     */
    public function getManualInclude() {
        return $this->manual_include;
    }

    /**
     * @override
     */
    public function getMigrationClass() {
        return $this->migration_class;
    }

    /**
     * @override
     */
    public function getMigrationCreationTime() {
        return $this->migration_creation_time;
    }

    /**
     * @override
     */
    public function getMigrationName() {
        return $this->migration_name;
    }

    /**
     * @override
     */
    public function getMigrationPathname() {
        return ($this->manual_include) ? $this->getPathname() : NULL;
    }

    /**
     * Set manual include.
     *
     * @param boolean $manual_include Whether or not to manually include
     *                                containing files for classes.
     */
    public function setManualInclude($manual_include) {
        $this->manual_include = $manual_include;
    }

    /**
     * Set migration namespace.
     *
     * The migration namespace is the prefix which should be prepended to all
     * migration class names. It's recommended that this is set, so as to
     * facilitate class autoloading and keep your root namespace tidy.
     *
     * @param string $namespace The migration namespace.
     *
     * @return void
     */
    public function setMigrationNamespace($namespace) {
        $this->migration_namespace = $namespace;
        $this->migration_class     = $this->migration_namespace . '\\'
                                   . $this->migration_class;
    }
}
