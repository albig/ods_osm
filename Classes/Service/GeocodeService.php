<?php

declare(strict_types=1);

namespace Bobosch\OdsOsm\Service;

use TYPO3\CMS\Core\Cache\CacheManager;

/*
 * This file is part of the "tt_address" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */
use TYPO3\CMS\Core\Cache\Exception\NoSuchCacheException;
use TYPO3\CMS\Core\Cache\Frontend\FrontendInterface;
use TYPO3\CMS\Core\Database\Connection;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Database\Query\QueryHelper;
use TYPO3\CMS\Core\Database\Query\Restriction\DeletedRestriction;
use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Service for category related stuff
 *
 * thanks to https://github.com/b13/t3ext-geocoding for inspiration
 */
class GeocodeService implements SingletonInterface
{
    /** @var int */
    protected $cacheTime = 7776000;

    /** @var string */
    protected $geoCodeUrlBase = 'https://nominatim.openstreetmap.org/search?q=';

    /** @var string */
    protected $geoCodeUrlQuery = '&format=jsonv2&addressdetails=1&limit=1&polygon_svg=1';

    /**
     * geocodes all missing records in a DB table and then stores the values
     * in the DB record.
     *
     * only works if your DB table has the necessary fields
     * helpful when calculating a batch of addresses and save the latitude/longitude automatically
     *
     * @param array $row
     */
    public function calculateCoordinatesForAddress($row): array
    {
        // remove all after first slash in address (top, floor ...)
        $address = preg_replace('/^([^\/]*).*$/', '$1', $row['address'] ?? '') . ' ';
        $address .= $row['city'] ?? '';

        // do the geocoding
        $coords = $this->getCoordinatesForAddress($address);

        return $coords;
    }

    /**
     * core functionality: asks nominatim for the coordinates of an address
     * stores known addresses in a local cache.
     *
     * @param string $address
     * @return array an array with latitude and longitude
     */
    public function getCoordinatesForAddress($address = null): array
    {
        $geoCodeUrl = '';

        // if we have at least some address part (saves geocoding calls)
        if (trim($address)) {
            // base url
            $geoCodeUrlAddress = $address;
            // replace newlines with spaces; remove multiple spaces
            $geoCodeUrl = trim(preg_replace('/\s\s+/', ' ', $this->geoCodeUrlBase . urlencode($geoCodeUrlAddress) . $this->geoCodeUrlQuery));
        }


        $cacheObject = $this->initializeCache();
        $cacheKey = 'geocode-' . strtolower(str_replace(' ', '-', preg_replace('/[^0-9a-zA-Z ]/m', '', $address)));
        // Found in cache? Return it.
        if ($cacheObject->has($cacheKey)) {
            return $cacheObject->get($cacheKey);
        }
        $result = $this->getApiCallResult($geoCodeUrl);

        if (empty($result)) {
            return [];
        }
        // Now store the $result in cache and return
        $cacheObject->set($cacheKey, $result, [], $this->cacheTime);

        return $result;
    }

    protected function getApiCallResult(string $url): array
    {
        $result = [];
        $response = GeneralUtility::getUrl($url);

        if ($response) {
            $result = json_decode($response, true);
            if (is_array($result)) {
                return $result[0];
            }
        }

        return [];
    }

    /**
     * Initializes the cache for the DB requests.
     *
     * @return FrontendInterface Cache Object
     */
    protected function initializeCache(string $name = 'odsosm_geocoding'): FrontendInterface
    {
        try {
            $cacheManager = GeneralUtility::makeInstance(CacheManager::class);
            return $cacheManager->getCache($name);
        } catch (NoSuchCacheException $e) {
            throw new \RuntimeException('Unable to load Cache!', 1548785854);
        }
    }
}
