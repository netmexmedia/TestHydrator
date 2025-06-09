<?php

namespace Netmex\HydratorBundle\Tests\Processor;

use Netmex\HydratorBundle\Processor\AttributeProcessor;
use PHPUnit\Framework\TestCase;
use Netmex\HydratorBundle\Attribute\Ignore;
use Symfony\Contracts\Cache\CacheInterface;

class AttributeProcessorTest extends TestCase
{
    public function testIgnoresPropertiesWithAttribute()
    {
        // Mock cache
        $cache = $this->createMock(CacheInterface::class);

        $cache->expects($this->once())
            ->method('get')
            ->willReturnCallback(function ($key, $callback) {
                // Just run the callback and return its result (simulate cache miss)
                return $callback();
            });

        $processor = new AttributeProcessor($cache);

        $metadata = $processor->getMetadata(DummyClass::class);

        $this->assertContains('ignoredProp', $metadata['ignoredProperties']);
        $this->assertNotContains('normalProp', $metadata['ignoredProperties']);
    }
}

#[Ignore]
class DummyClass
{
    #[Ignore]
    public string $ignoredProp = '';

    public string $normalProp = '';
}
