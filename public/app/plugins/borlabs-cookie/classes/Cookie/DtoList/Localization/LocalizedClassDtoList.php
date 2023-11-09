<?php

declare(strict_types=1);

namespace Borlabs\Cookie\DtoList\Localization;

use Borlabs\Cookie\Dto\Localization\LocalizedClassDto;
use Borlabs\Cookie\DtoList\AbstractDtoList;

class LocalizedClassDtoList extends AbstractDtoList
{
    public const DTO_CLASS = LocalizedClassDto::class;

    /**
     * @var array<LocalizedClassDto>
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
            $entry = new LocalizedClassDto(
                $value->className,
                $value->instance,
            );
            $list[$key] = $entry;
        }

        return $list;
    }

    public static function __listToJson(array $data)
    {
        $list = [];

        foreach ($data as $key => $value) {
            $list[$key] = LocalizedClassDto::prepareForJson($value);
        }

        return $list;
    }
}
