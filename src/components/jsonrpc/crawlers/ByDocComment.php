<?php
namespace extas\components\jsonrpc\crawlers;

/**
 * Class ByDocComment
 *
 * @package extas\components\jsonrpc\crawlers
 * @author jeyroik@gmail.com
 */
class ByDocComment extends ByInstallSection
{
    public const NAME = 'by.doc.comment';

    /**
     * @param $plugin
     * @param array $plugins
     * @throws \ReflectionException
     */
    protected function filterPlugin($plugin, array &$plugins): void
    {
        if ($plugin) {
            $reflection = new \ReflectionClass($plugin);
            $doc = $reflection->getDocComment();
            preg_match_all('/@jsonrpc_operation/', $doc, $matches);

            if (!empty($matches[0])) {
                $plugins[] = $plugin;
            }
        }
    }
}
