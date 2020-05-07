<?php
namespace tests;

use Dotenv\Dotenv;
use extas\components\extensions\Extension;
use extas\components\extensions\ExtensionRepository;
use extas\components\extensions\ExtensionRepositoryGet;
use extas\components\jsonrpc\App;
use extas\components\jsonrpc\operations\Index;
use extas\components\jsonrpc\operations\Operation;
use extas\components\jsonrpc\operations\OperationRepository;
use extas\components\protocols\ProtocolRepository;
use extas\components\SystemContainer;
use extas\interfaces\extensions\IExtensionRepositoryGet;
use extas\interfaces\jsonrpc\IResponse;
use extas\interfaces\jsonrpc\operations\IOperationRepository;
use extas\interfaces\repositories\IRepository;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Slim\Psr7\Headers;
use Slim\Psr7\Response;
use Slim\Psr7\Stream;
use Slim\Psr7\Uri;

/**
 * Class AppTest
 *
 * @package tests
 * @author jeyroik@gmail.com
 */
class AppTest extends TestCase
{
    protected IRepository $extRepo;
    protected IRepository $opRepo;

    protected function setUp(): void
    {
        parent::setUp();
        $env = Dotenv::create(getcwd() . '/tests/');
        $env->load();

        $this->extRepo = new ExtensionRepository();
        $this->opRepo = new OperationRepository();
        SystemContainer::addItem('jsonRpcOperationRepository', OperationRepository::class);
        SystemContainer::addItem('protocolRepository', ProtocolRepository::class);
    }

    protected function tearDown(): void
    {
        $this->opRepo->delete([Operation::FIELD__NAME => 'jsonrpc.operation.index']);
    }

    public function testConstructing()
    {
        $app = App::create();
        $this->assertCount(2, $app->getRouteCollector()->getRoutes());
    }

    public function testApiJsonRpc()
    {
        $opData = [
            Operation::FIELD__NAME => 'jsonrpc.operation.index',
            Operation::FIELD__CLASS => Index::class,
            Operation::FIELD__METHOD => 'index',
            Operation::FIELD__SPEC => [],
            Operation::FIELD__ITEM_CLASS => Operation::class,
            Operation::FIELD__ITEM_REPO => IOperationRepository::class,
            Operation::FIELD__ITEM_NAME => 'jsonrpc operation'
        ];
        $this->opRepo->create(new Operation($opData));
        $this->extRepo->create(new Extension([
            Extension::FIELD__CLASS => ExtensionRepositoryGet::class,
            Extension::FIELD__INTERFACE => IExtensionRepositoryGet::class,
            Extension::FIELD__SUBJECT => '*',
            Extension::FIELD__METHODS => [
                'jsonRpcOperationRepository',
                'protocolRepository'
            ]
        ]));

        $app = App::create();
        $routes = $app->getRouteCollector()->getRoutes();
        foreach ($routes as $route) {
            if ($route->getPattern() == '/api/jsonrpc') {
                $dispatcher = $route->getCallable();
                /**
                 * @var ResponseInterface $response
                 */
                $response = $dispatcher($this->getRequest(), $this->getResponse(), []);
                $this->assertEquals(
                    [
                        IResponse::RESPONSE__ID => '2f5d0719-5b82-4280-9b3b-10f23aff226b',
                        IResponse::RESPONSE__VERSION => IResponse::VERSION_CURRENT,
                        IResponse::RESPONSE__RESULT => [$opData]
                    ],
                    json_decode($response->getBody()->getContents(), true)
                );
            }
        }
    }

    /**
     * @return ResponseInterface
     */
    protected function getResponse(): ResponseInterface
    {
        return new Response();
    }

    /**
     * @return RequestInterface
     */
    protected function getRequest(): RequestInterface
    {
        return new \Slim\Psr7\Request(
            'GET',
            new Uri('http', 'localhost', 80, '/', 'test2=ok'),
            new Headers([
                'Content-type' => 'application/json'
            ]),
            [],
            [],
            new Stream(fopen(getcwd() . '/tests/request.json', 'r'))
        );
    }
}
