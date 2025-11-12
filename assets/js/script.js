/**
 * Custom JavaScript for Public Pages
 * File: assets/js/script.js
 */

$(document).ready(function() {
    
    // Smooth scrolling for anchor links
    $('a[href^="#"]').on('click', function(e) {
        e.preventDefault();
        
        var target = this.hash;
        var $target = $(target);
        
        if ($target.length) {
            $('html, body').animate({
                'scrollTop': $target.offset().top - 80
            }, 800);
        }
    });
    
    // Lazy loading images
    if ('IntersectionObserver' in window) {
        const imageObserver = new IntersectionObserver((entries, observer) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const img = entry.target;
                    img.src = img.dataset.src;
                    img.classList.remove('lazy');
                    imageObserver.unobserve(img);
                }
            });
        });
        
        const images = document.querySelectorAll('img.lazy');
        images.forEach(img => imageObserver.observe(img));
    }
    
    // Auto-hide alerts after 5 seconds
    setTimeout(function() {
        $('.alert').fadeOut('slow');
    }, 5000);
    
    // Form validation helper
    window.validateForm = function(formId) {
        let isValid = true;
        const form = document.getElementById(formId);
        
        if (!form) return false;
        
        const requiredFields = form.querySelectorAll('[required]');
        
        requiredFields.forEach(field => {
            if (!field.value.trim()) {
                isValid = false;
                field.classList.add('border-red-500');
            } else {
                field.classList.remove('border-red-500');
            }
        });
        
        return isValid;
    };
    
    // Image preview before upload
    window.previewImage = function(input, previewId) {
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            
            reader.onload = function(e) {
                $(previewId).attr('src', e.target.result).show();
            };
            
            reader.readAsDataURL(input.files[0]);
        }
    };
    
    // Confirm delete action
    window.confirmDelete = function(message) {
        return confirm(message || 'Apakah Anda yakin ingin menghapus data ini?');
    };
    
    // Format number to Indonesian currency
    window.formatCurrency = function(number) {
        return new Intl.NumberFormat('id-ID', {
            style: 'currency',
            currency: 'IDR'
        }).format(number);
    };
    
    // Show loading spinner
    window.showLoading = function() {
        $('body').append('<div class="loading-overlay"><div class="spinner"></div></div>');
    };
    
    // Hide loading spinner
    window.hideLoading = function() {
        $('.loading-overlay').remove();
    };
    
    // Toast notification
    window.showToast = function(message, type = 'info') {
        const colors = {
            'success': 'bg-green-500',
            'error': 'bg-red-500',
            'warning': 'bg-yellow-500',
            'info': 'bg-blue-500'
        };
        
        const color = colors[type] || colors['info'];
        
        const toast = $(`
            <div class="fixed top-20 right-4 ${color} text-white px-6 py-3 rounded-lg shadow-lg z-50 toast">
                ${message}
            </div>
        `);
        
        $('body').append(toast);
        
        setTimeout(function() {
            toast.fadeOut('slow', function() {
                $(this).remove();
            });
        }, 3000);
    };
    
});
