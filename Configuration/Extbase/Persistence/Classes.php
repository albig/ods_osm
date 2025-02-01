<?php

declare(strict_types=1);

use Bobosch\OdsOsm\Domain\Model\Layer;
use Bobosch\OdsOsm\Domain\Model\Marker;
use Bobosch\OdsOsm\Domain\Model\FrontendUser;
use Bobosch\OdsOsm\Domain\Model\FrontendGroup;

return [
    Layer::class => [
        'tableName' => 'tx_odsosm_layer',
    ],
    Marker::class => [
        'tableName' => 'tx_odsosm_marker',
    ],
    FrontendUser::class => [
        'tableName' => 'fe_users',
    ],
    FrontendGroup::class => [
        'tableName' => 'fe_groups',
    ],
];
