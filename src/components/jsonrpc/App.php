<?php
namespace extas\components\jsonrpc;

use extas\components\Plugins;
use Psr\Http\Message\RequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

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
        $router = new Router();

        $this->post('/api/jsonrpc', function (Request $request, Response $response, array $args) use ($router) {
            return $router->dispatch($request, $response);
        });

        $this->any('/specs', function (Request $request, Response $response, array $args) use ($router)  {
            return $router->getSpecs($request, $response);
        });

        foreach (Plugins::byStage('extas.jsonrpc.init') as $plugin) {
            $plugin($this);
        }
    }
}
