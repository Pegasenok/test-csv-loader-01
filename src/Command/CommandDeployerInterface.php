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
     * @return string
     */
    public function getQueueName(): string;
}