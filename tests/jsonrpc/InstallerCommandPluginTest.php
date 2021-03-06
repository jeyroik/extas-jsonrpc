<?php
namespace tests\jsonrpc;

use extas\commands\JsonrpcCommand;
use extas\components\crawlers\CrawlerRepository;
use extas\components\extensions\ExtensionRepository;
use extas\components\generators\GeneratorRepository;
use extas\components\options\CommandOptionRepository;
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
            'generatorRepository' => GeneratorRepository::class,
            'extensionRepository' => ExtensionRepository::class,
            'commandOptionRepository' => CommandOptionRepository::class
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
