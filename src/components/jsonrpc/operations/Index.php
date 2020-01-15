<?php
namespace extas\components\jsonrpc\operations;

use extas\components\expands\Expander;
use extas\components\servers\responses\ServerResponse;
use extas\components\SystemContainer;
use extas\interfaces\IItem;
use extas\interfaces\jsonrpc\IRequest;
use extas\interfaces\jsonrpc\IResponse;
use extas\interfaces\jsonrpc\operations\IOperation;
use extas\interfaces\jsonrpc\operations\IOperationIndex;
use extas\interfaces\parameters\IParameter;
use extas\interfaces\repositories\IRepository;
use extas\interfaces\servers\requests\IServerRequest;
use extas\interfaces\servers\responses\IServerResponse;
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
     * @param IServerRequest $request
     * @param IResponse $response
     *
     * @param $data
     */
    public function __invoke(IServerRequest $request, IResponse &$response, array $data)
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

        $items = $this->filter($data, $items);
        $itemName = $this->getOperation()->getItemName();
        $box = Expander::getExpandingBox($this->getOperation()->getMethod(), $itemName);
        $box->setData([$itemName . 's' => $items]);
        $box->expand($request, $this->getServerResponse($response));
        $box->pack();
        $expanded = $box->getValue();
        $items = $expanded[$itemName . 's'] ?? $items;

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
     * @param IResponse $response
     *
     * @return IServerResponse
     */
    protected function getServerResponse(IResponse $response): IServerResponse
    {
        return new ServerResponse([
            ServerResponse::FIELD__NAME => $this->getOperation()->getName(),
            ServerResponse::FIELD__PARAMETERS => [
                [
                    IParameter::FIELD__NAME => ServerResponse::PARAMETER__HTTP_RESPONSE,
                    IParameter::FIELD__VALUE => $response->getPsrResponse(),
                    IParameter::FIELD__TEMPLATE => ResponseInterface::class
                ]
            ]
        ]);
    }

    /**
     * @param array $jRpcData
     * @param IItem[] $items
     *
     * @return array
     */
    protected function filter($jRpcData, $items)
    {
        $filter = $jRpcData[IRequest::FIELD__FILTER] ?? [];

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
