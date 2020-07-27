<?php

namespace Command;

use App\Command\CommandRegistry;
use PHPUnit\Framework\TestCase;

class CommandRegistryTest extends TestCase
{
    public function testGetCommandFromArrayException()
    {
        $this->expectException(\Exception::class);
        $registry = new CommandRegistry();
        $registry->getCommandFromArray(['name'=>'something']);
    }
}
