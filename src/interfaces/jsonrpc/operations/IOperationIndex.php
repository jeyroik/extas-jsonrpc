<?php
namespace extas\interfaces\jsonrpc\operations;

use extas\interfaces\IItem;
use extas\interfaces\jsonrpc\IResponse;
use extas\interfaces\servers\requests\IServerRequest;

/**
 * Interface IOperationIndex
 *
 * @package extas\interfaces\jsonrpc\operations
 * @author jeyroik@gmail.com
 */
interface IOperationIndex extends IItem, IOperationDispatcher
{
    const FIELD__LIMIT = 'limit';
    const FIELD__REPO_NAME = 'repo';
    const FIELD__ITEM_NAME = 'item_name';
    const FIELD__ITEM_REPO = 'item_repo';

    /**
     * @param IServerRequest $jsonRpcRequest
     * @param IResponse $jsonRpcResponse
     * @param array $data
     *
     * @return void
     */
    public function __invoke(IServerRequest $jsonRpcRequest, IResponse &$jsonRpcResponse, array $data);

    /**
     * @return int
     */
    public function getLimit(): int;

    /**
     * @param int $limit
     *
     * @return IOperationIndex
     */
    public function setLimit(int $limit): IOperationIndex;
}
