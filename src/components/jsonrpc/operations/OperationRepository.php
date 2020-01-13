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
    protected $idAs = '';
    protected $scope = 'extas';
    protected $pk = Operation::FIELD__NAME;
    protected $name = 'jsonrpc_routes';
    protected $itemClass = Operation::class;
}
