<?php
namespace extas\components\plugins\jsonrpc;

use extas\commands\JsonrpcCommand;
use extas\components\plugins\Plugin;
use extas\interfaces\stages\IStageJsonRpcCommand;
use Symfony\Component\Console\Input\InputOption;

/**
 * Class PluginDefaultArguments
 *
 * @package extas\components\plugins\jsonrpc
 * @author jeyroik@gmail.com
 */
class PluginDefaultArguments extends Plugin implements IStageJsonRpcCommand
{
    public const OPTION__PREFIX = 'prefix';
    public const OPTION__FILTER = 'filter';
    public const OPTION__SPECS_PATH = 'specs';
    public const OPTION__ONLY_EDGE = 'only-edge';

    protected const DEFAULT__PREFIX = 'PluginInstall';

    /**
     * @param JsonrpcCommand $command
     */
    public function __invoke(JsonrpcCommand &$command): void
    {
        $command->addOption(
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
}
