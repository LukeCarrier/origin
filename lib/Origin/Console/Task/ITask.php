<?php

/**
 * Task host library.
 *
 * Origin.
 *
 * @author    Luke Carrier <luke@carrier.im>
 * @copyright 2014 Luke Carrier
 * @license   Proprietary; all rights reserved
 */

namespace Origin\Console\Task;

interface ITask {
    /**
     * Run a task.
     *
     * @param mixed $arg1 An unlimited number of command line arguments can be
     *        specified.
     * @param mixed $arg2 An unlimited number of command line arguments can be
     *        specified.
     *
     * @return mixed Any output you desire -- the caller must be aware of this.
     */
    //public function run([$arg1[, $arg2[, ...]]]);
}
