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

use Cake\Core\Configure;
use Cake\Event\EventManager;
use Cake\TestSuite\TestCase;
use Cake\TwigView\Event\ConstructEvent;
use Cake\TwigView\Event\EnvironmentConfigEvent;
use Cake\TwigView\View\TwigView;
use TestApp\Exception\MissingSomethingException;
use TestApp\View\AppView;
use Twig\Environment;
use Twig\Error\SyntaxError;

/**
 * Class TwigViewTest.
 */
class TwigViewTest extends TestCase
{
    public function testInheritance()
    {
        $this->assertInstanceOf('Cake\View\View', new TwigView());
    }

    public function testConstruct()
    {
        $this->_hibernateListeners(ConstructEvent::EVENT);

        $callbackFired = false;
        $eventCallback = function ($event) use (&$callbackFired) {
            self::assertInstanceof(Environment::class, $event->getSubject()->getTwig());
            $callbackFired = true;
        };
        EventManager::instance()->on(ConstructEvent::EVENT, $eventCallback);

        new TwigView();

        EventManager::instance()->off(ConstructEvent::EVENT, $eventCallback);
        $this->_wakeupListeners(ConstructEvent::EVENT);

        $this->assertTrue($callbackFired);
    }

    public function testConstructConfig()
    {
        Configure::write(TwigView::ENV_CONFIG, [
            'true' => true,
        ]);

        $this->_hibernateListeners(EnvironmentConfigEvent::EVENT);

        $callbackFired = false;
        $that = $this;
        $eventCallback = function ($event) use ($that, &$callbackFired) {
            $that->assertIsArray($event->getConfig());
            $that->assertTrue($event->getConfig()['true']);

            $callbackFired = true;
        };
        EventManager::instance()->on(EnvironmentConfigEvent::EVENT, $eventCallback);

        new TwigView();

        EventManager::instance()->off(EnvironmentConfigEvent::EVENT, $eventCallback);
        $this->_wakeupListeners(EnvironmentConfigEvent::EVENT);

        $this->assertTrue($callbackFired);
    }

    public function test_renderPhp()
    {
        $output = 'foo:bar with a beer';
        $filename = 'cakephp';

        $twig = $this->prophesize(Environment::class);

        $view = new TwigView();
        $view->setTwig($twig->reveal());
        $renderedView = $view->render($filename);

        self::assertSame($output, $renderedView);
    }

    /**
     * Tests that a twig file that throws a custom exception correctly renders the thrown exception and not a Twig one.
     */
    public function test_renderTwigCustomException()
    {
        $this->expectException(MissingSomethingException::class);

        $view = new AppView();
        $view->render('exception', false);
    }

    /**
     * Tests that a twig file that throws a Twig exception correctly throws the twig exception and does not get caught
     * byt the modification.
     */
    public function test_renderTwigTwigException()
    {
        $this->expectException(SyntaxError::class);

        $view = new AppView();
        $view->render('syntaxerror', false);
    }

    /**
     * @param $name
     * @return \Cake\TwigView\Test\TestCase\ReflectionMethod
     */
    protected static function getMethod($name)
    {
        $class = new ReflectionClass('Cake\TwigView\View\TwigView');
        $method = $class->getMethod($name);
        $method->setAccessible(true);

        return $method;
    }

    /**
     * @param $name
     * @return \Cake\TwigView\Test\TestCase\ReflectionProperty
     */
    protected static function getProperty($name)
    {
        $class = new ReflectionClass('Cake\TwigView\View\TwigView');
        $property = $class->getProperty($name);
        $property->setAccessible(true);

        return $property;
    }

    protected function _hibernateListeners($eventKey)
    {
        $this->__preservedEventListeners[$eventKey] = EventManager::instance()->listeners($eventKey);

        foreach ($this->__preservedEventListeners[$eventKey] as $eventListener) {
            EventManager::instance()->off($eventListener['callable'], $eventKey);
        }
    }

    protected function _wakeupListeners($eventKey)
    {
        if (isset($this->__preservedEventListeners[$eventKey])) {
            return;
        }

        foreach ($this->__preservedEventListeners[$eventKey] as $eventListener) {
            EventManager::instance()->on(
                $eventListener['callable'],
                $eventKey,
                [
                    'passParams' => $eventListener['passParams'],
                ]
            );
        }

        $this->__preservedEventListeners = [];
    }
}
