<?php

namespace Origin\Test\Util;

use Origin\Util\FileUtil,
    Origin\Util\PathUtil,
    Origin\TestFramework\GenericTestCase;

class FileUtilTest extends GenericTestCase {
    protected $directory;

    protected $ro_directory;
    protected $ro_subdirectory;
    protected $rw_directory;
    protected $rw_subdirectory;

    protected $ro_lockable_file;
    protected $rw_lockable_file;
    protected $lockable_file_contents;

    public function setUp() {
        parent::setUp();

        $this->directory = "{$this->test_temp_directory}/util/fileutil";

        $this->ro_directory = "{$this->directory}/ro_directory";
        $this->rw_directory = "{$this->directory}/rw_directory";

        $this->ro_subdirectory = "{$this->ro_directory}/subdirectory";
        $this->rw_subdirectory = "{$this->rw_directory}/subdirectory";

        $this->ro_lockable_file       = "{$this->ro_directory}/lockable.txt";
        $this->rw_lockable_file       = "{$this->rw_directory}/lockable.txt";
        $this->lockable_file_contents = 'some text here';

        mkdir($this->directory,    0755, true);
        mkdir($this->ro_directory, 0000);
    }

    public function tearDown() {
        chmod($this->ro_directory, 0755);

        $lockable_files = ['ro', 'rw'];
        foreach ($lockable_files as $lockable_file_property) {
            $lockable_file_property .= '_lockable_file';
            $lockable_file           = $this->{$lockable_file_property};

            if (is_file($lockable_file)) {
                unlink($lockable_file);
            }

            unset($this->{$lockable_file_property});
        }

        $directories = ['ro_subdirectory', 'ro_directory', 'rw_subdirectory', 'rw_directory', 'directory'];
        foreach ($directories as $directory_property) {
            $directory = $this->{$directory_property};

            if (is_dir($directory)) {
                rmdir($directory);
            }

            unset($this->{$directory_property});
        }

        parent::tearDown();
    }

    public function testCreateDirectoryIfNotExists() {
        FileUtil::createDirectoryIfNotExists($this->rw_subdirectory);

        $this->assertTrue(is_dir($this->rw_directory),    'parent directory exists after createDirectoryIfNotExists()');
        $this->assertTrue(is_dir($this->rw_subdirectory), 'directory exists after createDirectoryIfNotExists()');
    }

    /**
     * @expectedException \Origin\Util\Errors\File
     */
    public function testCreateDirectoryIfNotExistsThrowsExceptionOnFailure() {
        FileUtil::createDirectoryIfNotExists($this->ro_subdirectory);
    }

    public function testWriteDataOnlyIfLockable() {
        mkdir($this->rw_subdirectory, 0755, true);
        FileUtil::writeDataOnlyIfLockable($this->rw_lockable_file, $this->lockable_file_contents, LOCK_EX);

        $this->assertFileExists($this->rw_lockable_file);
    }

    public function testWriteDataOnlyIfLockableReturnsFalseOnInvalidLockType() {
        mkdir($this->rw_directory, 0755, true);

        $this->assertFalse(FileUtil::writeDataOnlyIfLockable($this->rw_lockable_file, $this->lockable_file_contents));
    }

    /**
     * @expectedException \Origin\Util\Errors\File
     */
    public function testWriteDataOnlyIfLockableThrowsExceptionOnReadOnlyFile() {
        FileUtil::writeDataOnlyIfLockable($this->ro_lockable_file, $this->lockable_file_contents);
    }
}
