<?php
namespace extas\components\jsonrpc;

use extas\components\Item;
use extas\interfaces\jsonrpc\ICrawler;
use extas\interfaces\plugins\IPlugin;
use extas\interfaces\plugins\IPluginInstallDefault;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

/**
 * Class Crawler
 *
 * @package extas\components\packages
 * @author jeyroik@gmail.com
 */
class Crawler extends Item implements ICrawler
{
    /**
     * @param string $path
     * @param string $prefix
     *
     * @return IPlugin[]
     */
    public function crawlPlugins(string $path, string $prefix): array
    {
        $finder = new Finder();
        $finder->name($prefix . '*.php');
        $plugins = [];

        foreach ($finder->in($path)->files() as $file) {
            /**
             * @var $file SplFileInfo
             */
            try {
                preg_match('/^namespace\s(.*?);$/m', $file->getContents(), $nsMatches);
                preg_match('/^class\s(.*?)\s/m', $file->getContents(), $classMatches);

                if (isset($nsMatches[1], $classMatches[1])) {
                    $className = $nsMatches[1] . '\\' . $classMatches[1];
                    $plugin = new $className();

                    if ($plugin instanceof IPluginInstallDefault) {
                        $plugins[] = new $className();
                    }
                }
            } catch (\Exception $e) {
                continue;
            }
        }

        return $plugins;
    }

    /**
     * @return string
     */
    protected function getSubjectForExtension(): string
    {
        return static::SUBJECT;
    }
}
