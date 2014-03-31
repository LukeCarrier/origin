<?php

/**
 * Array utility library.
 *
 * Origin.
 *
 * @author    Luke Carrier <luke@carrier.im>
 * @copyright 2014 Luke Carrier
 * @license   Proprietary; all rights reserved
 */

namespace Origin\Util;

use Origin\Util\Errors\CallableParameterMismatch,
    ReflectionClass,
    ReflectionFunction,
    ReflectionMethod;

/**
 * Utility library for working with callables.
 *
 * Provides functionality for processing function/method parameters via reflection and performing method calls
 * dynamically.
 */
class CallableUtil {
    /**
     * Call the specified function with the passed named parameters.
     *
     * Unlike PHP's call_user_func() and call_user_func_array() functions, this method will map parameters in the
     * supplied array to those in the function prototype of the callable.
     *
     * @param callable $callable The callable.
     * @param mixed[]  $values   The parameter values.
     *
     * @return mixed The return value of the callable.
     */
    public static function callWithNamedParameters($callable, $values) {
        if (is_array($callable)) {
            $reflector = new ReflectionMethod($callable[0], $callable[1]);
            $object    = (is_string($callable[0])) ? null : $callable[0];
        } else {
            $reflector = new ReflectionFunction($callable);
        }

        $parameters = static::resolveParameters($reflector, $values);

        return (is_array($callable)) ? $reflector->invokeArgs($object, $parameters)
                                     : $reflector->invokeArgs($parameters);
    }

    /**
     * Instantiate a class based on a set of values.
     *
     * @param string  $class  The name of the class to instantiate.
     * @param mixed[] $values The values to pass to the class's constructor.
     *
     * @return mixed An instance of the specified class.
     */
    public static function instantiateWithNamedParameters($class, $values) {
        $class_reflector = new ReflectionClass($class);
        $method_reflector = $class_reflector->getConstructor();

        $parameters = static::resolveParameters($method_reflector, $values);

        return $class_reflector->newInstanceArgs($parameters);
    }

    /**
     * Resolve parameters for a method utilising its reflector.
     *
     * @param \FunctionReflector|\MethodReflector $reflector The reflector.
     * @param mixed[]                             $values    An array of values.
     *
     * @throws \Exception If a non-optional parameter is missing from the array of values.
     *
     * @return mixed[] An array of values suitable for passing to {@link invokeArgs()}.
     *
     * @todo Phase out this method in favour of two distinct methods: one to query the reflector to get the parameter
     *       details (defaults, prototype order), and another to match the developer-supplied parameters to this this
     *       information. This would enable the application to handle some preliminary caching and cut out unnecessary
     *       calls to the reflection API.
     */
    public static function resolveParameters($reflector, $values) {
        $parameters = [];

        foreach ($reflector->getParameters() as $parameter) {
            if (array_key_exists($parameter->name, $values)) {
                $parameters[] = $values[$parameter->name];
                unset($values[$parameter->name]);
            } else {
                if ($parameter->isOptional()) {
                    $parameters[] = $parameter->getDefaultValue();
                } else {
                    throw new CallableParameterMismatch(CallableParameterMismatch::CODE_TOO_FEW, $parameter->name);
                }
            }
        }

        if (count($values) > 0) {
            $parameter_name = key($values);
            throw new CallableParameterMismatch(CallableParameterMismatch::CODE_TOO_MANY, $parameter_name,
                                                $values[$parameter_name]);
        }

        return $parameters;
    }
}
