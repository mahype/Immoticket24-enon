<?php

declare(strict_types=1);

namespace Borlabs\Cookie\Repository;

use Borlabs\Cookie\Adapter\WpDb;
use Borlabs\Cookie\Exception\UnexpectedRepositoryOperationException;

/**
 * This class runs a given `RepositoryQuery` against the WordPress database. If a result set is expected,
 * it is hydrated via the given `ResultHydratorInterface`.
 *
 * @internal
 */
class RepositoryQueryExecutor
{
    private ResultHydratorInterface $hydrator;

    private WpDb $wpdb;

    public function __construct(
        WpDb $wpdb,
        ResultHydratorInterface $hydrator
    ) {
        $this->wpdb = $wpdb;
        $this->hydrator = $hydrator;
    }

    /**
     * Use this method if you do NOT expect a result-set (INSERT, UPDATE, DELETE).
     */
    public function execute(RepositoryQuery $query)
    {
        $result = $this->wpdb->query(
            $this->wpdb->prepare($query->getWpSqlQuery(), $query->getParameters()),
        );

        if (!empty($this->wpdb->last_error)) {
            throw new UnexpectedRepositoryOperationException('[REPOSITORY_WP_QUERY_EXECUTOR:EXECUTE] ' . $this->wpdb->last_error);
        }

        return $result;
    }

    /**
     * Use this method if you expect a result-set (SELECT).
     */
    public function getResults(RepositoryQuery $query)
    {
        $result = $this->wpdb->get_results(
            $this->wpdb->prepare($query->getWpSqlQuery(), $query->getParameters()),
            ARRAY_A,
        );

        if (!empty($this->wpdb->last_error)) {
            throw new UnexpectedRepositoryOperationException('[REPOSITORY_WP_QUERY_EXECUTOR:GET_RESULTS] ' . $this->wpdb->last_error);
        }

        return $this->hydrator->hydrate($result);
    }
}
