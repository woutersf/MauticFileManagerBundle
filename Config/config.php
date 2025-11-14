<?php

declare(strict_types=1);

return [
    'name'        => 'File Manager',
    'description' => 'File and folder management for Mautic',
    'version'     => '1.0.0',
    'author'      => 'Frederik Wouters',
    'icon'        => 'plugins/MauticFileManagerBundle/Assets/folder2.png',

    'routes' => [
        'main' => [
            'mautic_filemanager_index' => [
                'path'       => '/files/{type}/{path}',
                'controller' => 'MauticPlugin\MauticFileManagerBundle\Controller\FileManagerController::indexAction',
                'defaults'   => [
                    'type' => 'images',
                    'path' => '',
                ],
                'requirements' => [
                    'path' => '.*',
                ],
            ],
            'mautic_filemanager_upload' => [
                'path'       => '/files/upload',
                'controller' => 'MauticPlugin\MauticFileManagerBundle\Controller\FileManagerController::uploadAction',
            ],
            'mautic_filemanager_delete' => [
                'path'       => '/files/delete',
                'controller' => 'MauticPlugin\MauticFileManagerBundle\Controller\FileManagerController::deleteAction',
            ],
            'mautic_filemanager_rename' => [
                'path'       => '/files/rename',
                'controller' => 'MauticPlugin\MauticFileManagerBundle\Controller\FileManagerController::renameAction',
            ],
            'mautic_filemanager_create_folder' => [
                'path'       => '/files/create-folder',
                'controller' => 'MauticPlugin\MauticFileManagerBundle\Controller\FileManagerController::createFolderAction',
            ],
        ],
        'public' => [],
        'api'    => [],
    ],

    'menu' => [
        'main' => [
            'mautic.filemanager.menu.index' => [
                'route'    => 'mautic_filemanager_index',
                'access'   => ['user:users:edit'],
                'parent'   => 'mautic.core.components',
                'priority' => 150,
            ],
        ],
    ],

    'services' => [
        'events' => [
            'mautic.filemanager.menu.subscriber' => [
                'class'     => MauticPlugin\MauticFileManagerBundle\EventListener\MenuSubscriber::class,
                'arguments' => [
                    'mautic.helper.core_parameters',
                    'mautic.security',
                ],
            ],
            'mautic.filemanager.asset.subscriber' => [
                'class' => MauticPlugin\MauticFileManagerBundle\EventListener\AssetSubscriber::class,
            ],
        ],
    ],

    'parameters' => [],
];
