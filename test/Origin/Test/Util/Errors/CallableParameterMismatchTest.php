<?php

namespace Origin\Test\Util\Errors;

use Origin\Util\Errors\CallableParameterMismatch,
    Origin\TestFramework\GenericTestCase;

class CallableParameterMismatchTest extends GenericTestCase {
    protected $code;
    protected $parameter;
    protected $value;
    protected $exception;

    public function setUp() {
        parent::setUp();

        $this->code      = CallableParameterMismatch::CODE_TOO_MANY;
        $this->parameter = 'the_parameter';
        $this->value     = 'does_not_exist_in_the_prototype';

        $this->exception = new CallableParameterMismatch($this->code, $this->parameter, $this->value);
    }

    public function tearDown() {
        unset($this->code);
        unset($this->parameter);
        unset($this->value);

        unset($this->exception);

        parent::tearDown();
    }

    public function test__toString() {
        $this->assertRegexp('/nonexistent parameter/', (string) $this->exception, 'returns correct message');
    }

    public function testGetParameter() {
        $this->assertEquals($this->parameter, $this->exception->getParameter(), 'returns correct parameter');
    }

    public function testGetValue() {
        $this->assertEquals($this->value, $this->exception->getValue(), 'returns correct value');
    }
}
