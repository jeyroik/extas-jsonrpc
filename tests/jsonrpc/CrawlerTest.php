<?php
namespace tests\jsonrpc;

use extas\components\console\TSnuffConsole;
use extas\components\jsonrpc\crawlers\ByDocComment;
use extas\components\jsonrpc\crawlers\ByInstallSection;
use extas\components\plugins\init\InitGenerators;
use extas\components\plugins\install\InstallJsonRpcOperations;
use extas\components\plugins\jsonrpc\PluginDefaultArguments;

use Dotenv\Dotenv;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Input\InputInterface;
use tests\OperationWithDocComment;

/**
 * Class CrawlerTest
 *
 * @package tests
 * @author jeyroik@gmail.com
 */
class CrawlerTest extends TestCase
{
    use TSnuffConsole;

    protected function setUp(): void
    {
        parent::setUp();
        $env = Dotenv::create(getcwd() . '/tests/');
        $env->load();
    }

    public function testCrawlByInstallSection()
    {
        $crawler = new ByInstallSection([
            ByInstallSection::FIELD__INPUT => $this->getTestInput(),
            ByInstallSection::FIELD__OUTPUT => $this->getOutput()
        ]);
        $plugins = $crawler();
        $this->assertCount(1, $plugins);
        $plugin = array_shift($plugins);
        $this->assertTrue(in_array(
            get_class($plugin),
            [
                InstallJsonRpcOperations::class,
                InitGenerators::class
            ]
        ));

        $crawler = new ByInstallSection([
            ByInstallSection::FIELD__INPUT => $this->getTestInput('PluginInstallMy', '/tests'),
            ByInstallSection::FIELD__OUTPUT => $this->getOutput()
        ]);
        $plugins = $crawler();
        $this->assertEmpty($plugins);
    }

    public function testCrawlByDocComment()
    {
        $crawler = new ByDocComment([
            ByDocComment::FIELD__INPUT => $this->getTestInput(),
            ByDocComment::FIELD__OUTPUT => $this->getOutput()
        ]);
        $operations = $crawler();
        $this->assertEmpty($operations, 'Found doc-comments operations in src');

        $crawler = new ByDocComment([
            ByDocComment::FIELD__INPUT => $this->getTestInput('DocComment', '/tests'),
            ByDocComment::FIELD__OUTPUT => $this->getOutput()
        ]);

        $operations = $crawler();
        $this->assertCount(
            1,
            $operations,
            'Incorrect operations count found:' . print_r($operations, true)
        );
        $plugin = array_shift($operations);
        $this->assertTrue(
            $plugin instanceof OperationWithDocComment,
            'Incorrect operation instance: ' . get_class($plugin)
        );
    }

    /**
     * @param string $prefix
     * @param string $path
     * @return InputInterface
     */
    protected function getTestInput(
        string $prefix = 'InstallJson',
        string $path = '/src/components'
    ): InputInterface
    {
        return $this->getInput([
            PluginDefaultArguments::OPTION__CRAWL_PATH => getcwd() . $path,
            PluginDefaultArguments::OPTION__PREFIX => $prefix
        ]);
    }
}
