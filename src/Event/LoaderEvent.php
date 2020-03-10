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
use Twig\Loader\LoaderInterface;

final class LoaderEvent extends Event
{
    public const EVENT = 'TwigView.TwigView.loader';

    /**
     * @param \Twig\Loader\LoaderInterface $loader LoaderInterface instance.
     * @return static
     */
    public static function create(LoaderInterface $loader): LoaderEvent
    {
        return new static(static::EVENT, $loader, [
            'loader' => $loader,
        ]);
    }

    /**
     * @return \Twig\Loader\LoaderInterface
     */
    public function getLoader(): LoaderInterface
    {
        return $this->getSubject();
    }

    /**
     * @return \Twig\Loader\LoaderInterface
     */
    public function getResultLoader(): LoaderInterface
    {
        if ($this->result instanceof LoaderInterface) {
            return $this->result;
        }

        if (is_array($this->result) && $this->result['loader'] instanceof LoaderInterface) {
            return $this->result['loader'];
        }

        return $this->getLoader();
    }
}
