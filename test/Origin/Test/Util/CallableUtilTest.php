<?php

namespace Origin\Test\Util;

use Origin\TestFramework\GenericTestCase,
    Origin\TestSupport\Util\CallableUtil\Greeter,
    Origin\Util\CallableUtil,
    ReflectionMethod;

class CallableUtilTest extends GenericTestCase {
    protected $greeter_class;
    protected $greeter;
    protected $method_callable;
    protected $method_reflector;
    protected $function_callable;

    public function setUp() {
        parent::setUp();

        $this->greeter_class    = 'Origin\TestSupport\Util\CallableUtil\Greeter';
        $this->greeter          = new Greeter();
        $this->method_callable  = [$this->greeter, 'greet'];
        $this->method_reflector = new ReflectionMethod($this->greeter_class, 'greet');

        $this->function_callable = function($person, $greeting='Hello, %s!') {
            return sprintf($greeting, $person);
        };
    }

    public function tearDown() {
        unset($this->greeter_class);
        unset($this->greeter);
        unset($this->method_callable);
        unset($this->method_reflector);

        unset($this->function_callable);

        parent::setUp();
    }

    public function testCallWithNamedParameters() {
        $params_no_greeting = [
            'person' => 'Luke',
        ];
        $params_greeting = [
            'person'   => 'David',
            'greeting' => 'Greetings, %s'
        ];

        $this->assertEquals('Hello, Luke!', CallableUtil::callWithNamedParameters($this->method_callable,
                                                                                  $params_no_greeting),
                            'assumes default parameter values in method call when unspecified');

        $this->assertEquals('Greetings, David', CallableUtil::callWithNamedParameters($this->method_callable,
                                                                                      $params_greeting),
                            'overrides default parameter values in method call when specified');

        $this->assertEquals('Hello, Luke!', CallableUtil::callWithNamedParameters($this->function_callable,
                                                                                  $params_no_greeting),
                            'assumes default parameter values in function call when unspecified');

        $this->assertEquals('Greetings, David', CallableUtil::callWithNamedParameters($this->function_callable,
                                                                                      $params_greeting),
                            'overrides default parameter values in function call when specified');
    }

    public function testInstantiateWithNamedParameters() {
        $greeter = CallableUtil::instantiateWithNamedParameters($this->greeter_class, []);
        $this->assertEquals('Hello, %s!', $greeter->getDefaultGreeting(),
                            'assumes default parameter values when unspecified');

        $greeter = CallableUtil::instantiateWithNamedParameters($this->greeter_class, [
            'default_greeting' => 'Nice to meet you, %s',
        ]);
        $this->assertEquals('Nice to meet you, %s', $greeter->getDefaultGreeting(),
                            'overrides default parameter values when specified');
    }

    public function testResolveParameters() {
        $this->assertEquals(['Luke', null], CallableUtil::resolveParameters($this->method_reflector, [
            'person' => 'Luke',
        ]), 'assumes default parameter values in method call when unspecified');

        $this->assertEquals(['Luke', 'Greetings, %s'], CallableUtil::resolveParameters($this->method_reflector, [
            'person'   => 'Luke',
            'greeting' => 'Greetings, %s',
        ]), 'overrides default parameter values in method call when specified');
    }

    /**
     * @expectedException     \Origin\Util\Errors\CallableParameterMismatch
     * @expectedExceptionCode \Origin\Util\Errors\CallableParameterMismatch::CODE_TOO_FEW
     */
    public function testResolveParametersWithTooFewParameters() {
        CallableUtil::resolveParameters($this->method_reflector, []);
    }

    /**
     * @expectedException     \Origin\Util\Errors\CallableParameterMismatch
     * @expectedExceptionCode \Origin\Util\Errors\CallableParameterMismatch::CODE_TOO_MANY
     */
    public function testResolveParametersWithTooManyParameters() {
        CallableUtil::resolveParameters($this->method_reflector, [
            'person'            => 'Luke',
            'invalid_parameter' => 'should trigger exception',
        ]);
    }
}
