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

use Cake\TwigView\Twig\Extension\UtilsExtension;
use TestApp\GadgetMarker;
use Twig\Environment;
use Twig\Loader\ArrayLoader;

class UtilsExtensionTest extends AbstractExtensionTest
{
    public function setUp(): void
    {
        parent::setUp();
        $this->extension = new UtilsExtension();
    }

    public function testUnserializePreventObject(): void
    {
        $this->skipIf(PHP_VERSION_ID < 80300, 'Requires PHP8.3 or higher');

        $twig = new Environment(new ArrayLoader([
            // {% set %} so we exercise the filter without stringifying the result.
            'object' => '{% set _ = payload|unserialize %}(rendered)',
            'array' => '{{ (payload|unserialize)["role"] }}',
        ]));
        $twig->addExtension(new UtilsExtension());

        // 1) Object payload: does a gadget's magic method run?
        GadgetMarker::$woken = false;
        $this->deprecated(function () use ($twig): void {
            $twig->render('object', ['payload' => serialize(new GadgetMarker())]);
            $this->assertFalse(GadgetMarker::$woken, 'Should not have modified GadgetMarker');

            $out = $twig->render('array', ['payload' => serialize(['role' => 'editor'])]);
            $this->assertStringContainsString('editor', $out);
        });
    }
}
