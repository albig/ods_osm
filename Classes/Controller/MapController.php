<?php

declare(strict_types=1);

namespace Bobosch\OdsOsm\Controller;

use Psr\Http\Message\ResponseInterface;

use Bobosch\OdsOsm\Domain\Model\Map;
use Bobosch\OdsOsm\Domain\Repository\LayerRepository;

use FriendsOfTYPO3\TtAddress\Domain\Model\Address;
use FriendsOfTYPO3\TtAddress\Domain\Repository\AddressRepository;

use Bobosch\OdsOsm\Domain\Model\FrontendUser;
use Bobosch\OdsOsm\Domain\Repository\FrontendUserRepository;

use Bobosch\OdsOsm\Domain\Model\FrontendGroup;
use Bobosch\OdsOsm\Domain\Repository\FrontendGroupRepository;

use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use TYPO3\CMS\Extbase\Http\ForwardResponse;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;
use TYPO3\CMS\Core\Utility\ArrayUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Authentication\GroupResolver;

/**
 * Controller for the main "Map" FE plugin.
 */
class MapController extends ActionController
{
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
     * @param AddressRepository $addressRepository
     */
    public function injectAddressRepository(AddressRepository $addressRepository): void
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

    /**
     */
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
            switch($item[0]) {
                case 'tt_address':
                    $markerToShow['tt_address'][] = $this->addressRepository->findByUid($item[1]);
                    break;
                case 'fe_users':
                    $markerToShow['fe_users'][] = $this->frontendUserRepository->findByUid($item[1]);
                    break;
                case 'fe_groups':
                    $markerToShow['fe_groups'] = GeneralUtility::makeInstance(GroupResolver::class)->findAllUsersInGroups(GeneralUtility::intExplode(',', $item[1] ?: ''), 'fe_groups', 'fe_users');
                    break;
            }

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
            'config' => $this->config,
            'currentUid' => $currentUid,
            'marker' => $marker,
            'baseMaps' => $this->layerRepository->findAllByUids(explode(',', $this->settings['base_layer'] ?? [])),
            'overlayMaps' => $this->layerRepository->findAllByUids(explode(',', $this->settings['overlays'] ?? [])),
            'overlaysActive' => $this->layerRepository->findAllByUids(explode(',', $this->settings['overlays_active'] ?? []))
        ];

        $this->view->assignMultiple($variables);

        return $this->htmlResponse();
    }

    protected function getSettings(): array
    {
        try {
            return GeneralUtility::makeInstance(ExtensionConfiguration::class)->get('ods_osm');
        } catch (\Exception $e) {
            return [];
        }
    }

}
