<?php
namespace extas\interfaces\stages;

use extas\commands\JsonrpcCommand;

/**
 * Interface IStageJsonRpcCommand
 *
 * @package extas\interfaces\stages
 * @author jeyroik@gmail.com
 */
interface IStageJsonRpcCommand
{
    public const NAME = 'extas.jsonrpc.command';

    /**
     * @param JsonrpcCommand $command
     */
    public function __invoke(JsonrpcCommand &$command): void;
}
