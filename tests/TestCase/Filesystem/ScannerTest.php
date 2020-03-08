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

namespace Cake\TwigView\Test\TestCase\Filesystem;

use Cake\Routing\Router;
use Cake\TestSuite\TestCase;
use Cake\TwigView\Filesystem\Scanner;

/**
 * Class ScannerTest.
 */
class ScannerTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        Router::reload();
        $this->loadPlugins(['TestTwigView']);
    }

    public function tearDown(): void
    {
        $this->removePlugins(['TestTwigView']);

        parent::tearDown();
    }

    public function testAll()
    {
        $this->assertSame([
            'APP' => [
                TEST_APP . 'templates' . DS . 'Blog' . DS . 'index.twig',
                TEST_APP . 'templates' . DS . 'element' . DS . 'element.twig',
                TEST_APP . 'templates' . DS . 'exception.twig',
                TEST_APP . 'templates' . DS . 'layout.twig',
                TEST_APP . 'templates' . DS . 'layout' . DS . 'layout.twig',
                TEST_APP . 'templates' . DS . 'syntaxerror.twig',
            ],
            //'Bake' => Scanner::plugin('Bake'),
            'TestTwigView' => [
                TEST_APP . 'plugins' . DS . 'TestTwigView' . DS . 'templates' . DS . 'Controller' . DS . 'Component' . DS . 'magic.twig',
                TEST_APP . 'plugins' . DS . 'TestTwigView' . DS . 'templates' . DS . 'Controller' . DS . 'index.twig',
                TEST_APP . 'plugins' . DS . 'TestTwigView' . DS . 'templates' . DS . 'Controller' . DS . 'view.twig',
                TEST_APP . 'plugins' . DS . 'TestTwigView' . DS . 'templates' . DS . 'twig.twig',
            ],
        ], Scanner::all());
    }

    public function testPlugin()
    {
        $this->assertSame([
            TEST_APP . 'plugins' . DS . 'TestTwigView' . DS . 'templates' . DS . 'Controller' . DS . 'Component' . DS . 'magic.twig',
            TEST_APP . 'plugins' . DS . 'TestTwigView' . DS . 'templates' . DS . 'Controller' . DS . 'index.twig',
            TEST_APP . 'plugins' . DS . 'TestTwigView' . DS . 'templates' . DS . 'Controller' . DS . 'view.twig',
            TEST_APP . 'plugins' . DS . 'TestTwigView' . DS . 'templates' . DS . 'twig.twig',
        ], Scanner::plugin('TestTwigView'));
    }
}
