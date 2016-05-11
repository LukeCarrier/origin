<?php

namespace Origin\Test\View\Errors;

use Origin\TestFramework\GenericTestCase,
    Origin\View\Errors\NoMatchingViews,
    ReflectionMethod;

class NoMatchingViewsTest extends GenericTestCase {
    protected $qualified_name;
    protected $exception;
    protected $exception_class;

    protected $get_real_message_reflector;

    public function setUp() {
        parent::setUp();

        $this->qualified_name  = 'controller.view';
        $this->exception       = new NoMatchingViews($this->qualified_name);
        $this->exception_class = 'Origin\View\Errors\NoMatchingViews';

        $this->get_real_message_reflector = new ReflectionMethod($this->exception_class, 'getRealMessage');
        $this->get_real_message_reflector->setAccessible(true);

    }

    public function tearDown() {
        $this->get_real_message_reflector->setAccessible(false);
        unset($this->get_real_message_reflector);

        unset($this->exception_class);
        unset($this->exception);
        unset($this->qualified_name);

        parent::tearDown();
    }

    public function test__toString() {
        $message = (string) $this->exception;

        $this->assertRegExp("/^[a-z\\\\]+\: [a-z\\'\. ]+$/i", $message,
                            "returns value matching 'Namespace\Class: Message' format");
        $this->assertContains("'{$this->qualified_name}'", $message, 'returns value containing view qualified name');
    }

    public function testGetRealMessage() {
        $message = $this->get_real_message_reflector->invoke($this->exception);

        $this->assertRegExp("/^[a-z\'\. ]+$/i", $message, "returns value matching 'Message' format");
        $this->assertContains("'{$this->qualified_name}'", $message, 'returns value containing view qualified name');
    }
}
