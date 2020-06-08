<?php
namespace extas\commands;

use extas\components\options\TConfigure;
use extas\interfaces\crawlers\ICrawler;
use extas\interfaces\IDispatcherWrapper;
use extas\interfaces\IItem;
use extas\interfaces\jsonrpc\generators\IGenerator;
use extas\interfaces\repositories\IRepository;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class JsonrpcCommand
 *
 * @method IRepository crawlerRepository()
 * @method IRepository generatorRepository()
 *
 * @package extas\commands
 * @author jeyroik@gmail.com
 */
class JsonrpcCommand extends DefaultCommand
{
    use TConfigure;

    protected const VERSION = '0.1.0';
    protected string $commandVersion = '0.2.0';
    protected string $commandTitle = 'Extas JSON-RPC spec generator';
    protected bool $allCrawlers;
    protected bool $allGenerators;
    protected JsonRpc $rpc;

    /**
     * Configure the current command.
     */
    protected function configure()
    {
        $this->rpc = new JsonRpc();
        $this
            ->setName('jsonrpc')
            ->setAliases([])
            ->setDescription('Create json rpc specs.')
            ->setHelp('This command allows you to create json rpc specs.');

        $this->addOptionsForCrawlers();
        $this->addOptionsForGenerators();
        $this->configureWithOptions('extas-jsonrpc', []);
    }

    /**
     * Add options for crawlers
     */
    protected function addOptionsForCrawlers()
    {
        $this->addOptionsFor(
            $this->getRepo('crawlerRepository')->all([ICrawler::FIELD__TAGS => 'jsonrpc']),
            'crawler'
        );
    }

    /**
     * Add options for generators
     */
    protected function addOptionsForGenerators()
    {
        $this->addOptionsFor(
            $this->getRepo('generatorRepository')->all([IGenerator::FIELD__TAGS => 'jsonrpc']),
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
     * @param string $name
     * @return IRepository
     */
    protected function getRepo(string $name): IRepository
    {
        $jsonRpc = new JsonRpc();
        return $jsonRpc->$name();
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
        $crawlers = $this->crawlerRepository()->all([ICrawler::FIELD__TAGS => 'jsonrpc']);
        $applicableClasses = [];
        foreach ($crawlers as $crawler) {
            if ($this->isCrawlerAllowed($crawler, $input)) {
                $applicableClasses[$crawler->getName()] = $crawler->dispatch(getcwd(), $input, $output);
            }
        }

        /**
         * @var IGenerator[] $generators[]
         */
        $generators = $this->generatorRepository()->all([IGenerator::FIELD__TAGS => 'jsonrpc']);
        foreach ($generators as $generator) {
            if ($this->isGeneratorAllowed($generator, $input)) {
                $generator->dispatch($input, $output, $applicableClasses);
            }
        }
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
