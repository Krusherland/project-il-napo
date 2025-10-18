/**
 * Simple Il-Napolitano Modal - Lightweight Version
 * For welcome.php admin panel
 */

function showIlNapolitanoModal(options) {
    // Remove existing modal if any
    const existingModal = document.getElementById('simpleIlModal');
    if (existingModal) {
        existingModal.remove();
    }

    // Create modal HTML
    const modalHTML = `
        <div id="simpleIlModal" style="
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.8);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 9999;
            font-family: 'Work Sans', sans-serif;
        ">
            <div style="
                background: linear-gradient(135deg, #fff5e6 0%, #fff 50%, #fff5e6 100%);
                border-radius: 12px;
                box-shadow: 0 8px 30px rgba(0, 0, 0, 0.3);
                max-width: 400px;
                width: 90%;
                overflow: hidden;
                border: 3px solid #d4af37;
            ">
                <!-- Header -->
                <div style="
                    background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%);
                    color: white;
                    padding: 1rem 1.5rem;
                    display: flex;
                    justify-content: space-between;
                    align-items: center;
                ">
                    <div style="display: flex; align-items: center; gap: 0.75rem;">
                        <span style="font-size: 1.5rem;">🍕</span>
                        <h3 style="margin: 0; font-family: 'Oswald', sans-serif; font-size: 1.2rem; text-transform: uppercase;">Il Napolitano</h3>
                    </div>
                    <button onclick="closeIlModal()" style="
                        background: none;
                        border: none;
                        color: white;
                        font-size: 1.8rem;
                        cursor: pointer;
                        padding: 0;
                        width: 32px;
                        height: 32px;
                        border-radius: 50%;
                    ">&times;</button>
                </div>
                
                <!-- Body -->
                <div style="padding: 2rem 1.5rem; text-align: center;">
                    <div style="margin-bottom: 1rem;">
                        <i class="fas fa-trash-alt" style="font-size: 3rem; color: #ce2b37;"></i>
                    </div>
                    <h4 style="
                        font-family: 'Oswald', sans-serif;
                        font-size: 1.5rem;
                        color: #2c3e50;
                        margin: 0 0 0.75rem 0;
                        text-transform: uppercase;
                    ">${options.title || '¿Confirmar acción?'}</h4>
                    <p style="
                        font-size: 1rem;
                        color: #555;
                        margin-bottom: 1.5rem;
                        line-height: 1.4;
                    ">${options.message || '¿Estás seguro?'}</p>
                    
                    ${options.extraInfo ? `
                    <div style="
                        background: rgba(212, 175, 55, 0.1);
                        border: 2px solid #d4af37;
                        border-radius: 6px;
                        padding: 1rem;
                        margin-bottom: 1rem;
                        display: flex;
                        justify-content: space-between;
                    ">
                        <span style="font-weight: 600; color: #2c3e50;">${options.extraLabel}:</span>
                        <span style="font-weight: 700; color: #d4af37;">${options.extraInfo}</span>
                    </div>
                    ` : ''}
                </div>
                
                <!-- Footer -->
                <div style="
                    padding: 1rem 1.5rem 1.5rem;
                    display: flex;
                    gap: 1rem;
                    justify-content: center;
                ">
                    <button onclick="closeIlModal()" style="
                        background: white;
                        color: #6c757d;
                        border: 2px solid #dee2e6;
                        border-radius: 6px;
                        padding: 0.75rem 1.5rem;
                        font-family: 'Oswald', sans-serif;
                        font-weight: 600;
                        cursor: pointer;
                        text-transform: uppercase;
                        font-size: 1rem;
                    ">
                        <i class="fas fa-times"></i> Cancelar
                    </button>
                    <button onclick="confirmIlModal()" style="
                        background: linear-gradient(135deg, #ce2b37 0%, #e74c3c 100%);
                        color: white;
                        border: 2px solid #ce2b37;
                        border-radius: 6px;
                        padding: 0.75rem 1.5rem;
                        font-family: 'Oswald', sans-serif;
                        font-weight: 600;
                        cursor: pointer;
                        text-transform: uppercase;
                        font-size: 1rem;
                    ">
                        <i class="fas fa-trash-can"></i> ${options.confirmText || 'Confirmar'}
                    </button>
                </div>
            </div>
        </div>
    `;

    // Add to page
    document.body.insertAdjacentHTML('beforeend', modalHTML);
    
    // Store callback
    window.currentModalCallback = options.onConfirm || function() {};
}

function closeIlModal() {
    const modal = document.getElementById('simpleIlModal');
    if (modal) {
        modal.remove();
    }
    window.currentModalCallback = null;
}

function confirmIlModal() {
    if (window.currentModalCallback) {
        window.currentModalCallback();
    }
    closeIlModal();
}

// Close on Escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeIlModal();
    }
});

console.log('Simple Il-Napolitano Modal loaded successfully');