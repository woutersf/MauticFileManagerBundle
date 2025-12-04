<?php

declare(strict_types=1);

namespace MauticPlugin\MauticFileManagerBundle\Integration;

use Mautic\IntegrationsBundle\Integration\BasicIntegration;
use Mautic\IntegrationsBundle\Integration\ConfigurationTrait;
use Mautic\IntegrationsBundle\Integration\Interfaces\BasicInterface;

class FileManagerIntegration extends BasicIntegration implements BasicInterface
{
    use ConfigurationTrait;

    public const NAME = 'FileManager';

    public function getName(): string
    {
        return self::NAME;
    }

    public function getDisplayName(): string
    {
        return 'File Manager';
    }

    public function getIcon(): string
    {
        return 'plugins/MauticFileManagerBundle/Assets/folder2.png';
    }
}
