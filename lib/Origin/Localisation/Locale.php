<?php

/**
 * Localisation API.
 *
 * Origin.
 *
 * @author Luke Carrier <luke@carrier.im>
 * @copyright 2014 Luke Carrier
 * @license Proprietary; all rights reserved
 */

namespace Origin\Localisation;

/**
 * Individual locale.
 *
 * The locale forms the heart of the localisation system in Origin.
 */
class Locale {
    /**
     * Language strings.
     *
     * An array of language strings, as loaded from the application's locale
     * files.
     *
     * @var array<string, string>
     */
    protected $strings = array();

    public function __construct($strings=NULL) {
        if ($strings !== NULL) {
            $this->strings = $strings;
        }
    }

    /**
     * Get a specific language string.
     *
     * @param string $name The name of the string to retrieve.
     * @param array<string, mixed> An array of substitutions.
     * @return string The language string.
     */
    public function translate($name, $substitutions=array()) {
        $substitution_keys   = array();
        $substitution_values = array();

        foreach ($substitutions as $key => &$value) {
            $substitution_keys[]   = $key;
            $substitution_values[] = $value;
        }

        return str_replace($substitution_keys, $substitution_values,
                           $this->strings[$name]);
    }

    /**
     * Get the plural form of a given word.
     *
     * @param string $word The word to pluralise.
     * @return string The pluralised word.
     */
    public function pluralise($word) {
        return $word;
    }
}
