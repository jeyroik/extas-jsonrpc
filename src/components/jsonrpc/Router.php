<?php
namespace extas\components\jsonrpc;

use extas\components\Item;
use extas\interfaces\jsonrpc\IRouter;
use extas\interfaces\operations\IOperation;
use extas\interfaces\jsonrpc\operations\IOperationDispatcher;
use extas\interfaces\repositories\IRepository;
use extas\interfaces\stages\IStageRunJsonRpc;
use Psr\Http\Message\ResponseInterface;

/**
 * Class Router
 *
 * @method IRepository jsonRpcOperationRepository()
 *
 * @package extas\components\jsonrpc
 * @author jeyroik@gmail.com
 */
class Router extends Item implements IRouter
{
    use THasPsrRequest;
    use THasPsrResponse;

    /**
     * @param string $name
     *
     * @return bool
     */
    public function hasOperation(string $name): bool
    {
        $operation = $this->jsonRpcOperationRepository()->one([IOperation::FIELD__NAME => $name]);
        return $operation ? true : false;
    }

    /**
     * @return ResponseInterface
     */
    public function dispatch(): ResponseInterface
    {
        /**
         * @var $operation IOperation
         */
        $jsonRpcRequest = $this->convertPsrToJsonRpcRequest();
        $routeName = $jsonRpcRequest->getMethod(static::ROUTE__DEFAULT);
        $operation = $this->jsonRpcOperationRepository()->one([IOperation::FIELD__NAME => $routeName]);
        $this->applyProtocols();

        try {
            if ($operation) {
                $this->runPluginsBefore($routeName);
                $this->runOperationDispatcher($operation);
                $this->runPluginsAfter($routeName);
            } else {
                throw new \Exception('Unknown operation "' . $routeName . '"', 404);
            }
        } catch (\Exception $e) {
            return $this->errorResponse($jsonRpcRequest->getId(), $e->getMessage(), $e->getCode());
        }

        return $this->getPsrResponse();
    }

    /**
     * @return ResponseInterface
     */
    public function getSpecs(): ResponseInterface
    {
        $jRpcRequest = $this->convertPsrToJsonRpcRequest();
        $routeName = $jRpcRequest->getMethod(static::ROUTE__DEFAULT);

        /**
         * @var $operation IOperation
         */
        $repo = $this->jsonRpcOperationRepository();

        $operations = ($routeName == static::ROUTE__ALL)
            ? $repo->all([])
            : $repo->all([IOperation::FIELD__NAME => $routeName]);

        $specs = array_column($operations, IOperation::FIELD__SPEC, IOperation::FIELD__NAME);

        return $this->successResponse($jRpcRequest->getId(), $specs);
    }

    /**
     * @param IOperation $operation
     */
    protected function runOperationDispatcher(IOperation $operation): void
    {
        /**
         * @var IOperationDispatcher $dispatcher
         */
        $dispatcher = $operation->buildClassWithParameters([
            IOperationDispatcher::FIELD__OPERATION => $operation,
            IOperationDispatcher::FIELD__PSR_REQUEST => $this->getPsrRequest(),
            IOperationDispatcher::FIELD__PSR_RESPONSE => $this->getPsrResponse(),
            IOperationDispatcher::FIELD__ARGUMENTS => $this->getArguments()
        ]);

        $this->setPsrResponse($dispatcher());
    }

    /**
     * @param string $routeName
     */
    protected function runPluginsBefore(string $routeName): void
    {
        $this->runPluginsByStage(IStageRunJsonRpc::NAME__BEFORE);
        $this->runPluginsByStage(IStageRunJsonRpc::NAME__BEFORE . '.' . $routeName);
    }

    /**
     * @param string $routeName
     */
    protected function runPluginsAfter(string $routeName): void
    {
        $this->runPluginsByStage(IStageRunJsonRpc::NAME__AFTER . '.' . $routeName);
        $this->runPluginsByStage(IStageRunJsonRpc::NAME__AFTER);
    }

    /**
     * @param string $stage
     */
    protected function runPluginsByStage(string $stage)
    {
        foreach ($this->getPluginsByStage($stage) as $plugin) {
            /**
             * @var IStageRunJsonRpc $plugin
             */
            $plugin(
                $this->config[static::FIELD__PSR_REQUEST],
                $this->config[static::FIELD__PSR_RESPONSE],
                $this->config[static::FIELD__ARGUMENTS]
            );
        }
    }

    /**
     * @return string
     */
    protected function getSubjectForExtension(): string
    {
        return static::SUBJECT;
    }
}
