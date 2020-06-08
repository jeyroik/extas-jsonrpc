<?php
namespace extas\components\plugins\uninstall;

use extas\components\operations\JsonRpcOperation;

/**
 * Class UninstallJsonRpcOperations
 *
 * @package extas\components\plugins\uninstall
 * @author jeyroik@gmail.com
 */
class UninstallJsonRpcOperations extends UninstallSection
{
    protected string $selfRepositoryClass = 'jsonRpcOperationRepository';
    protected string $selfUID = JsonRpcOperation::FIELD__NAME;
    protected string $selfSection = 'jsonrpc_operations';
    protected string $selfName = 'jsonrpc operation';
    protected string $selfItemClass = JsonRpcOperation::class;
}
