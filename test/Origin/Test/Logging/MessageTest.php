<?php

namespace Origin\Test\Logging;

use Origin\Logging\Logger,
    Origin\Logging\Message,
    Origin\TestFramework\GenericTestCase;

class MessageTest extends GenericTestCase {
    protected $level;
    protected $message_string;
    protected $context;

    protected $message;

    public function setUp() {
        parent::setUp();

        $this->level          = Logger::LEVEL_WARNING;
        $this->message_string = 'something happened';
        $this->context        = [
            'key' => 'value',
        ];

        $this->message = new Message($this->level, $this->message_string, $this->context);
    }

    public function tearDown() {
        unset($this->level);
        unset($this->message_string);
        unset($this->context);

        unset($this->message);

        parent::tearDown();
    }

    public function testGetContext() {
        $this->assertEquals($this->context, $this->message->getContext());
    }

    public function testSetContextKey() {
        $this->message->setContextKey('other_key', 'other_value');
        $context = $this->message->getContext();

        $this->assertEquals('other_value', $context['other_key']);
    }

    public function testGetLevel() {
        $this->assertEquals($this->level, $this->message->getLevel());
    }

    public function testGetLevelName() {
        $this->assertTrue(is_string($this->message->getLevelName()));
    }

    public function testGetMessage() {
        $this->assertEquals($this->message_string, $this->message->getMessage());
    }

    public function testGetTime() {
        $this->assertTrue(is_float($this->message->getTime()));
    }
}
