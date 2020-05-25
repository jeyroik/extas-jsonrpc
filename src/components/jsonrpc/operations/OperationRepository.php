<?php
namespace extas\components\jsonrpc\operations;

use extas\components\repositories\Repository;
use extas\interfaces\jsonrpc\operations\IOperationRepository;

/**
 * Class RouteRepository
 *
 * @package extas\components\jsonrpc\routes
 * @author jeyroik@gmail.com
 */
class OperationRepository extends Repository implements IOperationRepository
{
    protected string $scope = 'extas';
    protected string $pk = Operation::FIELD__NAME;
    protected string $name = 'jsonrpc_operations';
    protected string $itemClass = Operation::class;
}
