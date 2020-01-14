<?php
namespace extas\components\jsonrpc;

use extas\components\jsonrpc\operations\OperationDispatcher;
use extas\components\SystemContainer;
use extas\interfaces\IHasName;
use extas\interfaces\IItem;
use extas\interfaces\jsonrpc\IResponse;
use extas\interfaces\jsonrpc\operations\IOperationCreate;
use extas\interfaces\repositories\IRepository;
use extas\interfaces\servers\requests\IServerRequest;

/**
 * Class Create
 *
 * @package extas\components\jsonrpc
 * @author jeyroik@gmail.com
 */
class Create extends OperationDispatcher implements IOperationCreate
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
        $item = new $itemClass($data[static::FIELD__DATA] ?? []);
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
