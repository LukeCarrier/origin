<?php

namespace Origin\Test\Logging\Errors;

use Origin\Logging\Errors\InvalidLevel as InvalidLevelError,
    Origin\Logging\Logger,
    Origin\TestFramework\GenericTestCase;

class LoggerTest extends GenericTestCase {
    protected $exception;
    protected $level;

    public function setUp() {
        parent::setUp();

        $this->level     = Logger::LEVEL_DEBUG;
        $this->exception = new InvalidLevelError($this->level);
    }

    public function tearDown() {
        unset($this->level);
        unset($this->exception);

        parent::tearDown();
    }

    public function test__toString() {
        $this->assertRegExp('/^level ([0-9]+) is not known$/', (string) $this->exception);
    }

    public function testGetLevel() {
        $this->assertEquals($this->level, $this->exception->getLevel());
    }
}
