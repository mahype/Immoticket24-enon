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

namespace Borlabs\Cookie\Dto\Adapter;

use Borlabs\Cookie\Dto\AbstractDto;

class WpGetPagesArgumentDto extends AbstractDto
{
    public string $authors = '';

    public int $childOf = 0;

    /**
     * @var int[]
     */
    public array $exclude = [];

    /**
     * @var int[]
     */
    public array $excludeTree = [];

    public int $hierarchical = 1;

    /**
     * @var int[]
     */
    public array $include = [];

    public string $metaKey = '';

    public string $metaValue = '';

    public int $number = 0;

    public int $offset = 0;

    public int $parent = -1;

    public string $postStatus = 'publish';

    public string $postType = 'page';

    public string $sortColumn = 'post_title';

    public string $sortOrder = 'ASC';
}
