<?php

/**
 * Origin templating engine node array.
 *
 * Origin.
 *
 * @author Luke Carrier <luke@carrier.im>
 * @copyright 2014 Luke Carrier
 * @license Proprietary; all rights reserved
 */

namespace Origin\View;

/**
 * PHP code generator.
 *
 * This class provides a vastly oversimplified interface to generating PHP source code from within view engines. It
 * provides methods for generating argument lists, function calls and statement calls. The generated source code is
 * intended to be cached within a disk cache (for later execution with include/require) or executed immediately with
 * eval().
 */
class PhpFile {
    /**
     * PHP open tag.
     *
     * @var string
     */
    const PHP_OPEN = '<?php';

    /**
     * PHP close tag.
     *
     * @var string
     */
    const PHP_CLOSE = '?>';

    /**
     * Leading PHP content.
     *
     * Text to be prepended to all new files before the content body begins. Note that this text should evaluate as
     * valid PHP source code, so ensure all commentary remains in comment tags.
     *
     * @var string
     */
    const FILE_PRE = <<<EOT
/*
 * Origin template.
 *
 * This code is automatically compiled from the application's views. Any
 * changes made here will be overwritten the next time the views are
 * recompiled.
 */
EOT;

    /**
     * File content.
     *
     * The generated PHP source code we'll return for later caching or execution in a sandbox.
     *
     * @var string
     */
    protected $content;

    /**
     * Initialiser.
     *
     * Sets up an empty file containing an opening tag and the standard header.
     */
    public function __construct() {
        $this->content = static::PHP_OPEN . "\n\n" . static::FILE_PRE . "\n";
    }

    /**
     * Add arbitrary content to the file.
     *
     * @param string $content The content to add to the file.
     *
     * @return void
     */
    public function addContent($content) {
        $this->content .= "\n{$content}";
    }

    /**
     * Add a call to a given function.
     *
     * @param string  $function  The name of the function to call.
     * @param mixed[] $arguments A numerically indexed array of arguments to pass to the specified function.
     *
     * @return void
     */
    public function addFunctionCall($function, $arguments=null) {
        $this->addContent(static::getFunctionCall($function, $arguments) . ';');
    }

    /**
     * Add a statement call.
     *
     * @param string  $statement The name of the statement to call.
     * @param mixed[] $arguments A numerically indexed array of arguments to pass to the specified statement.
     *
     * @return void
     */
    public function addStatement($statement, $arguments=null) {
        $this->addContent(static::getStatement($statement, $arguments) . ';');
    }

    /**
     * Assemble an argument list from an array of arguments.
     *
     * @param mixed[] $arguments A numerically indexed array of arguments to generate an argument list from.
     *
     * @return string The formatted argument list.
     */
    public static function getArgumentList($arguments=null) {
        $result = '';

        if ($arguments !== null) {
            if (!is_array($arguments)) {
                $arguments = array($arguments);
            }

            $arguments = array_map(function($value) {
                return var_export($value, true);
            }, $arguments);

            $result = implode(', ', $arguments);
        }

        return $result;
    }

    /**
     * Get the syntax of a function call.
     *
     * @param string  $function  The name of the function to call.
     * @param mixed[] $arguments A numerically indexed array of arguments to pass to the specified function.
     *
     * @return string The source code for the function call.
     */
    public static function getFunctionCall($function, $arguments=null) {
        $arguments = static::getArgumentList($arguments);

        return "{$function}({$arguments})";
    }

    /**
     * Get the syntax of a statement call.
     *
     * @param string  $function  The name of the statement to call.
     * @param mixed[] $arguments A numerically indexed array of arguments to pass to the specified statement.
     *
     * @return string The source code for the statement call.
     */
    public static function getStatement($statement, $arguments=null) {
        $result = $statement;

        $arguments = static::getArgumentList($arguments);
        if (strlen($arguments) > 0) {
            $result .= ' ' . $arguments;
        }

        return "{$result}";
    }

    /**
     * Get the raw PHP source code for this instance.
     *
     * @return string The PHP source code.
     */
    public function getPhp() {
        return $this->content;
    }

    /**
     * Get a reference to a variable name.
     *
     * @param string $variable The variable to look up.
     *
     * @return string The variable reference source code.
     */
    public static function getVariableReference($variable) {
        return '$this->variables[' . var_export($variable, true) . ']';
    }
}
