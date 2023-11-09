<?php

declare(strict_types=1);

namespace Borlabs\Cookie\Repository;

/**
 * This class is used to collect parts to later build a complete `RepositoryQuery` object.
 *
 * @internal
 */
class RepositoryQueryPart
{
    public array $parameters;

    public string $wpSqlQuery;

    public function __construct(
        string $wpSqlQuery = '',
        array $parameters = []
    ) {
        $this->wpSqlQuery = $wpSqlQuery;
        $this->parameters = $parameters;
    }

    public function append(self $toAppendPart): void
    {
        $this->wpSqlQuery .= $toAppendPart->wpSqlQuery;
        $this->parameters = array_merge($this->parameters, $toAppendPart->parameters);
    }
}
