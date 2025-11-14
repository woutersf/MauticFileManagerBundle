<?php

declare(strict_types=1);

namespace MauticPlugin\MauticFileManagerBundle\EventListener;

use Mautic\CoreBundle\CoreEvents;
use Mautic\CoreBundle\Event\CustomAssetsEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class AssetSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            CoreEvents::VIEW_INJECT_CUSTOM_ASSETS => ['injectAssets', 0],
        ];
    }

    public function injectAssets(CustomAssetsEvent $event): void
    {
        $event->addScript('plugins/MauticFileManagerBundle/Assets/js/filemanager.js', 'bodyClose');
        $event->addStylesheet('plugins/MauticFileManagerBundle/Assets/css/filemanager.css');
    }
}
