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

namespace Borlabs\Cookie\Localization\IabTcf;

use Borlabs\Cookie\Localization\LocalizationInterface;

use function Borlabs\Cookie\System\WordPressGlobalFunctions\_x;

final class IabTcfConfigureVendorsLocalizationStrings implements LocalizationInterface
{
    /**
     * @return array<array<string>>
     */
    public static function get(): array
    {
        return [
            // Alert messages
            'alert' => [
                'globalVendorListNotDownloaded' => _x(
                    'The <translation-key id="Global-Vendor-List">Global Vendor List</translation-key> is not be downloaded. Please go to the IAB TCF settings and download the list.',
                    'Backend / IAB TCF Configure Vendors / Alert Message',
                    'borlabs-cookie',
                ),
                'vendorsPreviewListEmpty' => _x(
                    'Currently, the <translation-key id="Vendors-Preview">Vendors Preview</translation-key> list is empty. This may be because the <translation-key id="Update-Preview">Update Preview</translation-key> button has not been clicked to refresh the list, or because no consent parameters such as <translation-key id="Purpose">Purpose</translation-key>, <translation-key id="Feature">Feature</translation-key>, <translation-key id="Special-Feature">Special Feature</translation-key>, or <translation-key id="Special-Purpose">Special Purpose</translation-key> have been selected in the have been selected in the <translation-key id="Configure-Vendors">Configure Vendors</translation-key> panel. Please ensure that you\'ve made your selections and clicked <translation-key id="Update-Preview">Update Preview</translation-key> to see them in the <translation-key id="Vendors-Preview">Vendors Preview</translation-key> list.',
                    'Backend / IAB TCF Configure Vendors / Alert Message',
                    'borlabs-cookie',
                ),
            ],

            // Breadcrumbs
            'breadcrumb' => [
                'configure' => _x(
                    'Configure Vendors',
                    'Backend / IAB TCF Configure Vendors / Breadcrumb',
                    'borlabs-cookie',
                ),
                'module' => _x(
                    'IAB TCF',
                    'Backend / IAB TCF Configure Vendors / Breadcrumb',
                    'borlabs-cookie',
                ),
            ],

            // Buttons
            'button' => [
                'configureVendors' => _x(
                    'Configure Vendors',
                    'Backend / IAB TCF Configure Vendors / Button',
                    'borlabs-cookie',
                ),
                'updatePreview' => _x(
                    'Update Preview',
                    'Backend / IAB TCF Configure Vendors / Button',
                    'borlabs-cookie',
                ),
            ],

            // Fields
            'field' => [
                'features' => _x(
                    'Features',
                    'Backend / IAB TCF Configure Vendors / Field',
                    'borlabs-cookie',
                ),
                'manualVendorIds' => _x(
                    'Vendor IDs',
                    'Backend / IAB TCF Configure Vendors / Field',
                    'borlabs-cookie',
                ),
                'purposes' => _x(
                    'Purposes',
                    'Backend / IAB TCF Configure Vendors / Field',
                    'borlabs-cookie',
                ),
                'replaceVendorConfiguration' => _x(
                    'Replace Vendor Configuration',
                    'Backend / IAB TCF Configure Vendors / Field',
                    'borlabs-cookie',
                ),
                'specialFeatures' => _x(
                    'Special Features',
                    'Backend / IAB TCF Configure Vendors / Field',
                    'borlabs-cookie',
                ),
                'specialPurposes' => _x(
                    'Special Purposes',
                    'Backend / IAB TCF Configure Vendors / Field',
                    'borlabs-cookie',
                ),
            ],

            // Headlines
            'headline' => [
                'configureVendors' => _x(
                    'Configure Vendors',
                    'Backend / IAB TCF Configure Vendors / Headline',
                    'borlabs-cookie',
                ),
                'vendorsPreview' => _x(
                    'Vendors Preview',
                    'Backend / IAB TCF Configure Vendors / Headline',
                    'borlabs-cookie',
                ),
            ],

            // Hint
            'hint' => [
                'manualVendorIds' => _x(
                    'Alternatively, or in addition, you have the option to input the <translation-key id="IDs">IDs</translation-key> of the <translation-key id="Vendors">Vendors</translation-key> you wish to activate. Enter the <translation-key id="Vendor-IDs">Vendor IDs</translation-key> separated by comma.',
                    'Backend / IAB TCF Configure Vendors / Hint',
                    'borlabs-cookie',
                ),
                'replaceVendorConfiguration' => _x(
                    'If you enable this option, all existing <translation-key id="Vendors">Vendors</translation-key> will be disabled and only <translation-key id="Vendors">Vendors</translation-key> of this configuration will be enabled. If you leave the option disabled, the <translation-key id="Vendors">Vendors</translation-key> of this configuration will be enabled additionally.',
                    'Backend / IAB TCF Configure Vendors / Hint',
                    'borlabs-cookie',
                ),
            ],

            // Placeholder
            'placeholder' => [
            ],

            // Tables
            'table' => [
            ],

            // Text
            'text' => [
            ],

            // Things to know
            'thingsToKnow' => [
                'feature' => _x(
                    '<translation-key id="Feature">Feature</translation-key> means one of the features of processing personal data used by participants in the Framework that are defined in the Policies or the Specifications used in pursuit of one or several <translation-key id="Purposes">Purposes</translation-key> for which the user is not given choice separately to the choice afforded regarding the <translation-key id="Purposes">Purposes</translation-key> for which they are used.',
                    'Backend / IAB TCF Configure Vendors / Tip',
                    'borlabs-cookie',
                ),
                'headlineFeature' => _x(
                    'Feature',
                    'Backend / IAB TCF Configure Vendors / Tip',
                    'borlabs-cookie',
                ),
                'headlinePurpose' => _x(
                    'Purpose',
                    'Backend / IAB TCF Configure Vendors / Tip',
                    'borlabs-cookie',
                ),
                'headlineSpecialFeature' => _x(
                    'Special Feature',
                    'Backend / IAB TCF Configure Vendors / Tip',
                    'borlabs-cookie',
                ),
                'headlineSpecialPurpose' => _x(
                    'Special Purpose',
                    'Backend / IAB TCF Configure Vendors / Tip',
                    'borlabs-cookie',
                ),
                'purpose' => _x(
                    '<translation-key id="Purpose">Purpose</translation-key> means one of the defined purposes for processing of data, including users’ personal data, by participants in the Framework that are defined in the Policies or the Specifications for which <translation-key id="Vendors">Vendors</translation-key> declare a Legal Basis in the GVL and for which the user is given choice, i.e. to consent or to object depending on the Legal Basis for the processing, by a CMP.',
                    'Backend / IAB TCF Configure Vendors / Tip',
                    'borlabs-cookie',
                ),
                'specialFeature' => _x(
                    '<translation-key id="Special-Feature">Special Feature</translation-key> means one of the features of processing personal data used by participants in the Framework that are defined in the Policies or the Specifications used in pursuit of one or several <translation-key id="Purposes">Purposes</translation-key> for which the user is given the choice to opt-in separately from the choice afforded regarding the <translation-key id="Purposes">Purposes</translation-key> which they support.',
                    'Backend / IAB TCF Configure Vendors / Tip',
                    'borlabs-cookie',
                ),
                'specialPurpose' => _x(
                    '<translation-key id="Special-Purpose">Special Purpose</translation-key> means one of the defined purposes for processing of data, including users’ personal data, by participants in the Framework that are defined in the Policies or the Specifications for which <translation-key id="Vendors">Vendors</translation-key> declare a Legal Basis in the GVL and for which the user is not given choice by a CMP.',
                    'Backend / IAB TCF Configure Vendors / Tip',
                    'borlabs-cookie',
                ),
            ],
        ];
    }
}
