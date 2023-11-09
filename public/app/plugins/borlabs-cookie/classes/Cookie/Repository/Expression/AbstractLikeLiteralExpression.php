<?php

declare(strict_types=1);

namespace Borlabs\Cookie\Repository\Expression;

use InvalidArgumentException;

abstract class AbstractLikeLiteralExpression extends AbstractExpression
{
    protected LiteralExpression $literalExpression;

    public function __construct(
        LiteralExpression $literalExpression
    ) {
        $this->literalExpression = $literalExpression;
    }

    protected function escapeLiteralExpression(LiteralExpression $literalExpression): string
    {
        $queryPart = $literalExpression->toWpSqlQueryPart();

        if ($queryPart->wpSqlQuery !== '%s' || count($queryPart->parameters) !== 1) {
            throw new InvalidArgumentException('Literal expression inside of AbstractLikeLiteralExpression must be a single string');
        }

        return str_replace(
            ['%', '_'],
            ['\%', '\_'],
            $queryPart->parameters[0],
        );
    }
}
