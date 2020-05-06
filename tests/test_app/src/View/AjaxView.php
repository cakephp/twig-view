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
 * @since         1.0.3
 * @license       https://opensource.org/licenses/mit-license.php MIT License
 */

namespace TestApp\View;

use Cake\TwigView\View\TwigAjaxView;

class AjaxView extends TwigAjaxView
{
    /**
     * Initialization hook method.
     *
     * Loads the necessary helper and properly configures them.
     */
    public function initialize(): void
    {
        parent::initialize();
        $this->loadHelper('TestSecond');
    }

    /**
     * Clear internal Twig instances for testing.
     *
     * @return void
     */
    public static function destroyTwig(): void
    {
        static::$profile = null;
        static::$twig = null;
    }
}
