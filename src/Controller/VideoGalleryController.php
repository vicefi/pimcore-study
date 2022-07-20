<?php

namespace App\Controller;

use Pimcore\Controller\FrontendController;
use Pimcore\Model\Asset;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class VideoGalleryController extends FrontendController
{
    public function videoGalleryAction(): Response
    {
        return $this->render('video_gallery/video_gallery.html.twig');
    }

    /**
     * @Template
     */
    public function videoGalleryItemsAction(Request $request): array
    {
        $videos = [];
        if ('asset' ===$request->get('type')) {
            $asset = Asset::getById((int) $request->get('id'));

            if ('folder' === $asset->getType()) {
                $videos = new Asset\Listing();
                $videos->setCondition('parentId = ' . $request->get('id'))
                    ->load();
            }
        }

        return [
            'videos' => $videos,
            'info' => [
                'type' => $request->get('type') ?: 'no type',
                'id' => $request->get('id'),
                'count' => $request->get('type') ?: 'no type',
            ]
        ];
    }
}
