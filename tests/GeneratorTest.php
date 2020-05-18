<?php
namespace tests;

use Dotenv\Dotenv;
use extas\components\jsonrpc\crawlers\ByPluginInstallDefault as Crawler;
use extas\components\jsonrpc\generators\ByPluginInstallDefault;
use extas\components\plugins\jsonrpc\PluginDefaultArguments;
use PhpCsFixer\Console\Output\NullOutput;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputOption;

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

    public function testGenerateByPluginInstallDefault()
    {
        $generator = new class([
            ByPluginInstallDefault::FIELD__INPUT => new ArrayInput(
                [
                    PluginDefaultArguments::OPTION__SPECS_PATH => getcwd() . '/src/components',
                    PluginDefaultArguments::OPTION__PREFIX => 'PluginInstallJson',
                    PluginDefaultArguments::OPTION__FILTER => '',
                    PluginDefaultArguments::OPTION__ONLY_EDGE => false
                ],
                [
                    new InputOption(PluginDefaultArguments::OPTION__SPECS_PATH),
                    new InputOption(PluginDefaultArguments::OPTION__PREFIX),
                    new InputOption(PluginDefaultArguments::OPTION__FILTER),
                    new InputOption(PluginDefaultArguments::OPTION__ONLY_EDGE)
                ]
            ),
            ByPluginInstallDefault::FIELD__OUTPUT => new NullOutput()
        ]) extends ByPluginInstallDefault {
            public array $generationResult = [];
            protected function exportGeneratedData(string $path): void
            {
                $this->generationResult = $this->result;
                parent::exportGeneratedData(getcwd() . '/tests/generated.specs.json');
            }
        };

        $crawler = new Crawler([
            Crawler::FIELD__INPUT => new ArrayInput(
                [
                    PluginDefaultArguments::OPTION__SPECS_PATH => getcwd() . '/src/components',
                    PluginDefaultArguments::OPTION__PREFIX => 'PluginInstall'
                ],
                [
                    new InputOption(PluginDefaultArguments::OPTION__SPECS_PATH),
                    new InputOption(PluginDefaultArguments::OPTION__PREFIX)
                ]
            ),
            Crawler::FIELD__OUTPUT => new NullOutput()
        ]);
        $plugins = $crawler();

        $isDone = $generator->generate($plugins, '');
        $this->assertTrue($isDone);

        $mustBe = include 'specs.php';
        $this->assertEquals(
            $mustBe,
            $generator->generationResult['jsonrpc_operations']
        );
        unlink(getcwd() . '/tests/generated.specs.json');
    }
}
