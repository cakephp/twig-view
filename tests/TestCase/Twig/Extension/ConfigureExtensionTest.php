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

use Cake\TwigView\Twig\Extension\ConfigureExtension;

class ConfigureExtensionTest extends AbstractExtensionTest
{
    public function setUp(): void
    {
        parent::setUp();
        $this->extension = new ConfigureExtension();
    }

    public function testFunctionConfig()
    {
        $callable = $this->getFunction('config')->getCallable();

        $result = call_user_func($callable, 'foo');
        $this->assertNull($result);

        $result = call_user_func($callable, 'debug');
        $this->assertIsBool($result);
    }
}
