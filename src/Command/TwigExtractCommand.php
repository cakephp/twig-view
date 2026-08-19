<?php
declare(strict_types=1);

/**
 * CakePHP(tm) : Rapid Development Framework (https://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 * @link          https://cakephp.org CakePHP(tm) Project
 * @since         1.2.0
 * @license       https://opensource.org/licenses/mit-license.php MIT License
 */

namespace Cake\TwigView\Command;

use Cake\Console\Helper\ProgressHelper;
use Cake\Command\I18nExtractCommand;
use Cake\Console\Arguments;
use Cake\Console\ConsoleIo;
use Cake\Utility\Filesystem;
use Twig\Environment;
use Twig\Loader\ArrayLoader;
use Twig\Source;
use Twig\Token;
use function count; // Imports the global function
use function in_array; // Imports the global function
use function is_array; // Imports the global function

/**
 * Language string extractor
 */
class TwigExtractCommand extends I18nExtractCommand
{
    /**
     * @inheritDoc
     */
    public static function defaultName(): string
    {
        return 'i18n extract';
    }

    /**
     * @inheritDoc
     */
    public static function getDescription(): string
    {
        return 'Extract i18n POT files from application files and twig templates.';
    }

    /**
     * Extract tokens out of all files to be processed
     *
     * @param \Cake\Console\Arguments $args The io instance
     * @param \Cake\Console\ConsoleIo $io The io instance
     * @return void
     */
    protected function _extractTokens(Arguments $args, ConsoleIo $io): void
    {
        $progress = $io->helper('Progress');
        assert($progress instanceof ProgressHelper);
        $progress->init(['total' => count($this->_files)]);
        $isVerbose = $args->getOption('verbose');

        $functions = [
            '__' => ['singular'],
            '__n' => ['singular', 'plural'],
            '__d' => ['domain', 'singular'],
            '__dn' => ['domain', 'singular', 'plural'],
            '__x' => ['context', 'singular'],
            '__xn' => ['context', 'singular', 'plural'],
            '__dx' => ['domain', 'context', 'singular'],
            '__dxn' => ['domain', 'context', 'singular', 'plural'],
        ];
        $pattern = '/(' . implode('|', array_keys($functions)) . ')\s*\(/';

        foreach ($this->_files as $file) {
            $this->_file = $file;
            if ($isVerbose) {
                $io->verbose(sprintf('Processing %s...', $file));
            }
            if (pathinfo($file, PATHINFO_EXTENSION) === 'twig') {
                if ($this->_isTwigUsable()) {
                    $_parser = 'twig';
                } else {
                    $io->warning('Twig is not installed. Please install Twig to extract translations from twig templates.');
                    continue;
                }
                $_parser = 'twig';
            } else {
                $_parser = 'php';
            }
            $code = (string)file_get_contents($file);

            if (preg_match($pattern, $code) === 1) {
                if ($_parser === 'twig') {
                    $this->_tokenizeAsTwig($code, $file);
                } else {
                    $this->_tokenizeAsPHP($code, $file);
                }

                foreach ($functions as $functionName => $map) {
                    if ($_parser === 'twig') {
                        $this->_parseAsTwig($io, $functionName, $map);
                    } else {
                        $this->_parseAsPHP($io, $functionName, $map);
                    }
                }
            }
            if ($_parser === 'php') {
                $this->extractFileReflection($file, $code);
            }

            if (!$isVerbose) {
                $progress->increment(1);
                $progress->draw();
            }
        }
    }

