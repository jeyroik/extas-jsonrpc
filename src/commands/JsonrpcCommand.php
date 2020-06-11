<?php
namespace extas\commands;

use extas\components\options\TConfigure;
use extas\components\THasMagicClass;
use extas\interfaces\crawlers\ICrawler;
use extas\interfaces\IDispatcherWrapper;
use extas\interfaces\IItem;
use extas\interfaces\generators\IGenerator;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class JsonrpcCommand
 *
 * @package extas\commands
 * @author jeyroik@gmail.com
 */
class JsonrpcCommand extends DefaultCommand
{
    use TConfigure;
    use THasMagicClass;

    public const OPTION__EXPORT_PATH = 'export-path';

    protected string $commandVersion = '2.0.0';
    protected string $commandTitle = 'Extas JSON-RPC spec generator';
    protected bool $allCrawlers;
    protected bool $allGenerators;

    /**
     * Configure the current command.
     */
    protected function configure()
    {
        $this
            ->setName('jsonrpc')
            ->setAliases([])
            ->setDescription('Create json rpc specs.')
            ->setHelp('This command allows you to create json rpc specs.')
            ->addOption(
                static::OPTION__EXPORT_PATH,
                '',
                4,
                'Path to store result: resources/extas.json',
                getcwd() . '/resources/extas.json'
            );

        $this->addOptionsForCrawlers();
        $this->addOptionsForGenerators();
        $this->configureWithOptions('extas-jsonrpc', [static::OPTION__EXPORT_PATH => true]);
    }

    /**
     * Add options for crawlers
     */
    protected function addOptionsForCrawlers()
    {
        $this->addOptionsFor(
            $this->getMagicClass('crawlerRepository')->all([ICrawler::FIELD__TAGS => 'jsonrpc']),
            'crawler'
        );
    }

    /**
     * Add options for generators
     */
    protected function addOptionsForGenerators()
    {
        $this->addOptionsFor(
            $this->getMagicClass('generatorRepository')->all([IGenerator::FIELD__TAGS => 'jsonrpc']),
            'generator'
        );
    }

    /**
     * @param IDispatcherWrapper[]|IItem[] $items
     * @param string $prefix
     */
    protected function addOptionsFor(array $items, string $prefix): void
    {
        $this->addOption(
            $prefix . '-all',
            '',
            InputOption::VALUE_OPTIONAL,
            'Use all ' . $prefix . 's',
            true
        );

        foreach ($items as $item) {
            $this->addOption(
                $this->getOptionName($prefix, $item),
                '',
                InputOption::VALUE_OPTIONAL,
                $item->getDescription(),
                false
            );
        }
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    protected function dispatch(InputInterface $input, OutputInterface &$output): void
    {
        $this->setAllOptions($input);

        /**
         * @var ICrawler[] $crawlers
         */
        $crawlers = $this->getMagicClass('crawlerRepository')->all([ICrawler::FIELD__TAGS => 'jsonrpc']);
        $applicableClasses = [];
        foreach ($crawlers as $crawler) {
            if ($this->isCrawlerAllowed($crawler, $input)) {
                $applicableClasses[$crawler->getName()] = $crawler->dispatch(getcwd(), $input, $output);
            }
        }

        /**
         * @var IGenerator[] $generators[]
         */
        $generators = $this->getMagicClass('generatorRepository')->all([IGenerator::FIELD__TAGS => 'jsonrpc']);
        foreach ($generators as $generator) {
            if ($this->isGeneratorAllowed($generator, $input)) {
                $result = $generator->run($applicableClasses, $input, $output);
                $this->exportResult($result, $input);
                $output->writeln(['Exported result of generator "' . $generator->getName() . '"']);
            }
        }
    }

    /**
     * @param array $result
     * @param InputInterface $input
     * @return bool
     */
    protected function exportResult(array $result, InputInterface $input): bool
    {
        if (!isset($result['jsonrpc_operations'])) {
            return false;
        }

        $path = getcwd() . $input->getOption('export-path');

        if (is_file($path)) {
            $already = json_decode(file_get_contents($path), true);
            $already['jsonrpc_operations'] = array_merge($already['jsonrpc_operations'], $result['jsonrpc_operations']);
            $result = $already;
        }

        file_put_contents($path, json_encode($result));

        return true;
    }

    /**
     * @param InputInterface $input
     */
    protected function setAllOptions(InputInterface $input): void
    {
        $this->allCrawlers = (bool) $input->getOption('crawler-all');
        $this->allGenerators = (bool) $input->getOption('generator-all');
    }

    /**
     * @param ICrawler $crawler
     * @param InputInterface $input
     * @return bool
     */
    protected function isCrawlerAllowed(ICrawler $crawler, InputInterface $input): bool
    {
        return $this->isAllowed($crawler, 'crawler', $input, $this->allCrawlers);
    }

    /**
     * @param IGenerator $generator
     * @param InputInterface $input
     * @return bool
     */
    protected function isGeneratorAllowed(IGenerator $generator, InputInterface $input): bool
    {
        return $this->isAllowed($generator, 'generator', $input, $this->allGenerators);
    }

    /**
     * @param IDispatcherWrapper $item
     * @param string $prefix
     * @param InputInterface $input
     * @param bool $all
     * @return bool
     */
    protected function isAllowed(IDispatcherWrapper $item, string $prefix, InputInterface $input, bool $all): bool
    {
        $allowed = (bool) $input->getOption($this->getOptionName($prefix, $item));

        return $allowed || $all;
    }

    /**
     * @param string $prefix
     * @param IDispatcherWrapper $item
     * @return string
     */
    protected function getOptionName(string $prefix, IDispatcherWrapper $item): string
    {
        return $prefix . '-' . str_replace('.', '-', $item->getName());
    }
}
