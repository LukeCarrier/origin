<?php

/**
 * View engine.
 *
 * Origin.
 *
 * @author    Luke Carrier <luke@carrier.im>
 * @copyright 2014 Luke Carrier
 * @license   Proprietary; all rights reserved
 */

namespace Origin\View\Engines\Origin;
use Origin\View\Engines\Origin\CommandFactory,
    Origin\View\IEngine,
    Origin\View\View;

/**
 * Origin view engine.
 *
 * The Origin view engine class provides the utility functions necessary to hook the templating library into the Origin
 * view framework.
 *
 * Origin templates are primarily comprised of HTML, with the addition of Django-like tag constructs that allow for
 * conditional expressions, repeated loops and string operations. The templates compile down to pure PHP to facilitate
 * opcode caching for rapid evaluation and execution on deployments which support it.
 */
class Engine implements IEngine {
    /**
     * Command factory.
     *
     * The command factory is responsible for the sourcing of block commands.
     *
     * @var \Origin\View\Engines\Origin\CommandFactory
     */
    protected $command_factory;

    /**
     * Set the command loader.
     *
     * Set the command loader instance which should be used to source command classes for the block node.
     *
     * @param \Origin\View\Engines\Origin\CommandFactory $command_factory The command factory instance.
     */
    public function setCommandFactory(CommandFactory $command_factory) {
        $this->command_factory = $command_factory;
    }

    /**
     * @override
     */
    public function toPhp($view, $qualified_name, $raw_content) {
        $parser = new Parser($view, $qualified_name, $raw_content);

        if ($this->command_factory !== NULL) {
            $parser->setCommandFactory($this->command_factory);
        }

        $nodes  = $parser->parse();
        $output = $nodes->render();

        return $output;
    }
}
