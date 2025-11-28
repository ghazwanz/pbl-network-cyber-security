/**
 * Custom JavaScript for Admin Pages
 * File: assets/js/admin.js
 */

$(document).ready(function() {
    
    // Confirm delete with better UX
    $('.btn-delete').on('click', function(e) {
        if (!confirm('Apakah Anda yakin ingin menghapus data ini? Aksi ini tidak dapat dibatalkan.')) {
            e.preventDefault();
        }
    });
    
    // Auto-hide flash messages
    setTimeout(function() {
        $('.flash-message').fadeOut('slow');
    }, 5000);
    
    // Image preview before upload
    $('input[type="file"][accept*="image"]').on('change', function() {
        const file = this.files[0];
        const preview = $(this).data('preview');
        
        if (file && preview) {
            const reader = new FileReader();
            
            reader.onload = function(e) {
                $(preview).attr('src', e.target.result).show();
            };
            
            reader.readAsDataURL(file);
            $('#preview-image').attr('src', '').removeClass('hidden');
        }
    });
    
    // PDF file info
    $('input[type="file"][accept*="pdf"]').on('change', function() {
        const file = this.files[0];
        const info = $(this).data('info');
        
        if (file && info) {
            const sizeKB = (file.size / 1024).toFixed(2);
            $(info).html(`
                <div class="text-sm text-gray-600 mt-2">
                    <i class="fas fa-file-pdf text-red-500 mr-2"></i>
                    ${file.name} (${sizeKB} KB)
                </div>
            `);
        }
    });
    
    // Select All checkbox
    $('#select-all').on('change', function() {
        $('.item-checkbox').prop('checked', $(this).is(':checked'));
    });
    
    // Bulk delete
    $('#bulk-delete').on('click', function() {
        const selected = $('.item-checkbox:checked').length;
        
        if (selected === 0) {
            alert('Pilih minimal satu item untuk dihapus');
            return false;
        }
        
        if (!confirm(`Hapus ${selected} item yang dipilih?`)) {
            return false;
        }
    });
    
    // Form validation
    $('form.needs-validation').on('submit', function(e) {
        let isValid = true;
        
        $(this).find('[required]').each(function() {
            if (!$(this).val()) {
                isValid = false;
                $(this).addClass('border-red-500');
                
                // Show error message
                if (!$(this).next('.error-message').length) {
                    $(this).after('<span class="error-message text-red-500 text-sm">Field ini wajib diisi</span>');
                }
            } else {
                $(this).removeClass('border-red-500');
                $(this).next('.error-message').remove();
            }
        });
        
        if (!isValid) {
            e.preventDefault();
            showToast('Mohon lengkapi semua field yang wajib diisi', 'error');
        }
    });
    
    // Remove error on input
    $('[required]').on('input', function() {
        if ($(this).val()) {
            $(this).removeClass('border-red-500');
            $(this).next('.error-message').remove();
        }
    });
    
    // Character counter for textarea
    $('textarea[maxlength]').each(function() {
        const maxLength = $(this).attr('maxlength');
        const counter = $(`<div class="text-sm text-gray-500 mt-1">0 / ${maxLength} karakter</div>`);
        $(this).after(counter);
        
        $(this).on('input', function() {
            const length = $(this).val().length;
            counter.text(`${length} / ${maxLength} karakter`);
            
            if (length > maxLength * 0.9) {
                counter.addClass('text-orange-500');
            } else {
                counter.removeClass('text-orange-500');
            }
        });
    });
    
    // DataTable-like search
    $('#table-search').on('keyup', function() {
        const value = $(this).val().toLowerCase();
        
        $('#data-table tbody tr').filter(function() {
            $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
        });
    });
    
    // Loading overlay
    window.showLoading = function() {
        $('body').append(`
            <div class="loading-overlay">
                <div class="bg-white p-6 rounded-lg shadow-xl">
                    <div class="spinner mb-4 mx-auto"></div>
                    <p class="text-gray-600">Memproses...</p>
                </div>
            </div>
        `);
    };
    
    window.hideLoading = function() {
        $('.loading-overlay').remove();
    };
    
    // Toast notification
    window.showToast = function(message, type = 'info') {
        const icons = {
            'success': 'fa-check-circle',
            'error': 'fa-times-circle',
            'warning': 'fa-exclamation-triangle',
            'info': 'fa-info-circle'
        };
        
        const colors = {
            'success': 'bg-green-500',
            'error': 'bg-red-500',
            'warning': 'bg-yellow-500',
            'info': 'bg-blue-500'
        };
        
        const icon = icons[type] || icons['info'];
        const color = colors[type] || colors['info'];
        
        const toast = $(`
            <div class="fixed top-20 right-4 ${color} text-white px-6 py-3 rounded-lg shadow-lg z-50 toast flex items-center gap-2">
                <i class="fas ${icon}"></i>
                <span>${message}</span>
            </div>
        `);
        
        $('body').append(toast);
        
        setTimeout(function() {
            toast.fadeOut('slow', function() {
                $(this).remove();
            });
        }, 3000);
    };
    
    // Confirm action with custom message
    window.confirmAction = function(message, callback) {
        if (confirm(message)) {
            if (typeof callback === 'function') {
                callback();
            }
            return true;
        }
        return false;
    };
    
    // Copy to clipboard
    window.copyToClipboard = function(text) {
        const temp = $('<textarea>');
        $('body').append(temp);
        temp.val(text).select();
        document.execCommand('copy');
        temp.remove();
        showToast('Berhasil disalin ke clipboard', 'success');
    };
    
});
