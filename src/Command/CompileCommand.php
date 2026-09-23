<?php
declare(strict_types=1);

namespace Cake\TwigView\Command;

use Cake\Console\BaseCommand;
use Cake\Console\ConsoleOptionParser;
use Cake\TwigView\Filesystem\Scanner;
use Cake\TwigView\View\TwigView;
use Exception;

class CompileCommand extends BaseCommand
{
    protected TwigView $twigView;

    /**
     * @return string
     */
    public static function getDescription(): string
    {
        return 'Compile Twig templates for caching.';
    }

    /**
     * @inheritDoc
     */
    protected function buildOptionParser(ConsoleOptionParser $parser): ConsoleOptionParser
    {
        $parser->addArgument('type', [
            'required' => true,
            'choices' => ['all', 'file', 'plugin'],
            'help' => 'The type you want to compile.',
        ]);

        $parser->addArgument('target', [
            'required' => false,
            'help' => 'The file or plugin you want to compile.',
        ]);

        $parser->addOption('view-class', [
            'help' => 'The class name of the View used to load and compile.',
            'default' => TwigView::class,
        ]);

        return $parser;
    }

    /**
     * @inheritDoc
     */
    public function execute()
    {
        $type = $this->args->getArgumentAt(0);

        /** @phpstan-var class-string<\Cake\TwigView\View\TwigView> $viewClass */
        $viewClass = $this->args->getOption('view-class');

        // Setup cached TwigView to avoid creating for every file
        $this->twigView = new $viewClass();

        // $type is validated by the 'choices' option in buildOptionsParser
        return $this->{'execute' . $type}();
    }

    /**
     * Compile all templates.
     *
     * @return int
     */
    protected function executeAll(): int
    {
        $this->io->info('Compiling all templates');

        foreach (Scanner::all($this->twigView->getExtensions()) as $section => $templates) {
            $this->io->info('Compiling section ' . $section);
            foreach ($templates as $template) {
                if ($this->compileFile($template) === static::CODE_ERROR) {
                    return static::CODE_ERROR;
                }
            }
        }

        return static::CODE_SUCCESS;
    }

    /**
     * Compile all templates for a plugin.
     *
     * @return int
     */
    protected function executePlugin(): int
    {
        $plugin = $this->args->getArgumentAt(1);
        if ($plugin === null) {
            $this->io->error('Plugin name not specified.');

            return static::CODE_ERROR;
        }

        $this->io->info('Compiling plugin ' . $plugin);
        foreach (Scanner::plugin($plugin, $this->twigView->getExtensions()) as $template) {
            if ($this->compileFile($template) === static::CODE_ERROR) {
                return static::CODE_ERROR;
            }
        }

        return static::CODE_SUCCESS;
    }

    /**
     * Compile a single template file.
     *
     * @return int
     */
    protected function executeFile(): int
    {
        $filename = $this->args->getArgumentAt(1);
        if ($filename === null) {
            $this->io->error('File name not specified.');

            return static::CODE_ERROR;
        }

        return $this->compileFile($filename);
    }

    /**
     * Compile a single template file.
     *
     * @param string $filename The template filename
     * @return int
     */
    protected function compileFile(string $filename): int
    {
        try {
            $this->twigView->getTwig()->load($filename);
            $this->io->success(sprintf('Compiled %s.', $filename));
        } catch (Exception $exception) {
            $this->io->error(sprintf('Unable to compile %s.', $filename));
            $this->io->error($exception->getMessage());

            return static::CODE_ERROR;
        }

        return static::CODE_SUCCESS;
    }
}
