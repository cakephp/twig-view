<?php
declare(strict_types=1);

/**
 * CakePHP(tm) : Rapid Development Framework (https://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 * Copyright (c) 2014 Cees-Jan Kiewiet
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 * @link          https://cakephp.org CakePHP(tm) Project
 * @since         1.0.0
 * @license       https://opensource.org/licenses/mit-license.php MIT License
 */

namespace Cake\TwigView\Twig;

use Twig\Error\LoaderError;
use Twig\Loader\LoaderInterface;
use Twig\Source;

/**
 * Loads template files with aboslute paths.
 */
class AbsolutePathLoader implements LoaderInterface
{
    /**
     * Get the file contents of a template.
     *
     * @param string $name Template.
     * @return string
     */
    public function getSource(string $name): string
    {
        return file_get_contents($this->findTemplate($name));
    }

    /**
     * Returns the source context for a given template logical name.
     *
     * @param string $name The template logical name.
     * @return \Twig\Source
     */
    public function getSourceContext(string $name): Source
    {
        $code = $this->getSource($name);
        $path = $this->findTemplate($name);

        return new Source($code, $name, $path);
    }

    /**
     * Get cache key for template.
     *
     * @param string $name Template.
     * @return string
     */
    public function getCacheKey(string $name): string
    {
        return $this->findTemplate($name);
    }

    /**
     * Check if template is still fresh.
     *
     * @param string $name Template.
     * @param int $time Timestamp.
     * @return bool
     */
    public function isFresh(string $name, int $time): bool
    {
        $path = $this->findTemplate($name);

        return filemtime($path) < $time;
    }

    /**
     * Check if we have the source code of a template, given its name.
     *
     * @param string $name The name of the template to check if we can load.
     * @return bool If the template source code is handled by this loader or not.
     */
    public function exists(string $name): bool
    {
        return file_exists($name);
    }

    /**
     * Returns path to template name if exists.
     *
     * @param string $name The name of template to find.
     * @return string
     * @throws \Twig\Error\LoaderError When template doesn't exist
     */
    protected function findTemplate(string $name): string
    {
        if (!$this->exists($name)) {
            throw new LoaderError("Unable to find template `{$name}`.");
        }

        return $name;
    }
}
