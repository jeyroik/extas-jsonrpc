<?php
namespace extas\components\jsonrpc\generators;

use extas\components\Item;
use extas\components\plugins\jsonrpc\PluginDefaultArguments;
use extas\components\THasIO;
use extas\interfaces\jsonrpc\generators\IGeneratorDispatcher;

/**
 * Class GeneratorDispatcher
 *
 * @package extas\components\jsonrpc\generators
 * @author jeyroik@gmail.com
 */
abstract class GeneratorDispatcher extends Item implements IGeneratorDispatcher
{
    use THasIO;

    protected array $result = [
        'name' => '[ auto-generated ]',
        self::FIELD__OPERATIONS => []
    ];

    /**
     * @return bool
     */
    public function getOnlyEdge(): bool
    {
        return (bool) $this->getInput()->getOption('only-edge');
    }

    /**
     * @return string
     */
    public function getFilter(): string
    {
        return $this->getInput()->getOption('filter');
    }

    /**
     *
     */
    protected function exportGeneratedData(): void
    {
        $path = $this->getInput()->getOption(PluginDefaultArguments::OPTION__SPECS_PATH);

        if (is_file($path)) {
            $already = json_decode(file_get_contents($path), true);
            if (!isset($already[self::FIELD__OPERATIONS])) {
                $already[self::FIELD__OPERATIONS] = [];
            }

            $already[self::FIELD__OPERATIONS] = array_merge(
                $already[self::FIELD__OPERATIONS],
                $this->result[self::FIELD__OPERATIONS]
            );
            $this->result = $already;
        }
        file_put_contents($path, json_encode($this->result));
    }

    /**
     * @param string $fullName
     * @return bool
     */
    protected function isApplicableOperation(string $fullName): bool
    {
        $filter = $this->getFilter();

        if($filter && (strpos($fullName, $filter) === false)) {
            return false;
        }

        return true;
    }

    /**
     * @param array $operation
     */
    protected function addOperation(array $operation): void
    {
        foreach ($this->getPluginsByStage('extas.jsonrpc.generate.before.add') as $plugin) {
            $plugin($operation);
        }

        $this->result[static::FIELD__OPERATIONS][] = $operation;
    }

    /**
     * @return string
     */
    protected function getSubjectForExtension(): string
    {
        return 'extas.jsonrpc.generator.dispatcher';
    }
}
