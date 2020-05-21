<?php
namespace tests;

use Dotenv\Dotenv;
use extas\components\extensions\TSnuffExtensions;
use extas\components\jsonrpc\generators\ByDocComment;
use extas\components\jsonrpc\generators\ByPluginInstallDefault;
use extas\components\plugins\jsonrpc\PluginDefaultArguments;
use extas\components\plugins\PluginInstallJsonRpcOperations;

use extas\interfaces\jsonrpc\crawlers\ICrawlerRepository;
use extas\interfaces\jsonrpc\generators\IGeneratorRepository;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\NullOutput;

/**
 * Class GeneratorTest
 *
 * @package tests
 * @author jeyroik@gmail.com
 */
class GeneratorTest extends TestCase
{
    use TSnuffExtensions;

    protected function setUp(): void
    {
        parent::setUp();
        $env = Dotenv::create(getcwd() . '/tests/');
        $env->load();

        $this->addReposForExt([
            'jsonRpcCrawlerRepository' => ICrawlerRepository::class,
            'jsonRpcGeneratorRepository' => IGeneratorRepository::class
        ]);
        $this->createRepoExt(['jsonRpcCrawlerRepository', 'jsonRpcGeneratorRepository']);
    }

    protected function tearDown(): void
    {
        $this->deleteSnuffExtensions();
    }

    public function testGenerateByPluginInstallDefault()
    {
        $generator = new class([
            ByPluginInstallDefault::FIELD__INPUT => $this->getInput(),
            ByPluginInstallDefault::FIELD__OUTPUT => new NullOutput()
        ]) extends ByPluginInstallDefault {
            public array $generationResult = [];
            protected function exportGeneratedData(): void
            {
                $this->generationResult = $this->result;
                parent::exportGeneratedData();
            }
        };

        $plugins = [new PluginInstallJsonRpcOperations()];
        $isDone = $generator->generate($plugins);
        $this->assertTrue($isDone);

        $mustBe = include 'specs.php';
        $this->assertEquals(
            $mustBe,
            $generator->generationResult['jsonrpc_operations']
        );
        unlink(getcwd() . '/tests/generated.specs.json');
    }

    public function testGenerateByDocComment()
    {
        $generator = new class([
            ByDocComment::FIELD__INPUT => $this->getInput(),
            ByDocComment::FIELD__OUTPUT => new NullOutput()
        ]) extends ByDocComment {
            public array $generationResult = [];
            protected function exportGeneratedData(): void
            {
                $this->generationResult = $this->result;
                parent::exportGeneratedData();
            }
        };

        $plugins = [new OperationWithDocComment()];
        $isDone = $generator->generate($plugins);
        $this->assertTrue($isDone);

        $mustBe = include 'specs.comments.php';
        $this->assertEquals(
            $mustBe,
            $generator->generationResult['jsonrpc_operations']
        );
        unlink(getcwd() . '/tests/generated.specs.json');
    }

    /**
     * @param string $prefix
     * @param string $path
     * @return InputInterface
     */
    protected function getInput(
        string $prefix = 'PluginInstallJson',
        string $path = '/tests/generated.specs.json'
    ): InputInterface
    {
        return new ArrayInput(
            [
                '--' . PluginDefaultArguments::OPTION__SPECS_PATH => getcwd() . $path,
                '--' . PluginDefaultArguments::OPTION__PREFIX => $prefix,
                '--' . PluginDefaultArguments::OPTION__FILTER => '',
                '--' . PluginDefaultArguments::OPTION__ONLY_EDGE => false
            ],
            new InputDefinition([
                new InputOption(PluginDefaultArguments::OPTION__SPECS_PATH),
                new InputOption(PluginDefaultArguments::OPTION__PREFIX),
                new InputOption(PluginDefaultArguments::OPTION__FILTER),
                new InputOption(PluginDefaultArguments::OPTION__ONLY_EDGE)
            ])
        );
    }
}
