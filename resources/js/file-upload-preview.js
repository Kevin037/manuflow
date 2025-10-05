/**
 * Real-time File Upload Preview Utility
 * Provides immediate and real-time preview functionality for file uploads
 */

class FileUploadPreview {
    constructor(options = {}) {
        this.options = {
            inputId: 'photo',
            dropzoneId: 'photoDropzone',
            previewId: 'photoPreview',
            placeholderId: 'photoPlaceholder',
            previewImageId: 'previewImage',
            removeButtonId: 'removePhoto',
            currentPhotoId: 'currentPhoto',
            maxSize: 2 * 1024 * 1024, // 2MB
            allowedTypes: ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'],
            previewSize: 'h-32 w-32',
            showFileInfo: true,
            showAlerts: true,
            ...options
        };
        
        this.elements = {};
        this.init();
    }

    init() {
        this.getElements();
        this.bindEvents();
    }

    getElements() {
        this.elements.input = document.getElementById(this.options.inputId);
        this.elements.dropzone = document.getElementById(this.options.dropzoneId);
        this.elements.preview = document.getElementById(this.options.previewId);
        this.elements.placeholder = document.getElementById(this.options.placeholderId);
        this.elements.previewImage = document.getElementById(this.options.previewImageId);
        this.elements.removeButton = document.getElementById(this.options.removeButtonId);
        this.elements.currentPhoto = document.getElementById(this.options.currentPhotoId);
    }

    bindEvents() {
        if (!this.elements.input || !this.elements.dropzone) {
            console.warn('FileUploadPreview: Required elements not found');
            return;
        }

        // File input change event
        this.elements.input.addEventListener('change', (e) => {
            this.handleFileSelect(e.target.files[0]);
        });

        // Drag and drop events
        this.elements.dropzone.addEventListener('dragover', (e) => {
            e.preventDefault();
            e.stopPropagation();
            this.elements.dropzone.classList.add('border-primary-400', 'bg-primary-50');
            this.elements.dropzone.classList.remove('border-gray-300');
        });

        this.elements.dropzone.addEventListener('dragleave', (e) => {
            e.preventDefault();
            e.stopPropagation();
            if (!this.elements.dropzone.contains(e.relatedTarget)) {
                this.elements.dropzone.classList.remove('border-primary-400', 'bg-primary-50');
                this.elements.dropzone.classList.add('border-gray-300');
            }
        });

        this.elements.dropzone.addEventListener('drop', (e) => {
            e.preventDefault();
            e.stopPropagation();
            this.elements.dropzone.classList.remove('border-primary-400', 'bg-primary-50');
            this.elements.dropzone.classList.add('border-gray-300');
            
            const files = e.dataTransfer.files;
            if (files.length > 0) {
                const file = files[0];
                if (this.validateFile(file)) {
                    this.handleFileSelect(file);
                    const dt = new DataTransfer();
                    dt.items.add(file);
                    this.elements.input.files = dt.files;
                }
            }
        });

        // Remove button event
        if (this.elements.removeButton) {
            this.elements.removeButton.addEventListener('click', () => {
                this.removePhoto();
            });
        }
    }

    validateFile(file) {
        if (!this.options.allowedTypes.includes(file.type)) {
            if (this.options.showAlerts) {
                this.showAlert('Invalid file type. Please select a valid image file.', 'error');
            }
            return false;
        }
        
        if (file.size > this.options.maxSize) {
            const maxSizeMB = (this.options.maxSize / (1024 * 1024)).toFixed(1);
            if (this.options.showAlerts) {
                this.showAlert(`File size too large. Please select an image smaller than ${maxSizeMB}MB.`, 'error');
            }
            return false;
        }
        
        return true;
    }

    handleFileSelect(file) {
        if (!file) return;
        
        if (!this.validateFile(file)) {
            this.elements.input.value = '';
            return;
        }

        // Show loading state
        this.showLoadingState();

        const reader = new FileReader();
        
        // Progress event for large files
        reader.onprogress = (e) => {
            if (e.lengthComputable) {
                const progress = (e.loaded / e.total) * 100;
                if (progress < 100) {
                    this.updateLoadingProgress(Math.round(progress));
                }
            }
        };
        
        reader.onload = (e) => {
            this.createPreview(e.target.result, file);
        };
        
        reader.onerror = () => {
            if (this.options.showAlerts) {
                this.showAlert('Error loading image preview. Please try again.', 'error');
            }
            this.restorePhotoPlaceholder();
        };
        
        reader.readAsDataURL(file);
    }

    showLoadingState() {
        this.elements.placeholder.innerHTML = `
            <div class="flex flex-col items-center">
                <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-primary-600 mb-2"></div>
                <p class="text-sm text-gray-500">Loading preview...</p>
            </div>
        `;
    }

