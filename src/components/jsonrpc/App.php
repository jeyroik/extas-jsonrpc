<?php
namespace extas\components\jsonrpc;

use extas\components\Plugins;
use extas\interfaces\jsonrpc\IRouter;
use extas\interfaces\stages\IStageJsonRpcInit;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Slim\Factory\AppFactory;
use Slim\Interfaces\CallableResolverInterface;
use Slim\Interfaces\MiddlewareDispatcherInterface;
use Slim\Interfaces\RouteCollectorInterface;
use Slim\Interfaces\RouteResolverInterface;

/**
 * Class App
 *
 * @package extas\components\jsonrpc
 * @author jeyroik@gmail.com
 */
class App extends AppFactory
{
    /**
     * @param ResponseFactoryInterface|null $responseFactory
     * @param ContainerInterface|null $container
     * @param CallableResolverInterface|null $callableResolver
     * @param RouteCollectorInterface|null $routeCollector
     * @param RouteResolverInterface|null $routeResolver
     * @param MiddlewareDispatcherInterface|null $middlewareDispatcher
     * @return \Slim\App
     */
    public static function create(
        ?ResponseFactoryInterface $responseFactory = null,
        ?ContainerInterface $container = null,
        ?CallableResolverInterface $callableResolver = null,
        ?RouteCollectorInterface $routeCollector = null,
        ?RouteResolverInterface $routeResolver = null,
        ?MiddlewareDispatcherInterface $middlewareDispatcher = null
    ): \Slim\App
    {
        $app = parent::create(
            $responseFactory,
            $container,
            $callableResolver,
            $routeCollector,
            $routeResolver,
            $middlewareDispatcher
        );

        $app->post(
            '/api/jsonrpc',
            function (RequestInterface $request, ResponseInterface $response, array $args) {
                return static::getRouter($request, $response, $args)->dispatch();
            }
        );

        $app->any('/specs', function (RequestInterface $request, ResponseInterface $response, array $args)  {
            return static::getRouter($request, $response, $args)->getSpecs();
        });

        foreach (Plugins::byStage(IStageJsonRpcInit::NAME) as $plugin) {
            /**
             * @var IStageJsonRpcInit $plugin
             */
            $plugin($app);
        }

        return $app;
    }

    /**
     * @param RequestInterface $request
     * @param ResponseInterface $response
     * @param array $args
     * @return IRouter
     */
    protected static function getRouter(RequestInterface $request, ResponseInterface $response, array $args = []): IRouter
    {
        return new Router([
            Router::FIELD__PSR_REQUEST => $request,
            Router::FIELD__PSR_RESPONSE => $response,
            Router::FIELD__ARGUMENTS => $args
        ]);
    }
}
