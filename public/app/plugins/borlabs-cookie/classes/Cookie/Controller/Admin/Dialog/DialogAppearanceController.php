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

use Borlabs\Cookie\Adapter\WpFunction;
use Borlabs\Cookie\Controller\Admin\ControllerInterface;
use Borlabs\Cookie\Dto\System\RequestDto;
use Borlabs\Cookie\Localization\Dialog\DialogAppearanceLocalizationStrings;
use Borlabs\Cookie\Localization\GlobalLocalizationStrings;
use Borlabs\Cookie\System\Config\DialogStyleConfig;
use Borlabs\Cookie\System\Language\Language;
use Borlabs\Cookie\System\Message\MessageManager;
use Borlabs\Cookie\System\Style\StyleBuilder;
use Borlabs\Cookie\System\Template\Template;
use Borlabs\Cookie\System\ThirdPartyCacheClearer\ThirdPartyCacheClearerManager;
use Borlabs\Cookie\Validator\Dialog\DialogAppearanceValidator;

final class DialogAppearanceController implements ControllerInterface
{
    public const CONTROLLER_ID = 'borlabs-cookie-dialog-appearance';

    private DialogStyleConfig $dialogStyleConfig;

    private GlobalLocalizationStrings $globalLocalizationStrings;

    private Language $language;

    private MessageManager $messageManager;

    private StyleBuilder $styleBuilder;

    private Template $template;

    private ThirdPartyCacheClearerManager $thirdPartyCacheClearerManager;

    private WpFunction $wpFunction;

    public function __construct(
        DialogStyleConfig $dialogStyleConfig,
        GlobalLocalizationStrings $globalLocalizationStrings,
        Language $language,
        MessageManager $messageManager,
        StyleBuilder $styleBuilder,
        Template $template,
        ThirdPartyCacheClearerManager $thirdPartyCacheClearerManager,
        WpFunction $wpFunction
    ) {
        $this->dialogStyleConfig = $dialogStyleConfig;
        $this->globalLocalizationStrings = $globalLocalizationStrings;
        $this->language = $language;
        $this->messageManager = $messageManager;
        $this->styleBuilder = $styleBuilder;
        $this->template = $template;
        $this->thirdPartyCacheClearerManager = $thirdPartyCacheClearerManager;
        $this->wpFunction = $wpFunction;
    }

    public function reset(): bool
    {
        // Get default settings
        $defaultConfig = $this->dialogStyleConfig->defaultConfig();
        // Save settings
        $this->dialogStyleConfig->save($defaultConfig, $this->language->getSelectedLanguageCode());
        $this->styleBuilder->updateCssFileAndIncrementStyleVersion(
            $this->wpFunction->getCurrentBlogId(),
            $this->language->getSelectedLanguageCode(),
        );
        $this->thirdPartyCacheClearerManager->clearCache();

        $this->messageManager->success($this->globalLocalizationStrings::get()['alert']['resetSuccessfully']);

        return true;
    }

    public function route(RequestDto $request): ?string
    {
        $action = $request->postData['action'] ?? $request->getData['action'] ?? '';

        if ($action === 'reset') {
            $this->reset();
        } elseif ($action === 'save') {
            $validator = new DialogAppearanceValidator($this->messageManager);

            if ($validator->isValid($request->postData)) {
                $this->save($request->postData);
            }
        }

        return $this->viewOverview();
    }

