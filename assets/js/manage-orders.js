// Manage Orders JavaScript
function viewOrder(orderId) {
    openModal();
    loadOrderDetails(orderId, 'view');
}

function editOrder(orderId) {
    openModal();
    loadOrderDetails(orderId, 'edit');
}

function openModal() {
    document.getElementById('orderModal').style.display = 'block';
}

function closeModal() {
    document.getElementById('orderModal').style.display = 'none';
    document.getElementById('modalBody').innerHTML = '';
}

// Close modal when clicking outside
window.onclick = function(event) {
    const modal = document.getElementById('orderModal');
    if (event.target === modal) {
        closeModal();
    }
}

async function loadOrderDetails(orderId, mode = 'view') {
    const modalBody = document.getElementById('modalBody');
    modalBody.innerHTML = '<div class="loading-spinner"><i class="fas fa-spinner fa-spin"></i> Cargando...</div>';

    try {
        const response = await fetch(`get_order_details.php?order_id=${orderId}`);
        const data = await response.json();

        if (!data.success) {
            throw new Error(data.message || 'Error al cargar el pedido');
        }

        const order = data.order;
        const items = data.items;

        if (mode === 'view') {
            renderOrderView(order, items);
        } else {
            renderOrderEdit(order, items);
        }
    } catch (error) {
        modalBody.innerHTML = `
            <div class="error-message">
                <i class="fas fa-exclamation-circle"></i>
                <p>${error.message}</p>
            </div>
        `;
    }
}

function renderOrderView(order, items) {
    const statusLabels = {
        'pending': 'Pendiente',
        'confirmed': 'Confirmado',
        'preparing': 'En Preparación',
        'ready': 'Listo',
        'delivering': 'En Camino',
        'delivered': 'Entregado',
        'cancelled': 'Cancelado'
    };

    const paymentLabels = {
        'cash': 'Efectivo',
        'card': 'Tarjeta',
        'transfer': 'Transferencia',
        'mercadopago': 'Mercado Pago'
    };

    const html = `
        <div class="order-detail-header">
            <h2><i class="fas fa-receipt"></i> Pedido #${String(order.id_order).padStart(6, '0')}</h2>
            <p>Fecha: ${formatDate(order.created_at)}</p>
        </div>

        <div class="order-detail-section">
            <h3><i class="fas fa-user"></i> Información del Cliente</h3>
            <p><strong>Nombre:</strong> ${escapeHtml(order.customer_name)}</p>
            <p><strong>Email:</strong> ${escapeHtml(order.customer_email)}</p>
            <p><strong>Teléfono:</strong> ${escapeHtml(order.customer_phone)}</p>
        </div>

        <div class="order-detail-section">
            <h3><i class="fas fa-map-marker-alt"></i> Dirección de Entrega</h3>
            <p>${escapeHtml(order.delivery_address)}</p>
            <p>${escapeHtml(order.delivery_city)} ${order.delivery_zipcode ? `(${escapeHtml(order.delivery_zipcode)})` : ''}</p>
        </div>

        <div class="order-detail-section">
            <h3><i class="fas fa-pizza-slice"></i> Items del Pedido</h3>
            <table class="items-table">
                <thead>
                    <tr>
                        <th>Producto</th>
                        <th>Precio Unit.</th>
                        <th>Cantidad</th>
                        <th>Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    ${items.map(item => `
                        <tr>
                            <td>${escapeHtml(item.product_name)}</td>
                            <td>$${parseFloat(item.unit_price).toFixed(2)}</td>
                            <td>${item.quantity}</td>
                            <td>$${parseFloat(item.subtotal).toFixed(2)}</td>
                        </tr>
                    `).join('')}
                </tbody>
            </table>
        </div>

        <div class="order-detail-section">
            <h3><i class="fas fa-calculator"></i> Totales</h3>
            <div class="totals-grid">
                <div class="total-row">
                    <span>Subtotal:</span>
                    <span>$${parseFloat(order.subtotal).toFixed(2)}</span>
                </div>
                <div class="total-row">
                    <span>Envío:</span>
                    <span>$${parseFloat(order.delivery_fee).toFixed(2)}</span>
                </div>
                ${order.discount > 0 ? `
                <div class="total-row discount">
                    <span>Descuento:</span>
                    <span>-$${parseFloat(order.discount).toFixed(2)}</span>
                </div>
                ` : ''}
                <div class="total-row final">
                    <span>Total:</span>
                    <span>$${parseFloat(order.total).toFixed(2)}</span>
                </div>
            </div>
        </div>

        <div class="order-detail-section">
            <h3><i class="fas fa-info-circle"></i> Información Adicional</h3>
            <p><strong>Estado:</strong> <span class="status-badge status-${order.status}">${statusLabels[order.status]}</span></p>
            <p><strong>Método de Pago:</strong> ${paymentLabels[order.payment_method]}</p>
            <p><strong>Estado del Pago:</strong> <span class="payment-badge payment-${order.payment_status}">${order.payment_status === 'paid' ? 'Pagado' : 'Pendiente'}</span></p>
            ${order.notes ? `<p><strong>Notas:</strong> ${escapeHtml(order.notes)}</p>` : ''}
        </div>

        <div class="form-actions">
            <button class="btn btn-primary" onclick="editOrder(${order.id_order})">
                <i class="fas fa-edit"></i> Editar Pedido
            </button>
            <button class="btn btn-secondary" onclick="closeModal()">Cerrar</button>
        </div>
    `;

    document.getElementById('modalBody').innerHTML = html;
}

