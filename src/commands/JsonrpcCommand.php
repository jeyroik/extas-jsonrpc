<?php
namespace extas\commands;

use extas\components\jsonrpc\Generator;
use extas\components\jsonrpc\Crawler;

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
    protected const VERSION = '0.1.0';
    protected const OPTION__PREFIX = 'prefix';
    protected const OPTION__FILTER = 'filter';
    protected const OPTION__SPECS_PATH = 'specs';
    protected const OPTION__ONLY_EDGE = 'only-edge';

    protected const DEFAULT__PREFIX = 'PluginInstall';

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
            ->setDescription('Create json rpc CRUD specs.')
            ->setHelp('This command allows you to create json rpc specs by install plugins.')
            ->addOption(
                static::OPTION__PREFIX,
                'p',
                InputOption::VALUE_OPTIONAL,
                'Install plugins prefix',
                static::DEFAULT__PREFIX
            )
            ->addOption(
                static::OPTION__SPECS_PATH,
                's',
                InputOption::VALUE_OPTIONAL,
                'Path to store result specs',
                getcwd() . '/specs.extas.json'
            )
            ->addOption(
                static::OPTION__FILTER,
                'f',
                InputOption::VALUE_OPTIONAL,
                'Filter operations by filter entry in the operation name.' .
                'Ex.: "opera" for looking only names with "opera" in it',
                ''
            )->addOption(
                static::OPTION__ONLY_EDGE,
                'e',
                InputOption::VALUE_OPTIONAL,
                'Use as operation name only last word of section',
                ''
            )
        ;
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    protected function dispatch(InputInterface $input, OutputInterface &$output): void
    {
        $prefix = $input->getOption(static::OPTION__PREFIX);
        $path = $input->getOption(static::OPTION__SPECS_PATH);

        $crawler = new Crawler();
        $plugins = $crawler->crawlPlugins(getcwd(), $prefix);

        $serviceInstaller = new Generator([
            Generator::FIELD__FILTER => $input->getOption(static::OPTION__FILTER),
            Generator::FIELD__ONLY_EDGE => $input->getOption(static::OPTION__ONLY_EDGE)
        ]);
        $serviceInstaller->generate($plugins, $path);
    }
}
