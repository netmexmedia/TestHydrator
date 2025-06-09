<?php

namespace Netmex\HydratorBundle\Tests;

use Netmex\HydratorBundle\Hydrator\Hydrator;
use Netmex\TransformerBundle\Contracts\TransformerInterface;
use PHPUnit\Framework\TestCase;
use Netmex\HydratorBundle\Processor\AttributeProcessor;
use Netmex\HydratorBundle\Transformer\TransformerRegistry;
use Symfony\Component\Serializer\Serializer;

class HydratorTest extends TestCase
{
    public function testHydrateRemovesIgnoredProperties()
    {
        $serializer = $this->createMock(Serializer::class);
        $attributeProcessor = $this->createMock(AttributeProcessor::class);
        $transformerRegistry = $this->createMock(TransformerRegistry::class);

        $attributeProcessor->method('getMetadata')->willReturn([
            'ignoredProperties' => ['secret'],
            'transformers' => [],
        ]);

        $dataInput = [
            'name' => 'John',
            'secret' => 'should be removed',
        ];

        $expectedDataAfterIgnore = [
            'name' => 'John',
        ];

        $serializer->expects($this->once())
            ->method('denormalize')
            ->with(
                $expectedDataAfterIgnore,
                'SomeClass',
                null,
                []
            )
            ->willReturn(new \stdClass());

        $hydrator = new Hydrator($serializer, $attributeProcessor, $transformerRegistry);

        $result = $hydrator->hydrate('SomeClass', $dataInput);

        $this->assertInstanceOf(\stdClass::class, $result);
    }

    public function testHydrateAppliesTransformer()
    {
        $serializer = $this->createMock(Serializer::class);
        $attributeProcessor = $this->createMock(AttributeProcessor::class);
        $transformerRegistry = $this->createMock(TransformerRegistry::class);

        $attributeProcessor->method('getMetadata')->willReturn([
            'ignoredProperties' => [],
            'transformers' => ['name' => 'dummy'],
        ]);

        $dataInput = [
            'name' => 'john',
        ];

        $transformer = new class implements TransformerInterface {
            public function transform(mixed $value): mixed
            {
                return strtoupper($value);
            }

            public function reverse(mixed $data): mixed
            {
                return null;
            }
        };

        $transformerRegistry->method('getTransformer')
            ->with('dummy')
            ->willReturn($transformer);

        // After transform, name should be 'JOHN'
        $serializer->expects($this->once())
            ->method('denormalize')
            ->with(
                ['name' => 'JOHN'],
                'SomeClass',
                null,
                []
            )
            ->willReturn(new \stdClass());

        $hydrator = new Hydrator($serializer, $attributeProcessor, $transformerRegistry);

        $result = $hydrator->hydrate('SomeClass', $dataInput);

        $this->assertInstanceOf(\stdClass::class, $result);
    }
}
