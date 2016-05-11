<?php

/**
 * DBAL schema library.
 *
 * Origin.
 *
 * @author Luke Carrier <luke@carrier.im>
 * @copyright 2014 Luke Carrier
 * @license Proprietary; all rights reserved
 */

namespace Origin\Database\Dbal\Schema;

/**
 * DBAL schema column delta.
 *
 * This class represents a delta diff between two versions of a database column.
 */
class ColumnDelta {
    /**
     * Platform.
     *
     * @var \Origin\Database\Dbal\IPlatform
     */
    protected $platform;

    /**
     * Representation of the column as it currently exists.
     *
     * @var \Origin\Database\Dbal\Schema\Column
     */
    protected $current_column;

    /**
     * Representation of the column as it should exist post-apply.
     *
     * @var Origin\Database\Dbal\Schema\Column
     */
    protected $new_column;

    /**
     * Initialiser.
     *
     * @param \Origin\Database\Dbal\IPlatform     $platform
     * @param \Origin\Database\Dbal\Schema\Column $current_column
     * @param \Origin\Database\Dbal\Schema\Column $new_column
     */
    public function __construct($platform, $current_column, $new_column) {
        $this->platform       = $platform;
        $this->current_column = $current_column;
        $this->new_column     = $platform->normaliseColumnAttributes(clone $new_column);
    }
}
