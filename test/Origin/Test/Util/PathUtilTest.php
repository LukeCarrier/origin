<?php

use Origin\TestFramework\GenericTestCase,
    Origin\Util\PathUtil;

class PathUtilTest extends GenericTestCase {
    public function testJoin() {
        $this->assertEquals('1/2',   PathUtil::join('1', '2'));
        $this->assertEquals('1/2/3', PathUtil::join('1', '2', '3'));
    }

    public function testParent() {
        $this->assertEquals('1', PathUtil::parent('1/2'));
        $this->assertEquals('1/2', PathUtil::parent('1/2/3'));
    }
}
