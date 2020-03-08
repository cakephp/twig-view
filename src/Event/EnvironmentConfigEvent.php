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

final class EnvironmentConfigEvent extends Event
{
    public const EVENT = 'TwigView.TwigView.environment';

    /**
     * @var array
     */
    private $config = [];

    /**
     * @param array $config Config array
     * @return static
     */
    public static function create(array $config): EnvironmentConfigEvent
    {
        $event = new static(static::EVENT);
        $event->setConfig($config);

        return $event;
    }

    /**
     * @return array
     */
    public function getConfig(): array
    {
        return $this->config;
    }

    /**
     * @param array $config Config array
     * @return $this
     */
    public function setConfig(array $config)
    {
        /** @psalm-suppress PossiblyNullPropertyAssignmentValue */
        $this->config = array_replace_recursive($this->config, $config);

        return $this;
    }
}
