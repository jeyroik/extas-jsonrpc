<?php
namespace tests;

use Dotenv\Dotenv;
use extas\components\jsonrpc\crawlers\ByDocComment;
use extas\components\jsonrpc\crawlers\ByPluginInstallDefault;
use extas\components\plugins\jsonrpc\PluginDefaultArguments;
use extas\components\plugins\PluginInstallJsonRpcOperations;
use PhpCsFixer\Console\Output\NullOutput;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;

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
        $this->assertCount(1, $plugins);
        $plugin = array_shift($plugins);
        $this->assertTrue($plugin instanceof PluginInstallJsonRpcOperations);

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
        $this->assertEmpty($operations);

        $crawler = new ByDocComment([
            ByDocComment::FIELD__INPUT => $this->getInput('-', '/tests'),
            ByDocComment::FIELD__OUTPUT => new NullOutput()
        ]);

        $operations = $crawler();
        $this->assertCount(1, $operations);
        $plugin = array_shift($operations);
        $this->assertTrue($plugin instanceof OperationWithDocComment);
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
                new InputOption(PluginDefaultArguments::OPTION__SPECS_PATH),
                new InputOption(PluginDefaultArguments::OPTION__PREFIX)
            ])
        );
    }
}
