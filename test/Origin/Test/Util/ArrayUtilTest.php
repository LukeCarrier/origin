<?php

namespace Origin\Test\Util;

use Origin\TestFramework\GenericTestCase,
    Origin\TestSupport\Util\ArrayUtilSupport,
    Origin\Util\ArrayUtil;

class ArrayUtilTest extends GenericTestCase {
    protected $filter_array;
    protected $flatten_array;

    protected $support_class;

    public function setUp() {
        parent::setUp();

        $this->filter_array = [
            'Bill Gates'    => null,
            'Steve Ballmer' => null,
            'Steve Jobs'    => null,
            'Steve Wozniak' => null,
        ];

        $this->flatten_array = [
            'a' => [
                'a' => null,
                'b' => null,
            ],
            'b' => [
                'a' => null,
            ],
        ];

        $this->support_class = '\Origin\TestSupport\Util\ArrayUtilSupport';
    }

    public function tearDown() {
        unset($this->filter_array);
        unset($this->flatten_array);

        unset($this->support_class);

        parent::tearDown();
    }

    public function testFilterKeys() {
        $expected_keys = [
            'Bill Gates',
            'Steve Ballmer',
        ];

        $result = ArrayUtil::filterKeys($this->filter_array, $expected_keys);

        $this->assertEquals(2, count($result), 'filterKeys returns correct number of results');

        foreach ($expected_keys as $expected_key) {
            $this->assertArrayHasKey($expected_key, $result, "filterKeys result includes '{$expected_key}' key");
        }
    }

    public function testFilterKeysByCallback() {
        $result = ArrayUtil::filterKeysByCallback($this->filter_array, [$this->support_class, 'notCalledSteve']);

        $this->assertEquals(1, count($result), 'filterKeysByCallback returns correct number of results');
        $this->assertArrayHasKey('Bill Gates', $result, 'filterKeysByCallback returns only matching entries');
    }

    public function testFlatten() {
        $flattened_keys = ['a.a', 'a.b', 'b.a'];
        $result         = ArrayUtil::flatten($this->flatten_array);

        $this->assertEquals(count($flattened_keys), count($result), 'returns correct number of results');
        foreach ($flattened_keys as $key) {
            $this->assertArrayHasKey($key, $result, "flattened {$key} correctly");
        }
    }

    public function testImplodeWithKeys() {
        $this->filter_array = [
            'option1' => 'value1',
        ];

        $this->assertEquals('option1=value1', ArrayUtil::implodeWithKeys($this->filter_array),
                            'does not append delimeter for a singular pair');

        $this->filter_array['option2'] = 'value2';
        $this->assertEquals('option1=value1;option2=value2', ArrayUtil::implodeWithKeys($this->filter_array),
                            'separates pairs with delimiter');
    }

    public function testIsIndexed() {
        $this->filter_array = [
            0 => null,
            1 => null,
        ];
        $this->assertEquals(true, ArrayUtil::isIndexed($this->filter_array), 'returns true given a numerically indexed array');

        $this->filter_array = [
            'a' => null,
            'b' => null,
        ];
        $this->assertEquals(false, ArrayUtil::isIndexed($this->filter_array), 'returns false given a non-numerically indexed array');
    }

    public function testMapWithNamedParameters() {
        $this->filter_array = [2, 4, 6, 8, 10];
        $parameters = [
            'divisor' => 2,
        ];

        $callable = [$this->support_class, 'divide'];
        $this->assertEquals([1, 2, 3, 4, 5], ArrayUtil::mapWithNamedParameters($this->filter_array, $callable, $parameters, 'number'),
                            'executes callable once per each array index');
    }
}
