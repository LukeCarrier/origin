<?php

/**
 * Array utility library.
 *
 * Origin.
 *
 * @author Luke Carrier <luke@carrier.im>
 * @copyright 2014 Luke Carrier
 * @license Proprietary; all rights reserved
 */

namespace Origin\Serialisation;

/**
 * Serialiser manager.
 *
 * The serialiser managers provides a consistent, cross-format API for
 * serialising and unserialising PHP objects.
 */
class Manager {
    /**
     * Registered serialisers.
     *
     * @var \Origin\Serialisation\ISerialiser[]
     */
    protected $serialisers = [];

    /**
     * Register a serialiser.
     *
     * @param string|string[]                   $types      A singular file type
     *                                                      in string form, or
     *                                                      an array of such.
     * @param \Origin\Serialisation\ISerialiser $serialiser An instance of the
     *                                                      corresponding
     *                                                      serialiser class.
     */
    public function addSerialiser($types, $serialiser) {
        if (!is_array($types)) {
            $types = array($types);
        }

        foreach ($types as $type) {
            $this->serialisers[$type] = $serialiser;
        }

        return $this;
    }

    /**
     * Serialise a given piece of data.
     *
     * @param string $type The desired serialiser type.
     * @param mixed $data
     */
    public function serialise($type, $data) {
        return $this->serialisers[$type]->serialise($data);
    }

    /**
     * Unserialise a given piece of data.
     *
     * @param string $type The desired serialiser type.
     * @param string $data The type of data to unserialise.
     */
    public function unserialise($type, $data) {
        return $this->serialisers[$type]->unserialise($data);
    }

    /**
     * Unserialise the contents of a file.
     *
     * @param string      $filename The name of the file to attempt to
     *                              unserialise.
     * @param null|string $type     The type of the file, or null. If null, the
     *                              type will be assumed from the filename's
     *                              extension.
     *
     * @return mixed The unserialised data.
     *
     * @todo Allow serialisers to override this behaviour, as native code ought
     *       to be faster.
     */
    public function unserialiseFile($filename, $type=null) {
        if ($type === null) {
            $type = pathinfo($filename, PATHINFO_EXTENSION);
            if ($type === null) {
                throw new \Exception('no $type, no extension');
            }
        }

        return $this->unserialise($type, file_get_contents($filename));
    }
}
