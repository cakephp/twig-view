<?php
declare(strict_types=1);

/**
 * CakePHP :  Rapid Development Framework (https://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 * @link          https://cakephp.org CakePHP Project
 * @license       https://opensource.org/licenses/mit-license.php MIT License
 */

namespace Cake\TwigView\Test\TestCase\Command;

use Cake\Core\Configure;
use Cake\Routing\Router;
use Cake\TestSuite\ConsoleIntegrationTestTrait;
use Cake\TestSuite\TestCase;

/**
 * Tests CompileCommand.
 */
class CompileCommandTest extends TestCase
{
    use ConsoleIntegrationTestTrait;

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        Router::reload();
        $this->useCommandRunner();
    }

    /**
     * Test passing bad compile type
     *
     * @return void
     */
    public function testMissingType()
    {
        $this->exec('twig-view compile nonsense');
        $this->assertExitError();
        $this->assertErrorContains('is not a valid value for type');
    }

    /**
     * Test file with no file argument
     *
     * @return void
     */
    public function testFileNoArgument()
    {
        $this->exec('twig-view compile file');
        $this->assertExitError();
        $this->assertErrorContains('File name not specified');
    }

    /**
     * Test file
     *
     * @return void
     */
    public function testFile()
    {
        $this->exec('twig-view compile file ' . TEST_APP . DS . 'templates' . DS . 'simple.twig');
        $this->assertExitSuccess();
        $this->assertOutputContains('Compiled');
        $this->assertOutputContains(TEST_APP . DS . 'templates' . DS . 'simple.twig');
    }

    /**
     * Test plugin
     *
     * @return void
     */
    public function testPlugin()
    {
        $this->loadPlugins(['TestTwigView']);

        $this->exec('twig-view compile plugin TestTwigView');
        $this->assertExitSuccess();
        $this->assertOutputContains('Compiled');
        $this->assertOutputContains(TEST_APP . 'plugins' . DS . 'TestTwigView' . DS . 'templates' . DS . 'twig.twig');

        $this->removePlugins(['TestTwigView']);
    }

    /**
     * Test all
     *
     * @return void
     */
    public function testAll()
    {
        $templates = Configure::read('App.paths.templates');
        Configure::write('App.paths.templates', TEST_APP . 'templates' . DS . 'Blog' . DS);

        $this->exec('twig-view compile all');
        $this->assertExitSuccess();
        $this->assertOutputContains('Compiled');
        $this->assertOutputContains(TEST_APP . 'templates' . DS . 'Blog' . DS . 'index.twig');

        Configure::write('App.paths.templates', $templates);
    }
}
