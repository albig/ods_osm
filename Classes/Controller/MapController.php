<?php

declare(strict_types=1);

namespace Bobosch\OdsOsm\Controller;

use Psr\Http\Message\ResponseInterface;
use Bobosch\OdsOsm\Domain\Model\Map;
use Bobosch\OdsOsm\Domain\Repository\LayerRepository;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use TYPO3\CMS\Extbase\Http\ForwardResponse;
use TYPO3\CMS\Core\Utility\ArrayUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;

/**
 * Controller for the main "Map" FE plugin.
 */
class MapController extends ActionController
{
    /** @var LayerRepository */
    protected $layerRepository;

    protected $config = [];

	/**
     * @param \Bobosch\OdsOsm\Domain\Repository\LayerRepository $layerRepository
     */
    public function injectLayerRepository(LayerRepository $layerRepository): void
    {
        $this->layerRepository = $layerRepository;
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


    public function showAction(): ForwardResponse
    {
        $cObjectData = $this->request->getAttribute('currentContentObject');
        $currentUid = $cObjectData->data['uid'];
        switch ($this->settings['library'] ?? '') {
            case 'openlayers':
                return (new ForwardResponse('openlayers'))
                    ->withArguments(['currentUid' => $currentUid]);
            case 'leaflet':
                return (new ForwardResponse('leaflet'))
                    ->withArguments(['currentUid' => $currentUid, 'config' => $this->config]);
            default:
                return $this->htmlResponse();
        }
    }

    public function openlayersAction(int $currentUid): ResponseInterface
    {
        $variables = [
            'currentUid' => $currentUid,
        ];

        $this->view->assignMultiple($variables);

        return $this->htmlResponse();
    }

    public function leafletAction(int $currentUid, array $config): ResponseInterface
    {
        $variables = [
            'config' => $this->config,
            'currentUid' => $currentUid,
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
