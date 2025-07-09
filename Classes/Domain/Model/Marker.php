<?php

declare(strict_types=1);

namespace Bobosch\OdsOsm\Domain\Model;

use TYPO3\CMS\Extbase\Domain\Model\FileReference;
use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;

/**
 * This class represents a osm marker
 */
class Marker extends AbstractEntity
{
    /** @var string */
    protected $title = '';

    /** @var \TYPO3\CMS\Extbase\Domain\Model\FileReference|null */
    protected $icon = null;

    /**
     * @return \TYPO3\CMS\Extbase\Domain\Model\FileReference|null
     */
    public function getIcon(): ?FileReference
    {
        return $this->icon;
    }

    /**
     * @param \TYPO3\CMS\Extbase\Domain\Model\FileReference $icon
     */
    public function setIcon(FileReference $icon): void
    {
        $this->icon = $icon;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

}
