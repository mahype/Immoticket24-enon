<?php

declare(strict_types=1);

namespace Borlabs\Cookie\Repository\Expression;

use Borlabs\Cookie\Repository\RepositoryQueryPart;
use LogicException;

class ModelFieldNameExpression extends AbstractExpression
{
    private ?string $dbColumnName = null;

    private string $modelFieldName;

    public function __construct(
        string $modelFieldName
    ) {
        $this->modelFieldName = $modelFieldName;
    }

    public function getModelFieldName(): string
    {
        return $this->modelFieldName;
    }

    public function setDbColumnName(string $dbColumnName): void
    {
        $this->dbColumnName = $dbColumnName;
    }

    public function toWpSqlQueryPart(): RepositoryQueryPart
    {
        if ($this->dbColumnName === null) {
            throw new LogicException('This node requires setting a dbColumnName. Please do that.');
        }

        return new RepositoryQueryPart(
            '%i',
            [$this->dbColumnName],
        );
    }
}
