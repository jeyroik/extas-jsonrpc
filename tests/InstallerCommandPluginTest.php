<?php
namespace tests;

use extas\commands\JsonrpcCommand;
use extas\components\crawlers\CrawlerRepository;
use extas\components\extensions\ExtensionRepository;
use extas\components\jsonrpc\generators\GeneratorRepository;
use extas\components\plugins\installers\InstallerCommandPlugin;
use extas\components\repositories\TSnuffRepository;

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
    use TSnuffRepository;

    protected function setUp(): void
    {
        parent::setUp();
        $env = Dotenv::create(getcwd() . '/tests/');
        $env->load();

        $this->registerSnuffRepos([
            'crawlerRepository' => CrawlerRepository::class,
            'jsonRpcGeneratorRepository' => GeneratorRepository::class,
            'extensionRepository' => ExtensionRepository::class
        ]);
    }

    protected function tearDown(): void
    {
        $this->unregisterSnuffRepos();
    }

    public function testInvoke()
    {
        $plugin = new InstallerCommandPlugin();
        $command = $plugin();
        $this->assertTrue($command instanceof JsonrpcCommand);
    }
}
