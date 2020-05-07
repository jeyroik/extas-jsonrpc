<?php
namespace tests;

use Dotenv\Dotenv;
use extas\components\extensions\Extension;
use extas\components\extensions\ExtensionRepository;
use extas\components\extensions\ExtensionRepositoryGet;
use extas\components\jsonrpc\operations\OperationRepository;
use extas\components\jsonrpc\Router;
use extas\components\SystemContainer;
use extas\interfaces\extensions\IExtensionRepositoryGet;
use extas\interfaces\repositories\IRepository;
use PHPUnit\Framework\TestCase;

/**
 * Class RouterTest
 *
 * @package tests
 * @author jeyroik@gmail.com
 */
class RouterTest extends TestCase
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
    }

    protected function tearDown(): void
    {
        $this->extRepo->delete([Extension::FIELD__CLASS => ExtensionRepositoryGet::class]);
    }

    public function testHasOperation()
    {
        $router = new Router();
        $this->extRepo->create(new Extension([
            Extension::FIELD__CLASS => ExtensionRepositoryGet::class,
            Extension::FIELD__INTERFACE => IExtensionRepositoryGet::class,
            Extension::FIELD__SUBJECT => '*',
            Extension::FIELD__METHODS => ['jsonRpcOperationRepository']
        ]));

        $this->assertFalse($router->hasOperation('unknown'));
    }
}
