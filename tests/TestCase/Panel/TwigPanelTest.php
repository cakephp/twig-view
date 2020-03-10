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

namespace Cake\TwigView\Test\TestCase\Panel;

use Cake\TestSuite\TestCase;
use Cake\TwigView\Filesystem\TreeScanner;
use Cake\TwigView\Panel\TwigPanel;

class TwigPanelTest extends TestCase
{
    public function testData()
    {
        $this->assertSame([
            'templates' => TreeScanner::all(),
        ], (new TwigPanel())->data());
    }
}
