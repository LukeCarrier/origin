<?php

/**
 * Configuration library.
 *
 * Origin.
 *
 * @author    Luke Carrier <luke@carrier.im>
 * @copyright 2013 CloudFlux
 * @license   Proprietary; all rights reserved
 */

namespace Origin\Configuration;
use ArrayAccess,
    Origin\Util\ArrayUtil;

/**
 * Configuration interface.
 *
 * The configuration interface enables developers to interact with configuration files using an array-like interface.
 *
 * To set a value, just assign a value to an arbitrary key of the array:
 *
 *     $config['database.username'] = 'batman';
 *
 * With this value set, you can now retrieve the value in much the same way:
 *
 *     echo $config['database.username'];
 *
 * This is all well and good, but you're probably more interested in loading configuration values from files. In all
 * likelihood, those files are going to be in some nice INI, JSON or YAML format instead of an ugly multidimensional
 * array. We've got this covered, too, with {@link loadFromFile}:
 *
 *     $config = Config::loadFromFile('/some/file.ini');
 *     $other_config = Config::loadFromFile('/some/file.json');
 *
 * Finally, you're probably aware of the relative overhead of repeatedly parsing a large configuration file and loading
 * its values into memory on every request to your application. In order to combat this, the configuration library can
 * export your configuration information to an array which can easily be re-imported into a configuration instance. You
 * could use the cache library to assist with storing and retrieving this.
 *
 *     var_dump($config->exportData());
 */
class Configuration implements ArrayAccess {
    /**
     * The data contained within the configuration object.
     *
     * @var array
     */
    protected $data;

    /**
     * Add data to this instance.
     *
     * @param array   $data      The data to add.
     * @param boolean $overwrite Whether or not values in this array should take
     *                           precedence over existing items.
     *
     * @return void
     */
    public function addData($data, $overwrite=false) {
        $flat_data = ArrayUtil::flatten($data);

        if ($overwrite) {
            $this->data = array_merge($this->data, $flat_data);
        } else {
            $this->data = array_merge($flat_data, $this->data);
        }
    }

    /**
     * Export this instance's data.
     *
     * Return the raw array containing the configuration instance's data. It
     * would be advisable to cache the pre-compiled configuration array
     * somewhere where it can be sought on the initialisation of the framework,
     * so as to negate the need to parse configuration files on every request.
     *
     * @return array The data.
     */
    public function exportData() {
        return $this->data;
    }

    /**
     * Generate the PHP code.
     *
     * @return string The raw PHP string representative of the data array.
     */
    public function exportDataPhp() {
        return var_export($this->data, true);
    }

    /**
     * Populate a configuration object from a data array.
     *
     * @param array $data The data array to initialise the object from.
     *
     * @return \origin\configuration\Configuration A new configuration object
     *                                             pre-populated from the
     *                                             supplied value array.
     */
    public static function loadFromData($data) {
        $result = new static();
        $result->setData($data);
        return $result;
    }

    /**
     * Is the specified key set?
     *
     * @param mixed $offset The key to check the presence of.
     *
     * @return boolean True if the key=>value pair exists, false otherwise.
     *
     * @override
     */
    public function offsetExists($offset) {
        return array_key_exists($offset, $this->data);
    }   

    /**
     * Get a value by its key.
     *
     * @param mixed $offset The key to get the value for.
     *
     * @return mixed The value.
     *
     * @override
     */
    public function offsetGet($offset) {
        return $this->data[$offset];
    }

    /**
     * Add or update the value of a key=>value pair.
     *
     * @param mixed $offset The key.
     * @param mixed $value  The value.
     *
     * @return void
     *
     * @override
     */
    public function offsetSet($offset, $value) {
        return $this->data[$offset] = $value;
    }

    /**
     * Unset a key=>value pair.
     *
     * @param mixed $offset The key of the pair to unset.
     *
     * @return void
     *
     * @override
     */
    public function offsetUnset($offset) {
        unset($this->data[$offset]);
    }

    /**
     * Set this instance's data.
     *
     * @param array $data The data to set.
     *
     * @return void
     */
    public function setData($data) {
        $this->data = ArrayUtil::flatten($data);
    }
}
