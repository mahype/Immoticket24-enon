<?php

declare(strict_types=1);

namespace Borlabs\Cookie\Repository;

/**
 * This is an interface to transform the result set of a run WordPress query (using the `ARRAY_A` mode, see
 * https://developer.wordpress.org/reference/classes/wpdb/get_results/) into the desired format.
 */
interface ResultHydratorInterface
{
    public function hydrate(array $result);
}
