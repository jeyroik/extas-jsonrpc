<?php
namespace tests;

use extas\components\plugins\Plugin;
use extas\interfaces\stages\IStageJsonRpcInit;
use Slim\App;

/**
 * Class PluginEmpty
 *
 * @package tests
 * @author jeyroik@gmail.com
 */
class PluginEmpty extends Plugin implements IStageJsonRpcInit
{
    /**
     * @param App $app
     */
    public function __invoke(App &$app): void
    {

    }
}
