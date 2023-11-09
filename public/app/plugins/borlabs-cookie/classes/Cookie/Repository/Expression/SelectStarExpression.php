<?php

declare(strict_types=1);

namespace Borlabs\Cookie\Repository\Expression;

use Borlabs\Cookie\Repository\RepositoryQueryPart;

class SelectStarExpression extends AbstractExpression
{
    public function toWpSqlQueryPart(): RepositoryQueryPart
    {
        return new RepositoryQueryPart('*', []);
    }
}
