<?php
/**
 * CakePHP TwigView Plugin - Example Configuration
 *
 * Copy the relevant settings to your application's config/app.php or config/app_local.php.
 *
 * @copyright Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 * @link      https://cakephp.org CakePHP(tm) Project
 * @license   https://opensource.org/licenses/mit-license.php MIT License
 */

return [
    /**
     * TwigView Plugin Configuration
     *
     * These settings control the behavior of the TwigView plugin.
     */
    'TwigView' => [
        /**
         * Use underscore command names.
         *
         * When `true`, registers `twig_view compile` (with underscore).
         * When `false` or not set, registers `twig-view compile` (deprecated, with hyphen).
         *
         * Upgrade path:
         * 1. Update your scripts/CI to use `twig_view compile`
         * 2. Set this to `true`
         */
        'useUnderscoreCommands' => true,
    ],
];
