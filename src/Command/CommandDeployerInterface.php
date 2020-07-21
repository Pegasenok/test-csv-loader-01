<?php


namespace App\Command;


interface CommandDeployerInterface
{
    /**
     * @param CommandInterface $command
     * @return CommandId
     * @throws CommandPoolException
     */
    public function deployCommand(CommandInterface $command): CommandId;

    /**
     * @return CommandInterface
     * @throws EmptyCommandPoolException
     */
    public function getOneCommand(): CommandInterface;
}