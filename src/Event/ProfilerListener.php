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

use Cake\Event\EventListenerInterface;
use Cake\Event\EventManager;
use Cake\TwigView\Twig\Extension;
use Twig\Profiler\Profile;

/**
 * Class ExtensionsListener.
 */
final class ProfilerListener implements EventListenerInterface
{
    /**
     * Return implemented events.
     *
     * @return array
     */
    public function implementedEvents(): array
    {
        return [
            ConstructEvent::EVENT => 'construct',
        ];
    }

    /**
     * Event handler.
     *
     * @param \Cake\TwigView\Event\ConstructEvent $event Event.
     * @return void
     */
    public function construct(ConstructEvent $event): void
    {
        if ($event->getTwig()->hasExtension(Extension\Profiler::class)) {
            return;
        }

        $profile = new Profile();
        $event->getTwig()->addExtension(new Extension\Profiler($profile));

        EventManager::instance()->dispatch(ProfileEvent::create($profile));
    }
}
