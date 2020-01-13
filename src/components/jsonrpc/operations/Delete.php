<?php
namespace extas\components\jsonrpc;

use extas\components\jsonrpc\operations\OperationDispatcher;
use extas\components\SystemContainer;
use extas\interfaces\IHasName;
use extas\interfaces\IItem;
use extas\interfaces\jsonrpc\IResponse;
use extas\interfaces\jsonrpc\operations\IOperationDelete;
use extas\interfaces\repositories\IRepository;
use extas\interfaces\servers\requests\IServerRequest;

/**
 * Class JsonRpcDelete
 *
 * @package extas\components\jsonrpc
 * @author jeyroik@gmail.com
 */
class Delete extends OperationDispatcher implements IOperationDelete
{
    /**
     * @param IServerRequest $request
     * @param IResponse $response
     *
     * @param $data
     */
    public function __invoke(IServerRequest $request, IResponse &$response, $data)
    {
        /**
         * @var $repo IRepository
         * @var $item IItem|IHasName
         */
        $repo = SystemContainer::getItem($this->getOperation()->getItemRepo());
        $exist = $repo->all($data[static::FIELD__DATA]);
        if (!$exist) {
            $response->error('Unknown entity "' . $this->getOperation()->getItemName() . '"', 404);
        } else {
            $repo->delete($data[static::FIELD__DATA]);
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
