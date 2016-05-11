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

namespace Origin\View\Loaders;

use Origin\View\ILoader,
    Origin\View\Sandbox,
    Origin\View\Errors\NoMatchingViews,
    Origin\View\Errors\MultipleMatchingViews;

/**
 * File template loader.
 *
 * Sources templates from the filesystem based on a configured array of template directories.
 */
class File implements ILoader {
    /**
     * Available directories to attempt to source templates from.
     *
     * @var array
     */
    protected $template_directories = [];

    /**
     * Add a template directory.
     *
     * @param string $template_directory The template directory.
     */
    public function addTemplateDirectory($template_directory) {
        $this->template_directories[] = $template_directory;
    }

    /**
     * Initialiser.
     *
     * @param string[] $template_directories An array of template directories to search for matching views.
     */
    public function __construct($template_directories) {
        $this->template_directories = $template_directories;
    }

    /**
     * @override \Origin\View\ILoader
     */
    public function load($qualified_name) {
        $basename = str_replace('.', '/', $qualified_name);

        foreach ($this->template_directories as $directory) {
            $qualified_basename = "{$directory}/{$basename}";
            $matches = glob("{$qualified_basename}.*");

            $num_matches = count($matches);
            if ($num_matches === 0) {
                throw new NoMatchingViews();
            } elseif ($num_matches === 1) {
                $engines = explode('.', substr($matches[0], strlen($qualified_basename) + 1));
                return array(file_get_contents($matches[0]), $engines);
            } else {
                throw new MultipleMatchingViews();
            }
        }
    }
}
