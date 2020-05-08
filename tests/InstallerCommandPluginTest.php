<?php
namespace tests;

use extas\commands\JsonrpcCommand;
use extas\components\plugins\installers\InstallerCommandPlugin;
use PHPUnit\Framework\TestCase;
use Dotenv\Dotenv;

/**
 * Class InstallerCommandPluginTest
 *
 * @package tests
 * @author jeyroik@gmail.com
 */
class InstallerCommandPluginTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $env = Dotenv::create(getcwd() . '/tests/');
        $env->load();
    }

    public function testInvoke()
    {
        $plugin = new InstallerCommandPlugin();
        $command = $plugin();
        $this->assertTrue($command instanceof JsonrpcCommand);
    }
}
