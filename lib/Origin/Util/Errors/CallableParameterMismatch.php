<?php

/**
 * Callable utility library.
 *
 * Origin.
 *
 * @author    Luke Carrier <luke@carrier.im>
 * @copyright 2014 Luke Carrier
 * @license   Proprietary; all rights reserved
 */

namespace Origin\Util\Errors;

use Exception;

/**
 * Callable parameter mismatch exception.
 *
 * This exception is raised when attempting to map parameter values to their corresponding parameters fails. This can
 * happen when specifying values for parameters which don't exist in the function's prototype, or when omitting a
 * required parameter with no default value.
 */
class CallableParameterMismatch extends Exception {
    /**
     * Reason: too few parameters.
     *
     * @var integer
     */
    const CODE_TOO_FEW = 1;

    /**
     * Reason: too many parameters.
     *
     * @var integer
     */
    const CODE_TOO_MANY = 2;

    /**
     * Message: too few parameters.
     *
     * @var string
     */
    const MESSAGE_TOO_FEW = 'value omitted for required parameter \'%s\' which has no default value';
    
    /**
     * Message: too many parameters.
     *
     * @var string
     */
    const MESSAGE_TOO_MANY = 'could not match value \'%s\' to nonexistent parameter \'%s\'';

    /**
     * Parameter name.
     *
     * @var string
     */
    protected $parameter;

    /**
     * Parameter value.
     *
     * @var mixed
     */
    protected $value;

    /**
     * Initialiser.
     *
     * @param integer    $code      One of the CODE_* constants.
     * @param string     $parameter The parameter's name.
     * @param mixed      $value     The parameter's value.
     * @param \Exception $previous  Optional previous exception, if we were raised during the handling of another
     *                              exception condition.
     *
     * @override \Exception
     */
    public function __construct($code, $parameter, $value=null, $previous=null) {
        $this->code      = $code;
        $this->parameter = $parameter;
        $this->value     = $value;

        parent::__construct((string) $this, $this->code, $previous);
    }

    /**
     * Return a string representation of the exception.
     *
     * @return string Formatted string representation.
     *
     * @override \Exception
     */
    public function __toString() {
        $message = sprintf($this->getMessageFormatString(), $this->parameter, $this->value);

        return __NAMESPACE__ . __CLASS__ . ": {$message}";
    }

    /**
     * Get format string for the specified code.
     *
     * @return string The message's format string.
     */
    protected function getMessageFormatString() {
        switch ($this->getCode()) {
            case static::CODE_TOO_FEW:
                $string = static::MESSAGE_TOO_FEW;
                break;

            case static::CODE_TOO_MANY:
                $string = static::MESSAGE_TOO_MANY;
                break;
        }

        return $string;
    }

    /**
     * Get the parameter's name.
     *
     * @return string The parameter's name.
     */
    public function getParameter() {
        return $this->parameter;
    }

    /**
     * Get the parameter's value.
     *
     * @return mixed The parameter's value.
     */
    public function getValue() {
        return $this->value;
    }
}
