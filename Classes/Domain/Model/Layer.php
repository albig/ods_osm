<?php

declare(strict_types=1);

namespace Bobosch\OdsOsm\Domain\Model;

use TYPO3\CMS\Extbase\Annotation as Extbase;
use TYPO3\CMS\Extbase\Domain\Model\FileReference;
use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;
use TYPO3\CMS\Extbase\Persistence\Generic\LazyLoadingProxy;

/**
 * This class represents a osm layer 
 */
class Layer extends AbstractEntity
{
    /** @var string */
    protected $title = '';

    /** @var string */
    protected $tileUrl = '';

    /** @var string */
    protected $subdomains = '';

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    public function getTileUrl(): string
    {
        return $this->tileUrl;
    }

    public function setTileUrl(string $tileUrl): void
    {
        $this->tileUrl = $tileUrl;
    }

    public function getSubdomains(): string
    {
        return $this->subdomains;
    }

    public function setSubdomains(string $subdomains): void
    {
        $this->subdomains = $subdomains;
    }

}