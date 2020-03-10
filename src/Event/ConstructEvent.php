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

namespace Cake\TwigView\Event;

use Cake\Event\Event;
use Cake\TwigView\View\TwigView;
use Twig\Environment;

final class ConstructEvent extends Event
{
    public const EVENT = 'TwigView.TwigView.construct';

    /**
     * @param \Cake\TwigView\View\TwigView $twigView TwigView instance.
     * @param \Twig\Environment $twig Twig environment instance.
     * @return static
     */
    public static function create(TwigView $twigView, Environment $twig): ConstructEvent
    {
        return new static(static::EVENT, $twigView, [
            'twigView' => $twigView,
            'twig' => $twig,
        ]);
    }

    /**
     * @return \Cake\TwigView\View\TwigView
     */
    public function getTwigView(): TwigView
    {
        return $this->getData()['twigView'];
    }

    /**
     * @return \Twig\Environment
     */
    public function getTwig(): Environment
    {
        return $this->getData()['twig'];
    }
}
