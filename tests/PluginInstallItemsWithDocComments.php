<?php
namespace tests;

use extas\components\plugins\PluginInstallDefault;

/**
 * Class PluginInstallItemsWithDocComments
 *
 * @package tests
 * @author jeyroik@gmail.com
 */
class PluginInstallItemsWithDocComments extends PluginInstallDefault
{
    protected string $selfSection = 'items_doc';
    protected string $selfName = 'item doc';
    protected string $selfRepositoryClass = 'Some\\Repo';
    protected string $selfUID = '';
    protected string $selfItemClass = ItemWithDocComment::class;
}
