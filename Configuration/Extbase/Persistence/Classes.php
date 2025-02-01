<?php

declare(strict_types=1);

use Bobosch\OdsOsm\Domain\Model\Layer;
use Bobosch\OdsOsm\Domain\Model\FrontendUser;

return [
    Layer::class => [
        'tableName' => 'tx_odsosm_layer',
    ],
    FrontendUser::class => [
        'tableName' => 'fe_users',
    ],
];
