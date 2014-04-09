<?php

/**
 * Disk cache API.
 *
 * Origin.
 *
 * @author    Luke Carrier <luke@carrier.im>
 * @copyright 2013 CloudFlux
 * @license   Proprietary; all rights reserved
 */

namespace Origin\Cache\Disk;

use Origin\Util\FileUtil;

/**
 * Disk cache implementation.
 *
 * The disk cache library enables an application to take a designated directory and use it as a key-value store. Items
 * can be stored in and retrieved from the directory at runtime.
 *
 * This library is designed to be used within code that performs system/time intensive operations, the results of which
 * can be cached for later execution. For best results you can produce files which can be included, thus enabling an
 * opcode cache in the interpreter to keep your native code in memory or persistent storage.
 */
class Disk {
    /**
     * Cache directory.
     *
     * @var string
     */
    protected $directory;

    /**
     * Cache directory mode.
     *
     * @var integer
     */
    protected $directory_mode;

    /**
     * Cache file extension.
     *
     * @var string
     */
    protected $file_extension;

    /**
     * Cache file mode.
     *
     * @var integer
     */
    protected $file_mode;

    /**
     * Initialiser.
     *
     * @param string  $directory      The root directory for the cache instance.
     * @param string  $file_extension The extension for all files created within the cache directory.
     * @param integer $directory_mode The (octal) mode for all directories created within the cache directory.
     * @param integer $file_mode      The (octal) mode for all files created within the cache directory.
     */
    public function __construct($directory, $file_extension=null, $directory_mode=0750, $file_mode=0640) {
        $this->directory      = $directory;
        $this->file_extension = $file_extension;

        $this->directory_mode = $directory_mode;
        $this->file_mode      = $file_mode;
    }

    /**
     * Get the root directory for the cache instance.
     *
     * @return string The root directory for the cache instance.
     */
    public function getDirectory() {
        return $this->directory;
    }

    /**
     * Get the (octal) mode for all directories created within the cache directory.
     *
     * @return integer The (octal) mode for all directories created within the cache directory.
     */
    public function getDirectoryMode() {
        return $this->directory_mode;
    }

    /**
     * Retrieve a filename from the cache.
     *
     * @param string $index The index to retrieve.
     *
     * @return mixed The result of the callback or closure passed as the second argument.
     */
    public function getFilename($index) {
        $file_extension = $this->getFileExtension(true);

        return "{$this->directory}/{$index}{$file_extension}";
    }

    /**
     * Get file extension.
     *
     * @param boolean $include_dot Include the separator between the file's basename and extension. Note that the
     *                             separator will be omitted if the file extension is zero-length.
     *
     * @return string
     */
    public function getFileExtension($include_dot=false) {
        $file_extension = $this->file_extension;

        if ($include_dot) {
            $file_extension = empty($file_extension) ? '' : ".{$file_extension}";
        }

        return $file_extension;
    }

    /**
     * Get the (octal) mode for all files created within the cache directory.
     *
     * @return integer The (octal) mode for all files created within the cache directory.
     */
    public function getFileMode() {
        return $this->file_mode;
    }

    /**
     * Store the supplied contents in a file within the cache.
     *
     * @param string $index    The index in which to store the supplied contents.
     * @param string $contents The contents.
     *
     * @return void
     */
    public function put($index, $contents) {
        $file_extension = $this->getFileExtension(true);

        $filename = "{$this->directory}/{$index}{$file_extension}";
        $directory = dirname($filename);

        FileUtil::createDirectoryIfNotExists($directory, $this->directory_mode);
        FileUtil::writeDataOnlyIfLockable($filename, $contents, LOCK_EX | LOCK_NB);
    }
}
