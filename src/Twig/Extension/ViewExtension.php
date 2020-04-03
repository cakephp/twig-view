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
 * @since         1.0.0
 * @license       https://opensource.org/licenses/mit-license.php MIT License
 */

namespace Cake\TwigView\Twig\Extension;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * Class ViewExtension.
 */
class ViewExtension extends AbstractExtension
{
    /**
     * Get declared functions.
     *
     * @return \Twig\TwigFunction[]
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction(
                'helper_*_*',
                function ($context, $helper, $method, array $args = []) {
                    return $context['_view']->{$helper}->{$method}(...$args);
                },
                ['needs_context' => true, 'is_variadic' => true, 'is_safe' => ['all']]
            ),
        ];
    }

    /**
     * Get extension name.
     *
     * @return string
     */
    public function getName(): string
    {
        return 'twigview-view';
    }
}