<?php

namespace Origin\Test\Logging;

use Origin\Logging\Logger,
    Origin\Logging\Message,
    Origin\Logging\Targets\Stream as StreamTarget,
    Origin\TestFramework\GenericTestCase;

class StreamTest extends GenericTestCase {
    protected $file;
    protected $file_handle;
    protected $target;

    public function setUp() {
        parent::setUp();

        $this->file        = "{$this->test_temp_directory}/file.txt";
        $this->file_handle = fopen($this->file, 'w');
        $this->target      = new StreamTarget($this->file_handle);
    }

    public function tearDown() {
        unset($this->file_handle);

        unlink($this->file);

        unset($this->file);
        unset($this->target);

        parent::tearDown();
    }

    public function testGetDefaultFormatter() {
        $this->assertInstanceOf('Origin\Logging\Formatters\Line', $this->target->getDefaultFormatter());
    }

    public function testRecord() {
        $this->target->record(new Message(Logger::LEVEL_INFORMATION, 'magic!'));

        $this->assertContains('magic!', file_get_contents($this->file));
    }
}
