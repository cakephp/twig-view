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

namespace Cake\TwigView\Test\TestCase\Twig;

use Cake\TestSuite\TestCase;
use Cake\TwigView\Twig\Loader;
use Twig\Error\LoaderError;

/**
 * Class LoaderTest.
 */
class LoaderTest extends TestCase
{
    /**
     * @var Loader
     */
    protected $Loader;

    public function setUp(): void
    {
        parent::setUp();

        $this->loadPlugins(['TestTwigView']);

        $this->Loader = new Loader();
    }

    public function tearDown(): void
    {
        unset($this->Loader);

        $this->removePlugins(['TestTwigView']);

        parent::tearDown();
    }

    public function testGetSource()
    {
        $this->assertSame('TwigView', $this->Loader->getSource('TestTwigView.twig'));
        $this->assertSame('TwigView', $this->Loader->getSource('TestTwigView.twig.twig'));
    }

    public function testGetSourceNonExistingFile()
    {
        $this->expectException(LoaderError::class);

        $this->Loader->getSource('TestTwigView.no_twig');
    }

    public function testGetCacheKeyNoPlugin()
    {
        $this->assertSame(
            TEST_APP . 'templates/simple.twig',
            $this->Loader->getCacheKey('simple')
        );
    }

    public function testGetCacheKeyPlugin()
    {
        $this->assertSame(
            TEST_APP . 'plugins/TestTwigView/templates/twig.twig',
            $this->Loader->getCacheKey('TestTwigView.twig')
        );
        $this->assertSame(
            TEST_APP . 'plugins/TestTwigView/templates/twig.twig',
            $this->Loader->getCacheKey('TestTwigView.twig.twig')
        );
    }

    public function testGetCacheKeyPluginNonExistingFile()
    {
        $this->expectException(LoaderError::class);

        $this->Loader->getCacheKey('TestTwigView.twog');
    }

    public function testIsFresh()
    {
        file_put_contents(TMP . 'TwigViewIsFreshTest', 'TwigViewIsFreshTest');
        $time = filemtime(TMP . 'TwigViewIsFreshTest');

        $this->assertTrue($this->Loader->isFresh(TMP . 'TwigViewIsFreshTest', $time + 5));
        $this->assertTrue(!$this->Loader->isFresh(TMP . 'TwigViewIsFreshTest', $time - 5));

        unlink(TMP . 'TwigViewIsFreshTest');
    }

    public function testIsFreshNonExistingFile()
    {
        $this->expectException(LoaderError::class);

        $this->Loader->isFresh(TMP . 'foobar' . time(), time());
    }
}
