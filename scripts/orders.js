const statusSelects = document.querySelectorAll('.order__status');
const deliveryCells = document.querySelectorAll('.delivery__cell');

// MARK: - Order status
function setupStatusSelects() {
    statusSelects.forEach(select => {
        select.addEventListener('change', async (event) => {
            const newStatus = event.target.value;
            const row = event.target.closest('.order__row');
            const deliverySelect = row.querySelector('.delivery__select');
            const orderId = row.getAttribute('data-order-id');
        
            try {
                const response = await fetch_orders_data(orderId, 'update_status', newStatus);

                if(response.type == "error" || response.status !== newStatus) {
                    show_alert(response.title, response.message, response.type);
                    return;
                }
                
                select.setAttribute('data-status', response.status);
                if(response.status === 'ready' && deliverySelect) deliverySelect.disabled = false;
                else if(response.status === 'delivered' || response.status === 'cancelled') {
                    select.disabled = true;
                    if(deliverySelect) deliverySelect.disabled = true;
                }
                else if(deliverySelect) deliverySelect.disabled = true;
            } catch (error) {
                show_alert("Erreur", "Une erreur est survenue lors de la mise à jour du statut de la commande. Veuillez réessayer.", "error");
            }
        });
    });
}

// MARK: - Delivery person assignment
function update_delivery_list(cells, updatedSelect, oldSelectedPersonId, newSelectedPersonId) {
    const selectedIds = Array.from(cells)
        .map(cell => cell.querySelector('.delivery__select')?.value)
        .filter(id => id);

    cells.forEach(cell => {
        const select = cell.querySelector('.delivery__select');
        if (!select) return;

        for(const option of select.options) {
            if (option.value && option.value !== select.value) option.disabled = selectedIds.includes(option.value);
            else option.disabled = false;
        }
    });
}
async function setupDeliveryCell() {
    deliveryCells.forEach(cell => {
        cell.addEventListener('change', async (event) => {
            const select = event.target;
            const deliveryId = select.value;
            const orderId = select.closest('.order__row').getAttribute('data-order-id');
        
            try {
                const response = await fetch_delivery_data(deliveryId, 'assign_delivery_person', orderId);

                if(!response) show_alert("Erreur", "Une erreur est survenue lors de l'attribution du livreur. Veuillez réessayer.", "error");
                else update_delivery_list(deliveryCells, select, deliveryId, response);
            } catch (error) {
                show_alert("Erreur", "Une erreur est survenue lors de l'attribution du livreur. Veuillez réessayer.", "error");
            }
        });
    });
}

// MARK: - Initialization
if(statusSelects.length > 0) setupStatusSelects();
if(deliveryCells.length > 0) setupDeliveryCell();