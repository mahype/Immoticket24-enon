<?php

declare(strict_types=1);

namespace Borlabs\Cookie\Repository\Expression;

use Borlabs\Cookie\Repository\RepositoryQueryPart;

class AssignmentExpression extends AbstractExpression
{
    private ModelFieldNameExpression $fieldNameExpression;

    private AbstractExpression $valueExpression;

    public function __construct(
        ModelFieldNameExpression $fieldNameExpression,
        AbstractExpression $valueExpression
    ) {
        $this->fieldNameExpression = $fieldNameExpression;
        $this->valueExpression = $valueExpression;
    }

    public function getExpressionChildren(): array
    {
        return [$this->fieldNameExpression, $this->valueExpression];
    }

    public function getFieldNameExpression(): ModelFieldNameExpression
    {
        return $this->fieldNameExpression;
    }

    public function getValueExpression(): AbstractExpression
    {
        return $this->valueExpression;
    }

    public function toWpSqlQueryPart(): RepositoryQueryPart
    {
        $fieldNameWpQuery = $this->fieldNameExpression->toWpSqlQueryPart();
        $valueWpQuery = $this->valueExpression->toWpSqlQueryPart();

        return new RepositoryQueryPart(
            $fieldNameWpQuery->wpSqlQuery . ' = ' . $valueWpQuery->wpSqlQuery,
            array_merge($fieldNameWpQuery->parameters, $valueWpQuery->parameters),
        );
    }
}
