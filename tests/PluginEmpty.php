<?php
namespace tests;

use extas\components\plugins\Plugin;

/**
 * Class PluginEmpty
 *
 * @package tests
 * @author jeyroik@gmail.com
 */
class PluginEmpty extends Plugin
{
    /**
     * @param mixed ...$params
     */
    public function __invoke(...$params): void
    {

    }
}
