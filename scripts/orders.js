const statusSelects = document.querySelectorAll('.order__status');
const deliveryCells = document.querySelectorAll('.delivery__cell');

// MARK: - Order status
function setupStatusSelects() {
    statusSelects.forEach(select => {
        select.addEventListener('change', async (event) => {
            const newStatus = event.target.value;
            const orderId = event.target.closest('.order__row').getAttribute('data-order-id');
        
            try {
                const response = await fetch_orders_data(orderId, 'update_status', newStatus);

                if(!response.success || response.status !== newStatus) {
                    console.log(`Failed to update order ${orderId} status`); // TODO: Create message card
                    return;
                }
                
                select.setAttribute('data-status', response.status);
                if(response.status === 'delivered' || response.status === 'cancelled')select.disabled = true;
            } catch (error) {
                console.error(`[ERROR] - Updating status: ${newStatus}`, error.message); // TODO: Create message card
            }
        });
    });
}

// MARK: - Delivery
async function get_current_delivery(cell) {
    const orderId = cell.closest('.order__row').getAttribute('data-order-id');
    
    try {
        const response = await fetch_delivery_data(undefined, 'get_current_delivery', orderId);
        return response;
    } catch (error) {
        console.error(`[ERROR] - Fetching current delivery for order ${orderId}:`, error.message); // TODO: Create message card
        return { id: null, name: null };
    }

    return { id: deliveryId, name: deliveryName };
}
async function set_delivery_list(cells) {
    cells.forEach(cell => cell.innerHTML = '');

    try {
        const deliveries = await fetch_delivery_data(undefined, 'get_delivery_people', undefined);

        if(!deliveries || deliveries.length === 0) console.log('No delivery people found'); // TODO: Create message card
        else {
            cells.forEach(async cell => {
                const select = document.createElement('select');
                select.classList.add('delivery__select');

                const currentDelivery = await get_current_delivery(cell);
                const currentOption = document.createElement('option');
                currentOption.value = currentDelivery.id || '';
                currentOption.textContent = currentDelivery.name || 'Selectionner un livreur';
                select.appendChild(currentOption);
                
                Object.entries(deliveries).forEach(([id, name]) => {
                    const option = document.createElement('option');
                    option.value = id;
                    option.textContent = name;
                    select.appendChild(option);
                });

                cell.appendChild(select);
            });
        }
    } catch (error) {
        console.error(`[ERROR] - Fetching delivery list:`, error.message); // TODO: Create message card
        throw error;
    }
}
async function setupDeliveryCell() {
    await set_delivery_list(deliveryCells);

    deliveryCells.forEach(cell => {
        cell.addEventListener('change', async (event) => {
            const select = event.target;
            const deliveryId = select.value;
            const orderId = select.closest('.order__row').getAttribute('data-order-id');
        
            try {
                const response = await fetch_delivery_data(deliveryId, 'assign_delivery_person', orderId);

                if(!response) console.log(`Failed to update order ${orderId} delivery`); // TODO: Create message card
                else set_delivery_list(deliveryCells);
            } catch (error) {
                console.error(`[ERROR] - Updating delivery: ${deliveryId}`, error.message); // TODO: Create message card
            }
        });
    });
}

if(statusSelects.length > 0) setupStatusSelects();
if(deliveryCells.length > 0) setupDeliveryCell();