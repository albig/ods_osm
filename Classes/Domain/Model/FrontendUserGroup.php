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

use Bobosch\OdsOsm\Domain\Model\Marker;

/**
 * The domain model of fe_groups.
 *
 * @entity
 */
class FrontendUserGroup extends AbstractEntity
{
    /** @var string */
    protected $title = '';

    /** @var Marker */
    protected $txOdsosmMarker;

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    /**
     * @param Marker $txOdsosmMarker
     */
    public function setTxOdsosmMarker(Marker $txOdsosmMarker): void
    {
        $this->txOdsosmMarker = $txOdsosmMarker;
    }

    /**
     * @return Marker
     */
    public function getTxOdsosmMarker(): Marker
    {
        return $this->txOdsosmMarker;
    }

}
