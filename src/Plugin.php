<?php declare(strict_types=1);

namespace AsyncBot\Plugin\CustomMessageCommand;

use Amp\Promise;
use AsyncBot\Core\Message\Node\Message;

final class Plugin
{
    private Storage $storage;

    public function __construct(Storage $storage)
    {
        $this->storage = $storage;
    }

    /**
     * @return Promise<null>
     */
    public function registerNewCommand(string $command, Message $message): Promise
    {
        return $this->storage->registerCommand($command, $message);
    }

    /**
     * @return Promise<null>
     */
    public function unregisterNewCommand(string $command): Promise
    {
        return $this->storage->unregisterCommand($command);
    }

    /**
     * @return Promise<bool>
     */
    public function isCommand(string $command): Promise
    {
        return $this->storage->isCommandRegistered($command);
    }

    /**
     * @return Promise<Message|null>
     */
    public function getMessage(string $command): Promise
    {
        return $this->storage->getMessage($command);
    }
}
