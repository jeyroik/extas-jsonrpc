<?php
namespace tests;

use Dotenv\Dotenv;
use extas\components\jsonrpc\App;
use PHPUnit\Framework\TestCase;

/**
 * Class AppTest
 *
 * @package tests
 * @author jeyroik@gmail.com
 */
class AppTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $env = Dotenv::create(getcwd() . '/tests/');
        $env->load();
    }

    public function testConstructing()
    {
        $app = App::create();
        $this->assertCount(2, $app->getContainer()->get('router')->getRoutes());
    }
}
