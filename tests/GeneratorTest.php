<?php
namespace tests;

use Dotenv\Dotenv;
use PHPUnit\Framework\TestCase;
use extas\components\jsonrpc\Crawler;
use extas\components\jsonrpc\Generator;

/**
 * Class GeneratorTest
 *
 * @package tests
 * @author jeyroik@gmail.com
 */
class GeneratorTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $env = Dotenv::create(getcwd() . '/tests/');
        $env->load();
    }

    public function testGenerate()
    {
        $generator = new class([
            Generator::FIELD__FILTER => '',
            Generator::FIELD__ONLY_EDGE => false
        ]) extends Generator {
            public array $generationResult = [];
            protected function exportGeneratedData(string $path): void
            {
                $this->generationResult = $this->result;
                parent::exportGeneratedData(getcwd() . '/tests/generated.specs.json');
            }
        };

        $crawler = new Crawler();
        $plugins = $crawler->crawlPlugins(getcwd() . '/src/components', 'PluginInstall');

        $isDone = $generator->generate($plugins, '');
        $this->assertTrue($isDone);

        $mustBe = include 'specs.php';
        $this->assertEquals(
            $mustBe,
            $generator->generationResult['jsonrpc_operations']
        );

        $generator->setFilter('unknown');
        $generator->setOnlyEdge(false);

        $generator->generate($plugins, '');
        $this->assertEmpty($generator->generationResult['jsonrpc_operations']);
    }
}
