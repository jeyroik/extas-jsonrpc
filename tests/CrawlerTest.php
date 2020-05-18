<?php
namespace tests;

use Dotenv\Dotenv;
use extas\components\jsonrpc\crawlers\ByPluginInstallDefault;
use extas\components\plugins\jsonrpc\PluginDefaultArguments;
use extas\components\plugins\PluginInstallJsonRpcOperations;
use PhpCsFixer\Console\Output\NullOutput;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Input\ArrayInput;
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
            ByPluginInstallDefault::FIELD__INPUT => new ArrayInput(
                [
                    PluginDefaultArguments::OPTION__SPECS_PATH => getcwd() . '/src/components',
                    PluginDefaultArguments::OPTION__PREFIX => 'PluginInstallJson'
                ],
                [
                    new InputOption(PluginDefaultArguments::OPTION__SPECS_PATH),
                    new InputOption(PluginDefaultArguments::OPTION__PREFIX)
                ]
            ),
            ByPluginInstallDefault::FIELD__OUTPUT => new NullOutput()
        ]);
        $plugins = $crawler();
        $this->assertCount(1, $plugins);
        $plugin = array_shift($plugins);
        $this->assertTrue($plugin instanceof PluginInstallJsonRpcOperations);

        $crawler = new ByPluginInstallDefault([
            ByPluginInstallDefault::FIELD__INPUT => new ArrayInput(
                [
                    PluginDefaultArguments::OPTION__SPECS_PATH => getcwd() . '/tests',
                    PluginDefaultArguments::OPTION__PREFIX => 'PluginInstallMy'
                ],
                [
                    new InputOption(PluginDefaultArguments::OPTION__SPECS_PATH),
                    new InputOption(PluginDefaultArguments::OPTION__PREFIX)
                ]
            ),
            ByPluginInstallDefault::FIELD__OUTPUT => new NullOutput()
        ]);
        $plugins = $crawler();
        $this->assertEmpty($plugins);
    }
}
