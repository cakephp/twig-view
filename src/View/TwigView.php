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

namespace Cake\TwigView\View;

use Cake\Core\Configure;
use Cake\TwigView\Event\ConstructEvent;
use Cake\TwigView\Event\EnvironmentConfigEvent;
use Cake\TwigView\Event\LoaderEvent;
use Cake\TwigView\Twig\Loader;
use Cake\View\View;
use Exception;
use Twig\Environment;
use Twig\Loader\LoaderInterface;

/**
 * Class TwigView.
 */
class TwigView extends View
{
    public const EXT = '.twig';

    public const ENV_CONFIG = 'TwigView.environment';

    /**
     * Extension to use.
     *
     * @var string
     */
    protected $_ext = self::EXT;

    /**
     * @var string[]
     */
    protected $extensions = [
        self::EXT,
        '.php',
    ];

    /**
     * Twig instance.
     *
     * @var \Twig\Environment
     */
    protected $twig;

    /**
     * Return empty string when View instance is cast to string.
     *
     * @return string
     */
    public function __toString(): string
    {
        return '';
    }

    /**
     * Initialize view.
     *
     * @return void
     */
    public function initialize(): void
    {
        $this->twig = new Environment($this->getLoader(), $this->resolveConfig());

        $this->getEventManager()->dispatch(ConstructEvent::create($this, $this->twig));

        $this->_ext = self::EXT;

        parent::initialize();
    }

    /**
     * @param string $extension Extension.
     * @return void
     */
    public function unshiftExtension(string $extension): void
    {
        array_unshift($this->extensions, $extension);
    }

    /**
     * Get twig environment instance.
     *
     * @return \Twig\Environment
     */
    public function getTwig(): Environment
    {
        return $this->twig;
    }

    /**
     * @return array
     */
    protected function resolveConfig(): array
    {
        $debug = Configure::read('debug', false);

        $config = Configure::read(static::ENV_CONFIG, []);
        $config += [
            'charset' => Configure::read('App.encoding', 'UTF-8'),
            'debug' => $debug,
            'cache' => $debug ? false : CACHE . 'twigView' . DS,
        ];

        if ($config['cache'] === true) {
            $config['cache'] = CACHE . 'twigView' . DS;
        }

        $configEvent = EnvironmentConfigEvent::create($config);
        $this->getEventManager()->dispatch($configEvent);

        return $configEvent->getConfig();
    }

    /**
     * Create the template loader.
     *
     * @return \Twig\Loader\LoaderInterface
     */
    protected function getLoader(): LoaderInterface
    {
        $event = LoaderEvent::create(new Loader());
        $this->getEventManager()->dispatch($event);

        return $event->getResultLoader();
    }

    /**
     * Render the template.
     *
     * @param string $viewFile Template file.
     * @param array $data Data that can be used by the template.
     * @throws \Exception
     * @return string
     */
    protected function _render(string $viewFile, array $data = []): string
    {
        if (empty($data)) {
            $data = $this->viewVars;
        }

        if (substr($viewFile, -3) === 'php') {
            $out = parent::_render($viewFile, $data);
        } else {
            $data = array_merge(
                $data,
                iterator_to_array($this->helpers()->getIterator()),
                [
                    '_view' => $this,
                ]
            );

            try {
                $out = $this->getTwig()->load($viewFile)->render($data);
            } catch (Exception $e) {
                $previous = $e->getPrevious();

                if ($previous !== null && $previous instanceof Exception) {
                    throw $previous;
                } else {
                    throw $e;
                }
            }
        }

        return $out;
    }

    /**
     * @param string|null $name Template name.
     * @throws \Exception
     * @return string
     */
    protected function _getTemplateFileName(?string $name = null): string
    {
        $rethrow = new Exception('You\'re not supposed to get here');
        foreach ($this->extensions as $extension) {
            $this->_ext = $extension;
            try {
                return parent::_getTemplateFileName($name);
            } catch (Exception $exception) {
                $rethrow = $exception;
            }
        }

        throw $rethrow;
    }

    /**
     * @param string|null $name Layout name.
     * @throws \Exception
     * @return string
     */
    protected function _getLayoutFileName(?string $name = null): string
    {
        $rethrow = new Exception('You\'re not supposed to get here');
        foreach ($this->extensions as $extension) {
            $this->_ext = $extension;
            try {
                return parent::_getLayoutFileName($name);
            } catch (Exception $exception) {
                $rethrow = $exception;
            }
        }

        throw $rethrow;
    }

    /**
     * @param string $name Element name.
     * @param bool $pluginCheck Whether to check within plugin.
     * @return string|false
     */
    protected function _getElementFileName(string $name, bool $pluginCheck = true)
    {
        foreach ($this->extensions as $extension) {
            $this->_ext = $extension;
            $result = parent::_getElementFileName($name, $pluginCheck);
            if ($result !== false) {
                return $result;
            }
        }

        return false;
    }
}
