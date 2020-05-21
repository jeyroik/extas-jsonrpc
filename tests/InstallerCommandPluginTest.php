<?php
namespace tests;

use extas\commands\JsonrpcCommand;
use extas\components\extensions\TSnuffExtensions;
use extas\components\plugins\installers\InstallerCommandPlugin;
use extas\interfaces\jsonrpc\crawlers\ICrawlerRepository;
use extas\interfaces\jsonrpc\generators\IGeneratorRepository;
use PHPUnit\Framework\TestCase;
use Dotenv\Dotenv;

/**
 * Class InstallerCommandPluginTest
 *
 * @package tests
 * @author jeyroik@gmail.com
 */
class InstallerCommandPluginTest extends TestCase
{
    use TSnuffExtensions;

    protected function setUp(): void
    {
        parent::setUp();
        $env = Dotenv::create(getcwd() . '/tests/');
        $env->load();

        $this->addReposForExt([
            'jsonRpcCrawlerRepository' => ICrawlerRepository::class,
            'jsonRpcGeneratorRepository' => IGeneratorRepository::class
        ]);
        $this->createRepoExt(['jsonRpcCrawlerRepository', 'jsonRpcGeneratorRepository']);
    }

    protected function tearDown(): void
    {
        $this->deleteSnuffExtensions();
    }

    public function testInvoke()
    {
        $plugin = new InstallerCommandPlugin();
        $command = $plugin();
        $this->assertTrue($command instanceof JsonrpcCommand);
    }
}
