<?php
namespace tests\jsonrpc;

use extas\components\repositories\TSnuffRepository;
use extas\components\operations\OperationRepository;
use extas\components\jsonrpc\Router;

use PHPUnit\Framework\TestCase;
use Dotenv\Dotenv;

/**
 * Class RouterTest
 *
 * @package tests
 * @author jeyroik@gmail.com
 */
class RouterTest extends TestCase
{
    use TSnuffRepository;

    protected function setUp(): void
    {
        parent::setUp();
        $env = Dotenv::create(getcwd() . '/tests/');
        $env->load();
        $this->registerSnuffRepos([
            'jsonRpcOperationRepository' => OperationRepository::class
        ]);
    }

    protected function tearDown(): void
    {
        $this->unregisterSnuffRepos();
    }

    public function testHasOperation()
    {
        $router = new Router();
        $this->assertFalse($router->hasOperation('unknown'));
    }
}
