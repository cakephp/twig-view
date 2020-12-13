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

use Cake\TwigView\Twig\Extension\BasicExtension;

class BasicExtensionTest extends AbstractExtensionTest
{
    public function setUp(): void
    {
        $this->extension = new BasicExtension();
        parent::setUp();
    }

    public function testDeprecatedDebug()
    {
        $callable = $this->getFilter('debug')->getCallable();

        $this->expectDeprecation();
        call_user_func_array($callable, ['test']);
    }

    public function testDeprecatedPr()
    {
        $callable = $this->getFilter('pr')->getCallable();

        $this->expectDeprecation();
        call_user_func_array($callable, ['test']);
    }

    public function testDeprecatedLow()
    {
        $this->deprecated(function () {
            $callable = $this->getFilter('low')->getCallable();
            $this->assertSame('test', call_user_func_array($callable, ['TEST']));
        });
    }

    public function testDeprecatedUp()
    {
        $this->deprecated(function () {
            $callable = $this->getFilter('up')->getCallable();
            $this->assertSame('TEST', call_user_func_array($callable, ['test']));
        });
    }

    public function testDeprecatedCount()
    {
        $callable = $this->getFilter('count')->getCallable();

        $this->expectDeprecation();
        call_user_func_array($callable, ['test']);
    }

    public function testCount()
    {
        $this->deprecated(function () {
            $array = ['a', 'b', 'c'];
            $callable = $this->getFilter('count')->getCallable();
            $result = call_user_func_array($callable, [$array]);
            $this->assertSame(3, $result);
        });
    }
}
