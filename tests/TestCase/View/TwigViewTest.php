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

namespace Cake\TwigView\Test\TestCase;

use Cake\TestSuite\TestCase;
use TestApp\View\AppView;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

/**
 * Class TwigViewTest.
 */
class TwigViewTest extends TestCase
{
    /**
     * Test rendering simple twig template.
     *
     * @return void
     */
    public function testRenderSimpleTemplate()
    {
        $view = new AppView();
        $output = $view->render('simple', false);

        $this->assertSame('underscore_me', $output);
    }

    /**
     * Test rendering simple twig template with layout.
     *
     * @return void
     */
    public function testRenderSimpleTemplateWithLayout()
    {
        $view = new AppView();
        $output = $view->render('simple');

        $this->assertSame('underscore_me', $output);
    }

    /**
     * Test rendering template with layout.
     *
     * @return void
     */
    public function testRenderLayoutWithElements()
    {
        $view = new AppView();
        $output = $view->render('Blog/index');

        $this->assertSame('blog_entry', $output);
    }

    /**
     * Test rendering template with view block assignment
     *
     * @return void
     */
    public function testRenderLayoutWithViewBlockAssignment()
    {
        $view = new AppView();
        $output = $view->render('Blog/with_extra_block', 'with_extra_block');

        $this->assertSame("main content\nextra content", $output);
    }

    /**
     * Test setting custom View variable name.
     *
     * @return void
     */
    public function testCustomViewVariable()
    {
        $view = new AppView(null, null, null, ['viewVar' => 'myView']);

        $view->assign('title', 'my title');
        $output = $view->render('custom_variable', false);

        $this->assertSame('my title', $output);
    }

    /**
     * Tests using View helper in template.
     *
     * @return void
     */
    public function testHelper()
    {
        $view = new AppView(null, null, null, ['environment' => ['strict_variables' => true]]);
        $view->loadHelper('Text');

        $view->set('sample', 'Sample text.');
        $output = $view->render('helpers', false);

        $this->assertSame("<p>Sample text.</p>\n", $output);
    }

    /**
     * Tests a twig file that throws internal exception throw a Twig exception with message.
     *
     * @return void
     */
    public function testThrowWrappedException()
    {
        $this->expectException(RuntimeError::class);
        $this->expectExceptionMessage('Something is missing');

        $view = new AppView();
        $view->render('exception', false);
    }

    /**
     * Tests invalid twig template throws exception.
     *
     * @return void
     */
    public function testThrowSyntaxError()
    {
        $this->expectException(SyntaxError::class);

        $view = new AppView();
        $view->render('syntaxerror', false);
    }
}
