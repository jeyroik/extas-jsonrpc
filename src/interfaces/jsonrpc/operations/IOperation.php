<?php
namespace extas\interfaces\jsonrpc\operations;

use extas\interfaces\IHasClass;
use extas\interfaces\IHasDescription;
use extas\interfaces\IHasName;
use extas\interfaces\IItem;

/**
 * Interface IOperation
 *
 * @package extas\interfaces\jsonrpc\routes
 * @author jeyroik@gmail.com
 */
interface IOperation extends IItem, IHasName, IHasDescription, IHasClass
{
    const SUBJECT = 'extas.jsonrpc.route';

    const FIELD__SPEC = 'spec';
    const FIELD__ITEM_NAME = 'item_name';
    const FIELD__ITEM_CLASS = 'item_class';
    const FIELD__ITEM_REPO = 'item_repo';
    const FIELD__METHOD = 'method';

    /**
     * @return string
     */
    public function getItemClass(): string;

    /**
     * @param string $itemClass
     *
     * @return IOperation
     */
    public function setItemClass(string $itemClass): IOperation;

    /**
     * @return string
     */
    public function getItemRepo(): string;

    /**
     * @param string $repoName
     *
     * @return IOperation
     */
    public function setItemRepo(string $repoName): IOperation;

    /**
     * @return string
     */
    public function getMethod(): string;

    /**
     * @param string $method
     *
     * @return IOperation
     */
    public function setMethod(string $method): IOperation;

    /**
     * @return string
     */
    public function getItemName(): string;

    /**
     * @param string $itemName
     *
     * @return IOperation
     */
    public function setItemName(string $itemName): IOperation;

    /**
     * @return array
     */
    public function getSpec(): array;

    /**
     * @param array $spec
     *
     * @return IOperation
     */
    public function setSpec(array $spec): IOperation;
}
