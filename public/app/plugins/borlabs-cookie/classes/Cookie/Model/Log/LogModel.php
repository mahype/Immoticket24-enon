<?php
/*
 *  Copyright (c) 2023 Borlabs GmbH. All rights reserved.
 *  This file may not be redistributed in whole or significant part.
 *  Content of this file is protected by international copyright laws.
 *
 *  ----------------- Borlabs Cookie IS NOT FREE SOFTWARE -----------------
 *
 *  @copyright Borlabs GmbH, https://borlabs.io
 */

declare(strict_types=1);

namespace Borlabs\Cookie\Model\Log;

use Borlabs\Cookie\Enum\Log\LogLevelEnum;
use Borlabs\Cookie\Model\AbstractModel;
use DateTime;

class LogModel extends AbstractModel
{
    public ?array $backtrace = null;

    public ?array $context = null;

    public DateTime $createdAt;

    public LogLevelEnum $level;

    public string $message;

    public string $processId;
}
