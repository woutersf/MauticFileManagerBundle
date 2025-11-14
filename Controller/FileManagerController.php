<?php

declare(strict_types=1);

namespace MauticPlugin\MauticFileManagerBundle\Controller;

use Mautic\CoreBundle\Controller\CommonController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class FileManagerController extends CommonController
{
    private const ALLOWED_TYPES = ['images', 'files', 'themes'];
    private const BASE_PATHS = [
        'images' => 'media/images',
        'files'  => 'media/files',
        'themes' => 'themes',
    ];

    /**
     * Display file manager interface
     */
    public function indexAction(Request $request, string $type = 'images', string $path = ''): Response
    {
        // Validate type
        if (!in_array($type, self::ALLOWED_TYPES)) {
            $type = 'images';
        }

        // Security check - require file manager view permission
        if (!$this->security->isGranted('filemanager:filemanager:view')) {
            return $this->accessDenied();
        }

        // Build the full path
        $basePath = $this->getParameter('kernel.project_dir') . '/docroot/' . self::BASE_PATHS[$type];
        $currentPath = $basePath . ($path ? '/' . $path : '');

        // Security: prevent directory traversal
        $realPath = realpath($currentPath);
        $realBasePath = realpath($basePath);

        if ($realPath === false || !str_starts_with($realPath, $realBasePath)) {
            $this->addFlashMessage('mautic.filemanager.error.invalid_path');
            $currentPath = $basePath;
            $path = '';
        }

        // Get directory contents
        $items = $this->getDirectoryContents($currentPath, $type, $path);

        // Build breadcrumbs
        $breadcrumbs = $this->buildBreadcrumbs($type, $path);

        return $this->delegateView([
            'viewParameters' => [
                'type'        => $type,
                'path'        => $path,
                'items'       => $items,
                'breadcrumbs' => $breadcrumbs,
            ],
            'contentTemplate' => '@MauticFileManager/FileManager/index.html.twig',
            'passthroughVars' => [
                'activeLink'    => '#mautic_filemanager_index',
                'mauticContent' => 'fileManager',
                'route'         => $this->generateUrl('mautic_filemanager_index', [
                    'type' => $type,
                    'path' => $path,
                ]),
            ],
        ]);
    }

    /**
     * Upload files
     */
    public function uploadAction(Request $request): JsonResponse
    {
        if (!$this->security->isGranted('filemanager:filemanager:edit')) {
            return new JsonResponse(['success' => false, 'message' => 'Access denied'], 403);
        }

        $type = $request->request->get('type', 'images');
        $path = $request->request->get('path', '');
        $files = $request->files->get('files', []);

        if (empty($files)) {
            return new JsonResponse(['success' => false, 'message' => 'No files uploaded']);
        }

        $basePath = $this->getParameter('kernel.project_dir') . '/docroot/' . self::BASE_PATHS[$type];
        $targetPath = $basePath . ($path ? '/' . $path : '');

        // Security check
        $realPath = realpath($targetPath);
        $realBasePath = realpath($basePath);

        if ($realPath === false || !str_starts_with($realPath, $realBasePath)) {
            return new JsonResponse(['success' => false, 'message' => 'Invalid path']);
        }

        $uploadedCount = 0;
        $errors = [];

        foreach ($files as $file) {
            try {
                $fileName = $file->getClientOriginalName();

                // Security: sanitize filename
                $fileName = preg_replace('/[^a-zA-Z0-9._-]/', '_', $fileName);

                $file->move($targetPath, $fileName);
                $uploadedCount++;
            } catch (\Exception $e) {
                $errors[] = $file->getClientOriginalName() . ': ' . $e->getMessage();
            }
        }

        return new JsonResponse([
            'success' => $uploadedCount > 0,
            'message' => sprintf('Uploaded %d file(s)', $uploadedCount),
            'errors'  => $errors,
        ]);
    }

    /**
     * Delete file or folder
     */
    public function deleteAction(Request $request): JsonResponse
    {
        if (!$this->security->isGranted('filemanager:filemanager:delete')) {
            return new JsonResponse(['success' => false, 'message' => 'Access denied'], 403);
        }

        $type = $request->request->get('type');
        $path = $request->request->get('path');
        $name = $request->request->get('name');

        $basePath = $this->getParameter('kernel.project_dir') . '/docroot/' . self::BASE_PATHS[$type];
        $targetPath = $basePath . ($path ? '/' . $path : '') . '/' . $name;

        // Security check
        $realPath = realpath($targetPath);
        $realBasePath = realpath($basePath);

        if ($realPath === false || !str_starts_with($realPath, $realBasePath)) {
            return new JsonResponse(['success' => false, 'message' => 'Invalid path']);
        }

        try {
            if (is_dir($targetPath)) {
                $this->deleteDirectory($targetPath);
            } else {
                unlink($targetPath);
            }

            return new JsonResponse(['success' => true, 'message' => 'Deleted successfully']);
        } catch (\Exception $e) {
            return new JsonResponse(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    /**
     * Rename file or folder
     */
    public function renameAction(Request $request): JsonResponse
    {
        if (!$this->security->isGranted('filemanager:filemanager:edit')) {
            return new JsonResponse(['success' => false, 'message' => 'Access denied'], 403);
        }

        $type = $request->request->get('type');
        $path = $request->request->get('path');
        $oldName = $request->request->get('oldName');
        $newName = $request->request->get('newName');

        // Sanitize new name
        $newName = preg_replace('/[^a-zA-Z0-9._-]/', '_', $newName);

        $basePath = $this->getParameter('kernel.project_dir') . '/docroot/' . self::BASE_PATHS[$type];
        $oldPath = $basePath . ($path ? '/' . $path : '') . '/' . $oldName;
        $newPath = $basePath . ($path ? '/' . $path : '') . '/' . $newName;

        // Security check
        $realOldPath = realpath($oldPath);
        $realBasePath = realpath($basePath);

        if ($realOldPath === false || !str_starts_with($realOldPath, $realBasePath)) {
            return new JsonResponse(['success' => false, 'message' => 'Invalid path']);
        }

        // Check if new name already exists
        if (file_exists($newPath)) {
            return new JsonResponse(['success' => false, 'message' => 'A file or folder with this name already exists']);
        }

        try {
            rename($oldPath, $newPath);
            return new JsonResponse(['success' => true, 'message' => 'Renamed successfully']);
        } catch (\Exception $e) {
            return new JsonResponse(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    /**
     * Create new folder
     */
    public function createFolderAction(Request $request): JsonResponse
    {
        if (!$this->security->isGranted('filemanager:filemanager:create')) {
            return new JsonResponse(['success' => false, 'message' => 'Access denied'], 403);
        }

        $type = $request->request->get('type');
        $path = $request->request->get('path');
        $folderName = $request->request->get('folderName');

        // Sanitize folder name
        $folderName = preg_replace('/[^a-zA-Z0-9._-]/', '_', $folderName);

        $basePath = $this->getParameter('kernel.project_dir') . '/docroot/' . self::BASE_PATHS[$type];
        $targetPath = $basePath . ($path ? '/' . $path : '') . '/' . $folderName;

        // Security check
        $parentPath = realpath($basePath . ($path ? '/' . $path : ''));
        $realBasePath = realpath($basePath);

        if ($parentPath === false || !str_starts_with($parentPath, $realBasePath)) {
            return new JsonResponse(['success' => false, 'message' => 'Invalid path']);
        }

        // Check if folder already exists
        if (file_exists($targetPath)) {
            return new JsonResponse(['success' => false, 'message' => 'Folder already exists']);
        }

        try {
            mkdir($targetPath, 0755);
            return new JsonResponse(['success' => true, 'message' => 'Folder created successfully']);
        } catch (\Exception $e) {
            return new JsonResponse(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    /**
     * Get directory contents
     */
    private function getDirectoryContents(string $path, string $type, string $relativePath): array
    {
        if (!is_dir($path)) {
            return [];
        }

        $items = [];
        $files = scandir($path);

        foreach ($files as $file) {
            // Skip dotfiles and current directory
            if ($file === '.' || str_starts_with($file, '.')) {
                continue;
            }

            $fullPath = $path . '/' . $file;
            $isDir = is_dir($fullPath);

            $item = [
                'name'     => $file,
                'isDir'    => $isDir,
                'isParent' => $file === '..',
                'size'     => $isDir ? '-' : $this->formatFileSize(filesize($fullPath)),
                'modified' => date('Y-m-d H:i:s', filemtime($fullPath)),
                'url'      => $isDir ? null : '/' . self::BASE_PATHS[$type] . ($relativePath ? '/' . $relativePath : '') . '/' . $file,
            ];

            // Add parent (..) folder only if we're not at root
            if ($file === '..' && $relativePath === '') {
                continue;
            }

            $items[] = $item;
        }

        // Sort: directories first, then files, alphabetically
        usort($items, function ($a, $b) {
            if ($a['isParent']) {
                return -1;
            }
            if ($b['isParent']) {
                return 1;
            }
            if ($a['isDir'] === $b['isDir']) {
                return strcasecmp($a['name'], $b['name']);
            }
            return $a['isDir'] ? -1 : 1;
        });

        return $items;
    }

    /**
     * Build breadcrumb navigation
     */
    private function buildBreadcrumbs(string $type, string $path): array
    {
        $breadcrumbs = [
            [
                'name' => '/' . $type,
                'path' => '',
            ],
        ];

        if ($path) {
            $parts = explode('/', $path);
            $currentPath = '';

            foreach ($parts as $part) {
                $currentPath .= ($currentPath ? '/' : '') . $part;
                $breadcrumbs[] = [
                    'name' => $part,
                    'path' => $currentPath,
                ];
            }
        }

        return $breadcrumbs;
    }

    /**
     * Format file size
     */
    private function formatFileSize(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $i = 0;

        while ($bytes >= 1024 && $i < count($units) - 1) {
            $bytes /= 1024;
            $i++;
        }

        return round($bytes, 2) . ' ' . $units[$i];
    }

    /**
     * Recursively delete directory
     */
    private function deleteDirectory(string $dir): void
    {
        if (!is_dir($dir)) {
            return;
        }

        $files = array_diff(scandir($dir), ['.', '..']);

        foreach ($files as $file) {
            $path = $dir . '/' . $file;
            is_dir($path) ? $this->deleteDirectory($path) : unlink($path);
        }

        rmdir($dir);
    }
}
