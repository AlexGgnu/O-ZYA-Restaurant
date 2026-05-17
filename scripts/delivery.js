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
            else show_alert("Erreur", "Une erreur est survenue lors de la validation de la livraison. Veuillez réessayer.", "error");
        } catch (error) {
            show_alert("Erreur", "Une erreur est survenue lors de la validation de la livraison. Veuillez réessayer.", "error");
        }
    });
}

if(validateButton) initializeDeliveryButton();