<?php

namespace Origin\Test\Logging;

use Origin\Logging\Logger,
    Origin\TestFramework\GenericTestCase;

class LoggerTest extends GenericTestCase {
    protected $logger;
    protected $logger_name;

    public function setUp() {
        parent::setUp();

        $this->mock_stream_target = $this->getMockBuilder('Origin\Logging\Targets\Stream');

        $this->logger_name = 'unlikely';
        $this->logger = new Logger($this->logger_name, [
            Logger::LEVEL_DEBUG => [
                $this->getMockTarget(),
            ],
        ], [
            $this->getMockProcessor(),
        ]);
    }

    protected function getLoggingMock($class, $method, $expect_calls) {
        $expect_calls = $expect_calls ?: $this->any();

        $mock = $this->getMockBuilder($class)
                     ->disableOriginalConstructor()
                     ->getMock();

        $mock->expects($expect_calls)
             ->method($method);

        return $mock;
    }

    protected function getMockProcessor($expect_calls=null) {
        return $this->getLoggingMock('Origin\Logging\Processors\StackTrace', 'process', $expect_calls);
    }

    protected function getMockTarget($expect_calls=null) {
        return $this->getLoggingMock('Origin\Logging\Targets\Stream', 'record', $expect_calls);
    }

    public function tearDown() {
        unset($this->logger_name);
        unset($this->logger);

        parent::tearDown();
    }

    public function testGetLevelName() {
        $this->assertEquals('debug', Logger::getLevelName(Logger::LEVEL_DEBUG));
    }

    /**
     * @expectedException \Origin\Logging\Errors\InvalidLevel
     */
    public function testGetLevelNameThrowsOnInvalidLevel() {
        Logger::getLevelName(9001);
    }

    public function testGetName() {
        $this->assertEquals($this->logger_name, $this->logger->getName());
    }

    public function testLogCallsAllProcessors() {
        $this->logger->addProcessor($this->getMockProcessor($this->once()));
        $this->logger->addProcessor($this->getMockProcessor($this->once()));

        $this->logger->alert('guru meditation');
    }

    public function testLogCallsOnlyAppropriateTargets() {
        $this->logger->addTarget(Logger::LEVEL_DEBUG,     $this->getMockTarget($this->exactly(2)));
        $this->logger->addTarget(Logger::LEVEL_WARNING,   $this->getMockTarget($this->exactly(2)));
        $this->logger->addTarget(Logger::LEVEL_ERROR,     $this->getMockTarget($this->once()));
        $this->logger->addTarget(Logger::LEVEL_EMERGENCY, $this->getMockTarget($this->never()));

        $this->logger->warning('something timed out');
        $this->logger->error('something fell over');
    }
}
