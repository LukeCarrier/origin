<?php

/**
 * String inflexer utility library test.
 *
 * Origin.
 *
 * @author Luke Carrier <luke@carrier.im>
 * @copyright 2014 Luke Carrier
 * @license Proprietary; all rights reserved
 */

namespace Origin\Test\Util;

use Origin\TestFramework\GenericTestCase,
    Origin\Util\StringInflexerUtil;

class StringInflexerUtilTest extends GenericTestCase {
    protected $pluralise_tests;
    protected $underscore_tests;

    public function setUp() {
        parent::setUp();

        $this->pluralise_tests = [
            'book'    => 'books',
            'cherry'  => 'cherries',
            'march'   => 'marches',
            'success' => 'successes',
        ];

        $this->underscore_tests = [
            'fooBar'   => 'foo_bar',
            'fooBarNG' => 'foo_bar_nG',
        ];
    }

    public function tearDown() {
        unset($this->pluralise_tests);
        unset($this->underscore_tests);

        parent::tearDown();
    }

    public function testCamelCaseToUnderscore() {
        foreach ($this->underscore_tests as $camel_case => $expected_underscore) {
            $this->assertEquals($expected_underscore, StringInflexerUtil::camelCaseToUnderscore($camel_case),
                                "camelCaseToUnderscore translates '{$camel_case}' to '{$expected_underscore}'");
        }
    }

    public function testPluralise() {
        foreach ($this->pluralise_tests as $singular => $expected_plural) {
            $this->assertEquals($expected_plural, StringInflexerUtil::pluralise($singular),
                                "pluralise translates '{$singular}' to '{$expected_plural}'");
        }
    }

    public function testSingularise() {
        $singularise_tests = array_flip($this->pluralise_tests);

        foreach ($singularise_tests as $plural => $expected_singular) {
            $this->assertEquals($expected_singular, StringInflexerUtil::singularise($plural),
                                "singularise translates '{$plural}' to '{$expected_singular}'");
        }
    }

    public function testUnderscoreToCamelCase() {
        $camel_case_tests = array_flip($this->underscore_tests);

        foreach ($camel_case_tests as $underscore => $expected_camel_case) {
            $capitalised_expected_camel_case = ucfirst($expected_camel_case);

            $this->assertEquals($expected_camel_case, StringInflexerUtil::underscoreToCamelCase($underscore),
                                "underscoreToCamelCase translates '{$underscore} to '{$expected_camel_case}'");

            $this->assertEquals($capitalised_expected_camel_case,
                                StringInflexerUtil::underscoreToCamelCase($underscore, true),
                                "underscoreToCamelCase translates '{$underscore} to '{$capitalised_expected_camel_case}'");
        }
    }
}
