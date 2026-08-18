<?php
declare(strict_types=1);

/**
 * CakePHP :  Rapid Development Framework (https://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 * @link          https://cakephp.org CakePHP Project
 * @since         1.2.0
 * @license       https://opensource.org/licenses/mit-license.php MIT License
 */

namespace Cake\TwigView\Test\TestCase\Command;

use Cake\Console\TestSuite\ConsoleIntegrationTestTrait;
use Cake\Core\Configure;
use Cake\Routing\Router;
use Cake\TestSuite\TestCase;
use Cake\Utility\Filesystem;

/**
 * I18nExtractCommandTest
 */
class I18nExtractCommandTest extends TestCase
{
    use ConsoleIntegrationTestTrait;

    /**
     * @var string
     */
    protected $path;

    /**
     * setUp method
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->path = TMP . 'tests/extract_task_test';
        $fs = new Filesystem();
        $fs->deleteDir($this->path);
        $fs->mkdir($this->path . DS . 'locale');

        Router::reload();
        Configure::write('App.encoding', 'UTF-8');

        $this->loadPlugins(['Cake/TwigView']);
        $this->setAppNamespace();
    }

    /**
     * tearDown method
     */
    protected function tearDown(): void
    {
        parent::tearDown();

        $fs = new Filesystem();
        $fs->deleteDir($this->path);
        $this->clearPlugins();
    }

    /**
     * testExecute method
     */
    public function testExecute(): void
    {
        $this->exec(
            'i18n extract ' .
                '--merge=no ' .
                '--extract-core=no ' .
                '--paths=' . TEST_APP . 'templates' . DS . 'i18n ' . DS .
                '--output=' . $this->path . DS,
            [
                $this->path,
            ],
        );
        $this->assertExitSuccess();
        $this->assertFileExists($this->path . DS . 'default.pot');
        $this->assertFileExists($this->path . DS . 'test.pot');
        $this->assertFileDoesNotExist($this->path . DS . 'cake.pot');

        $result = file_get_contents($this->path . DS . 'default.pot');

        // The additional "./tests/test_app" is just due to the wonky folder structure of the test app.
        // In a regular app the path would start with "./templates".

        // no_domain
        $pattern = '@(\#: \./tests/test_app/templates/i18n/i18n_test\.twig:\d+\n)+';
        $pattern .= 'msgid "no_domain"@';
        $this->assertMatchesRegularExpression($pattern, $result);

        // no_domain, var
        $pattern = '@(\#: \./tests/test_app/templates/i18n/i18n_test\.twig:\d+\n)+';
        $pattern .= 'msgid "no_domain_with_\{var\}"@';
        $this->assertMatchesRegularExpression($pattern, $result);

        // no_domain, var, context
        $pattern = '@(\#: \./tests/test_app/templates/i18n/i18n_test\.twig:\d+\n)+';
        $pattern .= 'msgctxt "Context"\n';
        $pattern .= 'msgid "no_domain_with_context_with_\{var\}"@';
        $this->assertMatchesRegularExpression($pattern, $result);

        // no_domain, plural
        $pattern = '@(\#: \./tests/test_app/templates/i18n/i18n_test\.twig:\d+\n)+';
        $pattern .= 'msgid "no_domain_singular"\nmsgid_plural "no_domain_plural"@';
        $this->assertMatchesRegularExpression($pattern, $result);

        // no_domain, plural, context
        $pattern = '@(\#: \./tests/test_app/templates/i18n/i18n_test\.twig:\d+\n)+';
        $pattern .= 'msgctxt "Context"\n';
        $pattern .= 'msgid "no_domain_singular_with_context"\nmsgid_plural "no_domain_plural_with_context"@';
        $this->assertMatchesRegularExpression($pattern, $result);

        // no_domain, plural, var
        $pattern = '@(\#: \./tests/test_app/templates/i18n/i18n_test\.twig:\d+\n)+';
        $pattern .= 'msgid "no_domain_singular_with_\{var\}"\nmsgid_plural "no_domain_plural_with_\{var\}"@';
        $this->assertMatchesRegularExpression($pattern, $result, 'No duplicate msgid');

        // no_domain, plural, var, context
        $pattern = '@(\#: \./tests/test_app/templates/i18n/i18n_test\.twig:\d+\n)+';
        $pattern .= 'msgctxt "Context"\n';
        $pattern .= 'msgid "no_domain_singular_with_context_with_\{var\}"\nmsgid_plural "no_domain_plural_with_context_with_\{var\}"@';
        $this->assertMatchesRegularExpression($pattern, $result, 'No duplicate msgid');

        $this->assertStringContainsString('msgid "double \\"quoted\\""', $result, 'Strings with quotes not handled correctly');
        $this->assertStringContainsString("msgid \"single 'quoted'\"", $result, 'Strings with quotes not handled correctly');

        // test.pot
        $result = file_get_contents($this->path . DS . 'test.pot');

        // test_domain
        $pattern = '@(\#: \./tests/test_app/templates/i18n/i18n_test\.twig:\d+\n)+';
        $pattern .= 'msgid "test_domain"@';
        $this->assertMatchesRegularExpression($pattern, $result);

        // test_domain, var
        $pattern = '@(\#: \./tests/test_app/templates/i18n/i18n_test\.twig:\d+\n)+';
        $pattern .= 'msgid "test_domain_with_{var}"@';
        $this->assertMatchesRegularExpression($pattern, $result);

        // test_domain, var, context
        $pattern = '@(\#: \./tests/test_app/templates/i18n/i18n_test\.twig:\d+\n)+';
        $pattern .= 'msgctxt "Context"\n';
        $pattern .= 'msgid "test_domain_with_context_with_\{var\}"@';
        $this->assertMatchesRegularExpression($pattern, $result);

        // test_domain, plural
        $pattern = '@(\#: \./tests/test_app/templates/i18n/i18n_test\.twig:\d+\n)+';
        $pattern .= 'msgid "test_domain_singular"\nmsgid_plural "test_domain_plural"@';
        $this->assertMatchesRegularExpression($pattern, $result);

        // test_domain, plural, context
        $pattern = '@(\#: \./tests/test_app/templates/i18n/i18n_test\.twig:\d+\n)+';
        $pattern .= 'msgctxt "Context"\n';
        $pattern .= 'msgid "test_domain_singular_with_context"\nmsgid_plural "test_domain_plural_with_context"@';
        $this->assertMatchesRegularExpression($pattern, $result);

        // test_domain, plural, var
        $pattern = '@(\#: \./tests/test_app/templates/i18n/i18n_test\.twig:\d+\n)+';
        $pattern .= 'msgid "test_domain_singular_with_\{var\}"\nmsgid_plural "test_domain_plural_with_\{var\}"@';
        $this->assertMatchesRegularExpression($pattern, $result, 'No duplicate msgid');

        // test_domain, plural, var, context
        $pattern = '@(\#: \./tests/test_app/templates/i18n/i18n_test\.twig:\d+\n)+';
        $pattern .= 'msgctxt "Context"\n';
        $pattern .= 'msgid "test_domain_singular_with_context_with_\{var\}"\nmsgid_plural "test_domain_plural_with_context_with_\{var\}"@';
        $this->assertMatchesRegularExpression($pattern, $result, 'No duplicate msgid');

        $this->assertStringContainsString('msgid "double \\"quoted\\""', $result, 'Strings with quotes not handled correctly');
        $this->assertStringContainsString("msgid \"single 'quoted'\"", $result, 'Strings with quotes not handled correctly');
    }

