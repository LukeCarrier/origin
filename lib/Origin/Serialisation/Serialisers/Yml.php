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

namespace Origin\Serialisation\Serialisers;
use Origin\Serialisation\Errors\UnserialisableEntity,
    Origin\Serialisation\ISerialiser,
    Origin\Util\ArrayUtil;

class Yml implements ISerialiser {
    /**
     * @override
     */
    public function serialise($object) {
        return yaml_emit($object);
    }

    /**
     * @override
     */
    public function unserialise($data) {
        return yaml_parse($data);
    }
}
