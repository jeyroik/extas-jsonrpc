<?php
namespace extas\interfaces\stages;

use Slim\App;

/**
 * Interface IStageJsonRpcInit
 *
 * @package extas\interfaces\stages
 * @author jeyroik@gmail.com
 */
interface IStageJsonRpcInit
{
    public const NAME = 'extas.jsonrpc.init';

    /**
     * @param App $app
     */
    public function __invoke(App &$app): void;
}
