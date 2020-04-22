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
use Cake\TwigView\Twig\FileLoader;
use Cake\TwigView\View\TwigView;
use Twig\Error\LoaderError;

/**
 * Class FileLoaderTest.
 */
class FileLoaderTest extends TestCase
{
    /**
     * @var Cake\TwigView\Twig\FileLoader
     */
    protected $loader;

    public function setUp(): void
    {
        parent::setUp();

        $this->loadPlugins(['TestTwigView']);

        $this->loader = new FileLoader(new TwigView());
    }

    public function tearDown(): void
    {
        unset($this->loader);

        $this->removePlugins(['TestTwigView']);

        parent::tearDown();
    }

    public function testGetSource()
    {
        $source = $this->loader->getSourceContext(TEST_APP . DS . 'templates' . DS . 'simple.twig');
        $this->assertSame("{{ 'UnderscoreMe'|underscore }}", $source->getCode());
    }

    public function testGetSourceNonExistingFile()
    {
        $this->expectException(LoaderError::class);

        $this->loader->getSourceContext('TestTwigView.no_twig');
    }

    public function testGetCacheKey()
    {
        $this->assertSame(
            TEST_APP . 'templates/simple.twig',
            $this->loader->getCacheKey(TEST_APP . 'templates/simple.twig')
        );
    }

    public function testGetCacheKeyPluginNonExistingFile()
    {
        $this->expectException(LoaderError::class);

        $this->loader->getCacheKey('TestTwigView.twog');
    }

    public function testIsFresh()
    {
        file_put_contents(TMP . 'TwigViewIsFreshTest', 'TwigViewIsFreshTest');
        $time = filemtime(TMP . 'TwigViewIsFreshTest');

        $this->assertTrue($this->loader->isFresh(TMP . 'TwigViewIsFreshTest', $time + 5));
        $this->assertTrue(!$this->loader->isFresh(TMP . 'TwigViewIsFreshTest', $time - 5));

        unlink(TMP . 'TwigViewIsFreshTest');
    }

    public function testIsFreshNonExistingFile()
    {
        $this->expectException(LoaderError::class);

        $this->loader->isFresh(TMP . 'foobar' . time(), time());
    }
}
