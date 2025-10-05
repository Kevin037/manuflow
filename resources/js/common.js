/**
 * Common JavaScript functionality for Manuflow
 */

// Initialize Select2 on all select elements with select2 class
$(document).ready(function() {
    // Initialize Select2 for all select elements
    $('.select2').select2({
        theme: 'bootstrap-5',
        placeholder: function() {
            return $(this).data('placeholder') || 'Please select...';
        },
        allowClear: true
    });

    // Initialize Select2 for specific selectors
    $('select[data-select2]').each(function() {
        const $this = $(this);
        const options = {
            theme: 'bootstrap-5',
            placeholder: $this.data('placeholder') || 'Please select...',
            allowClear: $this.data('allow-clear') !== false
        };

        // Handle AJAX data source
        if ($this.data('ajax-url')) {
            options.ajax = {
                url: $this.data('ajax-url'),
                dataType: 'json',
                delay: 250,
                data: function(params) {
                    return {
                        q: params.term,
                        page: params.page
                    };
                },
                processResults: function(data, params) {
                    params.page = params.page || 1;
                    return {
                        results: data.items,
                        pagination: {
                            more: (params.page * 30) < data.total_count
                        }
                    };
                },
                cache: true
            };
            options.minimumInputLength = 1;
        }

        $this.select2(options);
    });

    // File upload preview functionality
    $('input[type="file"][data-preview]').each(function() {
        const input = this;
        const previewContainer = $($(this).data('preview'));
        
        $(input).on('change', function(e) {
            const file = e.target.files[0];
            if (file && file.type.startsWith('image/')) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    previewContainer.find('img').attr('src', e.target.result);
                    previewContainer.removeClass('hidden');
                    previewContainer.siblings('.file-placeholder').addClass('hidden');
                };
                reader.readAsDataURL(file);
            }
        });
    });

    // Modern confirmation dialogs using SweetAlert2
    $('[data-confirm]').on('click', function(e) {
        e.preventDefault();
        const element = this;
        const message = $(this).data('confirm');
        const title = $(this).data('confirm-title') || 'Are you sure?';
        const type = $(this).data('confirm-type') || 'warning';
        const confirmText = $(this).data('confirm-text') || 'Yes, proceed!';
        const cancelText = $(this).data('cancel-text') || 'Cancel';

        Swal.fire({
            title: title,
            text: message,
            icon: type,
            showCancelButton: true,
            confirmButtonColor: type === 'warning' ? '#ef4444' : '#6366f1',
            cancelButtonColor: '#6b7280',
            confirmButtonText: confirmText,
            cancelButtonText: cancelText,
            buttonsStyling: false,
            customClass: {
                confirmButton: 'inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-500 focus:outline-none focus:border-red-700 focus:ring focus:ring-red-200 active:bg-red-600 disabled:opacity-25 transition mr-3',
                cancelButton: 'inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-500 focus:outline-none focus:border-gray-700 focus:ring focus:ring-gray-200 active:bg-gray-600 disabled:opacity-25 transition'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                if (element.tagName === 'A') {
                    window.location.href = element.href;
                } else if (element.tagName === 'BUTTON' && element.form) {
                    element.form.submit();
                }
            }
        });
    });

    // Toast notifications
    window.showToast = function(message, type = 'success', duration = 3000) {
        Swal.fire({
            title: type === 'success' ? 'Success!' : 'Error!',
            text: message,
            icon: type,
            timer: duration,
            showConfirmButton: false,
            toast: true,
            position: 'top-end',
            background: type === 'success' ? '#f0f9ff' : '#fef2f2',
            color: type === 'success' ? '#065f46' : '#991b1b'
        });
    };

    // Form validation enhancement
    $('form[data-validate]').on('submit', function(e) {
        const form = this;
        const requiredFields = $(form).find('[required]');
        let isValid = true;

        requiredFields.each(function() {
            const field = $(this);
            if (!field.val().trim()) {
                field.addClass('border-red-500 ring-2 ring-red-200');
                isValid = false;
            } else {
                field.removeClass('border-red-500 ring-2 ring-red-200');
            }
        });

        if (!isValid) {
            e.preventDefault();
            showToast('Please fill in all required fields', 'error');
        }
    });

    // Auto-resize textareas
    $('textarea[data-auto-resize]').each(function() {
        this.style.height = 'auto';
        this.style.height = this.scrollHeight + 'px';
        
        $(this).on('input', function() {
            this.style.height = 'auto';
            this.style.height = this.scrollHeight + 'px';
        });
    });
});

// Utility functions
window.formatCurrency = function(amount, currency = '$') {
    return currency + parseFloat(amount).toLocaleString('en-US', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    });
};

window.formatNumber = function(number) {
    return parseInt(number).toLocaleString('en-US');
};

window.copyToClipboard = function(text) {
    navigator.clipboard.writeText(text).then(function() {
        showToast('Copied to clipboard!', 'success', 1500);
    });
};