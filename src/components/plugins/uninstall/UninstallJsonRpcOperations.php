<?php
namespace extas\components\plugins\uninstall;

use extas\components\jsonrpc\operations\Operation;

/**
 * Class UninstallJsonRpcOperations
 *
 * @package extas\components\plugins\uninstall
 * @author jeyroik@gmail.com
 */
class UninstallJsonRpcOperations extends UninstallSection
{
    protected string $selfRepositoryClass = 'jsonRpcOperationRepository';
    protected string $selfUID = Operation::FIELD__NAME;
    protected string $selfSection = 'jsonrpc_operations';
    protected string $selfName = 'jsonrpc operation';
    protected string $selfItemClass = Operation::class;
}
