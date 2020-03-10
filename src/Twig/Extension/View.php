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

use Cake\View\View as CakeView;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * Class View.
 *
 * @internal
 */
final class View extends AbstractExtension
{
    /**
     * View to call methods upon.
     *
     * @var \Cake\View\View
     */
    protected $view;

    /**
     * Constructor.
     *
     * @param \Cake\View\View $view View instance.
     */
    public function __construct(CakeView $view)
    {
        $this->view = $view;
    }

    /**
     * Get declared functions.
     *
     * @return \Twig\TwigFunction[]
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction('elementExists', function ($name) {
                return $this->view->elementExists($name);
            }),
            new TwigFunction('getVars', function () {
                return $this->view->getVars();
            }),
            new TwigFunction('get', function ($var, $default = null) {
                return $this->view->get($var, $default);
            }),
        ];
    }

    /**
     * Get extension name.
     *
     * @return string
     */
    public function getName(): string
    {
        return 'view';
    }
}
