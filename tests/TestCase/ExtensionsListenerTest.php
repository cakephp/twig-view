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

use Cake\Core\Configure;
use Cake\TestSuite\TestCase;
use Cake\TwigView\Event\ConstructEvent;
use Cake\TwigView\Event\ExtensionsListener;
use Cake\TwigView\View\TwigView;
use Prophecy\Argument;
use Twig\Environment;
use Twig\Extension\AbstractExtension;
use Twig\Extension\StringLoaderExtension;
use Twig\Extra\Markdown\MarkdownExtension;
use Twig\Extra\Markdown\MarkdownInterface;

/**
 * Class ExtensionsListenerTest.
 */
class ExtensionsListenerTest extends TestCase
{
    public function testImplementedEvents()
    {
        $eventsList = (new ExtensionsListener())->implementedEvents();
        $this->assertIsArray($eventsList);
        $this->assertSame(1, count($eventsList));
    }

    public function testConstruct()
    {
        $twig = $this->prophesize(Environment::class);
        $twig->hasExtension(StringLoaderExtension::class)->shouldBeCalled();
        $twig->addExtension(Argument::type(AbstractExtension::class))->shouldBeCalled();

        $twigView = new TwigView();
        (new ExtensionsListener())->construct(ConstructEvent::create($twigView, $twig->reveal()));
    }

    public function testConstructMarkdownEngine()
    {
        Configure::write(
            'TwigView.markdown.engine',
            $this->prophesize(MarkdownInterface::class)->reveal()
        );

        $twig = $this->prophesize(Environment::class);
        $twig->hasExtension(StringLoaderExtension::class)->shouldBeCalled();
        $twig->addExtension(Argument::type(AbstractExtension::class))->shouldBeCalled();
        $twig->addExtension(Argument::type(MarkdownExtension::class))->shouldBeCalled();
        $twig->addRuntimeLoader(Argument::type('object'))->shouldBeCalled();

        $twigView = new TwigView();
        (new ExtensionsListener())->construct(ConstructEvent::create($twigView, $twig->reveal()));
    }

    public function testConstructNoMarkdownEngine()
    {
        $twig = $this->prophesize(Environment::class);
        $twig->hasExtension(StringLoaderExtension::class)->shouldBeCalled();
        $twig->addExtension(Argument::type(AbstractExtension::class))->shouldBeCalled();
        $twig->addExtension(Argument::type(MarkdownExtension::class))->shouldNotBeCalled();

        $twigView = new TwigView();
        (new ExtensionsListener())->construct(ConstructEvent::create($twigView, $twig->reveal()));
    }

    public function testConstructDebug()
    {
        Configure::write('debug', true);

        $twig = $this->prophesize(Environment::class);
        $twig->hasExtension(StringLoaderExtension::class)->shouldBeCalled();
        $twig->addExtension(Argument::type(AbstractExtension::class))->shouldBeCalled();

        $twigView = new TwigView();
        (new ExtensionsListener())->construct(ConstructEvent::create($twigView, $twig->reveal()));
    }

    public function testConstructDebugFalse()
    {
        Configure::write('debug', false);

        $twig = $this->prophesize(Environment::class);
        $twig->hasExtension(StringLoaderExtension::class)->shouldBeCalled();
        $twig->addExtension(Argument::type(AbstractExtension::class))->shouldBeCalled();

        $twigView = new TwigView();
        (new ExtensionsListener())->construct(ConstructEvent::create($twigView, $twig->reveal()));
    }
}
