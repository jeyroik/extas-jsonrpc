<?php
namespace extas\components\jsonrpc;

use extas\components\Item;
use extas\components\servers\requests\ServerRequest;
use extas\components\SystemContainer;
use extas\interfaces\jsonrpc\IRequest;
use extas\interfaces\jsonrpc\IResponse;
use extas\interfaces\jsonrpc\IRouter;
use extas\interfaces\jsonrpc\operations\IOperation;
use extas\interfaces\jsonrpc\operations\IOperationDispatcher;
use extas\interfaces\jsonrpc\operations\IOperationRepository;
use extas\interfaces\protocols\IProtocol;
use extas\interfaces\protocols\IProtocolRepository;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * Class Router
 *
 * @package extas\components\jsonrpc
 * @author jeyroik@gmail.com
 */
class Router extends Item implements IRouter
{
    /**
     * @param string $name
     *
     * @return bool
     */
    public function hasOperation(string $name): bool
    {
        /**
         * @var $repo IOperationRepository
         */
        $repo = SystemContainer::getItem(IOperationRepository::class);
        $operation = $repo->one([IOperation::FIELD__NAME => $name]);

        return $operation ? true : false;
    }

    /**
     * @param RequestInterface $request
     * @param ResponseInterface $response
     *
     * @return ResponseInterface
     */
    public function dispatch(RequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        /**
         * @var $repo IOperationRepository
         * @var $operation IOperation
         */
        $jrpcRequest = json_decode($request->getBody()->getContents(), true);
        $routeName = $jrpcRequest[IRequest::FIELD__METHOD] ?? static::ROUTE__DEFAULT;
        $repo = SystemContainer::getItem(IOperationRepository::class);
        $operation = $repo->one([IOperation::FIELD__NAME => $routeName]);
        $serverRequest = $this->getServerRequest($request, $jrpcRequest);
        $jsonRpcResponse = Response::fromPsr($response, $jrpcRequest);

        try {
            if ($operation) {
                foreach ($this->getPluginsByStage('before.run.jsonrpc.' . $routeName) as $plugin) {
                    $plugin($serverRequest, $jsonRpcResponse, $jrpcRequest);
                }
                if (!isset($jrpcRequest[IResponse::RESPONSE__ERROR_MARKER])) {
                    $dispatcher = $operation->buildClassWithParameters([
                        IOperationDispatcher::FIELD__OPERATION => $operation
                    ]);
                    $dispatcher($serverRequest, $jsonRpcResponse, $jrpcRequest);

                    foreach ($this->getPluginsByStage('after.run.jsonrpc.' . $routeName) as $plugin) {
                        $plugin($serverRequest, $jsonRpcResponse, $jrpcRequest);
                    }
                }
            } else {
                throw new \Exception('Unknown operation "' . $routeName . '"', 404);
            }
        } catch (\Exception $e) {
            $jsonRpcResponse->error($e->getMessage(), $e->getCode());
        }

        return $jsonRpcResponse->getPsrResponse();
    }

    /**
     * @param RequestInterface $request
     * @param ResponseInterface $response
     *
     * @return ResponseInterface
     */
    public function getSpecs(RequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $jrpcRequest = json_decode($request->getBody()->getContents(), true);
        $routeName = $jrpcRequest[IRequest::FIELD__METHOD] ?? static::ROUTE__DEFAULT;

        /**
         * @var $repo IOperationRepository
         * @var $operation IOperation
         */
        $repo = SystemContainer::getItem(IOperationRepository::class);

        $operations = ($routeName == static::ROUTE__ALL)
            ? $repo->all([])
            : $repo->all([IOperation::FIELD__NAME => $routeName]);

        $specs = [];
        foreach ($operations as $operation) {
            $specs[$operation->getName()] = $operation->getSpec();
        }

        $jsonRpcResponse = Response::fromPsr($response, $jrpcRequest);
        $jsonRpcResponse->success($specs);

        return $jsonRpcResponse->getPsrResponse();
    }

    /**
     * @param RequestInterface $request
     * @param array $data
     *
     * @return ServerRequest
     */
    protected function getServerRequest(RequestInterface $request, array $data)
    {
        /**
         * @var $repo IProtocolRepository
         * @var $protocols IProtocol[]
         */
        $repo = SystemContainer::getItem(IProtocolRepository::class);
        $protocols = $repo->all([
            IProtocol::FIELD__ACCEPT => [$request->getHeader('ACCEPT'), '*']
        ]);

        foreach ($protocols as $protocol) {
            $protocol($data, $request);
        }

        return new ServerRequest([
            ServerRequest::FIELD__NAME => 'jsonrpc',
            ServerRequest::FIELD__PARAMETERS => ServerRequest::makeParametersFrom($data)
        ]);
    }

    /**
     * @return string
     */
    protected function getSubjectForExtension(): string
    {
        return static::SUBJECT;
    }
}
