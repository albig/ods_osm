<?php

namespace Bobosch\OdsOsm\Traits;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;

trait SettingsTrait
{
    protected function getSettings(): array
    {
        try {
            return GeneralUtility::makeInstance(ExtensionConfiguration::class)->get('ods_osm');
        } catch (\Exception $e) {
            return [];
        }
    }
}
