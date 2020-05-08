<?php
namespace tests;

use extas\components\http\TSnuffHttp;
use extas\components\jsonrpc\Request;
use PHPUnit\Framework\TestCase;
use Dotenv\Dotenv;

/**
 * Class RequestTest
 *
 * @package tests
 * @author jeyroik@gmail.com
 */
class RequestTest extends TestCase
{
    use TSnuffHttp;

    protected function setUp(): void
    {
        parent::setUp();
        $env = Dotenv::create(getcwd() . '/tests/');
        $env->load();
    }

    public function testBasicMethods()
    {
        $request = new Request();

        $this->assertEquals('default', $request->getMethod('default'));

        $request->setMethod('create');
        $this->assertEquals('create', $request->getMethod());

        $this->assertEquals(['default'], $request->getParams(['default']));
        $this->assertEquals(['default'], $request->getData(['default']));
        $this->assertEquals(['default'], $request->getFilter(['default']));

        $request->setParams(['id' => '']);
        $this->assertEquals(['id' => ''], $request->getParams(['default']));

        $request->setFilter(['name' => ['$eq' => 'test']]);
        $this->assertEquals(['name' => ['$eq' => 'test']], $request->getFilter());
    }

    public function testBuildFromPsr()
    {
        $psrRequest = $this->getPsrRequest();
        $request = Request::fromHttp($psrRequest);

        $this->assertEquals('2f5d0719-5b82-4280-9b3b-10f23aff226b', $request->getId());
    }
}
