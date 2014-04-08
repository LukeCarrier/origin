<?php

/**
 * Configuration library.
 *
 * Origin.
 *
 * @author    Luke Carrier <luke@carrier.im>
 * @copyright 2014 Luke Carrier
 * @license   Proprietary; all rights reserved
 */

namespace Origin\Util;

use Origin\Util\Errors\File as FileError;

/**
 * Assorted file management utilities.
 */
class FileUtil {
    /**
     * Create directory if it doesn't already exist.
     *
     * @param string  $directory The path to the directory to be created.
     * @param integer $mode      Octal mode of the newly created directory.
     *
     * @return void
     *
     * @throws \Origin\Util\Errors\File Throws an exception when it's not possible to create the directory, e.g. where
     *                                  the disk is full or ACLs or system policy make it impossible.
     */
    public static function createDirectoryIfNotExists($directory, $mode=0777) {
        if (!is_dir($directory)) {
            if (!@mkdir($directory, $mode, true)) {
                throw new FileError(FileError::ACCESS_WRITE | FileError::TYPE_DIRECTORY, $directory);
            }
        }
    }

    /**
     * Write data to a file only if it's lockable.
     *
     * @param string  $filename The path of the file to attempt to lock and write to.
     * @param string  $contents The data to write to the file.
     * @param integer $lock     One of PHP's LOCK_* constants indicating the mode 
     *
     * @return boolean True when the file was successfully locked for writing and the data written, else false.
     *
     * @throws \Origin\Util\Errors\File Throws an exception when the specified file cannot be written to due to ACLs or
     *                                  system policy, or a failure to obtain a write lock.
     */
    public static function writeDataOnlyIfLockable($filename, $contents, $lock=null) {
        if (!@$file = fopen($filename, 'w')) {
            throw new FileError(FileError::ACCESS_WRITE | FileError::TYPE_FILE, $filename);
        }

        if (@flock($file, $lock)) {
            if (@fwrite($file, $contents) === false) {
                // @codeCoverageIgnoreStart
                throw new FileError(FileError::ACCESS_WRITE | FileError::TYPE_FILE, $filename);
                // @codeCoverageIgnoreEnd
            }

            $result = true;
        } else {
            $result = false;
        }

        @fclose($file);

        return $result;
    }
}
