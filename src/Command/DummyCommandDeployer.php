<?php


namespace App\Command;


class DummyCommandDeployer implements CommandDeployerInterface
{
    /**
     * @param CommandInterface $command
     * @return CommandId
     * @throws CommandPoolException
     */
    public function deployCommand(CommandInterface $command): CommandId
    {
        $commandId = md5(random_bytes(10));
        return new CommandId($commandId);
    }

    /**
     * @return CommandInterface
     * @throws EmptyCommandPoolException
     */
    public function getOneCommand(): CommandInterface
    {
        throw new EmptyCommandPoolException();
    }

}