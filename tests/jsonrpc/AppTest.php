<?php
namespace tests\jsonrpc;

use extas\interfaces\jsonrpc\IResponse;
use extas\interfaces\repositories\IRepository;
use extas\interfaces\samples\parameters\ISampleParameter;
use extas\interfaces\stages\IStageJsonRpcInit;
use extas\interfaces\stages\IStageRunJsonRpc;

use extas\components\operations\JsonRpcOperation;
use extas\components\operations\JsonRpcOperationRepository;
use extas\components\extensions\ExtensionRepository;
use extas\components\http\TSnuffHttp;
use extas\components\jsonrpc\App;
use extas\components\jsonrpc\operations\Index;
use extas\components\operations\JsonRpcOperation as Operation;
use extas\components\plugins\PluginRepository;
use extas\components\plugins\TSnuffPlugins;
use extas\components\protocols\ProtocolRepository;
use extas\components\repositories\TSnuffRepository;

use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Dotenv\Dotenv;

/**
 * Class AppTest
 *
 * @package tests
 * @author jeyroik@gmail.com
 */
class AppTest extends TestCase
{
    use TSnuffRepository;
    use TSnuffPlugins;
    use TSnuffHttp;

    protected IRepository $opRepo;
    protected array $opData = [
        Operation::FIELD__NAME => 'jsonrpc.operation.index',
        Operation::FIELD__CLASS => Index::class,
        Operation::FIELD__PARAMETERS => [
            Operation::PARAM__METHOD => [
                ISampleParameter::FIELD__NAME => Operation::PARAM__METHOD,
                ISampleParameter::FIELD__VALUE => 'index'
            ],
            Operation::PARAM__ITEM_CLASS => [
                ISampleParameter::FIELD__NAME => Operation::PARAM__ITEM_CLASS,
                ISampleParameter::FIELD__VALUE => Operation::class
            ],
            Operation::PARAM__ITEM_REPOSITORY => [
                ISampleParameter::FIELD__NAME => Operation::PARAM__ITEM_REPOSITORY,
                ISampleParameter::FIELD__VALUE => 'jsonRpcOperationRepository'
            ],
            Operation::PARAM__ITEM_NAME => [
                ISampleParameter::FIELD__NAME => Operation::PARAM__ITEM_NAME,
                ISampleParameter::FIELD__VALUE => 'jsonrpc operation'
            ]
        ],
        Operation::FIELD__SPECS => [],
    ];

    protected function setUp(): void
    {
        parent::setUp();
        $env = Dotenv::create(getcwd() . '/tests/');
        $env->load();
        $this->registerSnuffRepos([
            'jsonRpcOperationRepository' => JsonRpcOperationRepository::class,
            'protocolRepository' => ProtocolRepository::class,
            'extensionRepository' => ExtensionRepository::class,
            'pluginRepository' => PluginRepository::class
        ]);
    }

    protected function tearDown(): void
    {
        $this->unregisterSnuffRepos();
    }

    public function testConstructing()
    {
        $app = App::create();
        $this->assertCount(2, $app->getRouteCollector()->getRoutes());
    }

    public function testApiJsonRpc()
    {
        $this->initOperationEnv();

        $app = App::create();
        $routes = $app->getRouteCollector()->getRoutes();
        foreach ($routes as $route) {
            if ($route->getPattern() == '/api/jsonrpc') {
                $dispatcher = $route->getCallable();
                /**
                 * @var ResponseInterface $response
                 */
                $response = $dispatcher($this->getPsrRequest(), $this->getPsrResponse(), []);
                $this->assertEquals(
                    [
                        IResponse::RESPONSE__ID => '2f5d0719-5b82-4280-9b3b-10f23aff226b',
                        IResponse::RESPONSE__VERSION => IResponse::VERSION_CURRENT,
                        IResponse::RESPONSE__RESULT => [
                            'items' => [$this->opData],
                            'total' => 1
                        ]
                    ],
                    json_decode($response->getBody(), true),
                    (string) $response->getBody()
                );
            }
        }
    }

    public function testApiJsonRpcError()
    {
        $this->initOperationEnv();
        $this->createPluginException([IStageRunJsonRpc::NAME__BEFORE]);

        $app = App::create();
        $routes = $app->getRouteCollector()->getRoutes();
        foreach ($routes as $route) {
            if ($route->getPattern() == '/api/jsonrpc') {
                $dispatcher = $route->getCallable();
                /**
                 * @var ResponseInterface $response
                 */
                $response = $dispatcher($this->getPsrRequest(), $this->getPsrResponse(), []);
                $this->assertEquals(
                    [
                        IResponse::RESPONSE__ID => '2f5d0719-5b82-4280-9b3b-10f23aff226b',
                        IResponse::RESPONSE__VERSION => IResponse::VERSION_CURRENT,
                        IResponse::RESPONSE__ERROR => [
                            IResponse::RESPONSE__ERROR_CODE => 500,
                            IResponse::RESPONSE__ERROR_DATA => [],
                            IResponse::RESPONSE__ERROR_MESSAGE => 'Expected exception'
                        ]
                    ],
                    json_decode($response->getBody(), true)
                );
            }
        }
    }

    public function testSpecs()
    {
        $this->initOperationEnv();

        $app = App::create();
        $routes = $app->getRouteCollector()->getRoutes();
        foreach ($routes as $route) {
            if ($route->getPattern() == '/specs') {
                $dispatcher = $route->getCallable();
                /**
                 * @var ResponseInterface $response
                 */
                $response = $dispatcher($this->getPsrRequest(), $this->getPsrResponse(), []);
                $this->assertEquals(
                    [
                        IResponse::RESPONSE__ID => '2f5d0719-5b82-4280-9b3b-10f23aff226b',
                        IResponse::RESPONSE__VERSION => IResponse::VERSION_CURRENT,
                        IResponse::RESPONSE__RESULT => [
                            'jsonrpc.operation.index' => []
                        ]
                    ],
                    json_decode($response->getBody(), true)
                );

                $response = $dispatcher($this->getPsrRequest('.all'), $this->getPsrResponse(), []);
                $this->assertEquals(
                    [
                        IResponse::RESPONSE__ID => '2f5d0719-5b82-4280-9b3b-10f23aff226b',
                        IResponse::RESPONSE__VERSION => IResponse::VERSION_CURRENT,
                        IResponse::RESPONSE__RESULT => [
                            'jsonrpc.operation.index' => []
                        ]
                    ],
                    json_decode($response->getBody(), true)
                );
            }
        }
    }

    /**
     * Create operation.
     * Create extension.
     * Create plugin.
     */
    protected function initOperationEnv(): void
    {
        $this->createWithSnuffRepo('jsonRpcOperationRepository', new JsonRpcOperation($this->opData));
        $this->createPluginEmpty([IStageJsonRpcInit::NAME, IStageRunJsonRpc::NAME__BEFORE]);
    }
}
