<?php

declare(strict_types=1);

namespace Bobosch\OdsOsm\Service;

/*
 * This file is part of the "tt_address" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */
use Bobosch\OdsOsm\Traits\SettingsTrait;
use TYPO3\CMS\Core\Cache\CacheManager;
use TYPO3\CMS\Core\Cache\Exception\NoSuchCacheException;
use TYPO3\CMS\Core\Cache\Frontend\FrontendInterface;
use TYPO3\CMS\Core\Http\RequestFactory;
use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Service for category related stuff
 *
 * thanks to https://github.com/b13/t3ext-geocoding for inspiration
 */
class GeocodeService implements SingletonInterface
{
    use SettingsTrait;

    /** @var int */
    protected $cacheTime = 7776000;

    /** @var array */
    protected $config = [];

    /**
     * geocodes all missing records in a DB table and then stores the values
     * in the DB record.
     *
     * only works if your DB table has the necessary fields
     * helpful when calculating a batch of addresses and save the latitude/longitude automatically
     *
     * @param array $row
     * @param array $tc
     * @return array an array with latitude and longitude
     */
    public function calculateCoordinatesForAddress($row, $tc): array
    {
        $this->config = $this->getSettings();

        // do the geocoding
        $coords = $this->getCoordinatesForAddress($row, $tc);

        $coords['lon'] = $coords['geometry']['coordinates'][0];
        $coords['lat'] = $coords['geometry']['coordinates'][1];
        $coords['address'] = $coords['properties']['geocoding'];

        return $coords;
    }

    /**
     * core functionality: asks nominatim for the coordinates of an address
     * stores known addresses in a local cache.
     *
     * @param array $address
     * @param array $tc
     * @return array an array with latitude and longitude
     */
    private function getCoordinatesForAddress($address, $tc): array
    {
        $geoCodeUrl = '';

        $country = strtoupper(strlen($address['country'] ?? false) == 2 ? $address['country'] : $this->config['default_country']);
        $email = GeneralUtility::validEmail($this->config['geo_service_email']) ? $this->config['geo_service_email'] : ($_SERVER['SERVER_ADMIN'] ?? 'unkown@example.com');

        $query['country'] = $country;
        $query['email'] = $email;
        $query['addressdetails'] = 1;
        $query['format'] = 'geocodejson';

        // remove all after first slash in address (top, floor ...)
        $address_combined = preg_replace('/^([^\/]*).*$/', '$1', $address['address'] ?? '') . ' ';
        $address_combined .= $address['city'] ?? '';

        // if we have at least some address part (saves geocoding calls)
        if ($address['address'] && $tc['type'] == 'structured') {
            if ($address['city'] ?? false) {
                $query['city'] = $address['city'];
            }
            if ($address['zip'] ?? false) {
                $query['postalcode'] = $address['zip'];
            }
            $query['street'] = $address['address'];
        } else {

            $query['q'] = $address_combined;
        }

        $cacheObject = $this->initializeCache();
        $cacheKey = 'geocode-' . strtolower(str_replace(' ', '-', preg_replace('/[^0-9a-zA-Z ]/m', '', $address_combined)));
        // Found in cache? Return it.
        if ($cacheObject->has($cacheKey)) {
            return $cacheObject->get($cacheKey);
        }
        $result = $this->getApiCallResult($query);

        if (empty($result)) {
            return [];
        }
        // Now store the $result in cache and return
        $cacheObject->set($cacheKey, $result, [], $this->cacheTime);

        return $result;
    }

    protected function getApiCallResult(array $query): array
    {
        $result = [];

        /** @var RequestFactory $requestFactory */
        $requestFactory = GeneralUtility::makeInstance(RequestFactory::class);
        $configuration = [
            'timeout' => 60,
            'headers' => [
                'Accept' => 'application/json',
                'User-Agent' => 'TYPO3 extension ods_osm/' . ExtensionManagementUtility::getExtensionVersion('ods_osm'),
            ],
        ];

        $response = $requestFactory->request('https://nominatim.openstreetmap.org/search?' . http_build_query($query, '', '&'), 'GET', $configuration);
        $content  = $response->getBody()->getContents();
        $result = json_decode($content, true);

        if (is_array($result)) {
            return $result['features'][0];
        }

        return [];
    }

    /**
     * Search for the given address in Nominatim service.
     *
     * Data lat, lon, zip and city may get updated.
     *
     * @param array $query The query sent to the nominatim API
     * @param array &$address Address record from database
     *
     * @return bool True if the address was found and got updated.
     */
    protected static function searchAddressNominatim($query, &$address)
    {
        $ll = false;

        /** @var RequestFactory $requestFactory */
        $requestFactory = GeneralUtility::makeInstance(RequestFactory::class);
        $configuration = [
            'timeout' => 60,
            'headers' => [
                'Accept' => 'application/json',
                'User-Agent' => 'TYPO3 extension ods_osm/' . ExtensionManagementUtility::getExtensionVersion('ods_osm'),
            ],
        ];

        $response = $requestFactory->request('https://nominatim.openstreetmap.org/search?' . http_build_query($query, '', '&'), 'GET', $configuration);
        $content  = $response->getBody()->getContents();
        $result = json_decode($content, true);

        // Save value in cache
        if ($result) {
            // take the first result
            if ($result[0] ?? false) {
                $ll = true;
                $address['lat'] = (string)$result[0]['lat'];
                $address['lon'] = (string)$result[0]['lon'];
                if ($result[0]['address']['road'] ?? false) {
                    $address['street'] = (string)$result[0]['address']['road'];
                }
                if ($result[0]['address']['house_number'] ?? false) {
                    $address['housenumber'] = substr((string)$result[0]['address']['house_number'], 0, 10);
                }
                if ($result[0]['address']['postcode'] ?? false) {
                    $address['zip'] = substr((string)$result[0]['address']['postcode'], 0, 10);
                }
                if ($result[0]['address']['city'] ?? false) {
                    $address['city'] = $result[0]['address']['city'];
                } elseif ($result[0]['address']['village'] ?? false) {
                    $address['city'] = (string)$result[0]['address']['village'];
                }
                if ($result[0]['address']['state'] ?? false) {
                    $address['state'] = (string)$result[0]['address']['state'];
                }
                if (($result[0]['address']['country_code'] ?? false) && empty($address['country'] ?? false)) {
                    $address['country'] = strtoupper((string)$result[0]['address']['country_code']);
                }
            }
        }

        return $ll;
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
