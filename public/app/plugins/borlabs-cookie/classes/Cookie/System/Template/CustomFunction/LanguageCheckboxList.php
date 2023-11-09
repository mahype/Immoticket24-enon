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

namespace Borlabs\Cookie\System\Template\CustomFunction;

use Borlabs\Cookie\Dto\System\KeyValueDto;
use Borlabs\Cookie\DtoList\System\KeyValueDtoList;
use Borlabs\Cookie\Localization\GlobalLocalizationStrings;
use Borlabs\Cookie\System\Language\Language;
use Borlabs\Cookie\System\Template\Template;
use Twig\TwigFunction;

final class LanguageCheckboxList
{
    private GlobalLocalizationStrings $globalLocalizationStrings;

    private Language $language;

    private Template $template;

    public function __construct(GlobalLocalizationStrings $globalLocalizationStrings, Language $language, Template $template)
    {
        $this->globalLocalizationStrings = $globalLocalizationStrings;
        $this->language = $language;
        $this->template = $template;
    }

    public function register()
    {
        $this->template->getTwig()->addFunction(
            new TwigFunction('languageCheckboxList', function (KeyValueDtoList $languageList, ?string $collectionName = null) {
                $languageListWithoutActiveLanguage = new KeyValueDtoList();

                foreach ($languageList->list as $language) {
                    if ($language->key !== $this->language->getSelectedLanguageCode()) {
                        $languageListWithoutActiveLanguage->add(new KeyValueDto($language->key, $language->value));
                    }
                }

                $languageList = $languageListWithoutActiveLanguage;

                return $this->template->getEngine()->render(
                    'system/checkbox-list.html.twig',
                    [
                        'collectionName' => $collectionName,
                        'checkboxes' => $languageList->list, // TODO rename
                        'checkedKeys' => array_column($languageList->list, 'key', 'key'), // TODO rename
                        'hint' => $this->globalLocalizationStrings::get()['hint']['languages'],
                        'id' => 'languages',
                        'name' => 'languages',
                        'title' => $this->globalLocalizationStrings::get()['field']['languages'],
                    ],
                );
            }),
        );
    }
}
