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
            preg_match('/^namespace\s(.*?);$/m', $file->getContents(), $nsMatches);
            preg_match('/^class\s(.*?)\s/m', $file->getContents(), $classMatches);

            $plugin = $this->getPlugin($nsMatches, $classMatches);
            $this->filterPlugin($plugin, $plugins);
        }

        return $plugins;
    }

    /**
     * @param $plugin
     * @param array $plugins
     */
    protected function filterPlugin($plugin, array &$plugins): void
    {
        if ($plugin instanceof IPluginInstallDefault) {
            $plugins[] = $plugin;
        }
    }

    /**
     * @param $nsMatches
     * @param $classMatches
     * @return mixed|null
     */
    protected function getPlugin($nsMatches, $classMatches)
    {
        $plugin = null;

        try {
            if (isset($nsMatches[1], $classMatches[1])) {
                $className = $nsMatches[1] . '\\' . $classMatches[1];
                $plugin = new $className();
            }
        } catch (\Exception $e) {

        }

        return $plugin;
    }

    /**
     * @return string
     */
    protected function getSubjectForExtension(): string
    {
        return static::SUBJECT;
    }
}
