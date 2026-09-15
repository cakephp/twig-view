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
use function is_string;

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
                '--paths=' . TEST_APP . 'templates' . DS . 'i18n ' .
                '--output=' . $this->path . DS,
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

    /**
     * testExtractWithoutLocations method
     */
    public function testExtractWithoutLocations(): void
    {
        $this->exec(
            'i18n extract ' .
            '--extract-core=no ' .
            '--no-location=true ' .
            '--exclude=Pages,Layout ' .
            '--paths=' . TEST_APP . 'templates' . DS . ' ' .
            '--output=' . $this->path . DS,
        );
        $this->assertExitSuccess();
        $this->assertFileExists($this->path . DS . 'default.pot');

        $result = file_get_contents($this->path . DS . 'default.pot');

        $pattern = '/\n\#: .*\n/';
        $this->assertDoesNotMatchRegularExpression($pattern, $result);
    }

    /**
     * test extract can read more than one path.
     */
    public function testExtractMultiplePaths(): void
    {
        $this->exec(
            'i18n extract ' .
            '--extract-core=no ' .
            '--exclude=Pages,Layout ' .
            '--paths=' . TEST_APP . 'templates/Pages,' .
                TEST_APP . 'templates/Posts,' .
                TEST_APP . 'templates/i18n ' .
            '--output=' . $this->path . DS,
        );
        $this->assertExitSuccess();
        $result = file_get_contents($this->path . DS . 'default.pot');

        $pattern = '/msgid "Add User"/';
        $this->assertMatchesRegularExpression($pattern, $result);
    }

    /**
     * Test that the extract shell overwrites existing files with the overwrite parameter
     */
    public function testExtractOverwrite(): void
    {
        file_put_contents($this->path . DS . 'default.pot', 'will be overwritten');
        $this->assertFileExists($this->path . DS . 'default.pot');
        $original = file_get_contents($this->path . DS . 'default.pot');

        $this->exec(
            'i18n extract ' .
            '--extract-core=no ' .
            '--overwrite ' .
            '--paths=' . TEST_APP . 'templates/ ' .
            '--output=' . $this->path . DS,
        );
        $this->assertExitSuccess();

        $result = file_get_contents($this->path . DS . 'default.pot');
        $this->assertNotEquals($original, $result);
    }

    /**
     *  Test that the extract shell scans the core libs
     */
    public function testExtractCore(): void
    {
        $this->exec(
            'i18n extract ' .
            '--extract-core=yes ' .
            '--paths=' . TEST_APP . '/ ' .
            '--output=' . $this->path . DS,
        );
        $this->assertNotNull($this->_err);
        $this->assertEmpty($this->_err->messages(), 'Should not have output to stderr');
        $this->assertExitSuccess();

        $this->assertFileExists($this->path . DS . 'cake.pot');
        $result = file_get_contents($this->path . DS . 'cake.pot');
        $this->assertTrue(is_string($result));

        $pattern = '/#: Console\/Templates\//';
        $this->assertDoesNotMatchRegularExpression($pattern, $result);

        $pattern = '/#: Test\//';
        $this->assertDoesNotMatchRegularExpression($pattern, $result);
    }

    /**
     * Test when marker-error option is set
     * When marker-error is unset, it's already test
     * with other functions like testExecute that not detects error because err never called
     */
    public function testMarkerErrorSets(): void
    {
        $this->exec(
            'i18n extract ' .
            '--marker-error ' .
            '--merge=no ' .
            '--extract-core=no ' .
            '--paths=' . TEST_APP . 'templates/Pages ' .
            '--output=' . $this->path . DS,
        );
        $this->assertExitSuccess();
        $this->assertErrorContains('Invalid marker content in');
        $this->assertErrorContains('extract.php');
    }

    /**
     * Test extraction of Label attribute strings from enum cases.
     */
    public function testExtractLabelAttributes(): void
    {
        $this->exec(
            'i18n extract ' .
            '--merge=no ' .
            '--extract-core=no ' .
            '--paths=' . TEST_APP . 'src/Model/Enum ' .
            '--output=' . $this->path . DS,
        );
        $this->assertExitSuccess();
        $this->assertFileExists($this->path . DS . 'default.pot');
        $result = file_get_contents($this->path . DS . 'default.pot');

        $this->assertStringContainsString('msgid "Published"', $result);
        $this->assertStringContainsString('msgid "Unpublished"', $result);

        $pattern = '/msgctxt "article_status"\nmsgid "Archived"/';
        $this->assertMatchesRegularExpression($pattern, $result);
    }

    /**
     * test relative-paths option
     */
    public function testExtractWithRelativePaths(): void
    {
        $this->exec(
            'i18n extract ' .
            '--extract-core=no ' .
            '--paths=' . TEST_APP . 'templates ' .
            '--output=' . $this->path . DS,
        );
        $this->assertExitSuccess();
        $this->assertFileExists($this->path . DS . 'default.pot');
        $result = file_get_contents($this->path . DS . 'default.pot');

        $expected = '#: ./tests/test_app/templates/Pages/extract.php:';
        $this->assertStringContainsString($expected, $result);
    }

    /**
     * test invalid path options
     */
    public function testExtractWithInvalidPaths(): void
    {
        $this->exec(
            'i18n extract ' .
            '--extract-core=no ' .
            '--paths=' . TEST_APP . 'templates,' . TEST_APP . 'unknown ' .
            '--output=' . $this->path . DS,
        );
        $this->assertExitSuccess();
        $this->assertFileExists($this->path . DS . 'default.pot');
        $result = file_get_contents($this->path . DS . 'default.pot');

        $expected = '#: ./tests/test_app/templates/Pages/extract.php:';
        $this->assertStringContainsString($expected, $result);
    }

    /**
     * Test with associative arrays in App.path.locales and App.path.templates.
     */
    public function testExtractWithAssociativePaths(): void
    {
        Configure::write('App.paths', [
            'plugins' => ['customKey' => TEST_APP . 'plugins' . DS],
            'templates' => ['customKey' => TEST_APP . 'templates' . DS],
            'locales' => ['customKey' => TEST_APP . 'resources' . DS . 'locales' . DS],
        ]);

        $this->exec(
            'i18n extract ' .
            '--merge=no ' .
            '--extract-core=no ',
            [
                // Sending two empty inputs so \Cake\Command\I18nExtractCommand::_getPaths()
                // loops through all paths
                $this->path,
                '',
                'D',
                $this->path . DS,
            ],
        );
        $this->assertExitSuccess();
        $this->assertFileExists($this->path . DS . 'default.pot');
        $result = file_get_contents($this->path . DS . 'default.pot');

        $expected = '#: ./tests/test_app/templates/Pages/extract.php:';
        $this->assertStringContainsString($expected, $result);
    }
}
