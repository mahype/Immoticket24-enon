<?php

declare(strict_types=1);

namespace Borlabs\Cookie\DtoList\Localization;

use Borlabs\Cookie\Dto\Localization\LocalizationCollectorEntryDto;
use Borlabs\Cookie\DtoList\AbstractDtoList;

class LocalizationCollectorEntryDtoList extends AbstractDtoList
{
    public const DTO_CLASS = LocalizationCollectorEntryDto::class;

    /**
     * @var array<LocalizationCollectorEntryDto>
     */
    public array $list = [];

    public function __construct(
        ?array $localizationCollectorEntryList = null
    ) {
        parent::__construct($localizationCollectorEntryList);
    }

    public static function __listFromJson(array $data)
    {
        $list = [];

        foreach ($data as $key => $value) {
            $entry = new LocalizationCollectorEntryDto(
                $value->localizationClassName,
                $value->text,
                $value->context,
                $value->domain,
                $value->translation,
            );
            $list[$key] = $entry;
        }

        return $list;
    }

    public static function __listToJson(array $data)
    {
        $list = [];

        foreach ($data as $key => $value) {
            $list[$key] = LocalizationCollectorEntryDto::prepareForJson($value);
        }

        return $list;
    }
}
