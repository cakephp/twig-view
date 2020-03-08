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

namespace Cake\TwigView\Test\TestCase\Event;

use Cake\TestSuite\TestCase;
use Cake\TwigView\Event\LoaderEvent;
use Cake\TwigView\Twig\Loader;
use Twig\Loader\LoaderInterface;

class LoaderEventTest extends TestCase
{
    public function testArrayResultLoader()
    {
        $loader = new Loader();
        $loader2 = $this->prophesize(LoaderInterface::class)->reveal();
        $event = LoaderEvent::create($loader);
        $event->setResult([
            'loader' => $loader2,
        ]);
        $this->assertEquals($loader2, $event->getResultLoader());
    }

    public function testResultLoader()
    {
        $loader = new Loader();
        $loader2 = $this->prophesize(LoaderInterface::class)->reveal();
        $event = LoaderEvent::create($loader);
        $event->setResult($loader2);
        $this->assertEquals($loader2, $event->getResultLoader());
    }

    public function testLoader()
    {
        $loader = new Loader();
        $event = LoaderEvent::create($loader);
        $this->assertEquals($loader, $event->getResultLoader());
    }
}
