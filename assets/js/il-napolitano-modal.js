/**
 * Il-Napolitano Unified Modal System
 * Provides consistent confirmation and alert dialogs across the application
 * @version 2.0 - Unified version replacing simple-il-modal.js
 */

class IlNapolitanoModal {
    constructor() {
        this.modal = null;
        this.isInitialized = false;
        this.init();
    }

    init() {
        if (this.isInitialized) return;
        
        if (!document.getElementById('ilNapolitanoModal')) {
            this.createModalHTML();
        }
        
        this.modal = document.getElementById('ilNapolitanoModal');
        this.isInitialized = true;
    }

    createModalHTML() {
        // Determine the correct path to assets based on current page location
        let assetsPath = '../assets/images/pizza.svg';
        
        if (window.location.pathname.includes('/src/handlers/') || 
            window.location.pathname.includes('/handlers/')) {
            assetsPath = '../../assets/images/pizza.svg';
        }
        
        const modalHTML = `
            <div id="ilNapolitanoModal" class="modal-overlay">
                <div class="modal-container">
                    <div class="modal-header">
                        <div class="modal-logo">
                            <img src="${assetsPath}" alt="Il Napolitano Logo" onerror="this.style.display='none'">
                            <h3>Il Napolitano</h3>
                        </div>
                        <button class="modal-close" id="ilNapolitanoModalClose">&times;</button>
                    </div>
                    <div class="modal-body">
                        <div class="modal-icon" id="ilNapolitanoModalIcon">
                            <i class="fa-solid fa-question-circle"></i>
                        </div>
                        <h4 id="ilNapolitanoModalTitle">Confirmación</h4>
                        <p class="modal-message" id="ilNapolitanoModalMessage">¿Estás seguro?</p>
                        <div class="modal-subtotal" id="ilNapolitanoModalExtra" style="display: none;">
                            <span class="subtotal-label" id="ilNapolitanoModalExtraLabel">Información:</span>
                            <span class="subtotal-amount" id="ilNapolitanoModalExtraValue">-</span>
                        </div>
                    </div>
                    <div class="modal-footer" id="ilNapolitanoModalFooter">
                        <button class="modal-btn modal-btn-cancel" id="ilNapolitanoModalCancel">
                            <i class="fa-solid fa-times"></i>
                            Cancelar
                        </button>
                        <button class="modal-btn modal-btn-confirm" id="ilNapolitanoModalConfirm">
                            <i class="fa-solid fa-check"></i>
                            Confirmar
                        </button>
                    </div>
                </div>
            </div>
        `;
        
        document.body.insertAdjacentHTML('beforeend', modalHTML);
    }

    /**
     * Show confirmation dialog
     * @param {Object} options - Configuration options
     * @param {string} options.title - Modal title
     * @param {string} options.message - Modal message
     * @param {string} options.confirmText - Confirm button text
     * @param {string} options.cancelText - Cancel button text
     * @param {string} options.icon - Font Awesome icon class
     * @param {string} options.type - Modal type (danger, warning, info, success)
     * @param {string} options.extraLabel - Extra info label
     * @param {string} options.extraValue - Extra info value
     * @param {Function} options.onConfirm - Callback for confirm action
     * @param {Function} options.onCancel - Callback for cancel action
     */
    confirm(options = {}) {
        this.init();
        
        if (!this.modal) {
            throw new Error('Modal element not available');
        }
        
        const defaults = {
            title: 'Confirmación',
            message: '¿Estás seguro?',
            confirmText: 'Confirmar',
            cancelText: 'Cancelar',
            icon: 'fa-question-circle',
            type: 'warning',
            extraLabel: '',
            extraValue: '',
            onConfirm: () => {},
            onCancel: () => {}
        };
        
        const config = { ...defaults, ...options };
        
        // Update modal content
        document.getElementById('ilNapolitanoModalTitle').textContent = config.title;
        document.getElementById('ilNapolitanoModalMessage').textContent = config.message;
        
        // Update icon
        const iconElement = document.querySelector('#ilNapolitanoModalIcon i');
        iconElement.className = `fa-solid ${config.icon}`;
        
        // Set icon color based on type
        iconElement.style.color = this.getIconColor(config.type);
        
        // Update buttons
        const confirmBtn = document.getElementById('ilNapolitanoModalConfirm');
        const cancelBtn = document.getElementById('ilNapolitanoModalCancel');
        
        confirmBtn.innerHTML = `<i class="fa-solid ${this.getConfirmIcon(config.type)}"></i> ${config.confirmText}`;
        cancelBtn.innerHTML = `<i class="fa-solid fa-times"></i> ${config.cancelText}`;
        
        // Update confirm button style based on type
        confirmBtn.className = `modal-btn modal-btn-${config.type}`;
        
        // Handle extra information
        const extraDiv = document.getElementById('ilNapolitanoModalExtra');
        if (config.extraLabel && config.extraValue) {
            document.getElementById('ilNapolitanoModalExtraLabel').textContent = config.extraLabel;
            document.getElementById('ilNapolitanoModalExtraValue').textContent = config.extraValue;
            extraDiv.style.display = 'flex';
        } else {
            extraDiv.style.display = 'none';
        }
        
        // Show modal
        this.modal.classList.add('show');
        document.body.style.overflow = 'hidden';
        
        // Setup event handlers
        this.setupEventHandlers(config.onConfirm, config.onCancel);
    }

