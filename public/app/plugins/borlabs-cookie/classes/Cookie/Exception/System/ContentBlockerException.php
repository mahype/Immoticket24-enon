<?php

declare(strict_types=1);

namespace Borlabs\Cookie\Exception\System;

use Borlabs\Cookie\Exception\TranslatedException;

class ContentBlockerException extends TranslatedException
{
    protected const LOCALIZATION_STRING_CLASS = \Borlabs\Cookie\Localization\ContentBlocker\ContentBlockerOverviewLocalizationStrings::class;
}
