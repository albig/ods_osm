<?php

/**
 * Extension Manager/Repository config file for ext "osm".
 */
$EM_CONF[$_EXTKEY] = [
    'title' => 'Osm',
    'description' => 'Playground to re-implement ods_osm with Extbase / Fluid',
    'category' => 'templates',
    'constraints' => [
        'depends' => [
            'typo3' => '12.4.0-13.4.99',
            'extbase' => '12.4.0-13.4.99',
            'fluid' => '12.4.0-13.4.99',
        ],
        'conflicts' => [
        ],
    ],
    'autoload' => [
        'psr-4' => [
            'Bobosch\\OdsOsm\\' => 'Classes/',
        ],
    ],
    'state' => 'stable',
    'uploadfolder' => 0,
    'createDirs' => '',
    'clearCacheOnLoad' => 1,
    'author' => 'Alexander Bigga',
    'author_email' => 'alexander@bigga.de',
    'author_company' => 'Bobosch',
    'version' => '1.0.0',
];
