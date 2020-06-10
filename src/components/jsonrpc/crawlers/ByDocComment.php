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
    public const OPTION__DOC_PATH = 'path-jsonrpc-doc-comment';
    public const OPTION__DOC_PREFIX = 'prefix-jsonrpc-doc-comment';

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

    /**
     * @return string
     */
    protected function getPathValue(): string
    {
        return $this->getInputOption(static::OPTION__DOC_PATH, getcwd());
    }

    /**
     * @return string
     */
    protected function getPrefixValue(): string
    {
        return $this->getInputOption(static::OPTION__DOC_PREFIX, '');
    }
}
