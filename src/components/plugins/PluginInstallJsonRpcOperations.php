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
    protected $selfRepositoryClass = IOperationRepository::class;
    protected $selfUID = Operation::FIELD__NAME;
    protected $selfSection = 'jsonrpc_operations';
    protected $selfName = 'jsonrpc operation';
    protected $selfItemClass = Operation::class;
}
