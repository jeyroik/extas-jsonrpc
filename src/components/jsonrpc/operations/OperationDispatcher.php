<?php
namespace extas\components\jsonrpc\operations;

use extas\components\Item;
use extas\interfaces\jsonrpc\IRequest;
use extas\interfaces\jsonrpc\IResponse;
use extas\interfaces\jsonrpc\operations\IOperation;
use extas\interfaces\jsonrpc\operations\IOperationDispatcher;
use extas\interfaces\servers\requests\IServerRequest;
use extas\interfaces\servers\responses\IServerResponse;

/**
 * Class OperationDispatcher
 *
 * @package extas\components\jsonrpc\operations
 * @author jeyroik@gmail.com
 */
abstract class OperationDispatcher extends Item implements IOperationDispatcher
{
    /**
     * @var IServerRequest
     */
    protected $serverRequest = null;

    /**
     * @var IServerResponse
     */
    protected $serverResponse = null;

    /**
     * @param IServerRequest $serverRequest
     * @param IServerResponse $serverResponse
     */
    public function __invoke(IServerRequest $serverRequest, IServerResponse &$serverResponse)
    {
        /**
         * @var $jsonRpcRequest IRequest
         * @var $jsonRpcResponse IResponse
         */
        $jsonRpcRequest = $serverRequest->getParameter(IRequest::SUBJECT)->getValue();
        $jsonRpcResponse = $serverResponse->getParameter(IResponse::SUBJECT)->getValue();

        $this->serverRequest = $serverRequest;
        $this->serverResponse = $serverResponse;

        $this->dispatch($jsonRpcRequest, $jsonRpcResponse);

        $serverResponse->setParameter(IResponse::SUBJECT, $jsonRpcResponse);
    }

    /**
     * @return IOperation|null
     */
    public function getOperation(): ?IOperation
    {
        return $this->config[static::FIELD__OPERATION] ?? null;
    }

    /**
     * @param IOperation $operation
     *
     * @return IOperationDispatcher
     */
    public function setOperation(IOperation $operation): IOperationDispatcher
    {
        $this->config[static::FIELD__OPERATION] = $operation;

        return $this;
    }

    /**
     * @param IRequest $request
     * @param IResponse $response
     *
     * @return void
     */
    abstract protected function dispatch(IRequest $request, IResponse &$response);

    /**
     * @return string
     */
    protected function getSubjectForExtension(): string
    {
        return static::SUBJECT;
    }
}
