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

namespace Cake\TwigView\Twig\Extension;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use function Cake\Core\deprecationWarning;

/**
 * Class UtilsExtension.
 */
class UtilsExtension extends AbstractExtension
{
    /**
     * Get declared filters.
     *
     * @return array<\Twig\TwigFilter>
     */
    public function getFilters(): array
    {
        return [
            new TwigFilter('serialize', function (string $value): mixed {
                deprecationWarning('5.0.2', 'Usage of serialize in templates deprecated.');

                return serialize($value);
            }),
            new TwigFilter('unserialize', function (string $value): mixed {
                deprecationWarning('5.0.2', 'unserialize is deprecated. Its usage creates arbitrary object deserialization issues');

                return unserialize($value, ['allowed_classes' => false]);
            }),
            new TwigFilter('md5', 'md5'),
            new TwigFilter('base64_encode', 'base64_encode'),
            new TwigFilter('base64_decode', 'base64_decode'),
            new TwigFilter('string', function ($str): string {
                return (string)$str;
            }),
        ];
    }
}
