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

use Cake\TwigView\View\TwigView;
use Cake\View\Exception\MissingTemplateException;
use Twig\Error\LoaderError;
use Twig\Loader\LoaderInterface;
use Twig\Source;

/**
 * Template file loader.
 *
 * Supports loading files from template paths and plugins.
 */
class FileLoader implements LoaderInterface
{
    /**
     * @var \Cake\TwigView\View\TwigView
     */
    protected $twigView;

    /**
     * @param \Cake\TwigView\View\TwigView $twigView TwigView instance.
     */
    public function __construct(TwigView $twigView)
    {
        $this->twigView = $twigView;
    }

    /**
     * @inheritDoc
     */
    public function getSourceContext(string $name): Source
    {
        $path = $this->findTemplate($name);

        return new Source(file_get_contents($path), $name, $path);
    }

    /**
     * @inheritDoc
     */
    public function getCacheKey(string $name): string
    {
        return $this->findTemplate($name);
    }

    /**
     * @inheritDoc
     */
    public function isFresh(string $name, int $time): bool
    {
        $path = $this->findTemplate($name);

        return filemtime($path) < $time;
    }

    /**
     * @inheritDoc
     */
    public function exists(string $name)
    {
        try {
            $this->findTemplate($name);
        } catch (MissingTemplateException $e) {
            return false;
        }

        return true;
    }

    /**
     * @param string $name Template name
     * @return string
     */
    public function findTemplate(string $name): string
    {
        if (file_exists($name)) {
            return $name;
        }

        try {
            $path = $this->twigView->resolveTemplatePath($name);
        } catch (MissingTemplateException $e) {
            throw new LoaderError($e->getMessage());
        }

        return $path;
    }
}
