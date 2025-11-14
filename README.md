# Mautic File Manager Bundle

![File Manager Icon](Assets/folder2.png)

A powerful file and folder management interface for Mautic that allows administrators to manage media files, uploaded files, and theme assets directly from the Mautic admin interface.

## Overview

The Mautic File Manager Bundle provides a user-friendly interface for browsing, uploading, organizing, and managing files and folders within your Mautic installation. No more FTP or SSH required for basic file operations!

## Features

- **Multiple Directory Types** - Manage three different file locations:
  - **Images** - `/media/images` for email images, landing page assets
  - **Files** - `/media/files` for downloadable content, PDFs, documents
  - **Themes** - `/themes` for theme assets and customization files

- **File Operations**
  - Upload multiple files at once
  - Create new folders
  - Rename files and folders
  - Delete files and folders (with confirmation)
  - Batch selection for multiple operations

- **Intuitive Navigation**
  - Breadcrumb navigation showing current path
  - Click folders to browse deeper
  - Parent folder (`..`) for navigating up
  - Tab-based switching between Images/Files/Themes

- **Standard Mautic Interface**
  - Consistent styling with Mautic's design system
  - Responsive table layout
  - Integrated into Components menu
  - Familiar action buttons and panels

- **Security Features**
  - Admin-only access (`user:users:edit` permission)
  - Directory traversal protection
  - Filename sanitization
  - Path validation on all operations
  - Dotfile protection (hidden files are not shown)

## Requirements

- Mautic 4.0+ or Mautic 5.0+
- PHP 7.4 or 8.0+
- Write permissions on:
  - `/docroot/media/images`
  - `/docroot/media/files`
  - `/docroot/themes`

## Installation

### Via Manual Installation

1. Download or clone this repository
2. Place the `MauticFileManagerBundle` folder in `docroot/plugins/`
3. Ensure the folder structure is correct:
   ```
   docroot/plugins/MauticFileManagerBundle/
   ├── Assets/
   ├── Config/
   ├── Controller/
   ├── EventListener/
   ├── Resources/
   └── Translations/
   ```
4. Clear Mautic cache:
   ```bash
   php bin/console cache:clear
   ```
5. Go to **Mautic Settings → Plugins**
6. Click **"Install/Upgrade Plugins"** button
7. Find "File Manager" and click **Publish**

### Verify Installation

After installation, you should see:
- **"Files"** menu item under **Components** in the main menu
- Clicking it takes you to `/s/files`

## Usage

### Accessing the File Manager

1. Navigate to **Components → Files** in the main Mautic menu
2. Or go directly to: `/s/files`

### Switching Between Directories

Use the tabs at the top to switch between:
- **Images** - For email images, landing page assets
- **Files** - For downloadable PDFs, documents, media
- **Themes** - For theme customization files

### Navigating Folders

- **Click a folder** to open it and view its contents
- **Click breadcrumb links** to jump back to parent folders
- **Click the parent folder (`..`)** to go up one level

### Uploading Files

1. Navigate to the folder where you want to upload
2. Click the **"Upload Files"** button in the panel header
3. Select one or multiple files from your computer
4. Files will be uploaded to the current directory
5. The page will refresh to show the uploaded files

**Note:** File names will be automatically sanitized (special characters replaced with underscores).

### Creating Folders

1. Navigate to the location where you want to create a folder
2. Click the **"New Folder"** button in the panel header
3. Enter a folder name in the prompt
4. The folder will be created in the current directory

**Tip:** Use folders to organize images by campaign, files by content type, etc.

### Renaming Files or Folders

1. Find the file or folder you want to rename
2. Click the **gear icon (⚙️)** in the options column
3. Select **"Rename"** from the dropdown menu
4. Enter the new name in the prompt
5. The item will be renamed

**Note:** You cannot rename the parent folder (`..`).

### Deleting Files or Folders

1. Find the file or folder you want to delete
2. Click the **gear icon (⚙️)** in the options column
3. Select **"Delete"** from the dropdown menu
4. Confirm the deletion in the popup
5. The item will be deleted

**Warning:**
- Deleting a folder will delete all its contents recursively
- This action cannot be undone
- Make backups before deleting important files

### Batch Operations

1. Use the checkboxes to select multiple items
2. Use the checkbox in the header to select all items
3. *(Batch operations are prepared for future functionality)*

## Directory Structure

The File Manager provides access to these directories:

```
/docroot/
  ├── media/
  │   ├── images/      ← Images tab
  │   └── files/       ← Files tab
  └── themes/          ← Themes tab
```

**Images Directory** (`/media/images`)
- Email images
- Landing page assets
- Form images
- General media files

**Files Directory** (`/media/files`)
- PDFs and documents
- Downloadable content
- Asset files for download tracking
- General file storage

**Themes Directory** (`/themes`)
- Theme templates
- Theme assets (CSS, JS, images)
- Custom theme files
- Theme configuration

## Security Considerations

### Access Control

- **Admin-only access** - Only users with `user:users:edit` permission can access the File Manager
- This typically means administrators and super users only
- Regular users and contacts cannot access this feature

### Path Security

The File Manager implements multiple security measures:

1. **Directory Traversal Protection**
   - All paths are validated against the base directory
   - Attempts to access files outside allowed directories are blocked
   - Uses `realpath()` validation to prevent `../` attacks

2. **Filename Sanitization**
   - Special characters are removed from filenames
   - Only alphanumeric characters, dots, dashes, and underscores allowed
   - Prevents malicious filename injections

