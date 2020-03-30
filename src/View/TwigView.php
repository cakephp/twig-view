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
use Cake\Core\Plugin;
use Cake\TwigView\Twig\Extension;
use Cake\TwigView\Twig\Loader;
use Cake\TwigView\Twig\TokenParser;
use Cake\View\Exception\MissingLayoutException;
use Cake\View\Exception\MissingTemplateException;
use Cake\View\View;
use Jasny\Twig\ArrayExtension;
use Jasny\Twig\DateExtension;
use Jasny\Twig\PcreExtension;
use Jasny\Twig\TextExtension;
use Twig\Environment;
use Twig\Extension\DebugExtension;
use Twig\Extension\StringLoaderExtension;
use Twig\Extra\Markdown\MarkdownExtension;
use Twig\Extra\Markdown\MarkdownInterface;
use Twig\Extra\Markdown\MarkdownRuntime;
use Twig\Loader\LoaderInterface;
use Twig\Profiler\Profile;
use Twig\RuntimeLoader\RuntimeLoaderInterface;

/**
 * Class TwigView.
 */
class TwigView extends View
{
    public const EXT = '.twig';

    public const ENV_CONFIG = 'TwigView.environment';

    /**
     * @inheritDoc
     */
    protected $_ext = '.twig';

    /**
     * List of extensions searched when loading templates.
     *
     * @var string[]
     */
    protected $extensions = [
        '.twig',
    ];

    /**
     * @var \Twig\Environment
     */
    protected $twig;

    /**
     * @var \Twig\Profiler\Profile
     */
    protected $profile;

    /**
     * Initialize view.
     *
     * @return void
     */
    public function initialize(): void
    {
        parent::initialize();

        $this->twig = new Environment($this->createLoader(), $this->createEnvironmentConfig());
        $this->initializeTokenParser();
        $this->initializeExtensions();

        if (Configure::read('debug') && Plugin::isLoaded('DebugKit')) {
            $this->initializeProfiler();
        }
    }

    /**
     * Get Twig Environment instance.
     *
     * @return \Twig\Environment
     */
    public function getTwig(): Environment
    {
        return $this->twig;
    }

    /**
     * Gets Twig Profile if profiler enabled.
     *
     * @return \Twig\Profiler\Profile
     */
    public function getProfile(): Profile
    {
        return $this->profile;
    }

    /**
     * Creates the Twig LoaderInterface instance.
     *
     * @return \Twig\Loader\LoaderInterface
     */
    protected function createLoader(): LoaderInterface
    {
        return new Loader();
    }

    /**
     * Creates the Twig Environment configuration.
     *
     * @return array
     */
    protected function createEnvironmentConfig(): array
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

        return $config;
    }

    /**
     * Adds custom Twig token parsers.
     *
     * @return void
     */
    protected function initializeTokenParser(): void
    {
        $this->twig->addTokenParser(new TokenParser\CellParser());
        $this->twig->addTokenParser(new TokenParser\ElementParser());
    }

    // phpcs:disable CakePHP.Commenting.FunctionComment.InvalidReturnVoid

    /**
     * Adds Twig extensions.
     *
     * @return void
     */
    protected function initializeExtensions(): void
    {
        // Twig core extensions
        $this->twig->addExtension(new StringLoaderExtension());
        $this->twig->addExtension(new DebugExtension());

        // CakePHP bridging extensions
        $this->twig->addExtension(new Extension\ArraysExtension());
        $this->twig->addExtension(new Extension\BasicExtension());
        $this->twig->addExtension(new Extension\ConfigureExtension());
        $this->twig->addExtension(new Extension\I18nExtension());
        $this->twig->addExtension(new Extension\InflectorExtension());
        $this->twig->addExtension(new Extension\NumberExtension());
        $this->twig->addExtension(new Extension\StringsExtension());
        $this->twig->addExtension(new Extension\TimeExtension());
        $this->twig->addExtension(new Extension\UtilsExtension());

        // Markdown extension
        if (Configure::read('TwigView.markdown.engine') instanceof MarkdownInterface) {
            $engine = Configure::read('TwigView.markdown.engine');
            $this->twig->addExtension(new MarkdownExtension());

            $this->twig->addRuntimeLoader(new class ($engine) implements RuntimeLoaderInterface {
                /**
                 * @var \Twig\Extra\Markdown\MarkdownInterface
                 */
                private $engine;

                /**
                 * @param \Twig\Extra\Markdown\MarkdownInterface $engine MarkdownInterface instance
                 */
                public function __construct(MarkdownInterface $engine)
                {
                    $this->engine = $engine;
                }

                /**
                 * @param string $class FQCN
                 * @return object|null
                 */
                public function load($class)
                {
                    if ($class === MarkdownRuntime::class) {
                        return new MarkdownRuntime($this->engine);
                    }

                    return null;
                }
            });
        }

        // jasny/twig-extensions
        $this->twig->addExtension(new DateExtension());
        $this->twig->addExtension(new ArrayExtension());
        $this->twig->addExtension(new PcreExtension());
        $this->twig->addExtension(new TextExtension());
    }

    // phpcs:enable

    /**
     * Initializes Twig profiler extension.
     *
     * @return void
     */
    protected function initializeProfiler(): void
    {
        $this->profile = new Profile();
        $this->twig->addExtension(new Extension\ProfilerExtension($this->profile));
    }

    /**
     * @inheritDoc
     */
    protected function _render(string $templateFile, array $data = []): string
    {
        $data = array_merge(
            empty($data) ? $this->viewVars : $data,
            iterator_to_array($this->helpers()->getIterator()),
            [
                '_view' => $this,
            ]
        );

        return $this->getTwig()->load($templateFile)->render($data);
    }

    /**
     * @inheritDoc
     */
    protected function _getTemplateFileName(?string $name = null): string
    {
        foreach ($this->extensions as $extension) {
            $this->_ext = $extension;
            try {
                return parent::_getTemplateFileName($name);
            } catch (MissingTemplateException $exception) {
                $missingException = $exception;
            }
        }

        throw $missingException ?? new MissingTemplateException($name ?? $this->getTemplate());
    }

    /**
     * @inheritDoc
     */
    protected function _getLayoutFileName(?string $name = null): string
    {
        foreach ($this->extensions as $extension) {
            $this->_ext = $extension;
            try {
                return parent::_getLayoutFileName($name);
            } catch (MissingLayoutException $exception) {
                $missingException = $exception;
            }
        }

        throw $missingException ?? new MissingLayoutException($name ?? $this->getLayout());
    }

    /**
     * @inheritDoc
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
