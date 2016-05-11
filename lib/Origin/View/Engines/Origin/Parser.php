<?php

/**
 * Origin view engine library.
 *
 * Origin.
 *
 * @author Luke Carrier <luke@carrier.im>
 * @copyright 2014 Luke Carrier
 * @license Proprietary; all rights reserved
 */

namespace Origin\View\Engines\Origin;
use Origin\View\View,
    Origin\View\Engines\Origin\Errors\NoSuchBlock,
    Origin\View\Engines\Origin\Nodes\Text,
    Origin\View\Engines\Origin\Nodes\Variable;

/**
 * Origin template parser.
 *
 * The template parser is responsible for 
 */
class Parser {
    /**
     * Command loader.
     *
     * @var Origin\View\Engines\Origin\CommandFactory
     */
    protected $command_loader;

    /**
     * Context stacks.
     *
     * Blocks with child nodes will require a context stack in order to track
     * which nodes are nested within them, and their depth levels.
     *
     * @var mixed[][]
     */
    protected $context_stacks = array();

    /**
     * Templates with nodes.
     *
     * @var boolean[]
     */
    protected $have_nodes = array();

    /**
     * Registered objects.
     *
     * As commands are called from within templates, they'll frequently need to
     * store data other commands will require access to in the future. Here, we
     *
     * @var mixed[]
     */
    protected $objects = array();

    /**
     * Template parents.
     *
     * @var string[]
     */
    protected $parents = array();

    /**
     * The fully-qualified name of the "origin" template.
     *
     * @var string
     */
    protected $template;

    /**
     * The tokens extracted from the template's raw content.
     *
     * @var \Origin\View\Engines\Origin\IToken[]
     */
    protected $tokens = array();
    
    /**
     * The view to use as a template file loader.
     *
     * @var \Origin\View\View
     */
    protected $view;

    /**
     * Initialiser.
     *
     * @param string $template
     * @param string $raw_content
     */
    public function __construct($view, $template, $raw_content) {
        $this->view     = $view;
        $this->template = $template;
        $this->tokens   = $this->getTokensForTemplate($template, $raw_content);
    }

    /**
     * Append the tokens of a named template to this parser instance.
     *
     * Given a fully-qualified template name, source the template from the view
     * instance that initialised the parser and append its tokens to ours.
     *
     * @param string $qualified_name The qualified name of the template.
     */
    public function appendTemplate($qualified_name) {
        list($raw_content, $engines) = $this->view->getRawTemplate($qualified_name);
        $tokens = $this->getTokensForTemplate($qualified_name, $raw_content);

        while ($value = array_shift($tokens)) {
            array_push($this->tokens, $value);
        }
    }

    /**
     * Pop a single context off of the top of a context stack.
     *
     * @param string $stack The name of the stack to pop the item off of.
     * @return mixed The removed item's value.
     */
    public function contextStackPop($stack) {
        return array_pop($this->context_stacks[$stack]);
    }

    /**
     * Push a single item onto a specified context stack.
     *
     * @param string $stack The name of the stack to push the item onto.
     * @param mixed $value The new item's value.
     */
    public function contextStackPush($stack, $value) {
        if (!array_key_exists($stack, $this->context_stacks)) {
            $this->context_stacks[$stack] = array();
        }

        array_push($this->context_stacks[$stack], $value);
    }

    /**
     * Get an object from the parser object store.
     *
     * @param string $name The name of the object in the parser's object store.
     * @return mixed The object's value.
     */
    public function getObject($name) {
        return (array_key_exists($name, $this->objects))
                ? $this->objects[$name] : NULL;
    }

    /**
     * Get an origin child.
     *
     * @param string $parent The parent name to seek for.
     * @return string The child name.
     */
    public function getOriginChild($parent) {
        $origin_children = array_flip($this->parents);
        return $origin_children[$parent];
    }

    /**
     * Get an origin parent.
     *
     * @param string $parent The child name to seek for.
     * @return string The parent name.
     */
    public function getOriginParent($child) {
        return $this->parents[$child];
    }

