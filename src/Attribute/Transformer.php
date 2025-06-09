<?php

namespace Netmex\HydratorBundle\Attribute;

#[\Attribute(\Attribute::TARGET_PROPERTY)]
class Transformer
{
    public string $name;

    public function __construct(string $name)
    {
        $this->name = $name;
    }
}
