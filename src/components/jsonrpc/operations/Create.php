<?php
namespace extas\components\jsonrpc\operations;

use extas\components\SystemContainer;
use extas\interfaces\IHasName;
use extas\interfaces\IItem;
use extas\interfaces\jsonrpc\IRequest;
use extas\interfaces\jsonrpc\IResponse;
use extas\interfaces\jsonrpc\operations\IOperationCreate;
use extas\interfaces\repositories\IRepository;

/**
 * Class Create
 *
 * @package extas\components\jsonrpc
 * @author jeyroik@gmail.com
 */
class Create extends OperationDispatcher implements IOperationCreate
{
    /**
     * @param IRequest $request
     * @param IResponse $response
     */
    protected function dispatch(IRequest $request, IResponse &$response)
    {
        /**
         * @var $repo IRepository
         * @var $item IItem|IHasName
         */
        $repo = SystemContainer::getItem($this->getOperation()->getItemRepo());
        $itemClass = $this->getOperation()->getItemClass();
        $item = new $itemClass($request->getData());
        $exist = $repo->one([IHasName::FIELD__NAME => $item->getName()]);

        if ($exist || !$item->getName()) {
            $response->error(ucfirst($this->getOperation()->getItemName()) . ' already exist', 400);
        } else {
            $repo->create($item);
            $response->success($item->__toArray());
        }
    }

    /**
     * @return string
     */
    protected function getSubjectForExtension(): string
    {
        return 'extas.jsonrpc.create';
    }
}
