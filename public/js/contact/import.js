/**
 * Contact CSV Import - Drag and Drop File Upload with Progress Tracking
 * Issue #12: 大量顧客データの一括CSVインポート機能
 */

(function() {
    'use strict';

    // DOM Elements
    const dropzoneArea = document.getElementById('dropzoneArea');
    const csvFileInput = document.getElementById('csvFileInput');
    const selectFileBtn = document.getElementById('selectFileBtn');
    const csvUploadForm = document.getElementById('csvUploadForm');
    const uploadBtn = document.getElementById('uploadBtn');
    const fileInfo = document.getElementById('fileInfo');
    const fileName = document.getElementById('fileName');
    const clearFile = document.getElementById('clearFile');
    const progressArea = document.getElementById('progressArea');
    const statusArea = document.getElementById('statusArea');
    const progressBar = document.getElementById('progressBar');
    const progressMessage = document.getElementById('progressMessage');
    const resetBtn = document.getElementById('resetBtn');

    // State variables
    let selectedFile = null;
    let statusCheckInterval = null;
    let currentFileKey = null;

    /**
     * Initialize event listeners
     */
    function init() {
        if (!dropzoneArea || !csvFileInput) {
            console.error('Required DOM elements not found');
            return;
        }

        // File input change
        csvFileInput.addEventListener('change', handleFileSelect);

        // Select file button
        selectFileBtn.addEventListener('click', () => csvFileInput.click());

        // Dropzone drag and drop
        dropzoneArea.addEventListener('dragover', handleDragOver);
        dropzoneArea.addEventListener('dragleave', handleDragLeave);
        dropzoneArea.addEventListener('drop', handleFileDrop);

        // Clear file button
        if (clearFile) {
            clearFile.addEventListener('click', clearSelectedFile);
        }

        // Upload form submit
        if (csvUploadForm) {
            csvUploadForm.addEventListener('submit', handleFormSubmit);
        }

        // Reset button
        if (resetBtn) {
            resetBtn.addEventListener('click', resetUI);
        }
    }

    /**
     * Handle file selection from input
     */
    function handleFileSelect(e) {
        const file = e.target.files[0];
        if (file) {
            validateAndSelectFile(file);
        }
    }

    /**
     * Handle drag over
     */
    function handleDragOver(e) {
        e.preventDefault();
        e.stopPropagation();
        dropzoneArea.classList.add('drag-over');
    }

    /**
     * Handle drag leave
     */
    function handleDragLeave(e) {
        e.preventDefault();
        e.stopPropagation();
        dropzoneArea.classList.remove('drag-over');
    }

    /**
     * Handle file drop
     */
    function handleFileDrop(e) {
        e.preventDefault();
        e.stopPropagation();
        dropzoneArea.classList.remove('drag-over');

        const files = e.dataTransfer.files;
        if (files.length > 0) {
            const file = files[0];
            validateAndSelectFile(file);
        }
    }

    /**
     * Validate and select file
     */
    function validateAndSelectFile(file) {
        // Validate file type
        const validTypes = ['text/csv', 'text/plain', 'application/vnd.ms-excel'];
        const validExtensions = ['.csv', '.txt'];
        
        const fileExtension = '.' + file.name.split('.').pop().toLowerCase();
        const isValidType = validTypes.includes(file.type) || validExtensions.includes(fileExtension);

        if (!isValidType) {
            showAlert('danger', 'ファイル形式エラー', 'CSVファイル（.csv）またはテキストファイル（.txt）をアップロードしてください。');
            return;
        }

        // Validate file size (max 10MB)
        const maxSize = 10 * 1024 * 1024;
        if (file.size > maxSize) {
            showAlert('danger', 'ファイルサイズエラー', 'ファイルサイズは10MB以下である必要があります。');
            return;
        }

        selectedFile = file;
        csvFileInput.files = null;
        displaySelectedFile(file);
    }

    /**
     * Display selected file information
     */
    function displaySelectedFile(file) {
        fileName.textContent = file.name + ' (' + formatFileSize(file.size) + ')';
        fileInfo.style.display = 'block';
        uploadBtn.style.display = 'block';
    }

    /**
     * Clear selected file
     */
    function clearSelectedFile() {
        selectedFile = null;
        csvFileInput.value = '';
        fileInfo.style.display = 'none';
        uploadBtn.style.display = 'none';
    }

    /**
     * Format file size
     */
    function formatFileSize(bytes) {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return Math.round((bytes / Math.pow(k, i)) * 100) / 100 + ' ' + sizes[i];
    }

    /**
     * Handle form submit - Upload CSV file
     */
    function handleFormSubmit(e) {
        e.preventDefault();

        if (!selectedFile) {
            showAlert('danger', 'ファイルが選択されていません', 'CSVファイルを選択してください。');
            return;
        }

        uploadFile(selectedFile);
    }

    /**
     * Upload file via AJAX
     */
    function uploadFile(file) {
        const formData = new FormData();
        formData.append('csv_file', file);
        formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));

        uploadBtn.disabled = true;
        uploadBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> アップロード中...';

        fetch('/admin/contact/import', {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
            }
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Upload failed: ' + response.statusText);
            }
            return response.json();
        })
        .then(data => {
            if (data.success && data.file_key) {
                currentFileKey = data.file_key;
                showUploadSuccess(data.file_key);
                startStatusPolling();
            } else {
                showAlert('danger', 'アップロードエラー', data.message || 'ファイルのアップロードに失敗しました。');
                uploadBtn.disabled = false;
                uploadBtn.innerHTML = '<i class="fas fa-upload"></i> アップロード開始';
            }
        })
        .catch(error => {
            console.error('Upload error:', error);
            showAlert('danger', 'エラー', 'ファイルのアップロード中にエラーが発生しました: ' + error.message);
            uploadBtn.disabled = false;
            uploadBtn.innerHTML = '<i class="fas fa-upload"></i> アップロード開始';
        });
    }

    /**
     * Show upload success message
     */
    function showUploadSuccess(fileKey) {
        csvUploadForm.style.display = 'none';
        progressArea.style.display = 'block';
        updateProgressUI('pending', 'ファイルを受け付けました');
    }

    /**
     * Start polling for status updates
     */
    function startStatusPolling() {
        // Initial immediate check
        checkStatus();

        // Poll every 1 second
        statusCheckInterval = setInterval(() => {
            checkStatus();
        }, 1000);
    }

    /**
     * Check import status
     */
    function checkStatus() {
        if (!currentFileKey) {
            stopStatusPolling();
            return;
        }

        fetch(`/admin/contact/import/status/${currentFileKey}`, {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
            }
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Status check failed');
            }
            return response.json();
        })
        .then(data => {
            updateProgressUI(data.status, data.message, data.success_count, data.error_count);

            // Stop polling if completed or failed
            if (data.status === 'completed' || data.status === 'failed') {
                stopStatusPolling();
                showFinalStatus(data.status, data.message, data.success_count, data.error_count);
            }
        })
        .catch(error => {
            console.error('Status check error:', error);
        });
    }

    /**
     * Stop polling for status
     */
    function stopStatusPolling() {
        if (statusCheckInterval) {
            clearInterval(statusCheckInterval);
            statusCheckInterval = null;
        }
    }

    /**
     * Update progress UI
     */
    function updateProgressUI(status, message, successCount = 0, errorCount = 0) {
        // Update progress bar
        let progressPercent = 0;
        if (status === 'processing') {
            progressPercent = 50;
        } else if (status === 'completed') {
            progressPercent = 100;
        }

        progressBar.style.width = progressPercent + '%';
        progressBar.setAttribute('aria-valuenow', progressPercent);

        // Update message
        if (progressMessage) {
            progressMessage.textContent = message;
        }
    }

    /**
     * Show final status
     */
    function showFinalStatus(status, message, successCount, errorCount) {
        progressArea.style.display = 'none';
        statusArea.style.display = 'block';

        const statusHeader = document.getElementById('statusHeader');
        const statusTitle = document.getElementById('statusTitle');
        const statusMessage = document.getElementById('statusMessage');
        const successCountEl = document.getElementById('successCount');
        const errorCountEl = document.getElementById('errorCount');

        // Set header color based on status
        statusHeader.className = 'card-header';
        if (status === 'completed') {
            statusHeader.classList.add('bg-success', 'text-white');
            statusTitle.textContent = '✓ 処理完了';
        } else if (status === 'failed') {
            statusHeader.classList.add('bg-danger', 'text-white');
            statusTitle.textContent = '✗ 処理失敗';
        }

        statusMessage.textContent = message;
        successCountEl.textContent = successCount;
        errorCountEl.textContent = errorCount;
    }

    /**
     * Reset UI for another upload
     */
    function resetUI() {
        // Reset state
        selectedFile = null;
        csvFileInput.value = '';
        currentFileKey = null;

        // Reset UI elements
        csvUploadForm.style.display = 'block';
        progressArea.style.display = 'none';
        statusArea.style.display = 'none';
        fileInfo.style.display = 'none';
        uploadBtn.style.display = 'none';
        uploadBtn.disabled = false;
        uploadBtn.innerHTML = '<i class="fas fa-upload"></i> アップロード開始';

        // Stop any active polling
        stopStatusPolling();
    }

    /**
     * Show alert message
     */
    function showAlert(type, title, message) {
        const alertDiv = document.createElement('div');
        alertDiv.className = 'alert alert-' + type + ' alert-dismissible fade show';
        alertDiv.setAttribute('role', 'alert');
        alertDiv.innerHTML = `
            <strong>${title}</strong> ${message}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        `;

        const container = document.querySelector('.container');
        if (container) {
            container.insertBefore(alertDiv, container.firstChild);
            
            // Auto-dismiss after 5 seconds
            setTimeout(() => {
                alertDiv.remove();
            }, 5000);
        }
    }

    /**
     * Initialize when DOM is ready
     */
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();
