<?php
namespace extas\components\jsonrpc\operations;

use extas\components\SystemContainer;
use extas\interfaces\IHasName;
use extas\interfaces\IItem;
use extas\interfaces\jsonrpc\IRequest;
use extas\interfaces\jsonrpc\IResponse;
use extas\interfaces\jsonrpc\operations\IOperationDelete;
use extas\interfaces\repositories\IRepository;

/**
 * Class JsonRpcDelete
 *
 * @package extas\components\jsonrpc
 * @author jeyroik@gmail.com
 */
class Delete extends OperationDispatcher implements IOperationDelete
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
        $exist = $repo->all($request->getData());
        if (!$exist) {
            $response->error('Unknown entity "' . $this->getOperation()->getItemName() . '"', 404);
        } else {
            $repo->delete($request->getData());
            $result = [];
            foreach ($exist as $item) {
                $result[] = $item->__toArray();
            }
            $response->success($result);
        }
    }

    /**
     * @return string
     */
    protected function getSubjectForExtension(): string
    {
        return 'extas.jsonrpc.update';
    }
}