    /**
     * Parse tokens
     *
     * @param \Cake\Console\ConsoleIo $io The io instance
     * @param string $functionName Function name that indicates translatable string (e.g: '__')
     * @param array $map Array containing what variables it will find (e.g: domain, singular, plural)
     * @return void
     */
    protected function _parseAsPHP(ConsoleIo $io, string $functionName, array $map): void
    {
        $count = 0;
        $tokenCount = count($this->_tokens);

        while ($tokenCount - $count > 1) {
            $countToken = $this->_tokens[$count];
            $firstParenthesis = $this->_tokens[$count + 1];
            if (!is_array($countToken)) {
                $count++;
                continue;
            }

            [$type, $string, $line] = $countToken;
            if (($type === T_STRING) && ($string === $functionName) && ($firstParenthesis === '(')) {
                $position = $count;
                $depth = 0;

                while (!$depth) {
                    if ($this->_tokens[$position] === '(') {
                        $depth++;
                    } elseif ($this->_tokens[$position] === ')') {
                        $depth--;
                    }
                    $position++;
                }

                $mapCount = count($map);
                $strings = $this->_getStrings($position, $mapCount);

                if ($mapCount === count($strings)) {
                    $singular = '';
                    $vars = array_combine($map, $strings);
                    extract($vars);
                    $domain ??= 'default';
                    $details = [
                        'file' => $this->_file,
                        'line' => $line,
                    ];
                    $details['file'] = '.' . str_replace(ROOT, '', $details['file']);
                    if (isset($plural)) {
                        $details['msgid_plural'] = $plural;
                    }
                    if (isset($context)) {
                        $details['msgctxt'] = $context;
                    }
                    $this->_addTranslation($domain, $singular, $details);
                } else {
                    $this->_markerError($io, $this->_file, $line, $functionName, $count);
                }
            }
            $count++;
        }
    }

    /**
     * Parse Twig tokens
     *
     * @param \Cake\Console\ConsoleIo $io The io instance
     * @param string $functionName Function name that indicates translatable string (e.g: '__')
     * @param array $map Array containing what variables it will find (e.g: domain, singular, plural)
     * @return void
     */
    protected function _parseAsTwig(ConsoleIo $io, string $functionName, array $map): void
    {
        /** @var \Twig\Token $token */
        foreach ($this->_tokens as $count => $token) {
            if ($token->test(Token::NAME_TYPE, $functionName)) {
                $singular = '';
                switch ($functionName) {
                    case '__':
                        $singular = $this->_getStringFromToken($count, 2);
                        break;
                    case '__n':
                        $singular = $this->_getStringFromToken($count, 2);
                        $plural = $this->_getStringFromToken($count, 4);
                        break;
                    case '__d':
                        $domain = $this->_getStringFromToken($count, 2);
                        $singular = $this->_getStringFromToken($count, 4);
                        break;
                    case '__dn':
                        $domain = $this->_getStringFromToken($count, 2);
                        $singular = $this->_getStringFromToken($count, 4);
                        $plural = $this->_getStringFromToken($count, 6);
                        break;
                    case '__x':
                        $context = $this->_getStringFromToken($count, 2);
                        $singular = $this->_getStringFromToken($count, 4);
                        break;
                    case '__xn':
                        $context = $this->_getStringFromToken($count, 2);
                        $singular = $this->_getStringFromToken($count, 4);
                        $plural = $this->_getStringFromToken($count, 6);
                        break;
                    case '__dx':
                        $domain = $this->_getStringFromToken($count, 2);
                        $context = $this->_getStringFromToken($count, 4);
                        $singular = $this->_getStringFromToken($count, 6);
                        break;
                    case '__dxn':
                        $domain = $this->_getStringFromToken($count, 2);
                        $context = $this->_getStringFromToken($count, 4);
                        $singular = $this->_getStringFromToken($count, 6);
                        $plural = $this->_getStringFromToken($count, 8);
                        break;
                }
                $domain ??= 'default';
                $details = [
                    'file' => $this->_file,
                    'line' => $token->getLine(),
                ];
                $details['file'] = '.' . str_replace(ROOT, '', $details['file']);
                if (in_array('plural', $map)) {
                    if (isset($plural)) {
                        $details['msgid_plural'] = $plural;
                    } else {
                        $this->_markerError($io, $this->_file, $token->getLine(), $functionName, $token->getOffset() ?? 0);
                        continue;
                    }
                }

                if (in_array('context', $map)) {
                    if (isset($context)) {
                        $details['msgctxt'] = $context;
                    } else {
                        $this->_markerError($io, $this->_file, $token->getLine(), $functionName, $token->getOffset() ?? 0);
                        continue;
                    }
                }
                $this->_addTranslation($domain, $singular, $details);
            }
        }
    }

