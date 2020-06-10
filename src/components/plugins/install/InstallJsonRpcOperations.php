<?php
namespace extas\components\plugins\install;

use extas\components\operations\JsonRpcOperation;

/**
 * Class InstallJsonRpcOperations
 *
 * @package extas\components\plugins\install
 * @author jeyroik@gmail.com
 */
class InstallJsonRpcOperations extends InstallSection
{
    protected string $selfRepositoryClass = 'jsonRpcOperationRepository';
    protected string $selfUID = JsonRpcOperation::FIELD__NAME;
    protected string $selfSection = 'jsonrpc_operations';
    protected string $selfName = 'jsonrpc operation';
    protected string $selfItemClass = JsonRpcOperation::class;
}
