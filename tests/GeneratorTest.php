<?php
namespace tests;

use extas\components\console\TSnuffConsole;
use extas\components\jsonrpc\generators\ByDocComment;
use extas\components\jsonrpc\generators\ByPluginInstallDefault;
use extas\components\plugins\install\InstallJsonRpcOperations;
use extas\components\plugins\jsonrpc\PluginDefaultArguments;

use Dotenv\Dotenv;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Input\InputInterface;

/**
 * Class GeneratorTest
 *
 * @package tests
 * @author jeyroik@gmail.com
 */
class GeneratorTest extends TestCase
{
    use TSnuffConsole;

    protected function setUp(): void
    {
        parent::setUp();
        $env = Dotenv::create(getcwd() . '/tests/');
        $env->load();
    }

    public function testGenerateByPluginInstallDefault()
    {
        $generator = new class([
            ByPluginInstallDefault::FIELD__INPUT => $this->getTestInput(),
            ByPluginInstallDefault::FIELD__OUTPUT => $this->getOutput()
        ]) extends ByPluginInstallDefault {
            public array $generationResult = [];
            protected function exportGeneratedData(): void
            {
                $this->generationResult = $this->result;
                parent::exportGeneratedData();
            }
        };

        $plugins = [new InstallJsonRpcOperations()];
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
            ByDocComment::FIELD__INPUT => $this->getTestInput(),
            ByDocComment::FIELD__OUTPUT => $this->getOutput()
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
    protected function getTestInput(
        string $prefix = 'PluginInstallJson',
        string $path = '/tests/generated.specs.json'
    ): InputInterface
    {
        return $this->getInput([
            PluginDefaultArguments::OPTION__SPECS_PATH => getcwd() . $path,
            PluginDefaultArguments::OPTION__PREFIX => $prefix,
            PluginDefaultArguments::OPTION__FILTER => '',
            PluginDefaultArguments::OPTION__ONLY_EDGE => false
        ]);
    }
}
