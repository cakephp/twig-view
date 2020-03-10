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

namespace Cake\TwigView\Test\TestCase\Shell;

use Cake\Console\ConsoleOptionParser;
use Cake\TestSuite\TestCase;
use Cake\TwigView\Filesystem\Scanner;
use Cake\TwigView\Shell\CompileShell;
use Cake\TwigView\View\TwigView;
use Twig\Environment;
use Twig\Template;
use Twig\TemplateWrapper;

/**
 * Class CompileShell.
 */
class CompileShellTest extends TestCase
{
    public function testAll()
    {
        $twig = $this->prophesize(Environment::class);
        $twig->getExtensions()->shouldBeCalled()->willReturn([]);
        foreach (Scanner::all() as $section => $templates) {
            foreach ($templates as $template) {
                $twig->load($template)->shouldBeCalled()->willReturn(new TemplateWrapper($twig->reveal(), new class ($twig->reveal()) extends Template {
                    public function getTemplateName()
                    {
                    }

                    public function getDebugInfo()
                    {
                    }

                    public function getSourceContext()
                    {
                    }

                    protected function doDisplay(array $context, array $blocks = [])
                    {
                    }
                }));
            }
        }

        $twigView = new TwigView();
        $twigView->setTwig($twig->reveal());

        $shell = new CompileShell();
        $shell->setTwigview($twigView);
        $shell->all();
    }

    public function testPlugin()
    {
        $this->expectException(\Exception::class);

        $twig = $this->prophesize(Environment::class);

        $twigView = new TwigView();
        $twigView->setTwig($twig->reveal());

        $shell = new CompileShell();
        $shell->setTwigview($twigView);

        $shell->plugin('bar:foo');
    }

    public function testFile()
    {
        $twig = $this->prophesize(Environment::class);
        $twig->getExtensions()->shouldBeCalled()->willReturn([]);
        $twig->load('foo:bar')->shouldBeCalled()->willReturn(new TemplateWrapper($twig->reveal(), new class ($twig->reveal()) extends Template {
            public function getTemplateName()
            {
            }

            public function getDebugInfo()
            {
            }

            public function getSourceContext()
            {
            }

            protected function doDisplay(array $context, array $blocks = [])
            {
            }
        }));

        $twigView = new TwigView();
        $twigView->setTwig($twig->reveal());

        $shell = new CompileShell();
        $shell->setTwigview($twigView);

        $shell->file('foo:bar');
    }

    public function testGetOptionParser()
    {
        $this->assertInstanceOf(ConsoleOptionParser::class, (new CompileShell())->getOptionParser());
    }
}
