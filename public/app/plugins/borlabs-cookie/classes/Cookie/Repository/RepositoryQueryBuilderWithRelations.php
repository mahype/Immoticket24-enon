<?php

declare(strict_types=1);

namespace Borlabs\Cookie\Repository;

use InvalidArgumentException;
use LogicException;

/**
 * This class extends the normal `RepositoryQueryBuilder` for an abstraction to collect
 * what relationships should be hydrated automatically. For that it replaces the default
 * `RepositoryQueryExecutor` with one using a `ModelHydratorWithRelations` instead of the default
 * `ArrayHydrator`.
 */
class RepositoryQueryBuilderWithRelations extends RepositoryQueryBuilder
{
    private ?ModelHydratorWithRelations $hydratorWithRelations = null;

    public function addWith(string $relationName, $callback = null): void
    {
        if ($callback !== null && !is_callable($callback)) {
            throw new InvalidArgumentException('Second parameter should be a callable with exactly one parameter (`Query`)');
        }
        $relationInfoProperties = $this->modelRepository->getRelationInfoProperties(
            $this->propertyMapDto,
        );

        if (!isset($relationInfoProperties[$relationName])) {
            throw new LogicException($relationName . ' not found in ' . get_class($this->modelRepository));
        }

        $this->initArrayHydratorProperty();

        $this->hydratorWithRelations->addRelation(
            $relationName,
            $callback,
        );
    }

    protected function getWpQueryExecutor(): RepositoryQueryExecutor
    {
        $this->initArrayHydratorProperty();

        return new RepositoryQueryExecutor(
            $this->wpdb,
            $this->hydratorWithRelations,
        );
    }

    private function initArrayHydratorProperty(): void
    {
        if ($this->hydratorWithRelations !== null) {
            return;
        }

        $this->hydratorWithRelations = new ModelHydratorWithRelations(
            $this->modelRepository,
        );
    }
}
