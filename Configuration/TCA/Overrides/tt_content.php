<?php

use TYPO3\CMS\Extbase\Utility\ExtensionUtility;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

defined('TYPO3') || die();

call_user_func(
    static function (): void {
        $pluginSignature = ExtensionUtility::registerPlugin(
            'Osm',
            'OsmShow',
            'LLL:EXT:map/Resources/Private/Language/locallang.xlf:plugin.tea_show',
            'EXT:map/Resources/Public/Icons/Extension.svg'
        );

        // This removes the default controls from the plugin.
        $controlsToRemove = 'recursive,pages';
        $GLOBALS['TCA']['tt_content']['types']['list']['subtypes_excludelist'][$pluginSignature] = $controlsToRemove;
        $GLOBALS['TCA']['tt_content']['types']['list']['subtypes_addlist'][$pluginSignature]     = 'pi_flexform';

        ExtensionManagementUtility::addPiFlexFormValue(
            $pluginSignature,
            'FILE:EXT:osm/Configuration/Flexform/flexform_basic.xml'
        );
        
    }
);

