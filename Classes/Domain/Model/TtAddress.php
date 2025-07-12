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
// use TYPO3\CMS\Extbase\Domain\Model\Category;

/**
 * The domain model of tt_address.
 *
 * @entity
 */
class TtAddress extends \FriendsOfTYPO3\TtAddress\Domain\Model\Address
{
    /**
     * @var ObjectStorage<Category>
     */
    protected $categories;

    public function getCategories(): ObjectStorage
    {
        return $this->categories;
    }

    public function setCategories(ObjectStorage $categories): void
    {
        $this->categories = $categories;
    }

}
