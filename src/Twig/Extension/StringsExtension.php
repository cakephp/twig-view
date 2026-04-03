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

use Cake\Utility\Text;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

/**
 * Class StringsExtension.
 */
class StringsExtension extends AbstractExtension
{
    /**
     * Get declared filters.
     *
     * @return array<\Twig\TwigFilter>
     */
    public function getFilters(): array
    {
        return [
            new TwigFilter('substr', 'substr'),
            new TwigFilter('tokenize', Text::class . '::tokenize'),
            new TwigFilter('insert', Text::class . '::insert'),
            new TwigFilter('cleanInsert', Text::class . '::cleanInsert'),
            new TwigFilter('wrap', Text::class . '::wrap'),
            new TwigFilter('wrapBlock', Text::class . '::wrapBlock'),
            new TwigFilter('wordWrap', Text::class . '::wordWrap'),
            new TwigFilter('highlight', Text::class . '::highlight'),
            new TwigFilter('tail', Text::class . '::tail'),
            new TwigFilter('truncate', Text::class . '::truncate'),
            new TwigFilter('excerpt', Text::class . '::excerpt'),
            new TwigFilter('toList', Text::class . '::toList'),
            new TwigFilter('isMultibyte', Text::class . '::isMultibyte'),
            new TwigFilter('utf8', Text::class . '::utf8'),
            new TwigFilter('ascii', Text::class . '::ascii'),
            new TwigFilter('parseFileSize', Text::class . '::parseFileSize'),
            new TwigFilter('none', function (): void {
            }),
        ];
    }

    /**
     * Get declared functions.
     *
     * @return array<\Twig\TwigFunction>
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction('uuid', Text::class . '::uuid'),
            new TwigFunction('sprintf', 'sprintf'),
        ];
    }
}
