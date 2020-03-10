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
use Twig\Profiler\Profile;

final class ProfileEvent extends Event
{
    public const EVENT = 'TwigView.TwigView.profile';

    /**
     * @param \Twig\Profiler\Profile $profile Profile instance.
     * @return static
     */
    public static function create(Profile $profile): ProfileEvent
    {
        return new static(static::EVENT, $profile);
    }

    /**
     * @return \Twig\Profiler\Profile
     */
    public function getLoader(): Profile
    {
        return $this->getSubject();
    }
}
