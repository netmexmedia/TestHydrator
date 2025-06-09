<?php

namespace Netmex\HydratorBundle\Tests\Transformer;

use Netmex\TransformerBundle\Contracts\TransformerInterface;
use PHPUnit\Framework\TestCase;
use Netmex\HydratorBundle\Transformer\TransformerRegistry;

class DummyTransformer implements TransformerInterface
{
    public function transform(mixed $value): mixed
    {
        return strtoupper($value);
    }

    public function reverse(mixed $data): mixed
    {
        return null;
    }
}

class TransformerRegistryTest extends TestCase
{
    public function testAddAndGetTransformer()
    {
        $registry = new TransformerRegistry();
        $transformer = new DummyTransformer();

        $registry->addTransformer('dummy', $transformer);

        $retrieved = $registry->getTransformer('dummy');
        $this->assertSame($transformer, $retrieved);

        $this->assertNull($registry->getTransformer('nonexistent'));
    }
}
