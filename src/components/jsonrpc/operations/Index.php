<?php
namespace extas\components\jsonrpc\operations;

use extas\components\conditions\ConditionParameter;
use extas\components\expands\Expander;
use extas\interfaces\IItem;
use extas\interfaces\jsonrpc\operations\IOperationIndex;
use Psr\Http\Message\ResponseInterface;

/**
 * Class Index
 *
 * @package extas\components\jsonrpc
 * @author jeyroik@gmail.com
 */
class Index extends OperationDispatcher implements IOperationIndex
{
    /**
     * @return ResponseInterface
     */
    public function __invoke(): ResponseInterface
    {
        $records = $this->getItemRepo()->all([]);
        $request = $this->convertPsrToJsonRpcRequest();
        $items = $this->filter($request->getFilter(), $this->cutByLimit($records));
        $items = $this->expandItems($items);

        return $this->successResponse(
            $request->getId(),
            [
                'items' => $items,
                'total' => count($items)
            ]
        );
    }

    /**
     * @return int
     */
    public function getLimit(): int
    {
        return (int) ($this->config[static::FIELD__LIMIT] ?? 0);
    }

    /**
     * @param $items
     * @return array
     */
    protected function expandItems($items): array
    {
        $itemName = $this->getOperation()->getItemName();
        $box = Expander::getExpandingBox($this->getOperation()->getMethod(), $itemName);
        $box->setData([$itemName . '_list' => $items]);
        $box->expand($this->getPsrRequest(), $this->getPsrResponse());
        $box->pack();
        $expanded = $box->getValue();

        return $expanded[$itemName . '_list'] ?? $items;
    }

    /**
     * @param array $records
     * @return array
     */
    protected function cutByLimit(array $records): array
    {
        $items = [];
        $limit = $this->getLimit();

        foreach ($records as $record) {
            if (!$limit || ($limit && (count($items) < $limit))) {
                $items[] = $record->__toArray();
            }
        }

        return $items;
    }

    /**
     * @param array $filter
     * @param array $items
     *
     * @return array
     */
    protected function filter($filter, $items)
    {
        if (empty($filter)) {
            return $items;
        }

        $result = [];
        $conditions = [];

        foreach ($filter as $fieldName => $filterOptions) {
            $this->appendCondition($fieldName, $filterOptions, $conditions);
        }

        foreach ($items as $item) {
            $this->filterByConditions($item, $conditions, $result);
        }

        return $result;
    }

    /**
     * @param string $fieldName
     * @param array $filterOptions
     * @param array $conditions
     */
    protected function appendCondition(string $fieldName, array $filterOptions, array &$conditions): void
    {
        foreach ($filterOptions as $filterCompare => $filterValue) {
            $conditions[] = new ConditionParameter([
                ConditionParameter::FIELD__NAME => $fieldName,
                ConditionParameter::FIELD__CONDITION => str_replace('$', '', $filterCompare),
                ConditionParameter::FIELD__VALUE => $filterValue
            ]);
        }
    }

    /**
     * @param array $item
     * @param ConditionParameter[] $conditions
     * @param array $result
     */
    protected function filterByConditions(array $item, array $conditions, array &$result): void
    {
        $valid = true;
        foreach ($conditions as $condition) {
            if (!$condition->isConditionTrue($item[$condition->getName()] ?? null)) {
                $valid = false;
                break;
            }
        }

        $valid && ($result[] = $item);
    }

    /**
     * @return string
     */
    protected function getSubjectForExtension(): string
    {
        return static::SUBJECT;
    }
}
