<?php

declare(strict_types=1);

namespace MauticPlugin\MauticFileManagerBundle\EventListener;

use Mautic\CoreBundle\CoreEvents;
use Mautic\CoreBundle\Event\MenuEvent;
use Mautic\IntegrationsBundle\Helper\IntegrationsHelper;
use MauticPlugin\MauticFileManagerBundle\Integration\FileManagerIntegration;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class MenuSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private IntegrationsHelper $integrationsHelper
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            CoreEvents::BUILD_MENU => ['onBuildMenu', 0],
        ];
    }

    public function onBuildMenu(MenuEvent $event): void
    {
        // Check if integration is enabled
        $integration = $this->integrationsHelper->getIntegration(FileManagerIntegration::NAME);
        if (!$integration || !$integration->getIntegrationConfiguration()->getIsPublished()) {
            // Remove the menu item if integration is disabled
            $event->removeMenuItems('mautic.filemanager.menu.index');
        }
    }
}
