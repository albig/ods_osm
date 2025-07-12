<?php

declare(strict_types=1);

use Bobosch\OdsOsm\Domain\Model\FrontendUserGroup;
use Bobosch\OdsOsm\Domain\Model\FrontendUser;
use Bobosch\OdsOsm\Domain\Model\Category;
use Bobosch\OdsOsm\Domain\Model\Layer;
use Bobosch\OdsOsm\Domain\Model\Marker;
use Bobosch\OdsOsm\Domain\Model\TtAddress;

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
    FrontendUserGroup::class => [
        'tableName' => 'fe_groups',
    ],
    Category::class => [
        'tableName' => 'sys_category',
    ],
    \TYPO3\CMS\Extbase\Domain\Model\Category::class => [
        'className' => Category::class,
    ],
    TtAddress::class => [
        'tableName' => 'tt_address',
    ],
    \FriendsOfTYPO3\TtAddress\Domain\Model\Address::class => [
        'className' => TtAddress::class,
    ]
];
