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

namespace Cake\TwigView;

use Cake\Core\BasePlugin;
use Cake\Core\Configure;
use Cake\Core\Plugin as CorePlugin;
use Cake\Core\PluginApplicationInterface;
use Cake\Event\EventManager;

/**
 * Plugin class for Cake\TwigView.
 */
class Plugin extends BasePlugin
{
    /**
     * Load routes or not
     *
     * @var bool
     */
    protected $routesEnabled = false;

    /**
     * Load all the plugin configuration and bootstrap logic.
     *
     * @param \Cake\Core\PluginApplicationInterface $app The host application
     * @return void
     */
    public function bootstrap(PluginApplicationInterface $app): void
    {
        EventManager::instance()->on(new Event\ExtensionsListener());
        EventManager::instance()->on(new Event\TokenParsersListener());

        if (Configure::read('debug') && CorePlugin::isLoaded('DebugKit')) {
            Configure::write('DebugKit.panels', array_merge(
                (array)Configure::read('DebugKit.panels'),
                [
                    'Cake/TwigView.Twig',
                ]
            ));
            EventManager::instance()->on(new Event\ProfilerListener());
        }
    }
}
