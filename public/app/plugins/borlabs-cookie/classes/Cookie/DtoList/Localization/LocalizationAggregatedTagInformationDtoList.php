<?php

declare(strict_types=1);

namespace Borlabs\Cookie\DtoList\Localization;

use Borlabs\Cookie\Dto\Localization\LocalizationAggregatedTagInformationDto;
use Borlabs\Cookie\DtoList\AbstractDtoList;

class LocalizationAggregatedTagInformationDtoList extends AbstractDtoList
{
    public const DTO_CLASS = LocalizationAggregatedTagInformationDto::class;

    public const UNIQUE_PROPERTY = 'id';

    /**
     * @var array<LocalizationAggregatedTagInformationDto>
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
            $entry = new LocalizationAggregatedTagInformationDto(
                $value->id,
                $value->contents,
            );
            $list[$key] = $entry;
        }

        return $list;
    }

    public static function __listToJson(array $data)
    {
        $list = [];

        foreach ($data as $key => $value) {
            $list[$key] = LocalizationAggregatedTagInformationDto::prepareForJson($value);
        }

        return $list;
    }
}
