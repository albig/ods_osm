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

/**
 * The domain model of fe_users.
 *
 * @entity
 */
class FrontendUser extends AbstractEntity
{
    /** @var string */
    protected $firstName = '';

    /** @var string */
    protected $lastName = '';

    /** @var string */
    protected $address = '';

    /** @var string */
    protected $zip = '';

    /** @var string */
    protected $city = '';

    /** @var float */
    protected $txOdsosmLon = '';

    /** @var float */
    protected $txOdsosmLat = '';

    public function getLastName(): string
    {
        return $this->lastName;
    }

    public function getFirstName(): string
    {
        return $this->firstName;
    }

    public function getAddress(): string
    {
        return $this->address;
    }

    public function getZip(): string
    {
        return $this->zip;
    }

    public function getCity(): string
    {
        return $this->city;
    }

    public function getTxOdsosmLon(): float
    {
        return $this->txOdsosmLon;
    }

    public function getTxOdsosmLat(): float
    {
        return $this->txOdsosmLat;
    }


}
