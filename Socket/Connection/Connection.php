<?php
/**
 * This file is part of the RatchetBundle project.
 *
 * (c) 2013 Philipp Boes <mostgreedy@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace P2\Bundle\RatchetBundle\Socket\Connection;

use P2\Bundle\RatchetBundle\Socket\ClientInterface;
use P2\Bundle\RatchetBundle\Socket\Payload\EventPayload;
use Ratchet\ConnectionInterface as RatchetConnectionInterface;

/**
 * Class Connection
 * @package P2\Bundle\RatchetBundle\Socket\Connection
 */

class Connection implements ConnectionInterface
{
    /**
     * @var ConnectionManagerInterface
     */
    protected $connectionManager;

    /**
     * @var \Ratchet\ConnectionInterface
     */
    protected $connection;

    /**
     * @var ClientInterface
     */
    protected $client;

    /**
     * @param ConnectionManagerInterface $connectionManager
     * @param RatchetConnectionInterface $connection
     */
    function __construct(
        ConnectionManagerInterface $connectionManager,
        RatchetConnectionInterface $connection
    ) {
        $this->connectionManager = $connectionManager;
        $this->connection = $connection;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->connection->resourceId;
    }

    /**
     * Returns this connections client remote address.
     *
     * @return string
     */
    public function getRemoteAddress()
    {
        return $this->connection->remoteAddr;
    }

    /**
     * @param ClientInterface $client
     * @return Connection
     */
    public function setClient(ClientInterface $client)
    {
        $this->client = $client;

        return $this;
    }

    /**
     * Returns the client for this connection.
     *
     * @return ClientInterface
     */
    public function getClient()
    {
        return $this->client;
    }

    /**
     * Emits an event to this connection with the given payload.
     *
     * @param EventPayload $payload
     *
     * @return boolean
     */
    public function emit(EventPayload $payload)
    {
        $this->connection->send($payload->encode());
    }

    /**
     * Broadcasts an event to all
     *
     * @param EventPayload $payload
     * @return mixed
     */
    public function broadcast(EventPayload $payload)
    {
        foreach ($this->connectionManager->getConnections() as $connection) {
            if ($connection->getId() !== $this->getId()) {
                $connection->emit($payload);
            }
        }
    }
}
