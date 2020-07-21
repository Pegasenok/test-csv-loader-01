<?php


namespace App\Command;


interface CommandInterface
{
    public function getName(): string;
    public function getPayload(): array;
}