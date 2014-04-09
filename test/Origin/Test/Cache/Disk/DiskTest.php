<?php

namespace Origin\Test\Cache\Disk;

use Origin\Cache\Disk\Disk,
    Origin\TestFramework\GenericTestCase;

class DiskTest extends GenericTestCase {
    protected $disk_cache_directory;
    protected $disk_cache;
    protected $sample_contents;
    protected $sample_file;
    protected $sample_index;

    public function setUp() {
        parent::setUp();

        $this->disk_cache_directory = "{$this->test_temp_directory}/cache/disk";
        $this->disk_cache           = new Disk($this->disk_cache_directory, 'php');

        $this->sample_contents  = '<?php /* pork and beans */';
        $this->sample_dir       = "{$this->disk_cache_directory}/directory";
        $this->sample_file      = "{$this->disk_cache_directory}/file.php";
        $this->sample_index     = 'file';
        $this->sample_dir_file  = "{$this->sample_dir}/file.php";
        $this->sample_dir_index = 'directory/file';

        mkdir($this->disk_cache_directory, 0755, true);
        mkdir($this->sample_dir);
        file_put_contents($this->sample_dir_file, $this->sample_contents);
        file_put_contents($this->sample_file, $this->sample_contents);
    }

    public function tearDown() {
        unlink($this->sample_file);
        unlink($this->sample_dir_file);
        rmdir($this->sample_dir);
        rmdir($this->disk_cache_directory);

        unset($this->sample_contents);
        unset($this->sample_file);
        unset($this->sample_index);

        unset($this->disk_cache_directory);
        unset($this->disk_cache);

        parent::tearDown();
    }

    public function testGetDirectory() {
        $this->assertEquals($this->disk_cache_directory, $this->disk_cache->getDirectory(),
                            'returns disk cache root directory');
    }

    public function testGetDirectoryMode() {
        $this->disk_cache = new Disk($this->disk_cache_directory, null, 0777);
        $this->assertEquals(0777, $this->disk_cache->getDirectoryMode(), 'returns mode for newly created directories');
    }

    public function testGetFilename() {
        $this->assertEquals($this->sample_file, $this->disk_cache->getFilename($this->sample_index),
                            'correctly generates filenames for files within the top-level cache directory');
        $this->assertEquals($this->sample_dir_file, $this->disk_cache->getFilename($this->sample_dir_index),
                            'correctly generates filenames for files within subdirectories of the cache directory');
    }

    public function testGetFileExtension() {
        $this->assertEquals('php', $this->disk_cache->getFileExtension(),
                            'returns extension without separator when not requested and extension is not null');
        $this->assertEquals('.php', $this->disk_cache->getFileExtension(true),
                            'returns extension with separator when requested and extension is not null');

        $this->disk_cache = new Disk($this->disk_cache_directory);
        $this->assertEquals('', $this->disk_cache->getFileExtension(),
                            'returns empty string without separator when not requested and extension is null');
        $this->assertEquals('', $this->disk_cache->getFileExtension(true),
                            'returns empty string without separator when requested and extension is null');
    }

    public function testGetFileMode() {
        $this->disk_cache = new Disk($this->disk_cache_directory, null, 0777, 0666);
        $this->assertEquals(0666, $this->disk_cache->getFileMode(), 'returns mode for newly created files');
    }

    public function testPut() {
        $this->disk_cache->put($this->sample_index, $this->sample_contents);
        $this->assertEquals($this->sample_contents, file_get_contents($this->sample_file));
    }
}
