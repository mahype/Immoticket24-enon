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

namespace Borlabs\Cookie\System\ConsentStatistic;

use Borlabs\Cookie\DtoList\ConsentLog\ServiceGroupConsentDtoList;
use Borlabs\Cookie\Model\ConsentStatistic\ConsentStatisticByDayEntryModel;
use Borlabs\Cookie\Model\ConsentStatistic\ConsentStatisticByHourEntryModel;
use Borlabs\Cookie\Repository\ConsentStatistic\ConsentStatisticByDayRepository;
use Borlabs\Cookie\Repository\ConsentStatistic\ConsentStatisticByHourRepository;
use Borlabs\Cookie\Repository\Expression\AssignmentExpression;
use Borlabs\Cookie\Repository\Expression\BinaryOperatorExpression;
use Borlabs\Cookie\Repository\Expression\LiteralExpression;
use Borlabs\Cookie\Repository\Expression\ModelFieldNameExpression;
use Borlabs\Cookie\System\Option\Option;
use DateTime;

class ConsentStatisticService
{
    private ConsentStatisticByDayRepository $consentStatisticByDayRepository;

    private ConsentStatisticByHourRepository $consentStatisticByHourRepository;

    private Option $option;

    public function __construct(
        ConsentStatisticByDayRepository $consentStatisticByDayRepository,
        ConsentStatisticByHourRepository $consentStatisticByHourRepository,
        Option $option
    ) {
        $this->consentStatisticByDayRepository = $consentStatisticByDayRepository;
        $this->consentStatisticByHourRepository = $consentStatisticByHourRepository;
        $this->option = $option;
    }

    public function add(ServiceGroupConsentDtoList $consents, string $uid, int $cookieVersion)
    {
        foreach ($consents->list as $serviceGroupData) {
            foreach ($serviceGroupData->services as $service) {
                $model = new ConsentStatisticByHourEntryModel();
                $model->serviceGroupKey = $serviceGroupData->key;
                $model->serviceKey = $service;
                $model->cookieVersion = $cookieVersion;
                $model->count = 1;
                $model->date = new DateTime();
                $model->hour = (int) (new DateTime())->format('H');
                $model->isAnonymous = $uid === 'anonymous';

                $this->addHourEntry($model);
            }
        }
    }

    public function addDayEntry(ConsentStatisticByDayEntryModel $consentStatisticByDayEntryModel): bool
    {
        return $this->consentStatisticByDayRepository->insertOrIncrementCount(
            $consentStatisticByDayEntryModel,
        ) ? true : false;
    }

    public function addHourEntry(ConsentStatisticByHourEntryModel $consentStatisticByHourEntryModel): bool
    {
        return $this->consentStatisticByHourRepository->insertOrIncrementCount(
            $consentStatisticByHourEntryModel,
        ) ? true : false;
    }

    public function aggregateHourEntries()
    {
        $hourEntries = $this->consentStatisticByHourRepository->getAll();
        $preparedHourEntries = [];

        foreach ($hourEntries as $hourEntry) {
            // Date > Service Group Key > Service Key > Cookie Version > is Anonymous = count
            if (!isset($preparedHourEntries[$hourEntry->date->format('Y-m-d')][$hourEntry->serviceGroupKey][$hourEntry->serviceKey][$hourEntry->cookieVersion][$hourEntry->isAnonymous])) {
                $preparedHourEntries[$hourEntry->date->format('Y-m-d')][$hourEntry->serviceGroupKey][$hourEntry->serviceKey][$hourEntry->cookieVersion][$hourEntry->isAnonymous] = 0;
            }

            $preparedHourEntries[$hourEntry->date->format('Y-m-d')][$hourEntry->serviceGroupKey][$hourEntry->serviceKey][$hourEntry->cookieVersion][$hourEntry->isAnonymous] += $hourEntry->count;
        }

        // Get todays day entries
        $dayEntries = $this->consentStatisticByDayRepository->getAll([
            new BinaryOperatorExpression(
                new ModelFieldNameExpression('date'),
                '=',
                new LiteralExpression(date('Y-m-d')),
            ),
        ]);

        // Delete todays day entries
        foreach ($dayEntries as $dayEntry) {
            $this->consentStatisticByDayRepository->delete($dayEntry);
        }

        // Add day entries
        foreach ($preparedHourEntries as $date => $dateEntries) {
            foreach ($dateEntries as $serviceGroupKey => $serviceGroupKeyEntries) {
                foreach ($serviceGroupKeyEntries as $service => $serviceEntries) {
                    foreach ($serviceEntries as $cookieVersion => $cookieVersionEntries) {
                        foreach ($cookieVersionEntries as $isAnonymous => $count) {
                            $model = new ConsentStatisticByDayEntryModel();
                            $model->cookieVersion = $cookieVersion;
                            $model->count = $count;
                            $model->date = new DateTime($date);
                            $model->isAnonymous = (bool) $isAnonymous;
                            $model->serviceGroupKey = $serviceGroupKey;
                            $model->serviceKey = $service;

                            $this->consentStatisticByDayRepository->insertOrUpdate(
                                $model,
                                [
                                    new AssignmentExpression(
                                        new ModelFieldNameExpression('count'),
                                        new BinaryOperatorExpression(new ModelFieldNameExpression('count'), '+', new LiteralExpression(1)),
                                    ),
                                ],
                            );
                        }
                    }
                }
            }
        }

        // Delete hour entries
        foreach ($hourEntries as $hourEntry) {
            // Delete all entries older than today
            if ($hourEntry->date->format('Ymd') < date('Ymd')) {
                $this->consentStatisticByHourRepository->delete($hourEntry);
            }
        }
    }

    /**
     * @return array<ConsentStatisticByDayEntryModel>
     */
    public function getLastDays(int $days): array
    {
        return $this->consentStatisticByDayRepository->getAll(
            [
                new BinaryOperatorExpression(
                    new ModelFieldNameExpression('date'),
                    '>=',
                    new LiteralExpression(date('Y-m-d', strtotime('-' . $days . ' days'))),
                ),
                new BinaryOperatorExpression(
                    new ModelFieldNameExpression('cookieVersion'),
                    '=',
                    new LiteralExpression($this->option->getGlobal('CookieVersion', 1)->value),
                ),
            ],
        );
    }

    /**
     * @return array<ConsentStatisticByHourEntryModel>
     */
    public function getToday(): array
    {
        return $this->consentStatisticByHourRepository->getAll(
            [
                new BinaryOperatorExpression(
                    new ModelFieldNameExpression('date'),
                    '>=',
                    new LiteralExpression(date('Y-m-d')),
                ),
                new BinaryOperatorExpression(
                    new ModelFieldNameExpression('cookieVersion'),
                    '=',
                    new LiteralExpression($this->option->getGlobal('CookieVersion', 1)->value),
                ),
            ],
        );
    }
}
