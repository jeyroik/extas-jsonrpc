<?php
namespace tests;

use Dotenv\Dotenv;
use extas\components\extensions\ExtensionRepository;
use extas\components\extensions\TSnuffExtensions;
use extas\components\jsonrpc\operations\OperationRepository;
use extas\components\jsonrpc\Router;
use extas\components\SystemContainer;
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
    use TSnuffExtensions;

    protected IRepository $opRepo;

    protected function setUp(): void
    {
        parent::setUp();
        $env = Dotenv::create(getcwd() . '/tests/');
        $env->load();

        $this->opRepo = new OperationRepository();

        SystemContainer::addItem('jsonRpcOperationRepository', OperationRepository::class);
    }

    protected function tearDown(): void
    {
        $this->deleteSnuffExtensions();
    }

    public function testHasOperation()
    {
        $router = new Router();
        $this->createRepoExt(['jsonRpcOperationRepository']);
        $this->assertFalse($router->hasOperation('unknown'));
    }
}