3. **Dotfile Protection**
   - Hidden files (starting with `.`) are not displayed
   - Prevents accidental exposure of `.htaccess`, `.env`, etc.
   - Current directory (`.`) is hidden

4. **Operation Validation**
   - All file operations validate the source and destination paths
   - Checks for file existence before operations
   - Prevents overwriting existing files during rename

### Best Practices

1. **Limit Access** - Only grant File Manager access to trusted administrators
2. **Regular Backups** - Back up your media files before bulk operations
3. **File Permissions** - Ensure proper file system permissions (755 for directories, 644 for files)
4. **Monitor Usage** - Check logs for unusual file operations
5. **Virus Scanning** - Consider implementing server-side virus scanning for uploads

## Troubleshooting

### "Files" menu item not appearing

**Solution:**
1. Ensure the plugin is published in **Settings → Plugins**
2. Clear cache: `php bin/console cache:clear`
3. Check that your user has `user:users:edit` permission
4. Refresh the browser

### Upload not working

**Solution:**
1. Check file permissions on the target directory (should be writable)
2. Check PHP upload limits in `php.ini`:
   - `upload_max_filesize`
   - `post_max_size`
   - `max_file_uploads`
3. Check server disk space
4. Look for JavaScript errors in browser console

### "Invalid path" error

**Solution:**
- This indicates a security check failure
- Do not attempt to manipulate URLs manually
- Use the interface navigation only
- If persistent, check directory permissions

### Permission denied errors

**Solution:**
1. Check file system permissions:
   ```bash
   chmod 755 docroot/media/images
   chmod 755 docroot/media/files
   chmod 755 docroot/themes
   ```
2. Ensure web server user (www-data, apache, nginx) owns the directories
3. On some systems, SELinux may block write access

### Translations not showing

**Solution:**
1. Ensure plugin is properly installed via **Install/Upgrade Plugins**
2. Clear cache: `php bin/console cache:clear`
3. Check that `Translations/en/messages.ini` exists
4. Refresh browser cache (Ctrl+Shift+R / Cmd+Shift+R)

### Files not displaying after upload

**Solution:**
1. Refresh the page (F5)
2. Check browser console for JavaScript errors
3. Verify files actually uploaded to the server filesystem
4. Check file ownership and permissions

## Development

### File Structure

```
MauticFileManagerBundle/
├── Assets/
│   ├── css/
│   │   └── filemanager.css       # Styling
│   ├── js/
│   │   └── filemanager.js        # Frontend interactions
│   └── folder2.png               # Plugin icon
├── Config/
│   └── config.php                # Routes, services, menu
├── Controller/
│   └── FileManagerController.php # All backend logic
├── EventListener/
│   ├── AssetSubscriber.php       # Load JS/CSS
│   └── MenuSubscriber.php        # Add menu item
├── Resources/
│   └── views/
│       └── FileManager/
│           └── index.html.twig   # Main interface
├── Translations/
│   └── en/
│       └── messages.ini          # English translations
├── composer.json
├── MauticFileManagerBundle.php
└── README.md
```

### Extending the Plugin

#### Adding New Directory Types

Edit `Controller/FileManagerController.php`:

```php
private const ALLOWED_TYPES = ['images', 'files', 'themes', 'custom'];
private const BASE_PATHS = [
    'images' => 'media/images',
    'files'  => 'media/files',
    'themes' => 'themes',
    'custom' => 'media/custom', // Add your custom path
];
```

Then add a tab in `Resources/views/FileManager/index.html.twig` and translations.

#### Customizing File Operations

All file operations are in `Controller/FileManagerController.php`:
- `uploadAction()` - Handle file uploads
- `deleteAction()` - Delete files/folders
- `renameAction()` - Rename files/folders
- `createFolderAction()` - Create new folders

#### Adding Batch Operations

JavaScript for batch operations is in `Assets/js/filemanager.js`. The checkboxes are already in place - add handlers for batch delete, move, etc.

## Known Limitations

1. **No Preview** - Files cannot be previewed in the interface
2. **No Search** - No search functionality for finding files
3. **No Sorting Options** - Files are sorted alphabetically by type (folders first)
4. **No File Editing** - Cannot edit text files directly
5. **No ZIP Upload** - Cannot upload and extract ZIP files
6. **Synchronous Operations** - Large folder deletions may timeout

## Roadmap

Future improvements could include:
- Image preview in modal
- File search/filter functionality
- Drag-and-drop upload
- Move/copy operations
- File size limits configuration
- Allowed file types configuration
- Image thumbnails
- Grid view option
- ZIP file extraction
- Direct file editing for text files

## Support

- **GitHub Issues**: [Report an issue](https://github.com/yourusername/mauticorangepoc/issues)
- **Mautic Community**: [community.mautic.org](https://community.mautic.org)
- **Documentation**: [Mautic Documentation](https://docs.mautic.org)

## License

GPL-3.0-or-later

## Credits

Created by Frederik Wouters and the Mautic Community

## Version

1.0.0

## Changelog

### 1.0.0 (2024)
- Initial release
- Browse files and folders in Images, Files, and Themes directories
- Upload multiple files
- Create folders
- Rename files and folders
- Delete files and folders
- Breadcrumb navigation
- Tab-based directory switching
- Standard Mautic interface styling
- Security: directory traversal protection, filename sanitization, admin-only access
