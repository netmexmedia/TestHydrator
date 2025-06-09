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
        if (isset($this->transformers[$name])) {
            return $this->transformers[$name];
        }

        if (class_exists($name)) {
            $transformer = new $name();

            if (!$transformer instanceof TransformerInterface) {
                throw new \InvalidArgumentException("Class $name must implement TransformerInterface.");
            }

            $this->transformers[$name] = $transformer;

            return $transformer;
        }

        return null;
    }
}