    /**
     * Show alert dialog (info only, no confirmation needed)
     */
    alert(options = {}) {
        const defaults = {
            title: 'Información',
            confirmText: 'Entendido',
            type: 'info',
            icon: 'fa-info-circle',
            onConfirm: () => {}
        };
        
        const config = { ...defaults, ...options };
        
        // Use confirm but hide cancel button
        this.confirm(config);
        
        // Hide cancel button for alert
        document.getElementById('ilNapolitanoModalCancel').style.display = 'none';
        
        // Center the confirm button
        document.getElementById('ilNapolitanoModalFooter').style.justifyContent = 'center';
    }

    setupEventHandlers(onConfirm, onCancel) {
        const closeModal = () => {
            this.modal.classList.remove('show');
            document.body.style.overflow = '';
            
            // Reset button visibility and footer alignment for next use
            document.getElementById('ilNapolitanoModalCancel').style.display = 'flex';
            document.getElementById('ilNapolitanoModalFooter').style.justifyContent = 'center';
            
            // Clean up event listeners
            document.getElementById('ilNapolitanoModalClose').removeEventListener('click', handleClose);
            document.getElementById('ilNapolitanoModalCancel').removeEventListener('click', handleClose);
            document.getElementById('ilNapolitanoModalConfirm').removeEventListener('click', handleConfirm);
            this.modal.removeEventListener('click', handleOverlayClick);
            document.removeEventListener('keydown', handleEscape);
        };

        const handleClose = () => {
            onCancel();
            closeModal();
        };

        const handleConfirm = () => {
            onConfirm();
            closeModal();
        };

        const handleOverlayClick = (e) => {
            if (e.target === this.modal) {
                handleClose();
            }
        };

        const handleEscape = (e) => {
            if (e.key === 'Escape') {
                handleClose();
            }
        };

        // Add event listeners
        document.getElementById('ilNapolitanoModalClose').addEventListener('click', handleClose);
        document.getElementById('ilNapolitanoModalCancel').addEventListener('click', handleClose);
        document.getElementById('ilNapolitanoModalConfirm').addEventListener('click', handleConfirm);
        this.modal.addEventListener('click', handleOverlayClick);
        document.addEventListener('keydown', handleEscape);
    }

    getIconColor(type) {
        const colors = {
            danger: 'var(--italian-red)',
            warning: 'var(--warm-gold)',
            info: 'var(--color-nav)',
            success: 'var(--italian-green)',
            confirm: 'var(--italian-red)'
        };
        return colors[type] || colors.warning;
    }

    getConfirmIcon(type) {
        const icons = {
            danger: 'fa-trash-can',
            warning: 'fa-exclamation-triangle',
            info: 'fa-info-circle',
            success: 'fa-check',
            confirm: 'fa-check'
        };
        return icons[type] || 'fa-check';
    }
}

// Create global instance
function getIlNapolitanoModal() {
    if (!window.IlNapolitanoModalInstance) {
        window.IlNapolitanoModalInstance = new IlNapolitanoModal();
    }
    return window.IlNapolitanoModalInstance;
}

// Main API functions
window.ilNapolitanoConfirm = function(options) {
    return getIlNapolitanoModal().confirm(options);
};

window.ilNapolitanoAlert = function(options) {
    return getIlNapolitanoModal().alert(options);
};

// Unified function to replace showIlNapolitanoModal from simple-il-modal.js
window.showIlNapolitanoModal = function(options) {
    const modalOptions = {
        title: options.title || '¿Confirmar acción?',
        message: options.message || '¿Estás seguro?',
        confirmText: options.confirmText || 'Confirmar',
        type: 'danger',
        icon: 'fa-trash-can',
        onConfirm: options.onConfirm || (() => {}),
        onCancel: options.onCancel || (() => {})
    };
    
    // Add extra info if provided
    if (options.extraLabel && options.extraInfo) {
        modalOptions.extraLabel = options.extraLabel;
        modalOptions.extraValue = options.extraInfo;
    }
    
    try {
        const modalInstance = getIlNapolitanoModal();
        modalInstance.confirm(modalOptions);
    } catch (error) {
        console.error('Error in showIlNapolitanoModal:', error);
        throw error; // Re-throw to trigger fallback
    }
};

// Simple wrapper functions for backward compatibility
window.confirmIlNapolitano = function(message, onConfirm = () => {}, onCancel = () => {}) {
    getIlNapolitanoModal().confirm({
        message: message,
        onConfirm: onConfirm,
        onCancel: onCancel
    });
};

window.alertIlNapolitano = function(message, onConfirm = () => {}) {
    getIlNapolitanoModal().alert({
        message: message,
        onConfirm: onConfirm
    });
};

console.log('Il-Napolitano Modal System loaded successfully - Unified version');