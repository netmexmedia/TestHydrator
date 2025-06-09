<?php

namespace Netmex\HydratorBundle\Transformer;


use Netmex\TransformerBundle\Contracts\TransformerInterface;

class TransformerRegistry
{
    /** @var TransformerInterface[] */
    private array $transformers = [];

    public function addTransformer(string $name, TransformerInterface $transformer): void
    {
        $this->transformers[$name] = $transformer;
    }

    public function getTransformer(string $name): ?TransformerInterface
    {
        return $this->transformers[$name] ?? null;
    }
}