    /**
     * Search files that may contain translatable strings
     *
     * @return void
     */
    protected function _searchFiles(): void
    {
        $pattern = false;
        if ($this->_exclude) {
            $exclude = [];
            foreach ($this->_exclude as $e) {
                if (DIRECTORY_SEPARATOR !== '\\' && !str_starts_with($e, DIRECTORY_SEPARATOR)) {
                    $e = DIRECTORY_SEPARATOR . $e;
                }
                $exclude[] = preg_quote($e, '/');
            }
            $pattern = '/' . implode('|', $exclude) . '/';
        }

        foreach ($this->_paths as $path) {
            $path = realpath($path);
            if ($path === false) {
                continue;
            }
            $path .= DIRECTORY_SEPARATOR;
            $fs = new Filesystem();
            $files = $fs->findRecursive($path, '/\.php$|\.twig$/');
            $files = array_keys(iterator_to_array($files));
            sort($files);
            if ($pattern) {
                $files = preg_grep($pattern, $files, PREG_GREP_INVERT) ?: [];
                $files = array_values($files);
            }
            $this->_files = array_merge($this->_files, $files);
        }
        $this->_files = array_unique($this->_files);
    }

    /**
     * Checks whether the Twig templating system is available.
     *
     * @return bool true if Twig is autoloadable and usable, false otherwise
     */
    protected function _isTwigUsable(): bool
    {
        return class_exists(Environment::class);
    }

    /**
     * Parses the given PHP source code for tokens, filtering out whitespace and inline HTML.
     *
     * @param string $code Source code of the file to parse
     * @param string $file File name and path of the file to parse
     * @return void
     */
    protected function _tokenizeAsPHP(string $code, string $file): void
    {
        $allTokens = token_get_all($code);
        $this->_tokens = [];
        foreach ($allTokens as $token) {
            if (!is_array($token) || ($token[0] !== T_WHITESPACE && $token[0] !== T_INLINE_HTML)) {
                $this->_tokens[] = $token;
            }
        }
        unset($allTokens);
    }

    /**
     * Parses the given Twig source code for tokens.
     *
     * @param string $code Source code of the file to parse
     * @param string $file File name and path of the file to parse
     * @return void
     */
    protected function _tokenizeAsTwig(string $code, string $file): void
    {
        $twig = new Environment(new ArrayLoader());
        $stream = $twig->tokenize(new Source(code: $code, name: $file, path: $file));
        $this->_tokens = [];
        while (!$stream->isEOF()) {
            $token = $stream->next();
            if (! $token->test(Token::TEXT_TYPE) && ! $token->test(Token::BLOCK_END_TYPE)) {
                $this->_tokens[] = $token;
            }
        }
        unset($stream);
    }

    /**
     * Return the string represented by a token and offset
     *
     * It also escapes double quotes with backslash: " -> \"
     *
     * @param int $position The position of the token in $this->_tokens
     * @param int $offset The offset from that position.
     * @return string The escaped string
     */
    protected function _getStringFromToken(int $position, int $offset): string
    {
        $string = $this->_tokens[$position + $offset]->getValue();

        return str_replace('"', '\"', $string);
    }

    /**
     * Adding this here to fix a PHP 8.2 error in the tests
     *
     * @return void
     */
    protected function extractFileReflection(string $file, string $code): void
    {
        parent::extractFileReflection($file, $code);
    }
}
