<?php

declare(strict_types=1);

use Bobosch\Osm\Controller\MapController;
use TYPO3\CMS\Extbase\Utility\ExtensionUtility;

defined('TYPO3') or die('Access denied.');

// This makes the plugin available for front-end rendering.
ExtensionUtility::configurePlugin(
    'Osm',
    'OsmShow',
    [
        MapController::class => 'show, leaflet, openlayers',
    ],
    [
        MapController::class => '',
    ]
);