<?php

namespace Origin\Test\View;

use Origin\TestFramework\GenericTestCase,
    Origin\View\PhpFile,
    ReflectionProperty;

class PhpFileTest extends GenericTestCase {
    protected $php_file;

    public function setUp() {
        parent::setUp();

        $this->php_file = new PhpFile();

        $this->content = new ReflectionProperty(get_class($this->php_file), 'content');
        $this->content->setAccessible(true);
    }

    public function tearDown() {
        unset($this->php_file);

        $this->content->setAccessible(false);
        unset($this->content);

        parent::tearDown();
    }

    public function testAddContent() {
        $content = '/* ' . mt_rand() . ' */';

        $this->php_file->addContent($content);
        $this->assertContains($content, $this->content->getValue($this->php_file));
    }

    public function testAddFunctionCall() {
        $this->php_file->addFunctionCall('sprintf', ['%s is tasty', 'cake']);
        $this->assertContains('sprintf(', $this->content->getValue($this->php_file));
    }

    public function testAddStatement() {
        $this->php_file->addStatement('echo', 2);
        $this->assertContains('echo 2', $this->content->getValue($this->php_file));
    }

    public function testGetArgumentList() {
        $this->assertEquals('true, 2, \'3\'', $this->php_file->getArgumentList([true, 2, '3']));
    }

    public function testGetFunctionCall() {
        $this->assertEquals('print()', $this->php_file->getFunctionCall('print'));
    }

    public function testGetStatement() {
        $this->assertEquals('die', $this->php_file->getStatement('die'));
    }

    public function testGetPhp() {
        $content = '/* ' . mt_rand() . ' */';

        $this->php_file->addContent($content);
        $this->assertContains($content, $this->php_file->getPhp());
    }

    public function testGetVariableReference() {
        $reference = $this->php_file->getVariableReference('foobar');

        $this->assertContains('[\'foobar\']', $reference);
    }
}
