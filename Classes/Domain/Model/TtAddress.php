<?php

declare(strict_types=1);

/*
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */

namespace Bobosch\OdsOsm\Domain\Model;

use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;

use Bobosch\OdsOsm\Domain\Model\Category;

/**
 * The domain model of sys_category.
 *
 * @entity
 */
class TtAddress extends \FriendsOfTYPO3\TtAddress\Domain\Model\Address
{
    /** @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<Category> */
    protected $categories;

    public function __construct()
    {
       $this->categories = new ObjectStorage();
    }

}
