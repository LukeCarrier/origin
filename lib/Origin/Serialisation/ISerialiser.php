<?php

/**
 * Serialisation utility API.
 *
 * Origin.
 *
 * @author Luke Carrier <luke@carrier.im>
 * @copyright 2014 Luke Carrier
 * @license Proprietary; all rights reserved
 */

namespace Origin\Serialisation;

/**
 * Serialiser interface.
 *
 * Serialisers are classes which translate formats like JSON, XML and YAML into
 * PHP-native objects and vice versa. Each class translates one format.
 */
interface ISerialiser {
    /**
     * Serialise an object into a data string.
     *
     * @param mixed $data The object to serialise.
     *
     * @return string                                            The serialised
     *                                                           data string.
     * @throws \Origin\Serialisation\Errors\UnserialisableEntity Throws an
     *                                                           exception when
     *                                                           an entity which
     *                                                           cannot be
     *                                                           represented in
     *                                                           the selected
     *                                                           format is
     *                                                           encountered.
     */
    public function serialise($data);

    /**
     * Unserialise a data string into an object.
     *
     * @param string $data The string to unserialise.
     *
     * @return mixed The resulting object.
     */
    public function unserialise($data);
}
