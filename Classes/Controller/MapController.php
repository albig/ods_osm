<?php

declare(strict_types=1);

namespace Bobosch\OdsOsm\Controller;

use Bobosch\OdsOsm\Domain\Model\Map;

use Bobosch\OdsOsm\Domain\Repository\FrontendGroupRepository;
use Bobosch\OdsOsm\Domain\Repository\FrontendUserRepository;
use Bobosch\OdsOsm\Domain\Repository\LayerRepository;
use Bobosch\OdsOsm\Traits\SettingsTrait;
use FriendsOfTYPO3\TtAddress\Domain\Repository\AddressRepository;

use Psr\Http\Message\ResponseInterface;

use TYPO3\CMS\Core\Authentication\GroupResolver;
use TYPO3\CMS\Core\Utility\ArrayUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Http\ForwardResponse;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;

/**
 * Controller for the main "Map" FE plugin.
 */
class MapController extends ActionController
{
    use SettingsTrait;

    /** @var LayerRepository */
    protected $layerRepository;

    /** @var AddressRepository */
    protected $addressRepository;

    /** @var FrontendUserRepository */
    protected $frontendUserRepository;

    /** @var FrontendGroupRepository */
    protected $frontendGroupRepository;

    /** @var array */
    protected $config = [];

    /**
     * @param LayerRepository $layerRepository
     */
    public function injectLayerRepository(LayerRepository $layerRepository): void
    {
        $this->layerRepository = $layerRepository;
    }

    /**
     * @param ?AddressRepository $addressRepository
     */
    public function injectAddressRepository(?AddressRepository $addressRepository): void
    {
        $this->addressRepository = $addressRepository;
    }

    /**
     * @param FrontendUserRepository $frontendUserRepository
     */
    public function injectFrontendUserRepository(FrontendUserRepository $frontendUserRepository): void
    {
        $this->frontendUserRepository = $frontendUserRepository;
    }

    /**
     * @param FrontendGroupRepository $frontendGroupRepository
     */
    public function injectFrontendGroupRepository(FrontendGroupRepository $frontendGroupRepository): void
    {
        $this->frontendGroupRepository = $frontendGroupRepository;
    }

    protected function initializeView(): void
    {
        // merge configs together into $this->config
        // 1. get extension configuration
        $this->config = $this->getSettings();
        // 2. get TypoScript settings
        ArrayUtility::mergeRecursiveWithOverrule($this->config, $this->settings['typoscript']);
        unset($this->settings['typoscript']);
        // 3. merge with Extbase settings, but skip empty values.
        ArrayUtility::mergeRecursiveWithOverrule($this->config, $this->settings, true, false);
    }

    public function showAction(): ResponseInterface
    {
        $cObjectData = $this->request->getAttribute('currentContentObject');
        $currentUid = $cObjectData->data['uid'];
        $markerToShow = [];
        foreach (GeneralUtility::trimExplode(',', $this->settings['marker']) as $tempGroup) {
            $item = GeneralUtility::revExplode('_', $tempGroup, 2);
            switch ($item[0]) {
                case 'tt_address':
                    $markerToShow['tt_address'][] = $this->addressRepository->findByUid((int)$item[1]);
                    break;
                case 'fe_users':
                    $markerToShow['fe_users'][] = $this->frontendUserRepository->findByUid((int)$item[1]);
                    break;
                case 'fe_groups':
                    $markerToShow['fe_groups'] = GeneralUtility::makeInstance(GroupResolver::class)->findAllUsersInGroups(GeneralUtility::intExplode(',', $item[1] ?: ''), 'fe_groups', 'fe_users');
                    break;
            }
        }

        /* Get the map center */
        if ($this->config['use_coords_only_nomarker'] ?? false) {
            $lons = [];
            $lats = [];
            foreach ($markerToShow as $table => $markers) {
                switch ($table) {
                    case 'tt_address':
                        foreach ($markers as $marker) {
                            $lons[] = $marker->getLongitude();
                            $lats[] = $marker->getLatitude();
                        }
                        break;
                    case 'fe_users':
                        foreach ($markers as $marker) {
                            $lons[] = $marker->getTxOdsosmLon();
                            $lats[] = $marker->getTxOdsosmLat();
                        }
                        break;
                }
            }

            $this->config['lon'] = array_sum($lons) / count($lons);
            $this->config['lat'] = array_sum($lats) / count($lats);
        } else {
            $this->config['lon'] = (float)($this->config['lon'] ?? $this->config['default_lon']);
            $this->config['lat'] = (float)($this->config['lat'] ?? $this->config['default_lat']);
        }

        switch ($this->settings['library'] ?? '') {
            case 'openlayers':
                return (new ForwardResponse('openlayers'))
                    ->withArguments(['currentUid' => $currentUid, 'config' => $this->config]);
            case 'leaflet':
                return (new ForwardResponse('leaflet'))
                    ->withArguments(['currentUid' => $currentUid, 'config' => $this->config, 'marker' => $markerToShow]);
            default:
                return $this->htmlResponse();
        }
    }

    public function openlayersAction(int $currentUid): ResponseInterface
    {
        $variables = [
            'config' => $this->config,
            'currentUid' => $currentUid,
        ];

        $this->view->assignMultiple($variables);

        return $this->htmlResponse();
    }

    public function leafletAction(int $currentUid, array $config, array $marker): ResponseInterface
    {
        $variables = [
            'config' => $config,
            'currentUid' => $currentUid,
            'marker' => $marker,
            'baseMaps' => $this->layerRepository->findAllByUids(explode(',', $this->settings['base_layer'] ?? [])),
            'overlayMaps' => $this->layerRepository->findAllByUids(explode(',', $this->settings['overlays'] ?? [])),
            'overlaysActive' => array_intersect($this->layerRepository->findAllByUids(explode(',', $this->settings['overlays_active'] ?? [])), $this->layerRepository->findAllByUids(explode(',', $this->settings['overlays'] ?? []))),
        ];

        $this->view->assignMultiple($variables);

        return $this->htmlResponse();
    }

}
