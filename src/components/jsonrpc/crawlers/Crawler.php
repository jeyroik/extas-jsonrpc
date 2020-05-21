<?php
namespace extas\components\jsonrpc\crawlers;

use extas\components\Item;
use extas\components\TDispatcherWrapper;
use extas\interfaces\jsonrpc\crawlers\ICrawler;
use extas\interfaces\jsonrpc\crawlers\ICrawlerDispatcher;
use extas\interfaces\jsonrpc\IHasInput;
use extas\interfaces\jsonrpc\IHasOutput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class Crawler
 *
 * @package extas\components\jsonrpc\crawlers
 * @author jeyroik@gmail.com
 */
class Crawler extends Item implements ICrawler
{
    use TDispatcherWrapper;

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return array
     */
    public function dispatch(InputInterface $input, OutputInterface $output): array
    {
        /**
         * @var ICrawlerDispatcher $dispatcher
         */
        $dispatcher = $this->buildClassWithParameters([
            IHasInput::FIELD__INPUT => $input,
            IHasOutput::FIELD__OUTPUT => $output
        ]);

        return $dispatcher();
    }

    /**
     * @return string
     */
    protected function getSubjectForExtension(): string
    {
        return static::SUBJECT;
    }
}
