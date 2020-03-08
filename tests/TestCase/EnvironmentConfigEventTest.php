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
use Cake\TwigView\Event\EnvironmentConfigEvent;

class EnvironmentConfigEventTest extends TestCase
{
    public function testCreate()
    {
        $config = [
            'foo' => 'bar',
        ];
        $event = EnvironmentConfigEvent::create($config);
        $this->assertEquals($config, $event->getConfig());
    }

    public function testSetConfig()
    {
        $event = EnvironmentConfigEvent::create([
            'foo' => 'bar',
            'beer' => 'crate',
            'baz' => [
                'oof' => 'rab',
                'foo' => 'bar',
            ],
        ]);
        $event->setConfig([
            'foo' => 'rab',
            'baz' => [
                'oof' => 'beer',
            ],
        ]);
        $this->assertEquals([
            'foo' => 'rab',
            'beer' => 'crate',
            'baz' => [
                'oof' => 'beer',
                'foo' => 'bar',
            ],
        ], $event->getConfig());
    }
}
