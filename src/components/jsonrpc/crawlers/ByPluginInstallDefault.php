<?php
namespace extas\components\jsonrpc\crawlers;

use extas\components\plugins\jsonrpc\PluginDefaultArguments;
use extas\interfaces\plugins\IPluginInstallDefault;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

/**
 * Class ByPluginInstallDefault
 *
 * @package extas\components\jsonrpc\crawlers
 * @author jeyroik@gmail.com
 */
class ByPluginInstallDefault extends CrawlerDispatcher
{
    public const NAME = 'by.plugin.install.default';

    /**
     * @return array
     */
    public function __invoke(): array
    {
        $path = $this->getInput()->getOption(PluginDefaultArguments::OPTION__SPECS_PATH);
        $prefix = $this->getInput()->getOption(PluginDefaultArguments::OPTION__PREFIX);

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
}
