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

namespace Borlabs\Cookie\Controller\Admin\IabTcf;

use Borlabs\Cookie\Adapter\WpFunction;
use Borlabs\Cookie\Controller\Admin\ControllerInterface;
use Borlabs\Cookie\Controller\Admin\ExtendedRouteValidationInterface;
use Borlabs\Cookie\Dto\System\RequestDto;
use Borlabs\Cookie\Exception\GenericException;
use Borlabs\Cookie\Exception\TranslatedException;
use Borlabs\Cookie\Localization\GlobalLocalizationStrings;
use Borlabs\Cookie\Localization\IabTcf\IabTcfSettingsLocalizationStrings;
use Borlabs\Cookie\Repository\IabTcf\VendorRepository;
use Borlabs\Cookie\Support\Formatter;
use Borlabs\Cookie\System\Config\IabTcfConfig;
use Borlabs\Cookie\System\Config\WidgetConfig;
use Borlabs\Cookie\System\IabTcf\IabTcfService;
use Borlabs\Cookie\System\Language\Language;
use Borlabs\Cookie\System\Message\MessageManager;
use Borlabs\Cookie\System\Script\BorlabsCookieGlobalsService;
use Borlabs\Cookie\System\Template\Template;
use Borlabs\Cookie\System\ThirdPartyCacheClearer\ThirdPartyCacheClearerManager;

final class IabTcfSettingsController implements ControllerInterface, ExtendedRouteValidationInterface
{
    public const CONTROLLER_ID = 'borlabs-cookie-iab-tcf-settings';

    private BorlabsCookieGlobalsService $borlabsCookieGlobalsService;

    private GlobalLocalizationStrings $globalLocalizationStrings;

    private IabTcfConfig $iabTcfConfig;

    private IabTcfService $iabTcfService;

    private IabTcfSettingsLocalizationStrings $iabTcfSettingsLocalizationStrings;

    private Language $language;

    private MessageManager $messageManager;

    private Template $template;

    private ThirdPartyCacheClearerManager $thirdPartyCacheClearerManager;

    private VendorRepository $vendorRepository;

    private WidgetConfig $widgetConfig;

    private WpFunction $wpFunction;

    public function __construct(
        BorlabsCookieGlobalsService $borlabsCookieGlobalsService,
        GlobalLocalizationStrings $globalLocalizationStrings,
        IabTcfConfig $iabTcfConfig,
        IabTcfService $iabTcfService,
        IabTcfSettingsLocalizationStrings $iabTcfSettingsLocalizationStrings,
        Language $language,
        MessageManager $messageManager,
        Template $template,
        ThirdPartyCacheClearerManager $thirdPartyCacheClearerManager,
        VendorRepository $vendorRepository,
        WidgetConfig $widgetConfig,
        WpFunction $wpFunction
    ) {
        $this->borlabsCookieGlobalsService = $borlabsCookieGlobalsService;
        $this->globalLocalizationStrings = $globalLocalizationStrings;
        $this->iabTcfConfig = $iabTcfConfig;
        $this->iabTcfService = $iabTcfService;
        $this->iabTcfSettingsLocalizationStrings = $iabTcfSettingsLocalizationStrings;
        $this->language = $language;
        $this->messageManager = $messageManager;
        $this->template = $template;
        $this->thirdPartyCacheClearerManager = $thirdPartyCacheClearerManager;
        $this->vendorRepository = $vendorRepository;
        $this->widgetConfig = $widgetConfig;
        $this->wpFunction = $wpFunction;
    }

    public function downloadGvl(): string
    {
        try {
            $this->iabTcfService->updateGlobalVendorListFile();
            $this->iabTcfService->updatePurposeTranslationFiles();
            $this->iabTcfService->updateVendors();

            $this->messageManager->success(
                $this->iabTcfSettingsLocalizationStrings::get()['alert']['downloadGvlSuccessfully'],
            );
        } catch (TranslatedException $exception) {
            $this->messageManager->error($exception->getTranslatedMessage());
        } catch (GenericException $exception) {
            $this->messageManager->error($exception->getMessage());
        }

        return $this->viewOverview();
    }

    public function route(RequestDto $request): ?string
    {
        $action = $request->postData['action'] ?? $request->getData['action'] ?? '';

        if ($action === 'downloadGvl') {
            return $this->downloadGvl();
        }

        if ($action === 'save') {
            return $this->save($request->postData);
        }

        return $this->viewOverview();
    }

    public function save(array $postData): string
    {
        $iabTcfConfig = $this->iabTcfConfig->get();
        $iabTcfConfig->iabTcfStatus = (bool) ($postData['iabTcfStatus'] ?? false);
        $this->iabTcfConfig->save($iabTcfConfig, $this->language->getSelectedLanguageCode());

        if ($iabTcfConfig->iabTcfStatus) {
            $this->iabTcfService->updateGlobalVendorListFile();
            $this->iabTcfService->updatePurposeTranslationFiles();
            $this->iabTcfService->updateVendors();

            // When utilizing the IAB TCF, enabling the widget is mandatory as it's part of the requirements.
            $widgetConfig = $this->widgetConfig->get();
            $widgetConfig->show = true;
            $this->widgetConfig->save($widgetConfig, $this->language->getSelectedLanguageCode());
        }

        $this->thirdPartyCacheClearerManager->clearCache();

        $this->messageManager->success($this->globalLocalizationStrings::get()['alert']['savedSuccessfully']);

        return $this->viewOverview();
    }

    public function validate(RequestDto $request, string $nonce, bool $isValid): bool
    {
        /*
         * The button to download the Global Vendor List (GVL) is in the same form as the settings,
         * so it shares the nonce of the "save" action.
         */
        if (in_array($request->postData['action'] ?? '', ['downloadGvl'], true)
            && $this->wpFunction->wpVerifyNonce(self::CONTROLLER_ID . '-save', $nonce)
        ) {
            $isValid = true;
        }

        return $isValid;
    }

    public function viewOverview(): string
    {
        $templateData = [];
        $templateData['controllerId'] = self::CONTROLLER_ID;
        $templateData['localized'] = $this->iabTcfSettingsLocalizationStrings::get();
        $templateData['localized']['global'] = $this->globalLocalizationStrings::get();
        $templateData['data'] = (array) $this->iabTcfConfig->get();
        $templateData['data']['gvlImported'] = $this->iabTcfService->isGlobalVendorListDownloaded();
        $lastSuccessfulCheckWithApiTimestamp = $this->iabTcfService->getLastSuccessfulCheckWithApiTimestamp();
        $templateData['data']['gvlLastSuccessfulCheckWithApiFormattedTime']
            = $lastSuccessfulCheckWithApiTimestamp === null
            ? '-' : Formatter::timestamp($lastSuccessfulCheckWithApiTimestamp);

        return $this->template->getEngine()->render(
            'iab-tcf/iab-tcf-settings/iab-tcf-settings.html.twig',
            $templateData,
        );
    }
}
