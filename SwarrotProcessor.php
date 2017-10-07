<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sam\Symfony\Bridge\SwarrotMessage;

use Swarrot\Broker\Message;
use Swarrot\Processor\ProcessorInterface;
use Symfony\Component\Message\Asynchronous\ConsumedMessage;
use Symfony\Component\Message\MessageBusInterface;
use Symfony\Component\Message\Transport\MessageDecoderInterface;

/**
 * @author Samuel Roze <samuel.roze@gmail.com>
 */
class SwarrotProcessor implements ProcessorInterface
{
    private $messageBus;
    private $messageDecoder;

    public function __construct(MessageBusInterface $messageBus, MessageDecoderInterface $messageDecoder)
    {
        $this->messageBus = $messageBus;
        $this->messageDecoder = $messageDecoder;
    }

    /**
     * {@inheritdoc}
     */
    public function process(Message $message, array $options)
    {
        return $this->messageBus->handle(new ConsumedMessage(
            $this->messageDecoder->decode([
                'body' => $message->getBody(),
                'headers' => $message->getProperties()['headers'] ?? [],
            ])
        ));
    }
}
