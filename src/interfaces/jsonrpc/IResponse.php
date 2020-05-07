<?php
namespace extas\interfaces\jsonrpc;

use extas\interfaces\IItem;

/**
 * Interface IResponse
 *
 * @package extas\interfaces\jsonrpc
 * @author jeyroik@gmail.com
 */
interface IResponse extends IItem
{
    const SUBJECT = 'extas.jsonrpc.response';

    const FIELD__RESPONSE = 'response';
    const FIELD__DATA = 'data';

    const RESPONSE__ID = 'id';
    const RESPONSE__VERSION = 'jsonrpc';
    const RESPONSE__RESULT = 'result';
    const RESPONSE__ERROR = 'error';
    const RESPONSE__ERROR_CODE = 'code';
    const RESPONSE__ERROR_DATA = 'data';
    const RESPONSE__ERROR_MESSAGE = 'message';
    const RESPONSE__ERROR_MARKER = '@error';

    const VERSION_CURRENT = '2.0';
}
