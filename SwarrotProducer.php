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
use Swarrot\SwarrotBundle\Broker\Publisher;
use Symfony\Component\Message\MessageProducerInterface;
use Symfony\Component\Message\Transport\MessageEncoderInterface;

/**
 * @author Samuel Roze <samuel.roze@gmail.com>
 */
class SwarrotProducer implements MessageProducerInterface
{
    private $publisher;
    private $messageEncoder;
    private $messageType;

    public function __construct(Publisher $publisher, MessageEncoderInterface $messageEncoder, string $messageType)
    {
        $this->publisher = $publisher;
        $this->messageEncoder = $messageEncoder;
        $this->messageType = $messageType;
    }

    /**
     * {@inheritdoc}
     */
    public function produce($message)
    {
        $encodedMessage = $this->messageEncoder->encode($message);

        $this->publisher->publish($this->messageType, new Message(
            $encodedMessage['body'],
            [
                'headers' => $encodedMessage['headers'] ?? [],
            ]
        ));
    }
}
