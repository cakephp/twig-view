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
use Twig\Extra\Markdown\DefaultMarkdown;

/**
 * Class TwigViewTest.
 */
class TwigViewTest extends TestCase
{
    /**
     * @var \TestApp\View\AppView
     */
    protected $view;

    public function setUp(): void
    {
        parent::setUp();

        $this->view = new AppView();
    }

    /**
     * Test rendering simple twig template.
     *
     * @return void
     */
    public function testRenderSimpleTemplate()
    {
        $output = $this->view->render('simple', false);

        $this->assertSame('underscore_me', $output);
    }

    /**
     * Test rendering simple twig template with layout.
     *
     * @return void
     */
    public function testRenderSimpleTemplateWithLayout()
    {
        $output = $this->view->render('simple');

        $this->assertSame('underscore_me', $output);
    }

    /**
     * Test rendering template with layout.
     *
     * @return void
     */
    public function testRenderLayoutWithElements()
    {
        $output = $this->view->render('Blog/index');

        $this->assertSame('<p>blog_entry</p>', $output);
    }

    /**
     * Test rendering template with view block assignment
     *
     * @return void
     */
    public function testRenderLayoutWithViewBlockAssignment()
    {
        $output = $this->view->render('Blog/with_extra_block', 'with_extra_block');

        $this->assertSame("main content\nextra content", $output);
    }

    public function testRenderWithPluginElement()
    {
        $this->loadPlugins(['TestTwigView']);

        $output = $this->view->render('plugin', false);
        $this->assertSame('from plugin', $output);

        $this->removePlugins(['TestTwigView']);
    }

    /**
     * Tests rendering a cell.
     *
     * @return void
     */
    public function testRenderCell()
    {
        $output = $this->view->render('cell', false);
        $this->assertSame('<b>10</b>', $output);
    }

    /**
     * Tests that rendering a cell doesn't create a new Twig Environment.
     *
     * @return void
     */
    public function testCellsShareTwig()
    {
        $cell = $this->view->cell('Test');
        $this->assertSame($this->view->getTwig(), $cell->createView(AppView::class)->getTwig());
    }

    /**
     * Tests rendering with markdown.
     *
     * @return void;
     */
    public function testMarkdownExtensionDefault()
    {
        AppView::destroyTwig();

        $view = new AppView(null, null, null, ['markdown' => 'default']);
        $output = $this->view->render('markdown', false);
        $this->assertSame("<h1>Title</h1>\n", $output);

        AppView::destroyTwig();
    }

    /**
     * Tests rendering with markdown.
     *
     * @return void;
     */
    public function testMarkdownExtensionCustom()
    {
        AppView::destroyTwig();

        $view = new AppView(null, null, null, ['markdown' => new DefaultMarkdown()]);
        $output = $this->view->render('markdown', false);
        $this->assertSame("<h1>Title</h1>\n", $output);

        AppView::destroyTwig();
    }

    /**
     * Tests deprecated element and cell tags render.
     *
     * @return void
     */
    public function testDeprecatedTags()
    {
        $this->deprecated(function () {
            $output = $this->view->render('deprecated_tags', false);
            $this->assertSame("<b>10</b>\nblog_entry", $output);
        });
    }

    /**
     * Tests missing variables throw exception in debug.
     *
     * @return void
     */
    public function testMissingVariableThrowsError()
    {
        $this->expectException(RuntimeError::class);
        $this->view->render('missing_variable', false);
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

        $this->view->render('exception', false);
    }

    /**
     * Tests invalid twig template throws exception.
     *
     * @return void
     */
    public function testThrowSyntaxError()
    {
        $this->expectException(SyntaxError::class);

        $this->view->render('syntaxerror', false);
    }

    public function testHelperFunction()
    {
        $view = new AppView(null, null, null, [
            'viewVars' => ['elementVar' => 'var echoed inside element'],
        ]);

        $output = $view->render('helper_test', false);

        $expected = "var echoed inside element\n<p>I love CakePHP</p>\n";
        $this->assertSame($expected, $output);
    }
}
