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

namespace Cake\TwigView\Test\TestCase\Twig\Extension;

use Cake\TestSuite\TestCase;
use Twig\TokenParser\TokenParserInterface;
use Twig\TwigFilter;

abstract class AbstractExtensionTest extends TestCase
{
    /**
     * @var \Twig\Extension\AbstractExtensionInterface
     */
    protected $extension;

    public function setUp(): void
    {
        parent::setUp();
    }

    public function tearDown(): void
    {
        parent::tearDown();
    }

    public function testGetTokenParsers()
    {
        $tokenParsers = $this->extension->getTokenParsers();
        $this->assertIsArray($tokenParsers);
        foreach ($tokenParsers as $tokenParser) {
            $this->assertTrue($tokenParser instanceof TokenParserInterface);
        }
    }

    public function testGetNodeVisitors()
    {
        $nodeVisitors = $this->extension->getNodeVisitors();
        $this->assertIsArray($nodeVisitors);
        foreach ($nodeVisitors as $nodeVisitor) {
            $this->assertInstanceOf('Twig_NodeVisitorInterface', $nodeVisitor);
        }
    }

    public function testGetFilters()
    {
        $filters = $this->extension->getFilters();
        $this->assertIsArray($filters);
        foreach ($filters as $filter) {
            $this->assertInstanceOf(TwigFilter::class, $filter);
        }
    }

    protected function getFilter($name)
    {
        $filters = $this->extension->getFilters();
        foreach ($filters as $filter) {
            if ($filter->getName() === $name) {
                return $filter;
            }
        }
    }

    protected function getFunction($name)
    {
        $functions = $this->extension->getFunctions();
        foreach ($functions as $function) {
            if ($function->getName() === $name) {
                return $function;
            }
        }
    }
}
