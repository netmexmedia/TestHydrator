<?php

namespace Netmex\HydratorBundle\Processor;

use Netmex\HydratorBundle\Attribute\Ignore;
use Netmex\HydratorBundle\Attribute\Transformer;
use Symfony\Contracts\Cache\CacheInterface;

class AttributeProcessor
{
    private CacheInterface $cache;

    public function __construct(CacheInterface $cache)
    {
        $this->cache = $cache;
    }

    public function getMetadata(string $className): array
    {
        $cacheKey = 'hydrator.metadata.' . str_replace('\\', '.', $className);

        return $this->cache->get($cacheKey, function () use ($className) {
            $ignoredProperties = [];
            $transformers = [];

            $reflection = new \ReflectionClass($className);
            foreach ($reflection->getProperties() as $property) {
                if (!empty($property->getAttributes(Ignore::class))) {
                    $ignoredProperties[] = $property->getName();
                }

                $transformAttrs = $property->getAttributes(Transformer::class);
                if (!empty($transformAttrs)) {
                    $transformerName = $transformAttrs[0]->newInstance()->name;
                    $transformers[$property->getName()] = $transformerName;
                }
            }

            return [
                'ignoredProperties' => $ignoredProperties,
                'transformers' => $transformers,
            ];
        });
    }
}