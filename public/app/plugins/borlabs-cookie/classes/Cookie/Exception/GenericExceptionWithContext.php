<?php

declare(strict_types=1);

namespace Borlabs\Cookie\Exception;

use Throwable;

class GenericExceptionWithContext extends GenericException
{
    private array $context;

    public function __construct(
        string $message = '',
        array $context = [],
        ?Throwable $previous = null
    ) {
        parent::__construct($message, $previous);

        $this->context = $context;
    }

    public function getContext(): array
    {
        return $this->context;
    }
}
