<?php declare(strict_types=1);

namespace AsyncBot\Plugin\CustomMessageCommand;

use Amp\Promise;
use AsyncBot\Core\Exception\Storage\EntryNotFound;
use AsyncBot\Core\Message\Node\Message;
use AsyncBot\Core\Message\Parser;
use AsyncBot\Core\Storage\KeyValue;
use function Amp\call;

final class Storage
{
    private KeyValue $storage;

    public function __construct(KeyValue $storage)
    {
        $this->storage = $storage;
    }

    /**
     * @return Promise<null>
     */
    public function registerCommand(string $command, Message $message): Promise
    {
        return call(function () use ($command, $message) {
            $commands = yield $this->getRegisteredCommands();

            $commands[$command] = $message->toString();

            yield $this->storeRegisteredCommands($commands);
        });
    }

    /**
     * @return Promise<null>
     */
    public function unregisterCommand(string $command): Promise
    {
        return call(function () use ($command) {
            $commands = yield $this->getRegisteredCommands();

            unset($commands[$command]);

            yield $this->storeRegisteredCommands($commands);
        });
    }

    /**
     * @return Promise<bool>
     */
    public function isCommandRegistered(string $command): Promise
    {
        return call(function () use ($command) {
            $commands = yield $this->getRegisteredCommands();

            return array_key_exists($command, $commands);
        });
    }

    /**
     * @return Promise<Message|null>
     */
    public function getMessage(string $command): Promise
    {
        return call(function () use ($command) {
            if (!yield $this->isCommandRegistered($command)) {
                return null;
            }

            return (new Parser())->parse((yield $this->getRegisteredCommands())[$command]);
        });
    }

    /**
     * @return Promise<array>
     */
    private function getRegisteredCommands(): Promise
    {
        return call(function () {
            try {
                return yield $this->storage->get(__CLASS__);
            } catch (EntryNotFound $e) {
                return [];
            }
        });
    }

    /**
     * @return Promise<null>
     */
    private function storeRegisteredCommands(array $commands): Promise
    {
        return $this->storage->set(__CLASS__, $commands);
    }
}
