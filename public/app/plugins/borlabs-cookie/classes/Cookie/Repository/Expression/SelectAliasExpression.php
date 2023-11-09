<?php

declare(strict_types=1);

namespace Borlabs\Cookie\Repository\Expression;

use Borlabs\Cookie\Repository\RepositoryQueryPart;

class SelectAliasExpression extends AbstractExpression
{
    private ?string $alias;

    private AbstractExpression $selection;

    public function __construct(
        AbstractExpression $selection,
        ?string $alias = null
    ) {
        $this->selection = $selection;
        $this->alias = $alias;
    }

    public function toWpSqlQueryPart(): RepositoryQueryPart
    {
        $return = $this->selection->toWpSqlQueryPart();

        if ($this->alias !== null) {
            $return->wpSqlQuery .= ' AS %i';
            $return->parameters[] = $this->alias;
        }

        return $return;
    }
}
