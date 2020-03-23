<?php
namespace extas\components\jsonrpc\operations;

use extas\components\SystemContainer;
use extas\interfaces\IHasName;
use extas\interfaces\IItem;
use extas\interfaces\jsonrpc\IRequest;
use extas\interfaces\jsonrpc\IResponse;
use extas\interfaces\jsonrpc\operations\IOperationUpdate;
use extas\interfaces\repositories\IRepository;

/**
 * Class JsonRpcUpdate
 *
 * @package extas\components\jsonrpc
 * @author jeyroik@gmail.com
 */
class Update extends OperationDispatcher implements IOperationUpdate
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
        $pkMethod = 'get' . ucfirst($repo->getPk());
        if (!method_exists($item, $pkMethod)) {
            $response->error(
                'Item has not primary key getter method "' . $pkMethod . '"',
                500
            );
        } else {
            $exist = $repo->one([IHasName::FIELD__NAME => $item->$pkMethod()]);

            if (!$exist) {
                $response->error('Unknown entity "' . $this->getOperation()->getItemName() . '"', 404);
            } else {
                foreach ($exist as $field => $value) {
                    if (!isset($item[$field])) {
                        $item[$field] = $value;
                    }
                }
                $repo->update($item);
                $response->success([$item->__toArray()]);
            }
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
