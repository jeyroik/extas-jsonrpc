<?php
namespace tests\operations;

use extas\interfaces\jsonrpc\IResponse;
use extas\interfaces\operations\IJsonRpcOperation;
use extas\interfaces\jsonrpc\operations\IOperationDispatcher;

use extas\components\operations\OperationRepository;
use extas\components\extensions\ExtensionRepository;
use extas\components\http\TSnuffHttp;
use extas\components\operations\JsonRpcOperation;
use extas\components\jsonrpc\operations\Update;
use extas\components\plugins\Plugin;
use extas\components\protocols\ProtocolRepository;
use extas\components\repositories\TSnuffRepository;

use extas\interfaces\samples\parameters\ISampleParameter;
use PHPUnit\Framework\TestCase;
use Dotenv\Dotenv;

/**
 * Class UpdateTest
 *
 * @package tests\operations
 * @author jeyroik@gmail.com
 */
class UpdateTest extends TestCase
{
    use TSnuffRepository;
    use TSnuffHttp;

    protected array $opDataMissedPk = [
        JsonRpcOperation::FIELD__NAME => 'jsonrpc.operation.update',
        JsonRpcOperation::FIELD__CLASS => Update::class,
        JsonRpcOperation::FIELD__SPECS => [],
        JsonRpcOperation::FIELD__PARAMETERS => [
            JsonRpcOperation::PARAM__METHOD => [
                ISampleParameter::FIELD__NAME => JsonRpcOperation::PARAM__ITEM_NAME,
                ISampleParameter::FIELD__VALUE => 'update'
            ],
            JsonRpcOperation::PARAM__ITEM_CLASS => [
                ISampleParameter::FIELD__NAME => JsonRpcOperation::PARAM__ITEM_CLASS,
                ISampleParameter::FIELD__VALUE => Plugin::class
            ],
            JsonRpcOperation::PARAM__ITEM_REPOSITORY => [
                ISampleParameter::FIELD__NAME => JsonRpcOperation::PARAM__ITEM_NAME,
                ISampleParameter::FIELD__VALUE => 'jsonRpcOperationRepository'
            ],
            JsonRpcOperation::PARAM__ITEM_NAME => [
                ISampleParameter::FIELD__NAME => JsonRpcOperation::PARAM__ITEM_NAME,
                ISampleParameter::FIELD__VALUE => 'jsonrpc operation'
            ]
        ]
    ];

    protected array $opData = [
        JsonRpcOperation::FIELD__NAME => 'jsonrpc.operation.update',
        JsonRpcOperation::FIELD__CLASS => Update::class,
        JsonRpcOperation::FIELD__SPECS => [],
        JsonRpcOperation::FIELD__PARAMETERS => [
            JsonRpcOperation::PARAM__METHOD => [
                ISampleParameter::FIELD__NAME => JsonRpcOperation::PARAM__ITEM_NAME,
                ISampleParameter::FIELD__VALUE => 'update'
            ],
            JsonRpcOperation::PARAM__ITEM_CLASS => [
                ISampleParameter::FIELD__NAME => JsonRpcOperation::PARAM__ITEM_CLASS,
                ISampleParameter::FIELD__VALUE => JsonRpcOperation::class
            ],
            JsonRpcOperation::PARAM__ITEM_REPOSITORY => [
                ISampleParameter::FIELD__NAME => JsonRpcOperation::PARAM__ITEM_NAME,
                ISampleParameter::FIELD__VALUE => 'jsonRpcOperationRepository'
            ],
            JsonRpcOperation::PARAM__ITEM_NAME => [
                ISampleParameter::FIELD__NAME => JsonRpcOperation::PARAM__ITEM_NAME,
                ISampleParameter::FIELD__VALUE => 'jsonrpc operation'
            ]
        ]
    ];

