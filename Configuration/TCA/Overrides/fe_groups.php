<?php

use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

defined('TYPO3') || die();

call_user_func(
    static function (): void {
        $tempColumns = [
            'tx_odsosm_marker' => [
                'exclude' => 1,
                'label' => 'LLL:EXT:ods_osm/Resources/Private/Language/locallang_db.xlf:tt_address_group.tx_odsosm_marker',
                'config' => [
                    'type' => 'group',
                    'allowed' => 'tx_odsosm_marker',
                    'size' => 1,
                    'minitems' => 0,
                    'maxitems' => 1,
                    'default' => 0,
                ],
            ],
        ];

        ExtensionManagementUtility::addTCAcolumns('fe_groups', $tempColumns);
        ExtensionManagementUtility::addToAllTCAtypes('fe_groups', 'tx_odsosm_marker');
    }
);
