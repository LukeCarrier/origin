<?php

namespace Origin\Test\Autoload;

use Origin\Autoload\Autoloader,
    Origin\TestFramework\GenericTestCase,
    ReflectionProperty;

class AutoloaderTest extends GenericTestCase {
    protected $autoloader;
    protected $load_method;
    protected $reflector;

    public function setUp() {
        parent::setUp();

        $this->autoloader  = new Autoloader();
        $this->load_method = [$this->autoloader, 'load'];
        $this->reflector   = new ReflectionProperty('Origin\Autoload\Autoloader', 'namespaces');

        $this->reflector->setAccessible(true);
    }

    public function tearDown() {
        $this->reflector->setAccessible(false);

        unset($this->autoloader);
        unset($this->load_method);
        unset($this->reflector);

        parent::tearDown();
    }

    public function testAddNamespace() {
        $root_namespace = 'Some\Namespace';
        $root_directory = __DIR__ . '/Some/Namespace';
        $formatter      = ['\Origin\Autoload\Autoloader', 'format'];

        $this->autoloader->addNamespace($root_namespace, $root_directory);
        $namespaces = $this->reflector->getValue($this->autoloader);
        $this->assertNamespaceHasProperties($root_namespace, $root_directory, $formatter, $namespaces[$root_namespace][0]);

        $root_namespace = 'Some\Other\Namespace';
        $root_directory = __DIR__ . '/Some/Other/Namespace';
        $formatter      = function($symbol) {};

        $this->autoloader->addNamespace($root_namespace, $root_directory, $formatter);
        $namespaces = $this->reflector->getValue($this->autoloader);
        $this->assertNamespaceHasProperties($root_namespace, $root_directory, $formatter, $namespaces[$root_namespace][0]);
    }

    public function testEnableAndDisable() {
        $this->assertEquals($this->autoloader, $this->autoloader->enable(),
                            'enable returns autoloader instance for method chaining');

        $this->assertContains($this->load_method, spl_autoload_functions(),
                              'enable registers the autoloader instance\'s load method with SPL');

        $this->assertEquals($this->autoloader, $this->autoloader->disable(),
                            'disable returns autoloader instance for method chaining');

        $this->assertNotContains($this->load_method, spl_autoload_functions(),
                                 'disable unregisters the autoloader\'s load method');
    }

    public function testFormat() {
        $this->assertEquals('Class.php',                       Autoloader::format('Class'));
        $this->assertEquals('Namespace/Class.php',             Autoloader::format('Namespace\Class'));
        $this->assertEquals('OtherNamespace/AnotherClass.php', Autoloader::format('OtherNamespace\AnotherClass'));
    }

    protected function assertNamespaceHasProperties($root_namespace, $root_directory, $formatter, $namespace) {
        $this->assertEquals($root_namespace, $namespace->getRootNamespace());
        $this->assertEquals($root_directory, $namespace->getRootDirectory());
        $this->assertEquals($formatter,      $namespace->getFormatter());
    }
 }
