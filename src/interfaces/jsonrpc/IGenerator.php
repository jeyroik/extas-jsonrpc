<?php
namespace extas\interfaces\jsonrpc;

use extas\interfaces\plugins\IPluginInstallDefault;

/**
 * Interface IGenerator
 *
 * @package extas\interfaces\jsonrpc
 * @author jeyroik@gmail.com
 */
interface IGenerator
{
    const SUBJECT = 'extas.jsonrpc.generator';

    const FIELD__FILTER = 'filter';
    const FIELD__ONLY_EDGE = 'only_edge';

    /**
     * @param IPluginInstallDefault[] $plugins
     * @param string $path
     *
     * @return bool
     */
    public function generate(array $plugins, string $path): bool;

    /**
     * @return bool
     */
    public function getOnlyEdge(): bool;

    /**
     * @return string
     */
    public function getFilter(): string;

    /**
     * @param string $filter
     *
     * @return IGenerator
     */
    public function setFilter(string $filter): IGenerator;

    /**
     * @param bool $onlyEdge
     *
     * @return IGenerator
     */
    public function setOnlyEdge(bool $onlyEdge): IGenerator;
}
