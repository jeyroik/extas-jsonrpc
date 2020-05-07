<?php
namespace extas\components\jsonrpc;

use extas\components\Plugins;
use extas\interfaces\jsonrpc\IRouter;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * Class App
 *
 * @package extas\components\jsonrpc
 * @author jeyroik@gmail.com
 */
class App extends \Slim\App
{
    /**
     * App constructor.
     * @param array $container
     */
    public function __construct($container = [])
    {
        parent::__construct($container);

        $this->post(
            '/api/jsonrpc',
            function (RequestInterface $request, ResponseInterface $response, array $args) {
                return $this->getRouter($request, $response, $args)->dispatch();
            }
        );

        $this->any('/specs', function (RequestInterface $request, ResponseInterface $response, array $args)  {
            return $this->getRouter($request, $response, $args)->getSpecs();
        });

        foreach (Plugins::byStage('extas.jsonrpc.init') as $plugin) {
            $plugin($this);
        }
    }

    /**
     * @param RequestInterface $request
     * @param ResponseInterface $response
     * @param array $args
     * @return IRouter
     */
    protected function getRouter(RequestInterface $request, ResponseInterface $response, array $args = []): IRouter
    {
        return new Router([
            Router::FIELD__PSR_REQUEST => $request,
            Router::FIELD__PSR_RESPONSE => $response,
            Router::FIELD__ARGUMENTS => $args
        ]);
    }
}
