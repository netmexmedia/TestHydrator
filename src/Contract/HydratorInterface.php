<?php

namespace Netmex\HydratorBundle\Contract;

interface HydratorInterface
{
    /**
     * Hydrate an object or create a new instance of the given class
     *
     * @param string|object $target
     * @param array $data
     * @return object
     */
    public function hydrate(string|object $target, array $data): object;

    /**
     * Convert an object to an array representation
     */
    public function dehydrate(object $object): array;
}