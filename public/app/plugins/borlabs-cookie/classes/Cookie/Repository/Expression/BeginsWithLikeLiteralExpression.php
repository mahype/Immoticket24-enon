<?php

declare(strict_types=1);

namespace Borlabs\Cookie\Repository\Expression;

use Borlabs\Cookie\Repository\RepositoryQueryPart;

class BeginsWithLikeLiteralExpression extends AbstractLikeLiteralExpression
{
    public function toWpSqlQueryPart(): RepositoryQueryPart
    {
        return new RepositoryQueryPart(
            '%s',
            ['%' . $this->escapeLiteralExpression($this->literalExpression)],
        );
    }
}
