<?php
namespace tests\operations;

use extas\components\extensions\TSnuffExtensions;
use extas\components\http\TSnuffHttp;
use extas\components\jsonrpc\operations\Create;
use extas\components\jsonrpc\operations\Operation;
use extas\components\jsonrpc\operations\OperationRepository;
use extas\components\plugins\Plugin;
use extas\components\protocols\ProtocolRepository;
use extas\interfaces\jsonrpc\IResponse;
use extas\interfaces\jsonrpc\operations\IOperation;
use extas\interfaces\jsonrpc\operations\IOperationDispatcher;
use extas\interfaces\jsonrpc\operations\IOperationRepository;
use extas\interfaces\repositories\IRepository;
use PHPUnit\Framework\TestCase;
use Dotenv\Dotenv;

/**
 * Class CreateTest
 *
 * @package tests\operations
 * @author jeyroik@gmail.com
 */
class CreateTest extends TestCase
{
    use TSnuffExtensions;
    use TSnuffHttp;

    protected IRepository $opRepo;

    protected array $opDataMissedPk = [
        Operation::FIELD__NAME => 'jsonrpc.operation.create',
        Operation::FIELD__CLASS => Create::class,
        Operation::FIELD__METHOD => 'create',
        Operation::FIELD__SPEC => [],
        Operation::FIELD__ITEM_CLASS => Plugin::class,
        Operation::FIELD__ITEM_REPO => IOperationRepository::class,
        Operation::FIELD__ITEM_NAME => 'jsonrpc operation'
    ];

    protected array $opData = [
        Operation::FIELD__NAME => 'jsonrpc.operation.create',
        Operation::FIELD__CLASS => Create::class,
        Operation::FIELD__METHOD => 'create',
        Operation::FIELD__SPEC => [],
        Operation::FIELD__ITEM_CLASS => Operation::class,
        Operation::FIELD__ITEM_REPO => IOperationRepository::class,
        Operation::FIELD__ITEM_NAME => 'jsonrpc operation'
    ];

    protected array $opDataNew = [
        Operation::FIELD__NAME => 'jsonrpc.operation.create.new',
        Operation::FIELD__CLASS => '',
        Operation::FIELD__METHOD => 'create',
        Operation::FIELD__SPEC => [],
        Operation::FIELD__ITEM_CLASS => '',
        Operation::FIELD__ITEM_REPO => '',
        Operation::FIELD__ITEM_NAME => ''
    ];

    protected function setUp(): void
    {
        parent::setUp();
        $env = Dotenv::create(getcwd() . '/tests/');
        $env->load();

        $this->opRepo = new OperationRepository();
        $this->addReposForExt([
            IOperationRepository::class => OperationRepository::class,
            'jsonRpcOperationRepository' => OperationRepository::class,
            'protocolRepository' => ProtocolRepository::class
        ]);
        $this->createRepoExt([
            IOperationRepository::class,
            'jsonRpcOperationRepository',
            'protocolRepository'
        ]);
    }

    protected function tearDown(): void
    {
        $this->opRepo->delete([Operation::FIELD__METHOD => 'create']);
        $this->deleteSnuffExtensions();
    }

    public function testMissedPkMethod()
    {
        $operation = $this->opRepo->create(new Operation($this->opDataMissedPk));
        $dispatcher = $this->getDispatcher($operation, '.create');
        $response = $dispatcher();
        $jsonRpcResponse = json_decode($response->getBody(), true);
        $this->assertEquals(
            [
                IResponse::RESPONSE__ID => '2f5d0719-5b82-4280-9b3b-10f23aff226b',
                IResponse::RESPONSE__VERSION => IResponse::VERSION_CURRENT,
                IResponse::RESPONSE__ERROR => [
                    IResponse::RESPONSE__ERROR_CODE => 500,
                    IResponse::RESPONSE__ERROR_DATA => [],
                    IResponse::RESPONSE__ERROR_MESSAGE => 'Item has not method "getName"'
                ]
            ],
            $jsonRpcResponse
        );
    }

    public function testItemAlreadyExists()
    {
        $operation = $this->opRepo->create(new Operation($this->opData));
        $dispatcher = $this->getDispatcher($operation, '.create.existed');
        $response = $dispatcher();
        $jsonRpcResponse = json_decode($response->getBody(), true);
        $this->assertEquals(
            [
                IResponse::RESPONSE__ID => '2f5d0719-5b82-4280-9b3b-10f23aff226b',
                IResponse::RESPONSE__VERSION => IResponse::VERSION_CURRENT,
                IResponse::RESPONSE__ERROR => [
                    IResponse::RESPONSE__ERROR_CODE => 400,
                    IResponse::RESPONSE__ERROR_DATA => [],
                    IResponse::RESPONSE__ERROR_MESSAGE => 'Jsonrpc operation already exist'
                ]
            ],
            $jsonRpcResponse
        );
    }

    public function testSuccess()
    {
        $operation = $this->opRepo->create(new Operation($this->opData));
        $dispatcher = $this->getDispatcher($operation, '.create');
        $response = $dispatcher();
        $jsonRpcResponse = json_decode($response->getBody(), true);
        $this->assertEquals(
            [
                IResponse::RESPONSE__ID => '2f5d0719-5b82-4280-9b3b-10f23aff226b',
                IResponse::RESPONSE__VERSION => IResponse::VERSION_CURRENT,
                IResponse::RESPONSE__RESULT => $this->opDataNew
            ],
            $jsonRpcResponse
        );
    }

    /**
     * @param IOperation $operation
     * @param string $streamSuffix
     * @return IOperationDispatcher
     */
    protected function getDispatcher(IOperation $operation, string $streamSuffix): IOperationDispatcher
    {
        return new Create([
            Create::FIELD__PSR_REQUEST => $this->getPsrRequest($streamSuffix),
            Create::FIELD__PSR_RESPONSE => $this->getPsrResponse(),
            Create::FIELD__OPERATION => $operation
        ]);
    }
}
