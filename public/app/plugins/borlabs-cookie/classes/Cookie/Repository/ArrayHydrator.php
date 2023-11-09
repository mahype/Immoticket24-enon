<?php

declare(strict_types=1);

namespace Borlabs\Cookie\Repository;

/**
 * This is the most-basic hydrator, doing nothing and just returning an array of rows and each row
 * is an associative array.
 */
class ArrayHydrator implements ResultHydratorInterface
{
    public function hydrate(array $result): array
    {
        return $result;
    }
}
