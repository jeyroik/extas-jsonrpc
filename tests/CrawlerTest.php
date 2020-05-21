<?php
namespace tests;

use Dotenv\Dotenv;
use extas\components\jsonrpc\crawlers\ByDocComment;
use extas\components\jsonrpc\crawlers\ByPluginInstallDefault;
use extas\components\plugins\jsonrpc\PluginDefaultArguments;
use extas\components\plugins\PluginInstallJsonRpcCrawlers;
use extas\components\plugins\PluginInstallJsonRpcGenerators;
use extas\components\plugins\PluginInstallJsonRpcOperations;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\NullOutput;

/**
 * Class CrawlerTest
 *
 * @package tests
 * @author jeyroik@gmail.com
 */
class CrawlerTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $env = Dotenv::create(getcwd() . '/tests/');
        $env->load();
    }

    public function testCrawlByPluginInstallDefault()
    {
        $crawler = new ByPluginInstallDefault([
            ByPluginInstallDefault::FIELD__INPUT => $this->getInput(),
            ByPluginInstallDefault::FIELD__OUTPUT => new NullOutput()
        ]);
        $plugins = $crawler();
        $this->assertCount(3, $plugins);
        $plugin = array_shift($plugins);
        $this->assertTrue(in_array(
            get_class($plugin),
            [
                PluginInstallJsonRpcOperations::class,
                PluginInstallJsonRpcCrawlers::class,
                PluginInstallJsonRpcGenerators::class
            ]
        ));

        $crawler = new ByPluginInstallDefault([
            ByPluginInstallDefault::FIELD__INPUT => $this->getInput('PluginInstallMy', '/tests'),
            ByPluginInstallDefault::FIELD__OUTPUT => new NullOutput()
        ]);
        $plugins = $crawler();
        $this->assertEmpty($plugins);
    }

    public function testCrawlByDocComment()
    {
        $crawler = new ByDocComment([
            ByDocComment::FIELD__INPUT => $this->getInput(),
            ByDocComment::FIELD__OUTPUT => new NullOutput()
        ]);
        $operations = $crawler();
        $this->assertEmpty($operations, 'Found doc-comments operations in src');

        $crawler = new ByDocComment([
            ByDocComment::FIELD__INPUT => $this->getInput('Operation', '/tests'),
            ByDocComment::FIELD__OUTPUT => new NullOutput()
        ]);

        $operations = $crawler();
        $this->assertCount(1, $operations, 'Incorrect operations count found');
        $plugin = array_shift($operations);
        $this->assertTrue($plugin instanceof OperationWithDocComment, 'Incorrect operation instance');
    }

    /**
     * @param string $prefix
     * @param string $path
     * @return InputInterface
     */
    protected function getInput(
        string $prefix = 'PluginInstallJson',
        string $path = '/src/components'
    ): InputInterface
    {
        return new ArrayInput(
            [
                '--' . PluginDefaultArguments::OPTION__CRAWL_PATH => getcwd() . $path,
                '--' . PluginDefaultArguments::OPTION__PREFIX => $prefix
            ],
            new InputDefinition([
                new InputOption(PluginDefaultArguments::OPTION__CRAWL_PATH),
                new InputOption(PluginDefaultArguments::OPTION__PREFIX)
            ])
        );
    }
}
