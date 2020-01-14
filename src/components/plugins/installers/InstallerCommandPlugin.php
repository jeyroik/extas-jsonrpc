<?php
namespace extas\components\plugins\installers;

use extas\commands\JsonrpcCommand;
use extas\components\plugins\Plugin;

/**
 * Class InstallerCommandPlugin
 *
 * @package extas\components\plugins\installers
 * @author jeyroik@gmail.com
 */
class InstallerCommandPlugin extends Plugin
{
    /**
     * @return JsonrpcCommand
     */
    public function __invoke()
    {
        return new JsonrpcCommand();
    }
}
