<?php
namespace tests\operations;

use extas\interfaces\jsonrpc\IResponse;

use extas\components\conditions\Condition;
use extas\components\conditions\ConditionLike;
use extas\components\conditions\ConditionRepository;
use extas\components\extensions\ExtensionRepository;
use extas\components\http\TSnuffHttp;
use extas\components\jsonrpc\operations\Index;
use extas\components\operations\JsonRpcOperation as Operation;
use extas\components\jsonrpc\operations\OperationDispatcher;
use extas\components\plugins\TSnuffPlugins;
use extas\components\protocols\ProtocolRepository;
use extas\components\repositories\TSnuffRepository;
use extas\components\operations\OperationRepository;
use extas\interfaces\samples\parameters\ISampleParameter;
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
    use TSnuffRepository;
    use TSnuffPlugins;
    use TSnuffHttp;

    protected array $opData = [
        Operation::FIELD__NAME => 'jsonrpc.operation.index',
        Operation::FIELD__CLASS => Index::class,
        Operation::FIELD__SPECS => [],
        Operation::FIELD__PARAMETERS => [
            Operation::PARAM__METHOD => [
                ISampleParameter::FIELD__NAME => Operation::PARAM__ITEM_NAME,
                ISampleParameter::FIELD__VALUE => 'index'
            ],
            Operation::PARAM__ITEM_CLASS => [
                ISampleParameter::FIELD__NAME => Operation::PARAM__ITEM_CLASS,
                ISampleParameter::FIELD__VALUE => Operation::class
            ],
            Operation::PARAM__ITEM_REPOSITORY => [
                ISampleParameter::FIELD__NAME => Operation::PARAM__ITEM_NAME,
                ISampleParameter::FIELD__VALUE => 'jsonRpcOperationRepository'
            ],
            Operation::PARAM__ITEM_NAME => [
                ISampleParameter::FIELD__NAME => Operation::PARAM__ITEM_NAME,
                ISampleParameter::FIELD__VALUE => 'jsonrpc operation'
            ]
        ]
    ];

    protected function setUp(): void
    {
        parent::setUp();
        $env = Dotenv::create(getcwd() . '/tests/');
        $env->load();
        $this->registerSnuffRepos([
            'jsonRpcOperationRepository' => OperationRepository::class,
            'protocolRepository' => ProtocolRepository::class,
            'conditionRepository' => ConditionRepository::class,
            'extensionRepository' => ExtensionRepository::class
        ]);
    }

    protected function tearDown(): void
    {
        $this->unregisterSnuffRepos();
    }

    public function testFilter()
    {
        $operation = $this->createWithSnuffRepo('jsonRpcOperationRepository', new Operation($this->opData));
        $this->createWithSnuffRepo('conditionRepository', new Condition([
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
