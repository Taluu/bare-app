<?php
namespace Bare\Amqp;

use Exception;
use RuntimeException;

class MessagingException extends RuntimeException
{
    private $messagingException;

    public function __construct(Exception $e)
    {
        $this->messagingException = $e;

        parent::__construct('There was an error while trying to use the Messaging service', $e->getCode(), $e);
    }
}

