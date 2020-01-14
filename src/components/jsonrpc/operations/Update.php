<?php
namespace extas\components\jsonrpc;

use extas\components\jsonrpc\operations\OperationDispatcher;
use extas\components\SystemContainer;
use extas\interfaces\IHasName;
use extas\interfaces\IItem;
use extas\interfaces\jsonrpc\IResponse;
use extas\interfaces\jsonrpc\operations\IOperationUpdate;
use extas\interfaces\repositories\IRepository;
use extas\interfaces\servers\requests\IServerRequest;

/**
 * Class JsonRpcUpdate
 *
 * @package extas\components\jsonrpc
 * @author jeyroik@gmail.com
 */
class Update extends OperationDispatcher implements IOperationUpdate
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
         * @var $item IItem|IHasName
         */
        $repo = SystemContainer::getItem($this->getOperation()->getItemRepo());
        $itemClass = $this->getOperation()->getItemClass();
        $item = new $itemClass($data[static::FIELD__DATA]);
        $exist = $repo->one([IHasName::FIELD__NAME => $item->getName()]);
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

    /**
     * @return string
     */
    protected function getSubjectForExtension(): string
    {
        return 'extas.jsonrpc.update';
    }
}