    /**
     * testExecute with no paths
     */
    public function testExecuteNoOutputOption(): void
    {
        $this->exec(
            'i18n extract ' .
                '--merge=no ' .
                '--extract-core=no ' .
                '--paths=' . TEST_APP . 'templates' . DS . 'i18n ',
            [
                $this->path,
                TEST_APP . 'templates' . DS . 'i18n' . DS,
                'D',
            ],
        );
        $this->assertExitSuccess();
        $this->assertFileExists($this->path . DS . 'default.pot');
    }

    /**
     * testExecute with merging on method
     */
    public function testExecuteMerge(): void
    {
        $this->exec(
            'i18n extract ' .
                '--merge=yes ' .
                '--extract-core=no ' .
                '--paths=' . TEST_APP . 'templates' . DS . 'i18n ' .
                '--output=' . $this->path . DS,
            [
                $this->path,
            ],
        );
        $this->assertExitSuccess();
        $this->assertFileExists($this->path . DS . 'default.pot');
        $this->assertFileDoesNotExist($this->path . DS . 'cake.pot');
        $this->assertFileDoesNotExist($this->path . DS . 'domain.pot');
    }

    /**
     * test exclusions
     */
    public function testExtractWithExclude(): void
    {
        $this->exec(
            'i18n extract ' .
                '--extract-core=no ' .
                '--exclude=Pages,Layout ' .
                '--paths=' . TEST_APP . 'templates' . DS . ' ' .
                '--output=' . $this->path . DS,
        );
        $this->assertExitSuccess();
        $this->assertFileExists($this->path . DS . 'default.pot');
        $result = file_get_contents($this->path . DS . 'default.pot');

        $pattern = '/\#: .*extract\.php:\d+\n/';
        $this->assertDoesNotMatchRegularExpression($pattern, $result);

        $pattern = '/\#: .*default\.php:\d+\n/';
        $this->assertDoesNotMatchRegularExpression($pattern, $result);
    }
}
