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

namespace Borlabs\Cookie\DtoList\CloudScan;

use Borlabs\Cookie\Dto\CloudScan\PageDto;
use Borlabs\Cookie\DtoList\AbstractDtoList;
use Borlabs\Cookie\Enum\CloudScan\PageFailureTypeEnum;
use Borlabs\Cookie\Enum\CloudScan\PageStatusEnum;

/**
 * @extends AbstractDtoList<PageDto>
 */
class PagesDtoList extends AbstractDtoList
{
    public const DTO_CLASS = PageDto::class;

    /**
     * @var array<\Borlabs\Cookie\Dto\CloudScan\PageDto>
     */
    public array $list = [];

    public function __construct(
        ?array $pageList = null
    ) {
        parent::__construct($pageList);
    }

    public static function __listFromJson(array $data)
    {
        $list = [];

        foreach ($data as $key => $pageData) {
            $page = new PageDto(
                $pageData->url,
                PageStatusEnum::fromValue($pageData->status),
                $pageData->failureType ? PageFailureTypeEnum::fromValue($pageData->failureType) : null,
            );
            $list[$key] = $page;
        }

        return $list;
    }

    public static function __listToJson(array $data)
    {
        $list = [];

        foreach ($data as $key => $pages) {
            $list[$key] = PageDto::prepareForJson($pages);
        }

        return $list;
    }
}
