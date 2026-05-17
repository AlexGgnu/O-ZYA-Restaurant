const statusSelects = document.querySelectorAll('.order__status');

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

if(statusSelects.length > 0) statusSelects.forEach(select => setupStatusSelects());