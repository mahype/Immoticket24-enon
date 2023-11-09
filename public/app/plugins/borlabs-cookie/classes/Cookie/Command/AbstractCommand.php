<?php

declare(strict_types=1);

namespace Borlabs\Cookie\Command;

use Borlabs\Cookie\Dto\System\MessageDto;
use WP_CLI;

abstract class AbstractCommand
{
    /**
     * @return \WP_CLI\Formatter
     */
    protected function getFormatter(array &$assocArgs, array $defaultFields): WP_CLI\Formatter
    {
        if (!empty($assocArgs['fields'])) {
            if (is_string($assocArgs['fields'])) {
                $fields = explode(',', $assocArgs['fields']);
            } else {
                $fields = $assocArgs['fields'];
            }
        } else {
            $fields = $defaultFields;
        }

        return new \WP_CLI\Formatter($assocArgs, $fields);
    }

    protected function printMessage(MessageDto $messageDto): void
    {
        if ($messageDto->type === 'error') {
            $type = 'Error';
        } elseif ($messageDto->type === 'success') {
            $type = 'Success';
        } elseif ($messageDto->type === 'info') {
            $type = 'Info';
        } elseif ($messageDto->type === 'warning') {
            $type = 'Warning';
        } elseif ($messageDto->type === 'offer') {
            $type = 'Offer';
        } elseif ($messageDto->type === 'critical') {
            $type = 'Critical';
        } else {
            $type = 'Info';
        }

        WP_CLI::line($type . ': ' . $messageDto->message);
    }

    /**
     * @param \Borlabs\Cookie\Dto\System\MessageDto[] $messageDtos
     */
    protected function printMessages(array $messageDtos): void
    {
        foreach ($messageDtos as $messageDto) {
            $this->printMessage($messageDto);
        }
    }
}
