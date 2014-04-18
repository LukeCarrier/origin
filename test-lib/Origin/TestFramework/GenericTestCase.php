<?php

/**
 * Test framework.
 *
 * Origin.
 *
 * @author    Luke Carrier <luke@carrier.im>
 * @copyright 2014 Luke Carrier
 * @license   Proprietary; all rights reserved
 */

namespace Origin\TestFramework;

use FileSystemIterator,
    PHPUnit_Framework_TestCase,
    RecursiveDirectoryIterator,
    RecursiveIteratorIterator;

/**
 * Base test case.
 *
 * The generic test case class should be subclassed once for each major type of unit test.
 */
class GenericTestCase extends PHPUnit_Framework_TestCase {
    /**
     * Absolute path to the test framework library directory.
     *
     * This directory is where additional support libraries for test cases are contained.
     *
     * @var string
     */
    protected $test_lib_directory;

    /**
     * Absolute path to the temporary directory.
     *
     * This directory is used as a scratch area for test cases, and is generally used for testing I/O libraries.
     *
     * @var string
     */
    protected $test_temp_directory;

    /**
     * Destroys any created temporary files created by previous tests.
     *
     * Calling this method in setUp() helps to ensure tests don't become order independent or misleading.
     *
     * @return void
     */
    protected function setUpTempDirectory() {
        if (is_dir($this->test_temp_directory)) {
            $iterator = new RecursiveDirectoryIterator($this->test_temp_directory, FilesystemIterator::SKIP_DOTS);

            foreach (new RecursiveIteratorIterator($iterator, RecursiveIteratorIterator::CHILD_FIRST) as $path) {
                $path->isFile() ? unlink($path->getPathname()) : rmdir($path->getPathname());
            }
        } else {
            mkdir($this->test_temp_directory, 0755, true);
        }
    }

    /**
     * Set up the test case.
     *
     * @return void
     */
    public function setUp() {
        $root_directory = dirname(dirname(dirname(__DIR__)));

        $this->test_lib_directory  = "{$root_directory}/test-lib";
        $this->test_temp_directory = "{$root_directory}/test-temp";

        $this->setUpTempDirectory();
    }

    /**
     * Tear down the test case.
     *
     * @return void
     */
    public function tearDown() {
        unset($this->test_lib_directory);
        unset($this->test_temp_directory);
    }
}