    public function save(array $postData): bool
    {
        $styleConfig = $this->dialogStyleConfig->get();
        $styleConfig->dialogAnimationDelay = $this->dialogStyleConfig->defaultConfig()->dialogAnimationDelay;
        $styleConfig->dialogBackgroundColor = (string) $postData['dialogBackgroundColor'];
        $styleConfig->dialogBorderRadiusBottomLeft = (int) $postData['dialogBorderRadiusBottomLeft'];
        $styleConfig->dialogBorderRadiusBottomRight = (int) $postData['dialogBorderRadiusBottomRight'];
        $styleConfig->dialogBorderRadiusTopLeft = (int) $postData['dialogBorderRadiusTopLeft'];
        $styleConfig->dialogBorderRadiusTopRight = (int) $postData['dialogBorderRadiusTopRight'];
        $styleConfig->dialogControlElementColor = (string) $postData['dialogControlElementColor'];
        $styleConfig->dialogControlElementColorHover = (string) $postData['dialogControlElementColorHover'];
        $styleConfig->dialogFontFamily = (string) ($postData['dialogFontFamily'] ?? 'inherit');
        $styleConfig->dialogFontFamilyStatus = (bool) ($postData['dialogFontFamilyStatus'] ?? false);
        $styleConfig->dialogFontSize = (int) $postData['dialogFontSize'];
        $styleConfig->dialogFooterBackgroundColor = (string) $postData['dialogFooterBackgroundColor'];
        $styleConfig->dialogFooterTextColor = (string) $postData['dialogFooterTextColor'];
        $styleConfig->dialogBackdropBackgroundColor = (string) $postData['dialogBackdropBackgroundColor'];
        $styleConfig->dialogBackdropBackgroundOpacity = (int) $postData['dialogBackdropBackgroundOpacity'];
        $styleConfig->dialogSeparatorColor = (string) $postData['dialogSeparatorColor'];
        $styleConfig->dialogTextColor = (string) $postData['dialogTextColor'];
        // Buttons
        $styleConfig->dialogButtonAcceptAllColor = (string) $postData['dialogButtonAcceptAllColor'];
        $styleConfig->dialogButtonAcceptAllColorHover = (string) $postData['dialogButtonAcceptAllColorHover'];
        $styleConfig->dialogButtonAcceptAllTextColor = (string) $postData['dialogButtonAcceptAllTextColor'];
        $styleConfig->dialogButtonAcceptAllTextColorHover = (string) $postData['dialogButtonAcceptAllTextColorHover'];
        $styleConfig->dialogButtonAcceptOnlyEssentialColor = (string) $postData['dialogButtonAcceptOnlyEssentialColor'];
        $styleConfig->dialogButtonAcceptOnlyEssentialColorHover
            = (string) $postData['dialogButtonAcceptOnlyEssentialColorHover'];
        $styleConfig->dialogButtonAcceptOnlyEssentialTextColor
            = (string) $postData['dialogButtonAcceptOnlyEssentialTextColor'];
        $styleConfig->dialogButtonAcceptOnlyEssentialTextColorHover
            = (string) $postData['dialogButtonAcceptOnlyEssentialTextColorHover'];
        $styleConfig->dialogButtonBorderRadiusBottomLeft = (int) $postData['dialogButtonBorderRadiusBottomLeft'];
        $styleConfig->dialogButtonBorderRadiusBottomRight = (int) $postData['dialogButtonBorderRadiusBottomRight'];
        $styleConfig->dialogButtonBorderRadiusTopLeft = (int) $postData['dialogButtonBorderRadiusTopLeft'];
        $styleConfig->dialogButtonBorderRadiusTopRight = (int) $postData['dialogButtonBorderRadiusTopRight'];
        $styleConfig->dialogButtonCloseColor = (string) $postData['dialogButtonCloseColor'];
        $styleConfig->dialogButtonCloseColorHover = (string) $postData['dialogButtonCloseColorHover'];
        $styleConfig->dialogButtonCloseTextColor = (string) $postData['dialogButtonCloseTextColor'];
        $styleConfig->dialogButtonCloseTextColorHover = (string) $postData['dialogButtonCloseTextColorHover'];
        $styleConfig->dialogButtonPreferencesColor = (string) $postData['dialogButtonPreferencesColor'];
        $styleConfig->dialogButtonPreferencesColorHover = (string) $postData['dialogButtonPreferencesColorHover'];
        $styleConfig->dialogButtonPreferencesTextColor = (string) $postData['dialogButtonPreferencesTextColor'];
        $styleConfig->dialogButtonPreferencesTextColorHover = (string) $postData['dialogButtonPreferencesTextColorHover'];
        $styleConfig->dialogButtonSaveConsentColor = (string) $postData['dialogButtonSaveConsentColor'];
        $styleConfig->dialogButtonSaveConsentColorHover = (string) $postData['dialogButtonSaveConsentColorHover'];
        $styleConfig->dialogButtonSaveConsentTextColor = (string) $postData['dialogButtonSaveConsentTextColor'];
        $styleConfig->dialogButtonSaveConsentTextColorHover
            = (string) $postData['dialogButtonSaveConsentTextColorHover'];
        $styleConfig->dialogButtonSelectionColor = (string) $postData['dialogButtonSelectionColor'];
        $styleConfig->dialogButtonSelectionColorHover = (string) $postData['dialogButtonSelectionColorHover'];
        $styleConfig->dialogButtonSelectionTextColor = (string) $postData['dialogButtonSelectionTextColor'];
        $styleConfig->dialogButtonSelectionTextColorHover = (string) $postData['dialogButtonSelectionTextColorHover'];
        // Card
        $styleConfig->dialogCardBackgroundColor = (string) $postData['dialogCardBackgroundColor'];
        $styleConfig->dialogCardBorderRadiusBottomLeft = (int) $postData['dialogCardBorderRadiusBottomLeft'];
        $styleConfig->dialogCardBorderRadiusBottomRight = (int) $postData['dialogCardBorderRadiusBottomRight'];
        $styleConfig->dialogCardBorderRadiusTopLeft = (int) $postData['dialogCardBorderRadiusTopLeft'];
        $styleConfig->dialogCardBorderRadiusTopRight = (int) $postData['dialogCardBorderRadiusTopRight'];
        $styleConfig->dialogCardControlElementColor = (string) $postData['dialogCardControlElementColor'];
        $styleConfig->dialogCardControlElementColorHover = (string) $postData['dialogCardControlElementColorHover'];
        $styleConfig->dialogCardListPaddingMediumScreenBottom = (int) $postData['dialogCardListPaddingMediumScreenBottom'];
        $styleConfig->dialogCardListPaddingMediumScreenLeft = (int) $postData['dialogCardListPaddingMediumScreenLeft'];
        $styleConfig->dialogCardListPaddingMediumScreenRight = (int) $postData['dialogCardListPaddingMediumScreenRight'];
        $styleConfig->dialogCardListPaddingMediumScreenTop = (int) $postData['dialogCardListPaddingMediumScreenTop'];
        $styleConfig->dialogCardListPaddingSmallScreenBottom = (int) $postData['dialogCardListPaddingSmallScreenBottom'];
        $styleConfig->dialogCardListPaddingSmallScreenLeft = (int) $postData['dialogCardListPaddingSmallScreenLeft'];
        $styleConfig->dialogCardListPaddingSmallScreenRight = (int) $postData['dialogCardListPaddingSmallScreenRight'];
        $styleConfig->dialogCardListPaddingSmallScreenTop = (int) $postData['dialogCardListPaddingSmallScreenTop'];
        $styleConfig->dialogCardSeparatorColor = (string) $postData['dialogCardSeparatorColor'];
        $styleConfig->dialogCardTextColor = (string) $postData['dialogCardTextColor'];
        // Checkbox
        $styleConfig->dialogCheckboxBackgroundColorActive = (string) $postData['dialogCheckboxBackgroundColorActive'];
        $styleConfig->dialogCheckboxBackgroundColorDisabled
            = (string) $postData['dialogCheckboxBackgroundColorDisabled'];
        $styleConfig->dialogCheckboxBackgroundColorInactive
            = (string) $postData['dialogCheckboxBackgroundColorInactive'];
        $styleConfig->dialogCheckboxBorderColorActive = (string) $postData['dialogCheckboxBorderColorActive'];
        $styleConfig->dialogCheckboxBorderColorDisabled = (string) $postData['dialogCheckboxBorderColorDisabled'];
        $styleConfig->dialogCheckboxBorderColorInactive = (string) $postData['dialogCheckboxBorderColorInactive'];
        $styleConfig->dialogCheckboxBorderRadiusBottomLeft = (int) $postData['dialogCheckboxBorderRadiusBottomLeft'];
        $styleConfig->dialogCheckboxBorderRadiusBottomRight = (int) $postData['dialogCheckboxBorderRadiusBottomRight'];
        $styleConfig->dialogCheckboxBorderRadiusTopLeft = (int) $postData['dialogCheckboxBorderRadiusTopLeft'];
        $styleConfig->dialogCheckboxBorderRadiusTopRight = (int) $postData['dialogCheckboxBorderRadiusTopRight'];
        $styleConfig->dialogCheckboxCheckMarkColorActive = (string) $postData['dialogCheckboxCheckMarkColorActive'];
        $styleConfig->dialogCheckboxCheckMarkColorDisabled = (string) $postData['dialogCheckboxCheckMarkColorDisabled'];
        // Links
        $styleConfig->dialogLinkPrimaryColor = (string) $postData['dialogLinkPrimaryColor'];
        $styleConfig->dialogLinkPrimaryColorHover = (string) $postData['dialogLinkPrimaryColorHover'];
        $styleConfig->dialogLinkSecondaryColor = (string) $postData['dialogLinkSecondaryColor'];
        $styleConfig->dialogLinkSecondaryColorHover = (string) $postData['dialogLinkSecondaryColorHover'];
        // List
        $styleConfig->dialogListBorderRadiusBottomLeft = (int) $postData['dialogListBorderRadiusBottomLeft'];
        $styleConfig->dialogListBorderRadiusBottomRight = (int) $postData['dialogListBorderRadiusBottomRight'];
        $styleConfig->dialogListBorderRadiusTopLeft = (int) $postData['dialogListBorderRadiusTopLeft'];
        $styleConfig->dialogListBorderRadiusTopRight = (int) $postData['dialogListBorderRadiusTopRight'];
        $styleConfig->dialogListItemBackgroundColorEven = (string) $postData['dialogListItemBackgroundColorEven'];
        $styleConfig->dialogListItemBackgroundColorOdd = (string) $postData['dialogListItemBackgroundColorOdd'];
        $styleConfig->dialogListItemControlElementColor = (string) $postData['dialogListItemControlElementColor'];
        $styleConfig->dialogListItemControlElementColorHover = (string) $postData['dialogListItemControlElementColorHover'];
        $styleConfig->dialogListItemControlElementSeparatorColor = (string) $postData['dialogListItemControlElementSeparatorColor'];
        $styleConfig->dialogListItemSeparatorColor = (string) $postData['dialogListItemSeparatorColor'];
        $styleConfig->dialogListItemSeparatorWidth = (int) $postData['dialogListItemSeparatorWidth'];
        $styleConfig->dialogListItemTextColorEven = (string) $postData['dialogListItemTextColorEven'];
        $styleConfig->dialogListItemTextColorOdd = (string) $postData['dialogListItemTextColorOdd'];
        $styleConfig->dialogListPaddingMediumScreenBottom = (int) $postData['dialogListPaddingMediumScreenBottom'];
        $styleConfig->dialogListPaddingMediumScreenLeft = (int) $postData['dialogListPaddingMediumScreenLeft'];
        $styleConfig->dialogListPaddingMediumScreenRight = (int) $postData['dialogListPaddingMediumScreenRight'];
        $styleConfig->dialogListPaddingMediumScreenTop = (int) $postData['dialogListPaddingMediumScreenTop'];
        $styleConfig->dialogListPaddingSmallScreenBottom = (int) $postData['dialogListPaddingSmallScreenBottom'];
        $styleConfig->dialogListPaddingSmallScreenLeft = (int) $postData['dialogListPaddingSmallScreenLeft'];
        $styleConfig->dialogListPaddingSmallScreenRight = (int) $postData['dialogListPaddingSmallScreenRight'];
        $styleConfig->dialogListPaddingSmallScreenTop = (int) $postData['dialogListPaddingSmallScreenTop'];
        // Search Bar
        $styleConfig->dialogSearchBarInputBackgroundColor = (string) $postData['dialogSearchBarInputBackgroundColor'];
        $styleConfig->dialogSearchBarInputBorderColorDefault = (string) $postData['dialogSearchBarInputBorderColorDefault'];
        $styleConfig->dialogSearchBarInputBorderColorFocus = (string) $postData['dialogSearchBarInputBorderColorFocus'];
        $styleConfig->dialogSearchBarInputBorderRadiusBottomLeft = (int) $postData['dialogSearchBarInputBorderRadiusBottomLeft'];
        $styleConfig->dialogSearchBarInputBorderRadiusBottomRight = (int) $postData['dialogSearchBarInputBorderRadiusBottomRight'];
        $styleConfig->dialogSearchBarInputBorderRadiusTopLeft = (int) $postData['dialogSearchBarInputBorderRadiusTopLeft'];
        $styleConfig->dialogSearchBarInputBorderRadiusTopRight = (int) $postData['dialogSearchBarInputBorderRadiusTopRight'];
        $styleConfig->dialogSearchBarInputBorderWidthBottom = (int) $postData['dialogSearchBarInputBorderWidthBottom'];
        $styleConfig->dialogSearchBarInputBorderWidthLeft = (int) $postData['dialogSearchBarInputBorderWidthLeft'];
        $styleConfig->dialogSearchBarInputBorderWidthRight = (int) $postData['dialogSearchBarInputBorderWidthRight'];
        $styleConfig->dialogSearchBarInputBorderWidthTop = (int) $postData['dialogSearchBarInputBorderWidthTop'];
        $styleConfig->dialogSearchBarInputTextColor = (string) $postData['dialogSearchBarInputTextColor'];
        // Switch Button
        $styleConfig->dialogSwitchButtonBackgroundColorActive
            = (string) $postData['dialogSwitchButtonBackgroundColorActive'];
        $styleConfig->dialogSwitchButtonBackgroundColorInactive
            = (string) $postData['dialogSwitchButtonBackgroundColorInactive'];
        $styleConfig->dialogSwitchButtonColorActive = (string) $postData['dialogSwitchButtonColorActive'];
        $styleConfig->dialogSwitchButtonColorInactive = (string) $postData['dialogSwitchButtonColorInactive'];
        // Tab Bar
        $styleConfig->dialogTabBarTabBackgroundColorActive = (string) $postData['dialogTabBarTabBackgroundColorActive'];
        $styleConfig->dialogTabBarTabBackgroundColorInactive = (string) $postData['dialogTabBarTabBackgroundColorInactive'];
        $styleConfig->dialogTabBarTabBorderColorBottomActive = (string) $postData['dialogTabBarTabBorderColorBottomActive'];
        $styleConfig->dialogTabBarTabBorderColorBottomInactive = (string) $postData['dialogTabBarTabBorderColorBottomInactive'];
        $styleConfig->dialogTabBarTabBorderColorLeftActive = (string) $postData['dialogTabBarTabBorderColorLeftActive'];
        $styleConfig->dialogTabBarTabBorderColorLeftInactive = (string) $postData['dialogTabBarTabBorderColorLeftInactive'];
        $styleConfig->dialogTabBarTabBorderColorRightActive = (string) $postData['dialogTabBarTabBorderColorRightActive'];
        $styleConfig->dialogTabBarTabBorderColorRightInactive = (string) $postData['dialogTabBarTabBorderColorRightInactive'];
        $styleConfig->dialogTabBarTabBorderColorTopActive = (string) $postData['dialogTabBarTabBorderColorTopActive'];
        $styleConfig->dialogTabBarTabBorderColorTopInactive = (string) $postData['dialogTabBarTabBorderColorTopInactive'];
        $styleConfig->dialogTabBarTabBorderRadiusBottomLeftActive = (int) $postData['dialogTabBarTabBorderRadiusBottomLeftActive'];
        $styleConfig->dialogTabBarTabBorderRadiusBottomRightActive = (int) $postData['dialogTabBarTabBorderRadiusBottomRightActive'];
        $styleConfig->dialogTabBarTabBorderRadiusTopLeftActive = (int) $postData['dialogTabBarTabBorderRadiusTopLeftActive'];
        $styleConfig->dialogTabBarTabBorderRadiusTopRightActive = (int) $postData['dialogTabBarTabBorderRadiusTopRightActive'];
        $styleConfig->dialogTabBarTabBorderRadiusBottomLeftInactive = (int) $postData['dialogTabBarTabBorderRadiusBottomLeftInactive'];
        $styleConfig->dialogTabBarTabBorderRadiusBottomRightInactive = (int) $postData['dialogTabBarTabBorderRadiusBottomRightInactive'];
        $styleConfig->dialogTabBarTabBorderRadiusTopLeftInactive = (int) $postData['dialogTabBarTabBorderRadiusTopLeftInactive'];
        $styleConfig->dialogTabBarTabBorderRadiusTopRightInactive = (int) $postData['dialogTabBarTabBorderRadiusTopRightInactive'];
        $styleConfig->dialogTabBarTabBorderWidthTopActive = (int) $postData['dialogTabBarTabBorderWidthTopActive'];
        $styleConfig->dialogTabBarTabBorderWidthRightActive = (int) $postData['dialogTabBarTabBorderWidthRightActive'];
        $styleConfig->dialogTabBarTabBorderWidthBottomActive = (int) $postData['dialogTabBarTabBorderWidthBottomActive'];
        $styleConfig->dialogTabBarTabBorderWidthLeftActive = (int) $postData['dialogTabBarTabBorderWidthLeftActive'];
        $styleConfig->dialogTabBarTabBorderWidthTopInactive = (int) $postData['dialogTabBarTabBorderWidthTopInactive'];
        $styleConfig->dialogTabBarTabBorderWidthRightInactive = (int) $postData['dialogTabBarTabBorderWidthRightInactive'];
        $styleConfig->dialogTabBarTabBorderWidthBottomInactive = (int) $postData['dialogTabBarTabBorderWidthBottomInactive'];
        $styleConfig->dialogTabBarTabBorderWidthLeftInactive = (int) $postData['dialogTabBarTabBorderWidthLeftInactive'];
        $styleConfig->dialogTabBarTabTextColorActive = (string) $postData['dialogTabBarTabTextColorActive'];
        $styleConfig->dialogTabBarTabTextColorInactive = (string) $postData['dialogTabBarTabTextColorInactive'];
        // Table
        $styleConfig->dialogTableBorderRadiusBottomLeft = (int) $postData['dialogTableBorderRadiusBottomLeft'];
        $styleConfig->dialogTableBorderRadiusBottomRight = (int) $postData['dialogTableBorderRadiusBottomRight'];
        $styleConfig->dialogTableBorderRadiusTopLeft = (int) $postData['dialogTableBorderRadiusTopLeft'];
        $styleConfig->dialogTableBorderRadiusTopRight = (int) $postData['dialogTableBorderRadiusTopRight'];
        $styleConfig->dialogTableCellPaddingBottom = (int) $postData['dialogTableCellPaddingBottom'];
        $styleConfig->dialogTableCellPaddingLeft = (int) $postData['dialogTableCellPaddingLeft'];
        $styleConfig->dialogTableCellPaddingRight = (int) $postData['dialogTableCellPaddingRight'];
        $styleConfig->dialogTableCellPaddingTop = (int) $postData['dialogTableCellPaddingTop'];
        $styleConfig->dialogTableRowBackgroundColorEven = (string) $postData['dialogTableRowBackgroundColorEven'];
        $styleConfig->dialogTableRowBackgroundColorOdd = (string) $postData['dialogTableRowBackgroundColorOdd'];
        $styleConfig->dialogTableRowBorderColor = (string) $postData['dialogTableRowBorderColor'];
        $styleConfig->dialogTableRowTextColorEven = (string) $postData['dialogTableRowTextColorEven'];
        $styleConfig->dialogTableRowTextColorOdd = (string) $postData['dialogTableRowTextColorOdd'];
        // Custom CSS
        $styleConfig->customCss = (string) $postData['customCss'];

        // Save config
        $this->dialogStyleConfig->save($styleConfig, $this->language->getSelectedLanguageCode());
        $this->styleBuilder->updateCssFileAndIncrementStyleVersion(
            $this->wpFunction->getCurrentBlogId(),
            $this->language->getSelectedLanguageCode(),
        );
        $this->thirdPartyCacheClearerManager->clearCache();

        $this->messageManager->success($this->globalLocalizationStrings::get()['alert']['savedSuccessfully']);

        return true;
    }

    public function viewOverview(): string
    {
        $templateData = [];
        $templateData['controllerId'] = self::CONTROLLER_ID;
        $templateData['localized'] = DialogAppearanceLocalizationStrings::get();
        $templateData['localized']['global'] = $this->globalLocalizationStrings::get();
        $templateData['data'] = $this->dialogStyleConfig->get();

        return $this->template->getEngine()->render(
            'dialog/dialog-appearance/dialog-appearance.html.twig',
            $templateData,
        );
    }
}
