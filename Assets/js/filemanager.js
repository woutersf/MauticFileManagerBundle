/**
 * Mautic File Manager
 */
(function() {
    'use strict';

    function initFileManager() {
        var context = document.getElementById('filemanager-context');

        if (!context) {
            return;
        }

        var config = {
            type: context.getAttribute('data-type'),
            path: context.getAttribute('data-path'),
            uploadUrl: context.getAttribute('data-upload-url'),
            deleteUrl: context.getAttribute('data-delete-url'),
            renameUrl: context.getAttribute('data-rename-url'),
            createFolderUrl: context.getAttribute('data-create-folder-url')
        };

        // Upload button click
        var uploadBtn = document.getElementById('filemanager-upload-btn');
        if (uploadBtn) {
            uploadBtn.addEventListener('click', function() {
                document.getElementById('filemanager-file-input').click();
            });
        }

        // File input change (upload files)
        var fileInput = document.getElementById('filemanager-file-input');
        if (fileInput) {
            fileInput.addEventListener('change', function(e) {
                if (this.files.length > 0) {
                    uploadFiles(this.files, config);
                }
            });
        }

        // Create folder button
        var createFolderBtn = document.getElementById('filemanager-create-folder-btn');
        if (createFolderBtn) {
            createFolderBtn.addEventListener('click', function() {
                var folderName = prompt('Enter folder name:');
                if (folderName) {
                    createFolder(folderName, config);
                }
            });
        }

        // Rename links
        var renameLinks = document.querySelectorAll('.filemanager-rename');
        renameLinks.forEach(function(link) {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                var oldName = this.getAttribute('data-name');
                var newName = prompt('Enter new name:', oldName);
                if (newName && newName !== oldName) {
                    renameItem(oldName, newName, config);
                }
            });
        });

        // Delete links
        var deleteLinks = document.querySelectorAll('.filemanager-delete');
        deleteLinks.forEach(function(link) {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                var name = this.getAttribute('data-name');
                if (confirm('Are you sure you want to delete "' + name + '"?')) {
                    deleteItem(name, config);
                }
            });
        });

        // Select all checkbox
        var selectAllCheckbox = document.getElementById('filemanager-select-all');
        if (selectAllCheckbox) {
            selectAllCheckbox.addEventListener('change', function() {
                var checkboxes = document.querySelectorAll('.filemanager-item-checkbox');
                checkboxes.forEach(function(checkbox) {
                    checkbox.checked = selectAllCheckbox.checked;
                });
            });
        }
    }

    /**
     * Upload files
     */
    function uploadFiles(files, config) {
        var formData = new FormData();
        formData.append('type', config.type);
        formData.append('path', config.path);

        for (var i = 0; i < files.length; i++) {
            formData.append('files[]', files[i]);
        }

        showSpinner();

        fetch(config.uploadUrl, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(function(response) { return response.json(); })
        .then(function(data) {
            hideSpinner();
            if (data.success) {
                showNotification(data.message, 'success');
                setTimeout(function() {
                    window.location.reload();
                }, 500);
            } else {
                showNotification(data.message || 'Upload failed', 'error');
                if (data.errors && data.errors.length > 0) {
                    console.error('Upload errors:', data.errors);
                }
            }
        })
        .catch(function(error) {
            hideSpinner();
            showNotification('Upload failed: ' + error.message, 'error');
            console.error('Upload error:', error);
        });
    }

    /**
     * Create folder
     */
    function createFolder(folderName, config) {
        showSpinner();

        fetch(config.createFolderUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: 'type=' + encodeURIComponent(config.type) +
                  '&path=' + encodeURIComponent(config.path) +
                  '&folderName=' + encodeURIComponent(folderName)
        })
        .then(function(response) { return response.json(); })
        .then(function(data) {
            hideSpinner();
            if (data.success) {
                showNotification(data.message, 'success');
                setTimeout(function() {
                    window.location.reload();
                }, 500);
            } else {
                showNotification(data.message || 'Failed to create folder', 'error');
            }
        })
        .catch(function(error) {
            hideSpinner();
            showNotification('Failed to create folder: ' + error.message, 'error');
            console.error('Create folder error:', error);
        });
    }

    /**
     * Rename item
     */
    function renameItem(oldName, newName, config) {
        showSpinner();

        fetch(config.renameUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: 'type=' + encodeURIComponent(config.type) +
                  '&path=' + encodeURIComponent(config.path) +
                  '&oldName=' + encodeURIComponent(oldName) +
                  '&newName=' + encodeURIComponent(newName)
        })
        .then(function(response) { return response.json(); })
        .then(function(data) {
            hideSpinner();
            if (data.success) {
                showNotification(data.message, 'success');
                setTimeout(function() {
                    window.location.reload();
                }, 500);
            } else {
                showNotification(data.message || 'Failed to rename', 'error');
            }
        })
        .catch(function(error) {
            hideSpinner();
            showNotification('Failed to rename: ' + error.message, 'error');
            console.error('Rename error:', error);
        });
    }

    /**
     * Delete item
     */
    function deleteItem(name, config) {
        showSpinner();

        fetch(config.deleteUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: 'type=' + encodeURIComponent(config.type) +
                  '&path=' + encodeURIComponent(config.path) +
                  '&name=' + encodeURIComponent(name)
        })
        .then(function(response) { return response.json(); })
        .then(function(data) {
            hideSpinner();
            if (data.success) {
                showNotification(data.message, 'success');
                setTimeout(function() {
                    window.location.reload();
                }, 500);
            } else {
                showNotification(data.message || 'Failed to delete', 'error');
            }
        })
        .catch(function(error) {
            hideSpinner();
            showNotification('Failed to delete: ' + error.message, 'error');
            console.error('Delete error:', error);
        });
    }

    /**
     * Show notification
     */
    function showNotification(message, type) {
        // Use Mautic's notification system if available
        if (typeof Mautic !== 'undefined' && typeof Mautic.showNotification === 'function') {
            Mautic.showNotification(message, type);
        } else {
            alert(message);
        }
    }

    /**
     * Show loading spinner
     */
    function showSpinner() {
        if (typeof Mautic !== 'undefined' && typeof Mautic.showLoadingBar === 'function') {
            Mautic.showLoadingBar();
        }
    }

    /**
     * Hide loading spinner
     */
    function hideSpinner() {
        if (typeof Mautic !== 'undefined' && typeof Mautic.hideLoadingBar === 'function') {
            Mautic.hideLoadingBar();
        }
    }

    // Initialize on page load
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initFileManager);
    } else {
        initFileManager();
    }

    // Handle AJAX navigation in Mautic
    if (typeof mQuery !== 'undefined') {
        mQuery(document).on('content.loaded', function() {
            initFileManager();
        });
    }

    if (typeof Mautic !== 'undefined' && typeof Mautic.onPageLoad === 'function') {
        Mautic.onPageLoad('fileManager', function() {
            initFileManager();
        });
    }

})();
