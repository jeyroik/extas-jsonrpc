<?php
namespace tests\jsonrpc\commands;

use extas\commands\JsonrpcCommand;
use extas\components\console\TSnuffConsole;
use extas\components\crawlers\Crawler;
use extas\components\crawlers\CrawlerRepository;
use extas\components\extensions\ExtensionRepository;
use extas\components\generators\Generator;
use extas\components\generators\GeneratorRepository;
use extas\components\jsonrpc\crawlers\ByDocComment;
use extas\components\options\CommandOption;
use extas\components\options\CommandOptionRepository;
use extas\components\packages\entities\EntityRepository;
use extas\components\plugins\PluginRepository;
use extas\components\plugins\TSnuffPlugins;
use extas\components\repositories\TSnuffRepository;

use Dotenv\Dotenv;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\BufferedOutput;

/**
 * Class JsonRpcCommandTest
 *
 * @package tests\jsonrpc\commands
 * @author jeyroik <jeyroik@gmail.com>
 */
class JsonRpcCommandTest extends TestCase
{
    use TSnuffConsole;
    use TSnuffRepository;
    use TSnuffPlugins;

    protected function setUp(): void
    {
        parent::setUp();
        $env = Dotenv::create(getcwd() . '/tests/');
        $env->load();

        $this->registerSnuffRepos([
            'pluginRepository' => PluginRepository::class,
            'extensionRepository' => ExtensionRepository::class,
            'entityRepository' => EntityRepository::class,
            'crawlerRepository' => CrawlerRepository::class,
            'generatorRepository' => GeneratorRepository::class,
            'commandOptionRepository' => CommandOptionRepository::class
        ]);
    }

    protected function tearDown(): void
    {
        $this->unregisterSnuffRepos();
        if (is_file(getcwd() . '/tests/runtime.json')) {
            unlink(getcwd() . '/tests/runtime.json');
        }
    }

    public function testRun()
    {
        /**
         * @var BufferedOutput $output
         */
        $output = $this->getOutput(true);

        $this->prepareCommand();

        $command = new JsonrpcCommand();
        $command->run($this->getTestInput(), $output);

        $outputText = $output->fetch();
        $this->assertStringContainsString('Exported result of generator "test.generator"', $outputText);
        $storage = json_decode(file_get_contents(getcwd() . '/tests/runtime.json'), true);
        $this->assertCount(
            3,
            $storage['jsonrpc_operations'],
            'Incorrect operations count:' . print_r($storage['jsonrpc_operations'],true) . PHP_EOL
            . $outputText
        );
    }

    /**
     * @return InputInterface
     */
    protected function getTestInput(): InputInterface
    {
        return $this->getInput([
            'prefix-doc-comment' => 'DocComment',
            'filter' => '',
            'path' => getcwd() . '/tests',
            'export-path' => '/tests/runtime.json',
            'crawler-by-doc-comment' => true,
            'generator-test-generator' => true
        ]);
    }

    protected function prepareCommand()
    {
        $this->createWithSnuffRepo('commandOptionRepository', new CommandOption([
            'name' => 'path-jsonrpc-doc-comment',
            'shortcut' => '',
            'mode' => 4,
            'default' => '',
            'description' => '',
            'commands' => ['extas-jsonrpc']
        ]));
        $this->createWithSnuffRepo('commandOptionRepository', new CommandOption([
            'name' => 'prefix-jsonrpc-doc-comment',
            'shortcut' => '',
            'mode' => 4,
            'default' => '',
            'description' => '',
            'commands' => ['extas-jsonrpc']
        ]));
        $this->createWithSnuffRepo('commandOptionRepository', new CommandOption([
            'name' => 'filter',
            'shortcut' => '',
            'mode' => 4,
            'default' => '',
            'description' => '',
            'commands' => ['extas-jsonrpc']
        ]));

        $this->createWithSnuffRepo('crawlerRepository', new Crawler([
            Crawler::FIELD__NAME => 'by.doc.comment',
            Crawler::FIELD__CLASS => ByDocComment::class,
            Crawler::FIELD__TAGS => ['jsonrpc']
        ]));

        $this->createWithSnuffRepo('generatorRepository', new Generator([
            Generator::FIELD__NAME => 'test.generator',
            Generator::FIELD__CLASS => \extas\components\generators\jsonrpc\ByDocComment::class,
            Generator::FIELD__TAGS => ['jsonrpc']
        ]));

        file_put_contents(getcwd() . '/tests/runtime.json', json_encode([
            'jsonrpc_operations' => [
                [
                    'name' => 'op1'
                ]
            ]
        ]));
    }
}
