<?php

declare(strict_types=1);

namespace MauticPlugin\MauticFileManagerBundle\EventListener;

use Mautic\CoreBundle\CoreEvents;
use Mautic\CoreBundle\Event\MenuEvent;
use Mautic\CoreBundle\Helper\CoreParametersHelper;
use Mautic\CoreBundle\Security\Permissions\CorePermissions;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class MenuSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private CoreParametersHelper $coreParametersHelper,
        private CorePermissions $security
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
        // Check if user has admin access
        if (!$this->security->isGranted(['user:users:edit'], 'MATCH_ONE')) {
            return;
        }

        $event->addMenuItems([
            [
                'route'    => 'mautic_filemanager_index',
                'id'       => 'mautic_filemanager_index',
                'parent'   => 'mautic.core.components',
                'priority' => 150,
            ],
        ]);
    }
}
