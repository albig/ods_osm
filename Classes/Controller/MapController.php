<?php

declare(strict_types=1);

namespace Bobosch\OdsOsm\Controller;

use Psr\Http\Message\ResponseInterface;
use Bobosch\OdsOsm\Domain\Model\Map;
use Bobosch\OdsOsm\Domain\Repository\LayerRepository;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use TYPO3\CMS\Extbase\Http\ForwardResponse;

/**
 * Controller for the main "Map" FE plugin.
 */
class MapController extends ActionController
{
    /** @var LayerRepository */
    protected $layerRepository;

	/**
     * @param \Bobosch\OdsOsm\Domain\Repository\LayerRepository $layerRepository
     */
    public function injectLayerRepository(LayerRepository $layerRepository): void
    {
        $this->layerRepository = $layerRepository;
    }

    public function showAction(): ResponseInterface
    {
        $cObjectData = $this->request->getAttribute('currentContentObject');
        $currentUid = $cObjectData->data['uid'];
        switch ($this->settings['library'] ?? '') {
            case 'openlayers':
                return (new ForwardResponse('openlayers'))
                    ->withArguments(['currentUid' => $currentUid]);
            case 'leaflet':
                return (new ForwardResponse('leaflet'))
                    ->withArguments(['currentUid' => $currentUid]);
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

    public function leafletAction(): ResponseInterface
    {
        $cObjectData = $this->request->getAttribute('currentContentObject');
        $variables = [
            'currentUid' => $cObjectData->data['uid'],
            'baseMaps' => $this->layerRepository->findAllByUids(explode(',', $this->settings['base_layer'] ?? [])),
            'overlayMaps' => $this->layerRepository->findAllByUids(explode(',', $this->settings['overlays'] ?? [])),
            'overlaysActive' => $this->layerRepository->findAllByUids(explode(',', $this->settings['overlays_active'] ?? []))
        ];

        $this->view->assignMultiple($variables);

        return $this->htmlResponse();
    }

}
