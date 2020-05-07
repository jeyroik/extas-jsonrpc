<?php
namespace extas\interfaces\stages;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * Interface IStageRunJsonRpc
 *
 * @package extas\interfaces\stages
 * @author jeyroik@gmail.com
 */
interface IStageRunJsonRpc
{
    public const NAME__BEFORE = 'before.run.jsonrpc';
    public const NAME__AFTER = 'after.run.jsonrpc';

    /**
     * @param RequestInterface $request
     * @param ResponseInterface $response
     * @param array $args
     */
    public function __invoke(RequestInterface $request, ResponseInterface &$response, array &$args): void;
}
