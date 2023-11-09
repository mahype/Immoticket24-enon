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

namespace Borlabs\Cookie\Controller\Admin\Dialog;

use Borlabs\Cookie\Controller\Admin\ControllerInterface;
use Borlabs\Cookie\Dto\System\RequestDto;
use Borlabs\Cookie\Localization\DefaultLocalizationStrings;
use Borlabs\Cookie\Localization\Dialog\DialogLocalizationLocalizationStrings;
use Borlabs\Cookie\Localization\GlobalLocalizationStrings;
use Borlabs\Cookie\System\Config\DialogLocalization;
use Borlabs\Cookie\System\Config\DialogSettingsConfig;
use Borlabs\Cookie\System\Language\Language;
use Borlabs\Cookie\System\Message\MessageManager;
use Borlabs\Cookie\System\Template\Template;
use Borlabs\Cookie\System\ThirdPartyCacheClearer\ThirdPartyCacheClearerManager;

/**
 * The **DialogLocalizationController** class takes care of displaying the "Dialog - Localization" section in the
 * backend. It also processes all requests that can be executed in the Dialog - Localization section.
 */
final class DialogLocalizationController implements ControllerInterface
{
    public const CONTROLLER_ID = 'borlabs-cookie-dialog-localization';

    private DefaultLocalizationStrings $defaultLocalizationStrings;

    private DialogLocalization $dialogLocalization;

    private DialogSettingsConfig $dialogSettingsConfig;

    private GlobalLocalizationStrings $globalLocalizationStrings;

    private Language $language;

    private MessageManager $messageManager;

    private Template $template;

    private ThirdPartyCacheClearerManager $thirdPartyCacheClearerManager;

    public function __construct(
        DefaultLocalizationStrings $defaultLocalizationStrings,
        DialogLocalization $dialogLocalization,
        DialogSettingsConfig $dialogSettingsConfig,
        GlobalLocalizationStrings $globalLocalizationStrings,
        Language $language,
        MessageManager $messageManager,
        Template $template,
        ThirdPartyCacheClearerManager $thirdPartyCacheClearerManager
    ) {
        $this->defaultLocalizationStrings = $defaultLocalizationStrings;
        $this->dialogLocalization = $dialogLocalization;
        $this->dialogSettingsConfig = $dialogSettingsConfig;
        $this->globalLocalizationStrings = $globalLocalizationStrings;
        $this->language = $language;
        $this->messageManager = $messageManager;
        $this->template = $template;
        $this->thirdPartyCacheClearerManager = $thirdPartyCacheClearerManager;
    }

    public function reset(): bool
    {
        $this->language->loadBlogLanguage();
        $this->dialogLocalization->save($this->dialogLocalization->defaultConfig(), $this->language->getSelectedLanguageCode());
        $this->language->unloadBlogLanguage();
        $this->thirdPartyCacheClearerManager->clearCache();

        $this->messageManager->success($this->globalLocalizationStrings::get()['alert']['resetSuccessfully']);

        return true;
    }

    /**
     * Is loaded by {@see \Borlabs\Cookie\System\WordPressAdminDriver\ControllerManager::load()} and gets information
     * what about to do.
     *
     * @throws \Twig\Error\Error
     */
    public function route(RequestDto $request): ?string
    {
        $action = $request->postData['action'] ?? $request->getData['action'] ?? '';

        if ($action === 'reset') {
            $this->reset();
        } elseif ($action === 'save') {
            $this->save($request->postData);
        }

        return $this->viewOverview();
    }

    /**
     * Updates the configuration.
     *
     * @see \Borlabs\Cookie\System\Config\AbstractConfigManagerWithLanguage
     *
     * @param array<string> $postData
     */
    public function save(array $postData): bool
    {
        $this->language->loadBlogLanguage();
        $defaultLocalization = $this->defaultLocalizationStrings->get();
        $this->language->unloadBlogLanguage();
        $localization = $this->dialogLocalization->get();
        // IAB related texts cannot be modified by the user due IAB policy.
        $ignoreKeys = [
            'iabTcfDescriptionIndiviualSettings',
            'iabTcfDescriptionLegInt',
            'iabTcfDescriptionMoreInformation',
            'iabTcfDescriptionNoCommitment',
            'iabTcfDescriptionPersonalData',
            'iabTcfDescriptionRevoke',
            'iabTcfDescriptionTechnology',
        ];

        foreach ($localization as $key => $string) {
            if (isset($postData[$key]) && in_array($key, $ignoreKeys, true) === false) {
                $localization->{$key} = $postData[$key];
            } elseif (isset($postData[$key]) && in_array($key, $ignoreKeys, true) === true) {
                $localization->{$key} = $defaultLocalization['dialog'][$key];
            }
        }
        $this->dialogLocalization->save($localization, $this->language->getSelectedLanguageCode());

        $dialogSettingsConfig = $this->dialogSettingsConfig->get();
        $dialogSettingsConfig->legalInformationDescriptionConfirmAgeStatus = (bool) ($postData['legalInformationDescriptionConfirmAgeStatus'] ?? false);
        $dialogSettingsConfig->legalInformationDescriptionIndividualSettingsStatus = (bool) ($postData['legalInformationDescriptionIndividualSettingsStatus'] ?? false);
        $dialogSettingsConfig->legalInformationDescriptionMoreInformationStatus = (bool) ($postData['legalInformationDescriptionMoreInformationStatus'] ?? false);
        $dialogSettingsConfig->legalInformationDescriptionNonEuDataTransferStatus = (bool) ($postData['legalInformationDescriptionNonEuDataTransferStatus'] ?? false);
        $dialogSettingsConfig->legalInformationDescriptionNoObligationStatus = (bool) ($postData['legalInformationDescriptionNoObligationStatus'] ?? false);
        $dialogSettingsConfig->legalInformationDescriptionPersonalDataStatus = (bool) ($postData['legalInformationDescriptionPersonalDataStatus'] ?? false);
        $dialogSettingsConfig->legalInformationDescriptionRevokeStatus = (bool) ($postData['legalInformationDescriptionRevokeStatus'] ?? false);
        $dialogSettingsConfig->legalInformationDescriptionTechnologyStatus = (bool) ($postData['legalInformationDescriptionTechnologyStatus'] ?? false);
        $this->dialogSettingsConfig ->save($dialogSettingsConfig, $this->language->getSelectedLanguageCode());
        $this->thirdPartyCacheClearerManager->clearCache();

        $this->messageManager->success($this->globalLocalizationStrings::get()['alert']['savedSuccessfully']);

        return true;
    }

    /**
     * Returns the overview.
     *
     * @throws \Twig\Error\Error
     */
    public function viewOverview(): string
    {
        $templateData = [];
        $templateData['controllerId'] = self::CONTROLLER_ID;
        $templateData['localized'] = DialogLocalizationLocalizationStrings::get();
        $templateData['localized']['global'] = $this->globalLocalizationStrings::get();
        $templateData['data'] = (array) $this->dialogLocalization->get();
        $templateData['data'] = array_merge($templateData['data'], (array) $this->dialogSettingsConfig->get());

        return $this->template->getEngine()->render(
            'dialog/dialog-localization/dialog-localization.html.twig',
            $templateData,
        );
    }
}
