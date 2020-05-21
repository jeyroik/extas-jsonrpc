<?php
namespace extas\components\plugins;

use extas\components\jsonrpc\generators\Generator;
use extas\interfaces\jsonrpc\generators\IGeneratorRepository;

/**
 * Class PluginInstallJsonRpcGenerators
 *
 * @package extas\components\plugins
 * @author jeyroik@gmail.com
 */
class PluginInstallJsonRpcGenerators extends PluginInstallDefault
{
    protected string $selfRepositoryClass = IGeneratorRepository::class;
    protected string $selfUID = Generator::FIELD__NAME;
    protected string $selfSection = 'jsonrpc_generators';
    protected string $selfName = 'jsonrpc generator';
    protected string $selfItemClass = Generator::class;
}
