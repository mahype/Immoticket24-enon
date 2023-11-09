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

namespace Borlabs\Cookie\System\ContentBlocker;

use Borlabs\Cookie\Model\ContentBlocker\ContentBlockerLocationModel;
use Borlabs\Cookie\Model\ContentBlocker\ContentBlockerModel;
use Borlabs\Cookie\Repository\ContentBlocker\ContentBlockerLocationRepository;
use Borlabs\Cookie\Support\Sanitizer;
use Borlabs\Cookie\Validator\ContentBlocker\ContentBlockerLocationValidator;

class ContentBlockerLocationService
{
    private ContentBlockerLocationRepository $contentBlockerLocationRepository;

    private ContentBlockerLocationValidator $contentBlockerLocationValidator;

    public function __construct(
        ContentBlockerLocationRepository $contentBlockerLocationRepository,
        ContentBlockerLocationValidator $contentBlockerLocationValidator
    ) {
        $this->contentBlockerLocationRepository = $contentBlockerLocationRepository;
        $this->contentBlockerLocationValidator = $contentBlockerLocationValidator;
    }

    public function save(ContentBlockerModel $contentBlockerModel, array $postData): void
    {
        if (isset($contentBlockerModel->contentBlockerLocations)) {
            foreach ($contentBlockerModel->contentBlockerLocations as $contentBlockerLocation) {
                $this->contentBlockerLocationRepository->delete($contentBlockerLocation);
            }
        }

        foreach ($postData as $newContentBlockerLocationData) {
            if (!$this->contentBlockerLocationValidator->isValid($newContentBlockerLocationData)) {
                continue;
            }

            $newContentBlockerLocationData = Sanitizer::requestData($newContentBlockerLocationData);

            $newModel = new ContentBlockerLocationModel();
            $newModel->contentBlockerId = $contentBlockerModel->id;
            $newModel->hostname = $newContentBlockerLocationData['hostname'];
            $newModel->path = $newContentBlockerLocationData['path'];
            $this->contentBlockerLocationRepository->insert($newModel);
        }
    }
}
