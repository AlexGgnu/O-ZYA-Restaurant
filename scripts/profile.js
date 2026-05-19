const editButton = document.getElementById("edit__button");
const editIcon = document.getElementById("edit__icon");
const checkIcon = document.getElementById("check__icon");
const modifyButtons = document.querySelectorAll(".modify__button");
const cancelButtons = document.querySelectorAll(".cancel__button");
if (!inputs) inputs = document.querySelectorAll("#profile__info__content input");

// MARK: - Edit profile info
async function updateProfileInfo(inputs) {
    const values = Array.from(inputs).reduce((list, input) => {
        list[input.name] = input.value;
        return list;
    }, {});

    try {
        const response = await fetch_accounts_data(undefined, 'update_profile_info', values);

        show_alert(response.title, response.message, response.type);
    } catch (error) {
        show_alert("Erreur", "Une erreur est survenue lors de la mise à jour des informations du profil. Veuillez réessayer.", "error");
    }
}
async function toggleEditMode() {
    inputs.forEach(input => input.disabled = !input.disabled);
    const allInputsEnabled = Array.from(inputs).every(input => !input.disabled);
    
    if (allInputsEnabled) {
        editIcon.style.display = "none";
        checkIcon.style.display = "block";
    } else {
        await updateProfileInfo(inputs);

        editIcon.style.display = "block";
        checkIcon.style.display = "none";
    }
}

// MARK: - Orders actions buttons
async function cancelOrder(orderRow) {
    const orderId = orderRow.getAttribute('data-order-id');

    try {
        const response = await fetch_orders_data(orderId, 'cancel_order');

        show_alert(response.title, response.message, response.type);
        if(response.type == "error" || response.status !== 'cancelled') return;
        
        const statusElement = orderRow.querySelector('.order__status');
        statusElement.textContent = 'Annulée';
        statusElement.setAttribute('data-status', response.status);

        const actionCell = orderRow.querySelector('.action__cell');
        actionCell.innerHTML = '-';
    } catch (error) {
        show_alert("Erreur", "Une erreur est survenue lors de l'annulation de la commande. Veuillez réessayer.", "error");
    }
}

//MARK: - Initialize
if (editButton) editButton.addEventListener('click', async () => await toggleEditMode());

if (modifyButtons.length > 0) {
    modifyButtons.forEach(button => button.addEventListener('click', async (event) => {
        const orderId = event.target.closest('.order__row').getAttribute('data-order-id');
    }));
}

if (cancelButtons.length > 0) {
    cancelButtons.forEach(button => button.addEventListener('click', async (event) => {
        const orderRow = event.target.closest('.order__row');
        await cancelOrder(orderRow);
    }));
}