<?php
namespace extas\commands;

use extas\components\jsonrpc\Generator;
use extas\components\jsonrpc\Crawler;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class JsonrpcCommand
 *
 * @package extas\commands
 * @author jeyroik@gmail.com
 */
class JsonrpcCommand extends Command
{
    protected const VERSION = '0.1.0';
    protected const OPTION__PREFIX = 'prefix';
    protected const OPTION__FILTER = 'filter';
    protected const OPTION__SPECS_PATH = 'specs';
    protected const OPTION__ONLY_EDGE = 'only-edge';

    protected const DEFAULT__PREFIX = 'PluginInstall';

    /**
     * Configure the current command.
     */
    protected function configure()
    {
        $this
            // the name of the command (the part after "bin/console")
            ->setName('jsonrpc')
            ->setAliases([])

            // the short description shown while running "php bin/console list"
            ->setDescription('Create json rpc CRUD specs.')

            // the full command description shown when running the command with
            // the "--help" option
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
     *
     * @return int|mixed
     * @throws
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $start = microtime(true);

        $prefix = $input->getOption(static::OPTION__PREFIX);
        $path = $input->getOption(static::OPTION__SPECS_PATH);

        $output->writeln([
            'Extas JSON-RPC spec generator v' . static::VERSION,
            '=========================='
        ]);

        $crawler = new Crawler();
        $plugins = $crawler->crawlPlugins(getcwd(), $prefix);

        $serviceInstaller = new Generator([
            Generator::FIELD__FILTER => $input->getOption(static::OPTION__FILTER),
            Generator::FIELD__ONLY_EDGE => $input->getOption(static::OPTION__ONLY_EDGE)
        ]);
        $serviceInstaller->generate($plugins, $path);

        $end = microtime(true) - $start;
        $output->writeln(['<info>Finished in ' . $end . ' s.</info>']);

        return 0;
    }

    /**
     * @param $data
     * @param $input InputInterface
     * @param $output OutputInterface
     */
    protected function storeGeneratedData($data, $input, $output)
    {
        if (!empty($data)) {
            $result = date('#[Y-m-d H:i:s]') . PHP_EOL;
            foreach ($data as $field => $value) {
                $result .= $field . ' = ' . $value . PHP_EOL;
            }
            $input->getOption(static::OPTION__REWRITE_GENERATED_DATA)
                ? file_put_contents(getcwd() . '/' . static::GENERATED_DATA__STORE, $result)
                : file_put_contents(getcwd() . '/' . static::GENERATED_DATA__STORE, $result, FILE_APPEND);

            $output->writeln([
                '<info>See generated data in the ' . static::GENERATED_DATA__STORE . '</info>'
            ]);
        }
    }

    /**
     * Copy container base dist
     *
     * @param bool $rewriteContainer
     */
    protected function prepareClassContainer($rewriteContainer = true)
    {
        $lockContainerPath = getenv('EXTAS__CONTAINER_PATH_STORAGE_LOCK')
            ?: getcwd() . '/configs/container.php';

        $storageContainerPath = getenv('EXTAS__CONTAINER_PATH_STORAGE')
            ?: getcwd() . '/configs/container.json';

        if ($rewriteContainer || !is_file($lockContainerPath)) {
            copy(
                getcwd() . '/vendor/jeyroik/extas-foundation/resources/container.dist.php',
                $lockContainerPath
            );
        }

        if ($rewriteContainer || !is_file($storageContainerPath)) {
            copy(
                getcwd() . '/vendor/jeyroik/extas-foundation/resources/container.dist.json',
                $storageContainerPath
            );
        }
    }
}
