<?php

namespace Origin\Test\Autoload;

use Origin\Autoload\AutoloadNamespace,
    Origin\TestFramework\GenericTestCase,
    Origin\Util\PathUtil;

class AutoloadNamespaceTest extends GenericTestCase {
    protected $formatter;
    protected $root_namespace;
    protected $root_directory;
    protected $autoload_namespace;
    protected $seek_class;
    protected $seek_file;

    public function setUp() {
        parent::setUp();

        $this->formatter          = ['Origin\Autoload\Autoloader', 'format'];
        $this->root_namespace     = 'Origin\TestSupport\Autoload\ExampleNamespace';
        $this->root_directory     = "{$this->test_lib_directory}/Origin/TestSupport/Autoload/ExampleNamespace";

        $this->autoload_namespace = new AutoloadNamespace($this->root_namespace, $this->root_directory, $this->formatter);
        $this->seek_class         = "{$this->root_namespace}\ExampleClass";
        $this->seek_file          = "{$this->root_directory}/ExampleClass.php";
    }

    public function tearDown() {
        unset($this->formatter);
        unset($this->root_namespace);
        unset($this->root_directory);

        unset($this->autoload_namespace);
        unset($this->seek_class);
        unset($this->seek_file);

        parent::tearDown();
    }

    public function testGetFormatter() {
        $this->assertEquals($this->formatter, $this->autoload_namespace->getFormatter(),
                            'returns configured formatter');
    }

    public function testGetRootNamespace() {
        $this->assertEquals($this->root_namespace, $this->autoload_namespace->getRootNamespace(),
                            'returns configured root namespace');
    }

    public function testGetRootDirectory() {
        $this->assertEquals($this->root_directory, $this->autoload_namespace->getRootDirectory(),
                            'returns configured root directory');
    }

    public function testLoad() {
        $this->assertTrue($this->autoload_namespace->load($this->seek_class),
                          'returns true given a valid class name');

        $this->assertContains($this->seek_file, get_included_files(), 'file containing class included');

        $this->assertTrue(class_exists($this->seek_class, false), 'class exists within interpreter instance');
    }
}
