<?php
declare(strict_types=1);

/**
 * CakePHP(tm) : Rapid Development Framework (https://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 * @link          https://cakephp.org CakePHP(tm) Project
 * @since         1.0.3
 * @license       https://opensource.org/licenses/mit-license.php MIT License
 */

namespace Cake\TwigView\Test\TestCase;

use Cake\Http\Response;
use Cake\TestSuite\TestCase;
use TestApp\View\AjaxView;

/**
 * Class TwigAjaxViewTest.
 */
class TwigAjaxViewTest extends TestCase
{
    /**
     * @var \TestApp\View\AppView
     */
    protected $view;

    public function setUp(): void
    {
        parent::setUp();

        $this->view = new AjaxView();
    }

    /**
     * Tests TwigAjaxView configures ajax properly.
     *
     * @return void
     */
    public function testConfiguration()
    {
        $this->assertSame('ajax', $this->view->getLayout());
        $this->assertEquals((new Response(['type' => 'ajax']))->getType(), $this->view->getResponse()->getType());
    }
}
