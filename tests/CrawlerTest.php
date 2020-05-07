<?php
namespace tests;

use Dotenv\Dotenv;
use extas\components\jsonrpc\Crawler;
use extas\components\plugins\PluginInstallJsonRpcOperations;
use PHPUnit\Framework\TestCase;

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

    public function testCrawlPlugins()
    {
        $crawler = new Crawler();
        $plugins = $crawler->crawlPlugins(getcwd() . '/src/components', 'PluginInstallJson');
        $this->assertCount(1, $plugins);
        $plugin = array_shift($plugins);
        $this->assertTrue($plugin instanceof PluginInstallJsonRpcOperations);
    }
}
