<?php


namespace App\Command;


interface CommandInterface extends \JsonSerializable
{
    public function setCommandId(CommandId $commandId);
    public function getCommandId(): CommandId;
    public function getName(): string;
    public function getPayload(): array;
}