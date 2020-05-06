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

namespace Cake\TwigView\View;

/**
 * Wraps TwigView with ajax layout and ajax response type.
 */
class TwigAjaxView extends TwigView
{
    /**
     * @inheritDoc
     */
    protected $layout = 'ajax';

    /**
     * @inheritDoc
     */
    public function initialize(): void
    {
        $this->response = $this->response->withType('ajax');
    }
}