    protected array $opDataNew = [
        JsonRpcOperation::FIELD__NAME => 'jsonrpc.operation.update',
        JsonRpcOperation::FIELD__CLASS => Update::class,
        JsonRpcOperation::FIELD__SPECS => [],
        JsonRpcOperation::FIELD__PARAMETERS => [
            JsonRpcOperation::PARAM__METHOD => [
                ISampleParameter::FIELD__NAME => JsonRpcOperation::PARAM__ITEM_NAME,
                ISampleParameter::FIELD__VALUE => 'update'
            ],
            JsonRpcOperation::PARAM__ITEM_CLASS => [
                ISampleParameter::FIELD__NAME => JsonRpcOperation::PARAM__ITEM_CLASS,
                ISampleParameter::FIELD__VALUE => JsonRpcOperation::class
            ],
            JsonRpcOperation::PARAM__ITEM_REPOSITORY => [
                ISampleParameter::FIELD__NAME => JsonRpcOperation::PARAM__ITEM_NAME,
                ISampleParameter::FIELD__VALUE => 'jsonRpcOperationRepository'
            ],
            JsonRpcOperation::PARAM__ITEM_NAME => [
                ISampleParameter::FIELD__NAME => JsonRpcOperation::PARAM__ITEM_NAME,
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
            'extensionRepository' => ExtensionRepository::class
        ]);
    }

    protected function tearDown(): void
    {
        $this->unregisterSnuffRepos();
    }

    public function testMissedPkMethod()
    {
        /**
         * @var IJsonRpcOperation $operation
         */
        $operation = $this->createWithSnuffRepo(
            'jsonRpcOperationRepository',
            new JsonRpcOperation($this->opDataMissedPk)
        );
        $dispatcher = $this->getDispatcher($operation, '.update');
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
            $jsonRpcResponse,
            'Current response: ' . print_r($jsonRpcResponse, true)
        );
    }

    public function testItemUnknown()
    {
        /**
         * @var IJsonRpcOperation $operation
         */
        $operation = $this->createWithSnuffRepo('jsonRpcOperationRepository', new JsonRpcOperation($this->opData));
        $dispatcher = $this->getDispatcher($operation, '.update.unknown');
        $response = $dispatcher();
        $jsonRpcResponse = json_decode($response->getBody(), true);
        $this->assertEquals(
            [
                IResponse::RESPONSE__ID => '2f5d0719-5b82-4280-9b3b-10f23aff226b',
                IResponse::RESPONSE__VERSION => IResponse::VERSION_CURRENT,
                IResponse::RESPONSE__ERROR => [
                    IResponse::RESPONSE__ERROR_CODE => 404,
                    IResponse::RESPONSE__ERROR_DATA => [],
                    IResponse::RESPONSE__ERROR_MESSAGE => 'Unknown entity "Jsonrpc operation"'
                ]
            ],
            $jsonRpcResponse,
            'Current response: ' . print_r($jsonRpcResponse, true)
        );
    }

    public function testSuccess()
    {
        /**
         * @var IJsonRpcOperation $operation
         */
        $operation = $this->createWithSnuffRepo('jsonRpcOperationRepository', new JsonRpcOperation($this->opData));
        $dispatcher = $this->getDispatcher($operation, '.update');
        $response = $dispatcher();
        $jsonRpcResponse = json_decode($response->getBody(), true);
        $this->assertEquals(
            [
                IResponse::RESPONSE__ID => '2f5d0719-5b82-4280-9b3b-10f23aff226b',
                IResponse::RESPONSE__VERSION => IResponse::VERSION_CURRENT,
                IResponse::RESPONSE__RESULT => [$this->opDataNew]
            ],
            $jsonRpcResponse,
            'Current response: ' . print_r($jsonRpcResponse, true)
        );
    }

    /**
     * @param IJsonRpcOperation $operation
     * @param string $streamSuffix
     * @return IOperationDispatcher
     */
    protected function getDispatcher(IJsonRpcOperation $operation, string $streamSuffix): IOperationDispatcher
    {
        return new Update([
            Update::FIELD__PSR_REQUEST => $this->getPsrRequest($streamSuffix),
            Update::FIELD__PSR_RESPONSE => $this->getPsrResponse(),
            Update::FIELD__OPERATION => $operation
        ]);
    }
}
