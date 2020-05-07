<?php
namespace tests;

use Dotenv\Dotenv;
use extas\components\jsonrpc\Request;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\RequestInterface;
use Slim\Factory\ServerRequestCreatorFactory;
use Slim\Tests\Mocks\MockStream;

/**
 * Class RequestTest
 *
 * @package tests
 * @author jeyroik@gmail.com
 */
class RequestTest extends TestCase
{
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

    /**
     * @return RequestInterface
     */
    protected function getPsrRequest(): RequestInterface
    {
        $creator = ServerRequestCreatorFactory::create();
        $request = $creator->createServerRequestFromGlobals();
        $request->withBody(new MockStream(fopen(getcwd() . '/tests/request.json', 'r')));

        return $request;
    }
}