    updateLoadingProgress(progress) {
        this.elements.placeholder.innerHTML = `
            <div class="flex flex-col items-center">
                <div class="w-16 h-16 rounded-full border-4 border-gray-200 border-t-primary-600 animate-spin mb-2"></div>
                <p class="text-sm text-gray-500">Loading... ${progress}%</p>
            </div>
        `;
    }

    createPreview(imageSrc, file) {
        const img = new Image();
        img.onload = () => {
            let fileInfo = '';
            if (this.options.showFileInfo) {
                fileInfo = `
                    <div class="text-xs text-gray-500 mt-2 space-y-1">
                        <div>Size: ${(file.size / 1024).toFixed(1)} KB</div>
                        <div>Dimensions: ${img.width} × ${img.height}px</div>
                        <div>Type: ${file.type}</div>
                    </div>
                `;
            }
            
            this.elements.preview.innerHTML = `
                <img id="${this.options.previewImageId}" 
                     class="mx-auto ${this.options.previewSize} rounded-full object-cover shadow-lg transition-all duration-300 hover:shadow-xl" 
                     src="${imageSrc}" 
                     alt="Preview">
                ${fileInfo}
                <button type="button" 
                        id="${this.options.removeButtonId}" 
                        class="mt-3 inline-flex items-center px-3 py-1 border border-gray-300 shadow-sm text-xs font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-all duration-200">
                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                    Remove
                </button>
            `;
            
            // Hide current photo and placeholder, show new preview
            if (this.elements.currentPhoto) {
                this.elements.currentPhoto.classList.add('hidden');
            }
            this.elements.placeholder.classList.add('hidden');
            this.elements.preview.classList.remove('hidden');
            
            // Re-bind remove button event
            this.getElements();
            this.elements.removeButton.addEventListener('click', () => {
                this.removePhoto();
            });
            
            if (this.options.showAlerts) {
                this.showAlert('Image preview loaded successfully!', 'success');
            }
        };
        img.src = imageSrc;
    }

    removePhoto() {
        this.elements.input.value = '';
        this.elements.preview.classList.add('hidden');
        this.elements.placeholder.classList.remove('hidden');
        if (this.elements.currentPhoto) {
            this.elements.currentPhoto.classList.remove('hidden');
        }
        this.restorePhotoPlaceholder();
    }

    restorePhotoPlaceholder() {
        const hasCurrentPhoto = this.elements.currentPhoto && !this.elements.currentPhoto.classList.contains('hidden');
        this.elements.placeholder.innerHTML = `
            <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
            <div class="flex text-sm text-gray-600">
                <label for="${this.options.inputId}" class="relative cursor-pointer bg-white rounded-md font-medium text-primary-600 hover:text-primary-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-primary-500">
                    <span>${hasCurrentPhoto ? 'Upload new photo' : 'Upload a file'}</span>
                    <input id="${this.options.inputId}" name="${this.options.inputId}" type="file" class="sr-only" accept="image/*">
                </label>
                <p class="pl-1">or drag and drop</p>
            </div>
            <p class="text-xs text-gray-500">PNG, JPG, GIF up to 2MB</p>
        `;
        
        // Re-bind events
        this.getElements();
        this.elements.input.addEventListener('change', (e) => {
            this.handleFileSelect(e.target.files[0]);
        });
    }

    showAlert(message, type = 'info') {
        const alertClass = type === 'error' ? 'bg-red-100 border-red-400 text-red-700' : 
                          type === 'success' ? 'bg-green-100 border-green-400 text-green-700' : 
                          'bg-blue-100 border-blue-400 text-blue-700';
        
        const iconSvg = type === 'error' ? 
            '<svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/></svg>' :
            type === 'success' ? 
            '<svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>' :
            '<svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/></svg>';
        
        const alert = document.createElement('div');
        alert.className = `fixed top-4 right-4 z-50 p-4 border rounded-lg shadow-lg ${alertClass} transition-all duration-300 transform translate-x-full`;
        alert.innerHTML = `
            <div class="flex items-center">
                <div class="flex-shrink-0">${iconSvg}</div>
                <div class="ml-3">
                    <p class="text-sm font-medium">${message}</p>
                </div>
                <div class="ml-auto pl-3">
                    <button class="inline-flex text-sm font-medium hover:opacity-75" onclick="this.parentElement.parentElement.parentElement.remove()">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                        </svg>
                    </button>
                </div>
            </div>
        `;
        
        document.body.appendChild(alert);
        
        setTimeout(() => {
            alert.classList.remove('translate-x-full');
        }, 100);
        
        setTimeout(() => {
            alert.classList.add('translate-x-full');
            setTimeout(() => {
                if (alert.parentElement) {
                    alert.remove();
                }
            }, 300);
        }, 4000);
    }
}

// Export for module usage
if (typeof module !== 'undefined' && module.exports) {
    module.exports = FileUploadPreview;
}

// Global usage
window.FileUploadPreview = FileUploadPreview;