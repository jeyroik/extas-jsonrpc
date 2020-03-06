<?php
namespace extas\components\jsonrpc;

use extas\components\Item;
use extas\components\servers\requests\ServerRequest;
use extas\components\servers\responses\ServerResponse;
use extas\components\SystemContainer;
use extas\interfaces\jsonrpc\IRequest;
use extas\interfaces\jsonrpc\IResponse;
use extas\interfaces\jsonrpc\IRouter;
use extas\interfaces\jsonrpc\operations\IOperation;
use extas\interfaces\jsonrpc\operations\IOperationDispatcher;
use extas\interfaces\jsonrpc\operations\IOperationRepository;
use extas\interfaces\protocols\IProtocol;
use extas\interfaces\protocols\IProtocolRepository;
use extas\interfaces\servers\requests\IServerRequest;
use extas\interfaces\servers\responses\IServerResponse;
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
     * @param RequestInterface $httpRequest
     * @param ResponseInterface $httpResponse
     * @param IRequest $jsonRpcRequest
     *
     * @return ResponseInterface
     */
    public function dispatch(
        RequestInterface $httpRequest,
        ResponseInterface $httpResponse,
        IRequest $jsonRpcRequest = null
    ): ResponseInterface
    {
        /**
         * @var $repo IOperationRepository
         * @var $operation IOperation
         */
        $jsonRpcRequest = $jsonRpcRequest ?: Request::fromHttp($httpRequest);
        $routeName = $jsonRpcRequest->getMethod(static::ROUTE__DEFAULT);
        $repo = SystemContainer::getItem(IOperationRepository::class);
        $operation = $repo->one([IOperation::FIELD__NAME => $routeName]);

        $serverRequest = $this->getServerRequest($httpRequest, $jsonRpcRequest);
        $serverResponse = $this->getServerResponse($httpResponse);

        try {
            if ($operation) {
                foreach ($this->getPluginsByStage('before.run.jsonrpc') as $plugin) {
                    $plugin($serverRequest, $serverResponse);
                }

                foreach ($this->getPluginsByStage('before.run.jsonrpc.' . $routeName) as $plugin) {
                    $plugin($serverRequest, $serverResponse);
                }

                $dispatcher = $operation->buildClassWithParameters([
                    IOperationDispatcher::FIELD__OPERATION => $operation
                ]);
                $dispatcher($serverRequest, $serverResponse);

                foreach ($this->getPluginsByStage('after.run.jsonrpc.' . $routeName) as $plugin) {
                    $plugin($serverRequest, $serverResponse);
                }

                foreach ($this->getPluginsByStage('after.run.jsonrpc') as $plugin) {
                    $plugin($serverRequest, $serverResponse);
                }
            } else {
                throw new \Exception('Unknown operation "' . $routeName . '"', 404);
            }
        } catch (\Exception $e) {
            $jsonRpcResponse = $serverResponse->getParameter(IResponse::SUBJECT)->getValue();
            $jsonRpcResponse->error($e->getMessage(), $e->getCode());

            return $jsonRpcResponse->getPsrResponse();
        }

        /**
         * @var $jsonRpcResponse IResponse
         */
        $jsonRpcResponse = $serverResponse->getParameter(IResponse::SUBJECT)->getValue();

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

        $jsonRpcResponse = Response::fromPsr($response);
        $jsonRpcResponse->success($specs);

        return $jsonRpcResponse->getPsrResponse();
    }

    /**
     * @param ResponseInterface $httpResponse
     *
     * @return IServerResponse
     */
    protected function getServerResponse(ResponseInterface $httpResponse): IServerResponse
    {
        $data = [IResponse::SUBJECT => Response::fromPsr($httpResponse)];

        return new ServerResponse([
            ServerResponse::FIELD__NAME => 'jsonrpc_service',
            ServerResponse::FIELD__PARAMETERS => ServerResponse::makeParametersFrom($data)
        ]);
    }

    /**
     * @param RequestInterface $request
     * @param IRequest $jsonRpcRequest
     *
     * @return IServerRequest
     */
    protected function getServerRequest(RequestInterface $request, IRequest $jsonRpcRequest): IServerRequest
    {
        /**
         * @var $repo IProtocolRepository
         * @var $protocols IProtocol[]
         */
        $repo = SystemContainer::getItem(IProtocolRepository::class);
        $protocols = $repo->all([
            IProtocol::FIELD__ACCEPT => [$request->getHeader('ACCEPT'), '*']
        ]);

        $data = [];

        foreach ($protocols as $protocol) {
            $protocol($data, $request);
        }

        $data[IRequest::SUBJECT] = $jsonRpcRequest;
        $data['extas.http.request'] = $request;

        return new ServerRequest([
            ServerRequest::FIELD__NAME => 'jsonrpc_service',
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
