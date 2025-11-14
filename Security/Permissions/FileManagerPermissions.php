<?php

declare(strict_types=1);

namespace MauticPlugin\MauticFileManagerBundle\Security\Permissions;

use Mautic\CoreBundle\Security\Permissions\AbstractPermissions;
use Symfony\Component\Form\FormBuilderInterface;

class FileManagerPermissions extends AbstractPermissions
{
    public function __construct($params)
    {
        parent::__construct($params);

        $this->addStandardPermissions('filemanager');
    }

    /**
     * Returns bundle name.
     */
    public function getName(): string
    {
        return 'filemanager';
    }

    /**
     * Defines available permissions.
     */
    public function buildForm(FormBuilderInterface &$builder, array $options, array $data): void
    {
        $this->addStandardFormFields('filemanager', 'filemanager', $builder, $data);
    }
}
