<?php

declare(strict_types=1);

namespace Bobosch\OdsOsm\Controller;

use Psr\Http\Message\ResponseInterface;
use Bobosch\OdsOsm\Domain\Model\Map;
// use Bobosch\OdsOsm\Domain\Repository\MapRepository;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use TYPO3\CMS\Extbase\Http\ForwardResponse;

/**
 * Controller for the main "Map" FE plugin.
 */
class MapController extends ActionController
{
    // private MapRepository $mapRepository;

    // public function __construct(MapRepository $mapRepository)
    // {
    //     $this->mapRepository = $mapRepository;

    // }

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
        ];

        $this->view->assignMultiple($variables);

        return $this->htmlResponse();
    }

}
