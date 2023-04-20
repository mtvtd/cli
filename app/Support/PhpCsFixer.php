<?php

namespace App\Support;

use App\Project;
use PhpCsFixer\Config;
use PhpCsFixer\Finder;
use PhpCsFixer\ToolInfo;
use PhpCsFixer\Runner\Runner;
use PhpCsFixer\ConfigInterface;
use PhpCsFixer\Error\ErrorsManager;
use PhpCsFixer\Console\ConfigurationResolver;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\EventDispatcher\EventDispatcher;
use PhpCsFixer\ConfigurationException\InvalidConfigurationException;

class PhpCsFixer
{
    public function lint(): array
    {
        return $this->process(dryRun: true);
    }

    public function fix(): array
    {
        return $this->process(dryRun: false);
    }

    protected function process(bool $dryRun): array
    {
        $output = resolve(OutputInterface::class);

        $resolver = new ConfigurationResolver(
            config: $this->getConfig(),
            options: [
                'config' => $this->getConfigFilePath(),
                'allow-risky' => 'yes',
                'diff' => $output->isVerbose(),
                'dry-run' => $dryRun,
                'stop-on-violation' => false,
                'verbosity' => $output->getVerbosity(),
                'show-progress' => 'true',
            ],
            cwd: Project::path(),
            toolInfo: new ToolInfo,
        );

        $errorManager = resolve(ErrorsManager::class);

        $runner = new Runner(
            finder: $this->getConfig()->getFinder(),
            fixers: $resolver->getFixers(),
            differ: $resolver->getDiffer(),
            eventDispatcher: resolve(EventDispatcher::class),
            errorsManager: $errorManager,
            linter: $resolver->getLinter(),
            isDryRun: $resolver->isDryRun(),
            cacheManager: $resolver->getCacheManager(),
            directory: $resolver->getDirectory(),
            stopOnViolation: $resolver->shouldStopOnViolation()
        );

        $changes = $runner->fix();
        $invalidErrors = $errorManager->getInvalidErrors();
        $exceptionErrors = $errorManager->getExceptionErrors();
        $lintErrors = $errorManager->getLintErrors();

        return compact('changes', 'invalidErrors', 'exceptionErrors', 'lintErrors');
    }

    private function getConfig(): ConfigInterface
    {
        $config = $this->includeConfig();

        if ( ! $config instanceof ConfigInterface) {
            throw new InvalidConfigurationException("The PHP CS Fixer config file does not return a 'PhpCsFixer\ConfigInterface' instance.");
        }

        return $config->setFinder($this->updateFinder($config->getFinder()));
    }

    /**
     * Update the finder with the paths and exclude from the config.
     * We are bypassing resolveFinder() in ConfigurationResolver
     * to allow for us to use the global duster config.
     */
    private function updateFinder(Finder $finder): Finder
    {
//        collect($this->dusterConfig->get('paths', []))->each(function ($path) use ($finder) {
//            if (is_dir($path)) {
//                $finder = $finder->in($path);
//            } elseif (is_file($path)) {
//                $finder = $finder->append([$path]);
//            }
//        });
//
//        collect($this->dusterConfig->get('exclude', []))->each(function ($path) use ($finder) {
//            if (is_dir($path)) {
//                $finder = $finder->exclude($path);
//            } elseif (is_file($path)) {
//                $finder = $finder->notPath($path);
//            }
//        });

        return $finder;
    }

    private function includeConfig(): Config
    {
        return include $this->getConfigFilePath();
    }

    private function getConfigFilePath(): string
    {
        return (string)collect([
            Project::path() . '/.php-cs-fixer.dist.php',
            Project::path() . '/.php-cs-fixer.php',
            base_path('standards/.php-cs-fixer.dist.php'),
        ])->first(fn($path) => file_exists($path));
    }
}
