/**
 * ResQ Main JavaScript
 * Handles navigation, real-time updates, and general functionality
 */

// Mobile navigation toggle
document.addEventListener('DOMContentLoaded', function() {
    const navbarToggle = document.getElementById('navbarToggle');
    const navbarMenu = document.getElementById('navbarMenu');
    
    if (navbarToggle && navbarMenu) {
        navbarToggle.addEventListener('click', function() {
            navbarMenu.classList.toggle('active');
        });
        
        // Close menu when clicking outside
        document.addEventListener('click', function(event) {
            if (!navbarToggle.contains(event.target) && !navbarMenu.contains(event.target)) {
                navbarMenu.classList.remove('active');
            }
        });
    }
    
    // Auto-hide alerts after 5 seconds
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(alert => {
        setTimeout(() => {
            alert.style.transition = 'opacity 0.5s';
            alert.style.opacity = '0';
            setTimeout(() => alert.remove(), 500);
        }, 5000);
    });
    
    // Real-time updates for dashboard (if on dashboard page)
    if (document.getElementById('kpi-total')) {
        updateDashboardStats();
        setInterval(updateDashboardStats, 30000); // Update every 30 seconds
    }
});

/**
 * Update dashboard statistics via AJAX
 */
function updateDashboardStats() {
    // This would fetch real-time data from the server
    // For now, it's a placeholder for future AJAX implementation
    console.log('Dashboard stats update (placeholder)');
}

/**
 * Format date for display
 */
function formatDate(dateString) {
    const date = new Date(dateString);
    return date.toLocaleDateString('nl-NL', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
}

/**
 * Show confirmation dialog
 */
function confirmAction(message) {
    return confirm(message);
}

/**
 * Show success message
 */
function showSuccess(message) {
    const alert = document.createElement('div');
    alert.className = 'alert alert-success';
    alert.textContent = message;
    document.querySelector('.container').insertBefore(alert, document.querySelector('.container').firstChild);
    
    setTimeout(() => {
        alert.style.transition = 'opacity 0.5s';
        alert.style.opacity = '0';
        setTimeout(() => alert.remove(), 500);
    }, 5000);
}

/**
 * Show error message
 */
function showError(message) {
    const alert = document.createElement('div');
    alert.className = 'alert alert-error';
    alert.textContent = message;
    document.querySelector('.container').insertBefore(alert, document.querySelector('.container').firstChild);
    
    setTimeout(() => {
        alert.style.transition = 'opacity 0.5s';
        alert.style.opacity = '0';
        setTimeout(() => alert.remove(), 500);
    }, 5000);
}

/**
 * QR Code Scanner Helper Functions
 * (Used by reservation.php)
 */
window.qrScannerHelpers = {
    startCamera: function(type) {
        // Implementation in reservation.php
    },
    
    stopCamera: function() {
        // Implementation in reservation.php
    },
    
    scanQR: function(type) {
        // Implementation in reservation.php
    }
};
