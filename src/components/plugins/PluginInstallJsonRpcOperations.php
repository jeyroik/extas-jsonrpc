<?php
namespace extas\components\plugins;

use extas\components\jsonrpc\operations\Operation;
use extas\interfaces\jsonrpc\operations\IOperationRepository;

/**
 * Class PluginInstallJsonRpcOperations
 *
 * @package extas\components\plugins
 * @author jeyroik@gmail.com
 */
class PluginInstallJsonRpcOperations extends PluginInstallDefault
{
    protected string $selfRepositoryClass = IOperationRepository::class;
    protected string $selfUID = Operation::FIELD__NAME;
    protected string $selfSection = 'jsonrpc_operations';
    protected string $selfName = 'jsonrpc operation';
    protected string $selfItemClass = Operation::class;
}
