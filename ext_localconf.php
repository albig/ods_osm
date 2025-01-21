<?php

declare(strict_types=1);

use Bobosch\OdsOsm\Controller\MapController;
use Bobosch\OdsOsm\Evaluation\LonLat;
use Bobosch\OdsOsm\TceMain;
use Bobosch\OdsOsm\Wizard\CoordinatepickerWizard;
use TYPO3\CMS\Extbase\Utility\ExtensionUtility;

defined('TYPO3') or die('Access denied.');

// This makes the plugin available for front-end rendering.
ExtensionUtility::configurePlugin(
    'OdsOsm',
    'OsmShow',
    [
        MapController::class => 'show, leaflet, openlayers',
    ],
    [
        MapController::class => '',
    ]
);

// Add wizard with map for setting geo location
$GLOBALS['TYPO3_CONF_VARS']['SYS']['formEngine']['nodeRegistry'][1616876515] = [
    'nodeName' => 'coordinatepickerWizard',
    'priority' => 30,
    'class' => CoordinatepickerWizard::class
];

// Register hooks
$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['processDatamapClass'][] = TceMain::class;

// Register evaluations for TCA
$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['tce']['formevals'][LonLat::class] = '';

/* ===========================================================================
  Custom cache, done with the caching framework
=========================================================================== */
if (!isset($GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations']['odsosm_geocoding'])) {
    $GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations']['odsosm_geocoding'] = [
        'frontend' => \TYPO3\CMS\Core\Cache\Frontend\VariableFrontend::class,
        'backend' => \TYPO3\CMS\Core\Cache\Backend\Typo3DatabaseBackend::class,
    ];
}
