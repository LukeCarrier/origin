<?php

namespace Origin\Test\View;

use Origin\TestFramework\GenericTestCase,
    Origin\Util\PathUtil,
    Origin\View\Sandbox;

class SandboxTest extends GenericTestCase {
    protected $view_temp_directory;

    protected $disk_cache;
    protected $disk_cache_directory;
    protected $sandbox;

    protected $raw_php;
    protected $expected_output;

    public function setUp() {
        parent::setUp();

        $this->sandbox = new Sandbox();

        $this->view_temp_directory  = "{$this->test_temp_directory}/view";

        $this->disk_cache_directory = "{$this->view_temp_directory}/sandbox";
        $this->disk_cache = $this->getMockBuilder('Origin\Cache\Disk\Disk')
                                 ->disableOriginalConstructor()
                                 ->getMock();

        $this->raw_php         = '<?php echo "it works";';
        $this->expected_output = 'it works';

        mkdir($this->disk_cache_directory, 0755, true);
        file_put_contents("{$this->disk_cache_directory}/existent.php", $this->raw_php);
    }

    public function setUpDiskCacheGetFilename($cache_index, $cache_filename) {
        $this->disk_cache->expects($this->any())
                         ->method('getFilename')
                         ->with($this->identicalTo($cache_index))
                         ->will($this->returnCallback(function($index) use($cache_filename) {
                             return $cache_filename;
                         }));
    }

    public function tearDown() {
        unset($this->sandbox);

        unlink("{$this->disk_cache_directory}/existent.php");
        rmdir($this->disk_cache_directory);
        rmdir($this->view_temp_directory);

        unset($this->disk_cache_directory);
        unset($this->disk_cache);

        unset($this->raw_php);

        parent::tearDown();
    }

    public function testLoadCachedPhpFile() {
        $this->setUpDiskCacheGetFilename('existent', "{$this->disk_cache_directory}/existent.php");
        $this->sandbox->loadCachedPhpFile($this->disk_cache, 'existent');

        $this->assertEquals($this->expected_output, (string) $this->sandbox);
    }

    /**
     * @expectedException \Origin\View\Errors\NoMatchingViews
     */
    public function testLoadCachedPhpFileThrowsNoMatchingViews() {
        $this->setUpDiskCacheGetFilename('nonexistent', null);
        $this->sandbox->loadCachedPhpFile($this->disk_cache, 'nonexistent');
    }

    public function testLoadRawPhp() {
        $this->sandbox->loadRawPhp($this->raw_php);

        $this->assertEquals($this->expected_output, (string) $this->sandbox);
    }

    public function testSetVariables() {
        $this->sandbox->setVariables([
            'output' => 'some text',
        ]);
        $this->sandbox->loadRawPhp('<?php echo $this->variables[\'output\'];');

        $this->assertEquals('some text', (string) $this->sandbox);
    }
}
