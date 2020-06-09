<?php
namespace extas\components\jsonrpc\crawlers;

use extas\components\crawlers\CrawlerDispatcher;
use extas\components\plugins\init\InitSection;
use extas\components\plugins\install\InstallSection;
use extas\components\plugins\jsonrpc\PluginDefaultArguments;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

/**
 * Class ByInstallSection
 *
 * @package extas\components\jsonrpc\crawlers
 * @author jeyroik@gmail.com
 */
class ByInstallSection extends CrawlerDispatcher
{
    public const NAME = 'by.install.section';

    /**
     * @return array
     */
    public function __invoke(): array
    {
        $path = $this->getInput()->getOption(PluginDefaultArguments::OPTION__CRAWL_PATH);
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
        if ($plugin instanceof InstallSection || $plugin instanceof InitSection) {
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
