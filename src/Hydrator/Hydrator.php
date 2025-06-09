<?php

namespace Netmex\HydratorBundle\Hydrator;

use Netmex\HydratorBundle\Attribute\Ignore;
use Netmex\HydratorBundle\Contract\HydratorInterface;
use Netmex\HydratorBundle\Processor\AttributeProcessor;
use Netmex\HydratorBundle\Transformer\TransformerRegistry;
use Symfony\Component\Serializer\SerializerInterface;

class Hydrator implements HydratorInterface
{
    private SerializerInterface $serializer;

    private AttributeProcessor $attributeProcessor;

    private TransformerRegistry $transformerRegistry;

    public function __construct(
        SerializerInterface $serializer,
        AttributeProcessor $attributeProcessor,
        TransformerRegistry $transformerRegistry
    )
    {
        $this->serializer = $serializer;
        $this->attributeProcessor = $attributeProcessor;
        $this->transformerRegistry = $transformerRegistry;
    }

    public function hydrate(string|object $target, array $data): object
    {
        $className = is_string($target) ? $target : get_class($target);
        $metadata = $this->attributeProcessor->getMetadata($className);

        foreach ($metadata['ignoredProperties'] as $ignored) {
            unset($data[$ignored]);
        }

        foreach ($metadata['transformers'] as $property => $transformerName) {
            if (isset($data[$property])) {
                $transformer = $this->transformerRegistry->getTransformer($transformerName);
                if ($transformer !== null) {
                    $data[$property] = $transformer->transform($data[$property]);
                }
            }
        }

        return $this->serializer->denormalize(
            $data,
            $className,
            null,
            is_object($target) ? ['object_to_populate' => $target] : []
        );
    }

    public function dehydrate(object $object): array
    {
        return $this->serializer->normalize($object);
    }
}