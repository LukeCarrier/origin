<?php

namespace Origin\Test\Database;

use Origin\Database\ConnectorManager,
    Origin\TestFramework\GenericTestCase;

class DriverManagerTest extends GenericTestCase {
    protected $manager;

    public function setUp() {
        parent::setUp();

        $this->manager = new ConnectorManager();
    }

    public function tearDown() {
        unset($this->manager);

        parent::tearDown();
    }
}
