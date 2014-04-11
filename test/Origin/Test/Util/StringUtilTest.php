<?php

/**
 * String utilities library tests.
 *
 * Origin.
 *
 * @author Luke Carrier <luke@carrier.im>
 * @copyright 2014 Luke Carrier
 * @license Proprietary; all rights reserved
 */

namespace Origin\Test\Util;

use Origin\TestFramework\GenericTestCase,
    Origin\Util\StringUtil;

class StringUtilTest extends GenericTestCase {
    protected $haystack;
    protected $haystack_format;

    protected $haystack_parameters;

    public function setUp() {
        parent::setUp();

        $this->haystack        = 'I like trains.';
        $this->haystack_format = 'I {opinion} {object}s.';

        $this->format_parameters = [
            'opinion' => 'like',
            'object'  => 'train',
        ];
    }

    public function tearDown() {
        unset($this->haystack_format);
        unset($this->haystack);

        unset($this->format_parameters);

        parent::tearDown();
    }

    public function testContains() {
        $this->assertTrue(StringUtil::contains($this->haystack, 'trains'));
        $this->assertFalse(StringUtil::contains($this->haystack, 'turtles'));
    }

    public function testEndsWith() {
        $this->assertTrue(StringUtil::endsWith($this->haystack, 'trains.'));
        $this->assertFalse(StringUtil::endsWith($this->haystack, 'like'));
    }

    public function testFormat() {
        $this->assertEquals($this->haystack, StringUtil::format($this->haystack_format, $this->format_parameters));
    }

    public function testStartsWith() {
        $this->assertTrue(StringUtil::startsWith($this->haystack, 'I '));
        $this->assertFalse(StringUtil::startsWith($this->haystack, 'like'));
    }

    public function testSurroundedBy() {
        $this->assertEquals('trains', StringUtil::surroundedBy($this->haystack, 'I like ', '.'),
                            'surroundedBy returns delimited content');
        $this->assertFalse(StringUtil::surroundedBy($this->haystack, 'I', 'I'));
        $this->assertFalse(StringUtil::surroundedBy($this->haystack, '.', 'I'));
    }
}
