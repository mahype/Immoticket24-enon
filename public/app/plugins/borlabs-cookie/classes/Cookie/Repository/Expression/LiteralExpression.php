<?php

declare(strict_types=1);

namespace Borlabs\Cookie\Repository\Expression;

use Borlabs\Cookie\Repository\RepositoryQueryPart;
use LogicException;

class LiteralExpression extends AbstractExpression
{
    /**
     * @var mixed
     */
    private $literal;

    public function __construct(
        $literal
    ) {
        $this->literal = $literal;
    }

    public function toWpSqlQueryPart(): RepositoryQueryPart
    {
        switch (gettype($this->literal)) {
            case 'integer':
            case 'boolean':
                return new RepositoryQueryPart('%d', [$this->literal]);

            case 'double':
                return new RepositoryQueryPart('%f', [$this->literal]);

            case 'string':
                return new RepositoryQueryPart('%s', [$this->literal]);

            case 'NULL':
            default:
                throw new LogicException('Unexpected type: ' . gettype($this->literal));
        }
    }
}
