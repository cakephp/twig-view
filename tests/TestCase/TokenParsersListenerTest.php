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
use Cake\TwigView\Event\ConstructEvent;
use Cake\TwigView\Event\TokenParsersListener;
use Cake\TwigView\View\TwigView;
use Prophecy\Argument;
use Twig\Environment;
use Twig\TokenParser\IncludeTokenParser;

/**
 * Class TokenParserListenerTest.
 */
class TokenParsersListenerTest extends TestCase
{
    public function testImplementedEvents()
    {
        $eventsList = (new TokenParsersListener())->implementedEvents();
        $this->assertIsArray($eventsList);
        $this->assertSame(1, count($eventsList));
    }

    public function testConstruct()
    {
        $twig = $this->prophesize(Environment::class);
        $twig->addTokenParser(Argument::type(IncludeTokenParser::class))->shouldBeCalled();

        $twigView = new TwigView();
        (new TokenParsersListener())->construct(ConstructEvent::create($twigView, $twig->reveal()));
    }
}
