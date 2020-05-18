<?php
namespace extas\components\jsonrpc\generators;

use extas\components\repositories\Repository;
use extas\interfaces\jsonrpc\generators\IGeneratorRepository;

/**
 * Class GeneratorRepository
 *
 * @package extas\components\jsonrpc\generators
 * @author jeyroik@gmail.com
 */
class GeneratorRepository extends Repository implements IGeneratorRepository
{
    protected string $name = 'jsonrpc_generators';
    protected string $scope = 'extas';
    protected string $pk = Generator::FIELD__NAME;
    protected string $itemClass = Generator::class;
}
