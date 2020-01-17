<?php
namespace extas\components\jsonrpc\operations;

use extas\components\expands\Expander;
use extas\components\SystemContainer;
use extas\interfaces\IItem;
use extas\interfaces\jsonrpc\IRequest;
use extas\interfaces\jsonrpc\IResponse;
use extas\interfaces\jsonrpc\operations\IOperationIndex;
use extas\interfaces\repositories\IRepository;

/**
 * Class Index
 *
 * @package extas\components\jsonrpc
 * @author jeyroik@gmail.com
 */
class Index extends OperationDispatcher implements IOperationIndex
{
    /**
     * @param IRequest $request
     * @param IResponse $response
     */
    protected function dispatch(IRequest $request, IResponse &$response)
    {
        /**
         * @var $repo IRepository
         * @var $records IItem[]
         */
        $repo = SystemContainer::getItem($this->getOperation()->getItemRepo());

        $records = $repo->all([]);
        $items = [];
        $limit = $this->getLimit();

        foreach ($records as $record) {
            if (!$limit || ($limit && (count($items) < $limit))) {
                $items[] = $record->__toArray();
            }
        }

        $items = $this->filter($request->getFilter(), $items);
        $itemName = $this->getOperation()->getItemName();
        $box = Expander::getExpandingBox($this->getOperation()->getMethod(), $itemName);
        $box->setData([$itemName . '_list' => $items]);
        $box->expand($this->serverRequest, $this->serverResponse);
        $box->pack();
        $expanded = $box->getValue();
        $items = $expanded[$itemName . '_list'] ?? $items;

        $response->success([
            'items' => $items,
            'total' => count($items)
        ]);
    }

    /**
     * @return int
     */
    public function getLimit(): int
    {
        return (int) ($this->config[static::FIELD__LIMIT] ?? 0);
    }

    /**
     * @param int $limit
     *
     * @return IOperationIndex
     */
    public function setLimit(int $limit): IOperationIndex
    {
        $this->config[static::FIELD__LIMIT] = $limit;

        return $this;
    }

    /**
     * @param array $filter
     * @param IItem[] $items
     *
     * @return array
     */
    protected function filter($filter, $items)
    {
        if (empty($filter)) {
            return $items;
        }

        $result = [];

        foreach ($items as $item) {
            $success = true;
            foreach ($filter as $fieldName => $filterOptions) {
                foreach ($filterOptions as $filterCompare => $filterValue) {
                    $filterCompare = str_replace('$', '', $filterCompare);
                    $filterDispatcher = $this->getOperation()->getFilter();

                    if (!isset($item[$fieldName])) {
                        $success = false;
                        break 2;
                    }

                    $isValid = $filterDispatcher
                        ? $filterDispatcher->isValid($item[$fieldName], $filterValue, $filterCompare)
                        : false;

                    if (!$isValid) {
                        $success = false;
                        break 2;
                    }
                }
            }

            $success && ($result[] = $item);
        }

        return $result;
    }

    /**
     * @return string
     */
    protected function getSubjectForExtension(): string
    {
        return static::SUBJECT;
    }
}
