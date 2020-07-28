<?php

namespace Util;

use App\Exception\BadLogicException;
use App\Util\ConfigurationAwareTrait;
use PHPUnit\Framework\TestCase;

class ConfigurationAwareTraitTest extends TestCase
{
    public function testTrait()
    {
        /** @var ConfigurationAwareTrait $mock */
        $mock = $this->getMockForTrait(ConfigurationAwareTrait::class);
        $mock->setConfiguration(['something' => new \ArrayObject()]);

        $reflector = new \ReflectionClass($mock);
        $method = $reflector->getMethod('get');
        $method->setAccessible(true);

        $this->assertInstanceOf(\ArrayObject::class, $method->invoke($mock, 'something'));

        $this->expectException(BadLogicException::class);
        $this->assertInstanceOf(\ArrayObject::class, $method->invoke($mock, 'another'));
    }
}
