<?php
namespace extas\components\jsonrpc\operations;

use extas\components\Item;
use extas\components\THasClass;
use extas\components\THasDescription;
use extas\components\THasName;
use extas\interfaces\jsonrpc\operations\IOperation;
use extas\interfaces\jsonrpc\operations\IOperationFilter;

/**
 * Class Operation
 *
 * @package extas\components\jsonrpc\routes
 * @author jeyroik@gmail.com
 */
class Operation extends Item implements IOperation
{
    use THasName;
    use THasDescription;
    use THasClass;

    /**
     * @return string
     */
    public function getItemName(): string
    {
        return $this->config[static::FIELD__ITEM_NAME] ?? '';
    }

    /**
     * @return string
     */
    public function getItemClass(): string
    {
        return $this->config[static::FIELD__ITEM_CLASS] ?? '';
    }

    /**
     * @return string
     */
    public function getItemRepo(): string
    {
        return $this->config[static::FIELD__ITEM_REPO] ?? '';
    }

    /**
     * @return IOperationFilter|null
     */
    public function getFilter(): ?IOperationFilter
    {
        $filterClass = $this->config[static::FIELD__FILTER] ?? '';

        return $filterClass ? new $filterClass() : null;
    }

    /**
     * @return string
     */
    public function getMethod(): string
    {
        return $this->config[static::FIELD__METHOD] ?? '';
    }

    /**
     * @param string $itemName
     *
     * @return IOperation
     */
    public function setItemName(string $itemName): IOperation
    {
        $this->config[static::FIELD__ITEM_NAME] = $itemName;

        return $this;
    }

    /**
     * @param string $itemClass
     *
     * @return IOperation
     */
    public function setItemClass(string $itemClass): IOperation
    {
        $this->config[static::FIELD__ITEM_CLASS] = $itemClass;

        return $this;
    }

    /**
     * @param string $repoName
     *
     * @return IOperation
     */
    public function setItemRepo(string $repoName): IOperation
    {
        $this->config[static::FIELD__ITEM_REPO] = $repoName;

        return $this;
    }

    /**
     * @param IOperationFilter $filter
     *
     * @return IOperation
     */
    public function setFilter(IOperationFilter $filter): IOperation
    {
        $this->config[static::FIELD__FILTER] = get_class($filter);

        return $this;
    }

    /**
     * @param string $method
     *
     * @return IOperation
     */
    public function setMethod(string $method): IOperation
    {
        $this->config[static::FIELD__METHOD] = $method;

        return $this;
    }

    /**
     * @return array
     */
    public function getSpec(): array
    {
        return $this->config[static::FIELD__SPEC] ?? [];
    }

    /**
     * @param array $spec
     *
     * @return IOperation
     */
    public function setSpec(array $spec): IOperation
    {
        $this->config[static::FIELD__SPEC] = $spec;

        return $this;
    }

    /**
     * @return string
     */
    protected function getSubjectForExtension(): string
    {
        return static::SUBJECT;
    }
}
