<?php
namespace tests\operations;

use extas\components\jsonrpc\operations\Operation;
use PHPUnit\Framework\TestCase;

/**
 * Class OperationTest
 *
 * @package tests\operations
 * @author jeyroik@gmail.com
 */
class OperationTest extends TestCase
{
    public function testBasicMethods()
    {
        $operation = new Operation();

        $operation->setMethod('create');
        $operation->setSpec(['spec']);
        $operation->setItemClass('item');
        $operation->setItemRepo('repo');
        $operation->setItemName('item');

        $this->assertEquals('create', $operation->getMethod());
        $this->assertEquals(['spec'], $operation->getSpec());
        $this->assertEquals('item', $operation->getItemClass());
        $this->assertEquals('repo', $operation->getItemRepo());
        $this->assertEquals('item', $operation->getItemName());
    }
}
