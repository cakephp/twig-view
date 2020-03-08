<?php
declare(strict_types=1);

/**
 * This file is part of TwigView.
 *
 ** (c) 2015 Cees-Jan Kiewiet
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cake\TwigView\Test\TestCase\Filesystem;

use Cake\Routing\Router;
use Cake\TestSuite\TestCase;
use Cake\TwigView\Filesystem\Scanner;

/**
 * Class ScannerTest.
 * @package Cake\TwigView\Test\TestCase\Twig
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
