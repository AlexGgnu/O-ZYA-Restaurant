const validateButton = document.getElementById('validate__delivery__button');

function update_delivery_page() {
    window.location.reload();
}

// MARK: - API functions
function initializeDeliveryButton() {
    validateButton.addEventListener('click', async () => {
        const orderId = validateButton.dataset.orderId;

        try {
            const response = await fetch_delivery_data(undefined, 'validate_delivery', orderId);
            
            if (response) update_delivery_page();
            else console.error(`[ERROR] - Validation failed`); // TODO: Create message card
        } catch (error) {
            console.error(`[ERROR] - Validation failed: `, error.message); // TODO: Create message card
        }
    });
}

if(validateButton) initializeDeliveryButton();