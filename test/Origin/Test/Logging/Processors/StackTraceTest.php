<?php

namespace Origin\Test\Logging\Processors;

use Origin\Logging\Logger,
    Origin\Logging\Message,
    Origin\Logging\Processors\StackTrace as StackTraceProcessor,
    Origin\TestFramework\GenericTestCase;

class StackTraceTest extends GenericTestCase {
    protected $message;
    protected $stack_trace_processor;

    public function setUp() {
        parent::setUp();

        $this->message               = new Message(Logger::LEVEL_INFORMATION, 'some message');
        $this->stack_trace_processor = new StackTraceProcessor();
    }

    public function tearDown() {
        unset($this->stack_trace_processor);

        parent::tearDown();
    }

    public function testProcess() {
        $this->stack_trace_processor->process($this->message);
        $context = $this->message->getContext();

        $this->assertArrayHasKey('stack_trace', $context);

        $expect_trace = debug_backtrace();
        $actual_trace = $context['stack_trace'];

        $check = 2;
        $this->assertEquals($expect_trace[count($expect_trace) - $check],
                            $actual_trace[count($actual_trace) - $check]);
    }
}
