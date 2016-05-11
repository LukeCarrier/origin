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

/**
 * INI configuration serialiser.
 *
 * Interprets MS INI files for the serialisation library.
 */
class Ini implements ISerialiser {
    /**
     * @override
     */
    public function serialise($data) {
        if (!is_array($data)) {
            throw new UnserialisableEntity('$data must be an array');
        }

        $result = '';

        foreach ($data as $section => &$values) {
            $result .= "[{$section}]\n";

            foreach ($values as $key => &$value) {
                if (is_array($value)) {
                    $indexed = ArrayUtil::isIndexed($value);

                    foreach ($value as $sub_key => &$sub_value) {
                        if ($value_seq_keys) {
                            $sub_key = '';
                        }
                        $result .= "{$key}[{$sub_key}] = {$sub_value}\n";
                    }
                } else {
                    $result .= "{$key} = {$value}\n";
                }
            }
        }

        return $result;
    }

    /**
     * @override
     */
    public function unserialise($data) {
        return parse_ini_string($data, true);
    }
}
