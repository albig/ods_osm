<?php

defined('TYPO3') or die;

return [
    'dependencies' => [
        'backend',
    ],
    'imports' => [
        '@bobosch/ods-osm/' => 'EXT:ods_osm/Resources/Public/JavaScript/esm/',
    ],
];
