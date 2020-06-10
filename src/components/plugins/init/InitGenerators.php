<?php
namespace extas\components\plugins\init;

use extas\components\jsonrpc\generators\Generator;

/**
 * Class InitGenerators
 *
 * @package extas\components\plugins\init
 * @author jeyroik@gmail.com
 */
class InitGenerators extends InitSection
{
    protected string $selfRepositoryClass = 'generatorRepository';
    protected string $selfUID = Generator::FIELD__NAME;
    protected string $selfSection = 'generators';
    protected string $selfName = 'generator';
    protected string $selfItemClass = Generator::class;
}