    /**
     * Tokenise the raw content from a template.
     *
     * @param string $raw_content The content.
     * @return array<IToken> The tokens.
     */
    public function getTokensForTemplate($qualified_name, $raw_content) {
        return Lexer::tokenise($qualified_name, $raw_content);
    }

    /**
     * Parse the tokens into nodes.
     *
     * @param string[] $until_blocks An optional array of block names. When one
     *        of these is encountered, the parser will halt and return.
     * @return NodeArray<INode> A node array object (itself a descendent of
     *         PHP's ArrayObject class) containing all of the parsed nodes.
     *         Calling the render() method of the node array will return PHP
     *         source code ready for executing or storing in your template
     *         cache.
     */
    public function parse($until_blocks=NULL) {
        $nodes = new NodeArray();
        $reached_until_block = false;

        while ($token = array_shift($this->tokens)) {
            $node = NULL;

            switch ($token::TOKEN_TYPE) {
                case 'Block':
                    $command = $token->getContents()[0];

                    /* If were told to halt at blocks of this type, put the
                     * token back on the stack and set the reached flag. */
                    if ($until_blocks !== NULL
                            && in_array($command, $until_blocks)) {
                        $reached_until_block = true;
                        array_unshift($this->tokens, $token);
                        break;
                    }

                    $command_class = $this->command_loader->getCommand($command);
                    $node = $command_class::getNode($token->getOriginTemplate(),
                                                    $this,
                                                    $token->getContents());

                    break;

                case 'Text':
                    $node = new Text($token->getOriginTemplate(),
                                     $token->getContents());
                    break;

                case 'Variable':
                    $node = new Variable($token->getOriginTemplate(),
                                         $token->getContents());
                    break;
            }

            // Some commands may refrain from returning a node
            if ($node !== NULL) {
                $nodes[] = $node;
                $this->have_nodes[$token->getOriginTemplate()] = true;
            }

            /* If we reached one of the blocks we were told to halt at, break
             * the loop. */
            if ($reached_until_block === true) {
                break;
            }
        }

        /* If we were told to parse until we saw a block of a certain type but
         * we never encountered it, throw an error; we probably parsed a bad
         * template. */
        if ($until_blocks !== NULL && $reached_until_block === false) {
            throw new NoSuchBlock(implode(', ', $until_blocks));
        }

        return $nodes;
    }

    /**
     * Prepend the tokens of a named template to this parser instance.
     *
     * Given a fully-qualified template name, source the template from the view
     * instance that initialised the parser and prepend its tokens to ours.
     *
     * @param string $qualified_name The qualified name of the template.
     */
    public function prependTemplate($qualified_name) {
        list($raw_content, $engines) = $this->view->getRawTemplate($qualified_name);
        $tokens = $this->getTokensForTemplate($qualified_name, $raw_content);

        while ($value = array_pop($tokens)) {
            array_unshift($this->tokens, $value);
        }
    }

    /**
     * Set block command loader.
     *
     * The block command loader is required to source commands for use by block
     * nodes.
     *
     * @param \Origin\View\Engines\Origin\CommandFactory $command_loader The
     *        command loader instance to source commands via.
     */
    public function setCommandFactory($command_loader) {
        $this->command_loader = $command_loader;
    }

    /**
     * Insert an object into or update an object in the parser object store.
     *
     * The parser provides developers with a simple key=>value object store,
     * useful for retaining parser state for use between blocks.
     *
     * @param string $name The key under which to store the value.
     * @param mixed $value The value to store.
     */
    public function setObject($name, $value) {
        $this->objects[$name] = $value;
    }

    /**
     * Set the parent of a template (from within a node).
     *
     * @param string $origin The name of the child template.
     * @param string $parent The name of the parent template.
     */
    public function setOriginParent($parent, $child) {
        $this->parents[$parent] = $child;
    }

    /**
     * Drop the top token from the token stack.
     *
     * This method will be useful for commands which need to parse up until a
     * specific block (to find their child nodes) then resume the parser's
     * normal execution, excluding their own end token.
     *
     * @return IToken The removed token.
     */
    public function tokenShift() {
        return array_shift($this->tokens);
    }
}
