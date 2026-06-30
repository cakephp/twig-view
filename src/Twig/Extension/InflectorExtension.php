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

use Cake\Utility\Inflector;
use Cake\Utility\Text;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

/**
 * Class InflectorExtension.
 */
class InflectorExtension extends AbstractExtension
{
    /**
     * Get filters for this extension.
     *
     * @return array<\Twig\TwigFilter>
     */
    public function getFilters(): array
    {
        return [
            new TwigFilter('pluralize', Inflector::class . '::pluralize'),
            new TwigFilter('singularize', Inflector::class . '::singularize'),
            new TwigFilter('camelize', Inflector::class . '::camelize'),
            new TwigFilter('underscore', Inflector::class . '::underscore'),
            new TwigFilter('humanize', Inflector::class . '::humanize'),
            new TwigFilter('tableize', Inflector::class . '::tableize'),
            new TwigFilter('classify', Inflector::class . '::classify'),
            new TwigFilter('variable', Inflector::class . '::variable'),
            new TwigFilter('dasherize', Inflector::class . '::dasherize'),
            new TwigFilter('slug', Text::class . '::slug'),
        ];
    }
}
