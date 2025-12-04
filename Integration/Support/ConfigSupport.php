<?php

declare(strict_types=1);

namespace MauticPlugin\MauticFileManagerBundle\Integration\Support;

use Mautic\IntegrationsBundle\Integration\DefaultConfigFormTrait;
use Mautic\IntegrationsBundle\Integration\Interfaces\ConfigFormInterface;
use MauticPlugin\MauticFileManagerBundle\Integration\FileManagerIntegration;

class ConfigSupport extends FileManagerIntegration implements ConfigFormInterface
{
    use DefaultConfigFormTrait;
}