function renderOrderEdit(order, items) {
    const html = `
        <div class="order-detail-header">
            <h2><i class="fas fa-edit"></i> Editar Pedido #${String(order.id_order).padStart(6, '0')}</h2>
        </div>

        <form method="POST" action="manage_orders.php">
            <input type="hidden" name="action" value="update_status">
            <input type="hidden" name="order_id" value="${order.id_order}">

            <div class="order-detail-section">
                <h3><i class="fas fa-info-circle"></i> Estado del Pedido</h3>
                <div class="form-group">
                    <label for="status">Estado:</label>
                    <select name="status" id="status" required>
                        <option value="pending" ${order.status === 'pending' ? 'selected' : ''}>Pendiente</option>
                        <option value="confirmed" ${order.status === 'confirmed' ? 'selected' : ''}>Confirmado</option>
                        <option value="preparing" ${order.status === 'preparing' ? 'selected' : ''}>En Preparación</option>
                        <option value="ready" ${order.status === 'ready' ? 'selected' : ''}>Listo</option>
                        <option value="delivering" ${order.status === 'delivering' ? 'selected' : ''}>En Camino</option>
                        <option value="delivered" ${order.status === 'delivered' ? 'selected' : ''}>Entregado</option>
                        <option value="cancelled" ${order.status === 'cancelled' ? 'selected' : ''}>Cancelado</option>
                    </select>
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Guardar Cambios
                </button>
                <button type="button" class="btn btn-secondary" onclick="closeModal()">Cancelar</button>
            </div>
        </form>

        <hr style="margin: 2rem 0;">

        <form method="POST" action="manage_orders.php">
            <input type="hidden" name="action" value="update_payment">
            <input type="hidden" name="order_id" value="${order.id_order}">

            <div class="order-detail-section">
                <h3><i class="fas fa-credit-card"></i> Estado del Pago</h3>
                <div class="form-group">
                    <label for="payment_status">Estado del Pago:</label>
                    <select name="payment_status" id="payment_status" required>
                        <option value="pending" ${order.payment_status === 'pending' ? 'selected' : ''}>Pendiente</option>
                        <option value="paid" ${order.payment_status === 'paid' ? 'selected' : ''}>Pagado</option>
                        <option value="failed" ${order.payment_status === 'failed' ? 'selected' : ''}>Fallido</option>
                        <option value="refunded" ${order.payment_status === 'refunded' ? 'selected' : ''}>Reembolsado</option>
                    </select>
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Actualizar Pago
                </button>
            </div>
        </form>
    `;

    document.getElementById('modalBody').innerHTML = html;
}

function formatDate(dateString) {
    const date = new Date(dateString);
    return date.toLocaleDateString('es-AR', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
}

function escapeHtml(text) {
    const map = {
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        '"': '&quot;',
        "'": '&#039;'
    };
    return text.replace(/[&<>"']/g, m => map[m]);
}
