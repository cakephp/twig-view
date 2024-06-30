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

namespace Cake\TwigView\Test\TestCase\View;

use Cake\Core\Configure;
use Cake\I18n\Date;
use Cake\I18n\DateTime;
use Cake\I18n\I18n;
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

        Configure::write('App.encoding', 'UTF-8');

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
     * Test rendering template with view block assignment.
     *
     * @return void
     */
    public function testRenderLayoutWithViewBlockAssignment()
    {
        $output = $this->view->render('Blog/with_extra_block', 'with_extra_block');

        $this->assertSame("main content\nextra content", $output);
    }

    /**
     * Tests setting layout from template.
     *
     * @return void
     */
    public function testLayoutFromTemplate()
    {
        $output = $this->view->render('set_layout');

        $this->assertSame("custom\nset layout", $output);
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
     * Test that Cake date/time objects are formatted correctly
     */
    public function testTwigDateFormat()
    {
        $restore = I18n::getLocale();
        I18n::setLocale('fr');

        $this->view->set('date', new Date('2024-06-24'));
        $this->view->set('datetime', new DateTime('2024-06-24 12:13:14'));

        $output = $this->view->render('date_format', false);
        I18n::setLocale($restore);

        $expected = <<<TEXT
Date: 2024/06/24
Datetime: 2024/06/24 12:13:14
TEXT;
        $this->assertSame($expected, $output);
    }

    /**
     * Tests rendering with markdown.
     *
     * @return void;
     */
    public function testMarkdownExtensionDefault()
    {
        AppView::destroyTwig();

        new AppView(null, null, null, ['markdown' => 'default']);
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

        new AppView(null, null, null, ['markdown' => new DefaultMarkdown()]);
        $output = $this->view->render('markdown', false);
        $this->assertSame("<h1>Title</h1>\n", $output);

        AppView::destroyTwig();
    }

    /**
     * Tests templates loaded from Twig functions like
     * extends and include() work.
     *
     * @return void
     */
    public function testTwigInclude()
    {
        $this->loadPlugins(['TestTwigView']);

        $output = $this->view->render('test_include', false);
        $this->assertSame('underscore_me', $output);

        $this->removePlugins(['TestTwigView']);
    }

    /**
     * Tests extends loads templates from root templates paths.
     *
     * @return void
     */
    public function testTwigExtendsRootPath()
    {
        $view = new AppView(null, null, null, ['templatePath' => 'Blog']);
        $output = $view->render('blog_with_extends');
        $this->assertSame('base from subdir/base', $output);
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

        $output = $view->render('helper', false);

        $expected = "var echoed inside element\n<p>I love CakePHP</p>\n";
        $this->assertSame($expected, $output);
    }

    public function testPluginHelperFunction()
    {
        $this->loadPlugins(['TestTwigView']);

        $view = new AppView(null, null, null, [
            'helpers' => ['TestTwigView.Output'],
        ]);

        $expected = "from OutputHelper\nfrom OutputHelper";
        $this->assertSame($expected, $view->render('plugin_helper', false));

        $view = new AppView();
        $view->helpers()->load('TestTwigView.Output');

        $expected = "from OutputHelper\nfrom OutputHelper";
        $this->assertSame($expected, $view->render('plugin_helper', false));

        $this->clearPlugins();
    }
}
