<?php
namespace extas\commands;

use extas\components\Plugins;
use extas\interfaces\jsonrpc\crawlers\ICrawler;
use extas\interfaces\jsonrpc\generators\IGenerator;
use extas\interfaces\stages\IStageJsonRpcCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class JsonrpcCommand
 *
 * @package extas\commands
 * @author jeyroik@gmail.com
 */
class JsonrpcCommand extends DefaultCommand
{
    protected const VERSION = '0.1.0';
    protected string $commandVersion = '0.2.0';
    protected string $commandTitle = 'Extas JSON-RPC spec generator';

    /**
     * Configure the current command.
     */
    protected function configure()
    {
        $this
            ->setName('jsonrpc')
            ->setAliases([])
            ->setDescription('Create json rpc specs.')
            ->setHelp('This command allows you to create json rpc specs.');

        $this->addOptionsByPlugins();
    }

    /**
     * Add options for crawling and generation.
     */
    protected function addOptionsByPlugins(): void
    {
        foreach (Plugins::byStage(IStageJsonRpcCommand::NAME) as $plugin) {
            /**
             * @var IStageJsonRpcCommand $plugin
             */
            $plugin($this);
        }
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    protected function dispatch(InputInterface $input, OutputInterface &$output): void
    {
        $jsonRpc = new JsonRpc();

        /**
         * @var ICrawler[] $crawlers
         */
        $crawlers = $jsonRpc->jsonRpcCrawlerRepository()->all([]);
        $applicableClasses = [];
        foreach ($crawlers as $crawler) {
            $applicableClasses[$crawler->getName()] = $crawler->dispatch($input, $output);
        }

        /**
         * @var IGenerator[] $generators[]
         */
        $generators = $jsonRpc->jsonRpcGeneratorRepository()->all([]);
        foreach ($generators as $generator) {
            $generator->dispatch($input, $output, $applicableClasses);
        }
    }
}
