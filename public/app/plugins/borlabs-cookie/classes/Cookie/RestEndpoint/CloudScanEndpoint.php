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

namespace Borlabs\Cookie\RestEndpoint;

use Borlabs\Cookie\ApiClient\CloudScanApiClient;
use Borlabs\Cookie\Dto\CloudScan\PageDto;
use Borlabs\Cookie\Dto\CloudScan\ScanResultDto;
use Borlabs\Cookie\Enum\CloudScan\CloudScanStatusEnum;
use Borlabs\Cookie\Enum\CloudScan\PageStatusEnum;
use Borlabs\Cookie\Exception\GenericException;
use Borlabs\Cookie\Exception\TranslatedException;
use Borlabs\Cookie\Repository\CloudScan\CloudScanRepository;
use Borlabs\Cookie\System\CloudScan\CloudScanService;
use Borlabs\Cookie\System\Log\Log;
use WP_REST_Request;

final class CloudScanEndpoint
{
    private CloudScanApiClient $cloudScanApiClient;

    private CloudScanRepository $cloudScanRepository;

    private CloudScanService $cloudScanService;

    private Log $log;

    public function __construct(
        CloudScanApiClient $cloudScanApiClient,
        CloudScanRepository $cloudScanRepository,
        CloudScanService $cloudScanService,
        Log $log
    ) {
        $this->cloudScanApiClient = $cloudScanApiClient;
        $this->cloudScanRepository = $cloudScanRepository;
        $this->cloudScanService = $cloudScanService;
        $this->log = $log;
    }

    public function getResult(WP_REST_Request $request): ?ScanResultDto
    {
        $idParam = $request->get_param('scanId');

        if ($idParam === null) {
            $this->log->error('CloudScanEndpoint: Parameter "scanId" missing', [
                'value' => (string) $idParam,
            ]);

            return null;
        }
        $scanId = (int) $idParam;

        $cloudScan = $this->cloudScanRepository->findById($scanId);

        if ($cloudScan === null) {
            $this->log->error('CloudScanEndpoint: Could not find cloud scan', [
                'scanId' => $scanId,
            ]);

            return null;
        }

        try {
            $cloudScanResponse = $this->cloudScanApiClient->getScan($cloudScan->externalId);
            $failedPagesCount = count(array_filter($cloudScanResponse->pages->list, function (PageDto $page): bool {
                return $page->status->is(PageStatusEnum::FAILED());
            }));
            $finishedPagesCount = count(array_filter($cloudScanResponse->pages->list, function (PageDto $page): bool {
                return $page->status->is(PageStatusEnum::FINISHED());
            }));
            $scanningPagesCount = count(array_filter($cloudScanResponse->pages->list, function (PageDto $page): bool {
                return $page->status->is(PageStatusEnum::SCANNING());
            }));

            if ($cloudScanResponse->status->is(CloudScanStatusEnum::FINISHED())) {
                $cloudScan = $this->cloudScanService->syncScanResult($scanId);

                return new ScanResultDto($cloudScan->status, $failedPagesCount, $finishedPagesCount, $scanningPagesCount);
            }

            return new ScanResultDto($cloudScanResponse->status, $failedPagesCount, $finishedPagesCount, $scanningPagesCount);
        } catch (TranslatedException $exception) {
            $this->log->error('CloudScanEndpoint: ' . $exception->getTranslatedMessage());
        } catch (GenericException $exception) {
            $this->log->error('CloudScanEndpoint: ' . $exception->getMessage());
        }

        return null;
    }

    public function register(): void
    {
        register_rest_route(RestEndpointManager::NAMESPACE . '/v1', '/cloud-scan/(?P<scanId>[0-9]{1,})', [
            'methods' => 'GET',
            'callback' => [$this, 'getResult'],
            'permission_callback' => function () {
                return current_user_can('manage_borlabs_cookie');
            },
        ]);
    }
}
