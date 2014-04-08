<?php

namespace Origin\Test\Util\Errors;

use Origin\TestFramework\GenericTestCase,
    Origin\Util\Errors\File as FileError,
    ReflectionMethod;

class FileTest extends GenericTestCase {
    protected $format_string_reflector;

    protected $directory_exception;
    protected $file_exception;

    public function setUp() {
        parent::setUp();

        $this->format_string_reflector = new ReflectionMethod('Origin\Util\Errors\File', 'getMessageFormatString');
        $this->format_string_reflector->setAccessible(true);

        $this->directory_exception = new FileError(FileError::ACCESS_READ  | FileError::TYPE_DIRECTORY, '/directory');
        $this->file_exception      = new FileError(FileError::ACCESS_WRITE | FileError::TYPE_FILE     , '/file',
                                                   'better entropy than Debian');
    }

    public function tearDown() {
        unset($this->directory_exception);
        unset($this->file_exception);

        $this->format_string_reflector->setAccessible(false);
        unset($this->format_string_reflector);

        parent::tearDown();
    }

    public function test__toString() {
        $this->assertNotContains(': ', (string) $this->directory_exception);
        $this->assertContains(': ',    (string) $this->file_exception);
    }

    public function testGetAccessType() {
        $this->assertEquals(FileError::ACCESS_READ,  $this->directory_exception->getAccessType());
        $this->assertEquals(FileError::ACCESS_WRITE, $this->file_exception->getAccessType());
    }

    public function testGetMessageFormatString() {
        $this->assertEquals('failed to %s %s "%s"',     $this->format_string_reflector->invoke($this->directory_exception));
        $this->assertEquals('failed to %s %s "%s": %s', $this->format_string_reflector->invoke($this->file_exception));
    }

    public function testGetPathType() {
        $this->assertEquals(FileError::TYPE_DIRECTORY, $this->directory_exception->getPathType());
        $this->assertEquals(FileError::TYPE_FILE,      $this->file_exception->getPathType());
    }
}
