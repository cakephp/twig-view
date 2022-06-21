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

/**
 * Tests boostrap
 */

use Cake\Cache\Cache;
use Cake\Core\Configure;
use Cake\Core\Plugin;
use Cake\Datasource\ConnectionManager;
use Cake\Filesystem\Filesystem;

require dirname(__DIR__) . '/vendor/autoload.php';
require dirname(__DIR__) . '/vendor/cakephp/cakephp/src/basics.php';

define('ROOT', dirname(__DIR__));
define('CORE_PATH', ROOT . DS . 'vendor/cakephp/cakephp');
define('APP', sys_get_temp_dir());
define('TMP', sys_get_temp_dir() . '/TwigViewTmp/');
define('CACHE', sys_get_temp_dir() . '/TwigViewTmp/cache/');
define('PLUGIN_REPO_ROOT', dirname(__DIR__) . DS);
define('TEST_APP', PLUGIN_REPO_ROOT . 'tests/test_app/');
define('CONFIG', TEST_APP . 'config' . DS);

$fs = new Filesystem();
$fs->mkdir(TMP . 'cache/models', 0777);
$fs->mkdir(TMP . 'cache/persistent', 0777);
$fs->mkdir(TMP . 'cache/views', 0777);

Configure::write('debug', true);
if (!getenv('db_dsn')) {
    putenv('db_dsn=sqlite:///:memory:');
}
ConnectionManager::setConfig('test', ['url' => getenv('db_dsn')]);

Plugin::getCollection()->add(new \Cake\TwigView\Plugin());

Configure::write('App', [
    'namespace' => 'TestApp',
    'paths' => [
        'plugins' => [TEST_APP . 'plugins' . DS],
        'templates' => [TEST_APP . 'templates' . DS],
    ],
]);

$cache = [
    'default' => [
        'engine' => 'File',
    ],
    '_cake_core_' => [
        'className' => 'File',
        'prefix' => '_cake_core_',
        'path' => CACHE . 'persistent/',
        'serialize' => true,
        'duration' => '+10 seconds',
    ],
];

Cache::setConfig($cache);

Configure::write(
    'Error.ignoredDeprecationPaths',
    ['vendor/cakephp/cakephp/src/TestSuite/Fixture/FixtureInjector.php']
);
