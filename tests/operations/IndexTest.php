<?php
namespace tests\operations;

use extas\components\conditions\Condition;
use extas\components\conditions\ConditionLike;
use extas\components\conditions\ConditionRepository;
use extas\components\extensions\TSnuffExtensions;
use extas\components\http\TSnuffHttp;
use extas\components\jsonrpc\operations\Index;
use extas\components\jsonrpc\operations\Operation;
use extas\components\jsonrpc\operations\OperationDispatcher;
use extas\components\jsonrpc\operations\OperationRepository;
use extas\components\plugins\TSnuffPlugins;
use extas\components\protocols\ProtocolRepository;
use extas\interfaces\conditions\IConditionRepository;
use extas\interfaces\jsonrpc\IResponse;
use extas\interfaces\jsonrpc\operations\IOperationRepository;
use extas\interfaces\repositories\IRepository;
use PHPUnit\Framework\TestCase;
use Dotenv\Dotenv;

/**
 * Class IndexTest
 *
 * @package tests\operations
 * @author jeyroik@gmail.com
 */
class IndexTest extends TestCase
{
    use TSnuffExtensions;
    use TSnuffPlugins;
    use TSnuffHttp;

    protected IRepository $opRepo;
    protected IRepository $condRepo;
    protected array $opData = [
        Operation::FIELD__NAME => 'jsonrpc.operation.index',
        Operation::FIELD__CLASS => Index::class,
        Operation::FIELD__METHOD => 'index',
        Operation::FIELD__SPEC => [],
        Operation::FIELD__ITEM_CLASS => Operation::class,
        Operation::FIELD__ITEM_REPO => IOperationRepository::class,
        Operation::FIELD__ITEM_NAME => 'jsonrpc operation'
    ];

    protected function setUp(): void
    {
        parent::setUp();
        $env = Dotenv::create(getcwd() . '/tests/');
        $env->load();

        $this->opRepo = new OperationRepository();
        $this->condRepo = new ConditionRepository();
        $this->addReposForExt([
            IOperationRepository::class => OperationRepository::class,
            'jsonRpcOperationRepository' => OperationRepository::class,
            'protocolRepository' => ProtocolRepository::class,
            'conditionRepository' => ConditionRepository::class,
            IConditionRepository::class => ConditionRepository::class
        ]);
        $this->createRepoExt([
            IOperationRepository::class,
            'jsonRpcOperationRepository',
            'protocolRepository',
            'conditionRepository'
        ]);
    }

    protected function tearDown(): void
    {
        $this->opRepo->delete([Operation::FIELD__CLASS => Index::class]);
        $this->condRepo->delete([Condition::FIELD__NAME => 'like']);
        $this->deleteSnuffExtensions();
    }

    public function testFilter()
    {
        $operation = $this->opRepo->create(new Operation($this->opData));
        $this->condRepo->create(new Condition([
            Condition::FIELD__CLASS => ConditionLike::class,
            Condition::FIELD__ALIASES => ['like', '~'],
            Condition::FIELD__NAME => 'like'
        ]));

        $dispatcher = new Index([
            OperationDispatcher::FIELD__PSR_REQUEST => $this->getPsrRequest('.filter'),
            OperationDispatcher::FIELD__PSR_RESPONSE => $this->getPsrResponse(),
            OperationDispatcher::FIELD__ARGUMENTS => [],
            OperationDispatcher::FIELD__OPERATION => $operation
        ]);

        $response = $dispatcher();
        $jsonRpcResponse = json_decode($response->getBody(), true);

        $this->assertEquals(
            [
                IResponse::RESPONSE__ID => '2f5d0719-5b82-4280-9b3b-10f23aff226b',
                IResponse::RESPONSE__VERSION => IResponse::VERSION_CURRENT,
                IResponse::RESPONSE__RESULT => [
                    'items' => [],
                    'total' => 0
                ]
            ],
            $jsonRpcResponse
        );
    }
}
