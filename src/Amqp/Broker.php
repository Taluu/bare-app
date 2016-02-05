<?php
namespace Bare\Amqp;

use InvalidArgumentException;

use AMQPQueue;
use AMQPChannel;
use AMQPExchange;
use AMQPConnection;

use AMQPException;
use AMQPChannelException;
use AMQPExchangeException;
use AMQPConnectionException;

use Swarrot\Broker\MessageProvider\PeclPackageMessageProvider;
use Swarrot\Broker\MessagePublisher\PeclPackageMessagePublisher;

class Broker
{
    /** @var AMQPChannel */
    private $channel;

    /** @var PeclPackageMessageProvider[] */
    private $providers = [];

    /** @var PeclPackageMessagePublisher[] */
    private $producers = [];

    /** @var object Value object containing information on the current connection */
    private $connection;

    public function __construct(string $host, int $port, string $login, string $password, string $vhost = '/')
    {
        $this->connection = new class($host, $port, $login, $password, $vhost) {
            /** @var string */
            private $host;

            /** @var integer */
            private $port;

            /** @var string */
            private $login;

            /** @var string */
            private $password;

            /** @var string */
            private $vhost;

            public function __construct(string $host, int $port, string $login, string $password, string $vhost = '/')
            {
                $this->host = $host;
                $this->port = $port;
                $this->login = $login;
                $this->password = $password;
                $this->vhost = $vhost;
            }

            public function getHost() : string
            {
                return $this->host;
            }

            public function getPort() : int
            {
                return $this->port;
            }

            public function getLogin() : string
            {
                return $this->login;
            }

            public function getPassword() : string
            {
                return $this->password;
            }

            public function getVirtualHost() : string
            {
                return $this->vhost;
            }
        };
    }

    public function getProvider(Gate $gate) : PeclPackageMessageProvider
    {
        $name = $gate->getName();

        if (!isset($this->providers[$name])) {
            $queue = new AMQPQueue($this->getChannel());
            $queue->setName($gate->getQueue());

            $this->providers[$name] = new PeclPackageMessageProvider($queue);
        }

        return $this->providers[$name];
    }

    /** {@inheritDoc} */
    public function getProducer(Gate $gate) : PeclPackageMessagePublisher
    {
        $name = $gate->getName();

        if (!isset($this->producers[$name])) {
            try {
                $exchange = new AMQPExchange($this->getChannel());
                $exchange->setName($gate->getExchange());
            } catch (AMQPExchangeException $e) {
                throw new MessagingException($e);
            }

            $this->producers[$name] = new PeclPackageMessagePublisher($exchange);
        }

        return $this->producers[$name];
    }

    public function __destruct()
    {
        if (null === $this->channel) {
            return;
        }

        $this->channel->getConnection()->disconnect();
    }

    /**
     * Get a room... a channel
     *
     * @return AMQPChannel
     */
    private function getChannel() : AMQPChannel
    {
        if (null !== $this->channel) {
            return $this->channel;
        }

        try {
            $connection = new AMQPConnection([
                'host' => $this->connection->getHost(),
                'port' => $this->connection->getPort(),
                'login' => $this->connection->getLogin(),
                'password' => $this->connection->getPassword(),
                'vhost' => $this->connection->getVirtualHost()
            ]);

            $connection->connect();

            return $this->channel = new AMQPChannel($connection);
        } catch (AMQPException $e) {
            throw new MessagingException($e);
        }
    }
}

