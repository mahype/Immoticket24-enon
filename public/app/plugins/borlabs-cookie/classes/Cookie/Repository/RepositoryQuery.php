<?php

declare(strict_types=1);

namespace Borlabs\Cookie\Repository;

/**
 * This class contains a prepare-able and then executable WordPress query (see
 * https://developer.wordpress.org/reference/classes/wpdb/prepare/ for the corresponding format) and its parameters.
 * It can be run via a `RepositoryQueryExecutor`.
 */
class RepositoryQuery
{
    private array $parameters;

    private RepositoryQueryExecutor $repositoryQueryExecutor;

    private string $wpSqlQuery;

    public function __construct(
        RepositoryQueryExecutor $repositoryQueryExecutor,
        string $wpSqlQuery,
        array $parameters
    ) {
        $this->repositoryQueryExecutor = $repositoryQueryExecutor;
        $this->wpSqlQuery = $wpSqlQuery;
        $this->parameters = $parameters;
    }

    public function execute()
    {
        return $this->repositoryQueryExecutor->execute($this);
    }

    public function getParameters(): array
    {
        return $this->parameters;
    }

    public function getResults()
    {
        return $this->repositoryQueryExecutor->getResults($this);
    }

    public function getWpSqlQuery(): string
    {
        return $this->wpSqlQuery;
    }
}
