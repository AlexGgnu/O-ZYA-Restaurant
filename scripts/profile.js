const editButton = document.getElementById("edit__button");
const copyButtons = document.querySelectorAll(".copy__button");
const modifyButtons = document.querySelectorAll(".modify__button");
const cancelButtons = document.querySelectorAll(".cancel__button");

let ordersContainerSwitched = false;
let initialOrdersItems = [];
let newOrdersItems = [];

function init_new_orders_variable(initialItems) {
    newOrdersItems = new Proxy(initialItems.map(item => ({ ...item })), { // NOTE: Proxy is used to intercept modifications
        set(orderList, index, newValue) { // NOTE: 'set' is for intercept modifications (push, splice, direct index assignment, etc.)
            orderList[index] = newValue; // NOTE: Update normaly the newOrdersItems array

            // NOTE: In addition to update list, we will also update the difference in price and the confirm modification button state
            const confirmBtn = document.getElementById('confirm_modification');
            const priceDifferenceElement = document.getElementById('diffenrece_price');

            if (!confirmBtn || !priceDifferenceElement) return true;

            const initialTotalPrice = initialOrdersItems.reduce((total, item) => total + (item.price * item.quantity), 0);
            const newTotalPrice = orderList.reduce((total, item) => total + (item.price * item.quantity), 0);
            const priceDifference = newTotalPrice - initialTotalPrice;

            priceDifferenceElement.textContent = `${priceDifference >= 0 ? '+' : '-'}${Math.abs(priceDifference).toFixed(2)} €`;

            if (priceDifference === 0) {
                confirmBtn.disabled = true;
                confirmBtn.style.display = "none";
            } else {
                confirmBtn.disabled = false;
                confirmBtn.style.display = "block";
            }

            return true; // NOTE: Indicate that the assignment was successful
        }
    });
}

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
    if (!inputs) inputs = document.querySelectorAll("#profile__info__content input");

    inputs.forEach(input => input.disabled = !input.disabled);
    const allInputsEnabled = Array.from(inputs).every(input => !input.disabled);
    
    const editIcon = document.getElementById("edit__icon");
    const checkIcon = document.getElementById("check__icon");
    if (allInputsEnabled) {
        editIcon.style.display = "none";
        checkIcon.style.display = "block";
    } else {
        await updateProfileInfo(inputs);

        editIcon.style.display = "block";
        checkIcon.style.display = "none";
    }
}

// MARK: - Promotions copy button
function copyPromotionCode(clickedButton) {
    const promotionCode = clickedButton.closest('.promotion__card').getAttribute('data-promo-code');

    if (!promotionCode) {
        show_alert("Erreur", "Impossible de récupérer le code promotionnel.", "error");
        return;
    }

    navigator.clipboard.writeText(promotionCode);
    show_alert("Code copié", `Le code promotionnel "${promotionCode}" a été copié.`, "success");
}

// MARK: - Orders cancelation button
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

// MARK: - Orders modification functions
async function get_order_items(orderId) {
    try {
        const response = await fetch_orders_data(orderId, 'get_order_details');

        if(response.type === "error" || !response.order_data) {
            show_alert(response.title, response.message, response.type);
            return;
        }

        let orderItems = [];
        const orderDetails = response.order_data.details;
        for (const [item_id, item_quantity] of Object.entries(orderDetails)) {
            try {
                const response = await get_products_data('get_product', item_id);
                
                if(response.type === "error" || !response.product_data) {
                    show_alert(response.title, response.message, response.type);
                    return;
                }
                    
                const product = response.product_data;
                product.quantity = item_quantity;
                orderItems.push(product);
            } catch (error) {
                show_alert("Erreur", "Une erreur est survenue lors du chargement des détails de la commande. Veuillez réessayer", "error");
            }
        };

        return orderItems;
    } catch (error) {
        show_alert("Erreur", "Une erreur est survenue lors du chargement des détails de la commande. Veuillez réessayer", "error");
    }
}
async function updateOrder(orderId) {
    try {
        const response = await fetch_orders_data(orderId, 'update_order', {new_details: newOrdersItems});

        show_alert(response.title, response.message, response.type);
        if(response.type === "error") return;

        setTimeout(() => window.location.reload(), (alertDuration ?? 3000) / 2);
    } catch (error) {
        show_alert("Erreur", "Une erreur est survenue lors de la modification de la commande. Veuillez réessayer", "error");
        console.error(error.message);
    }
}

function create_modify_order_buttons(parentContainer, orderId) {
    const cardContainer = parentContainer.querySelector('.form__card');

    if(cardContainer) {
        // NOTE: Action Button Container creation
        const actBtnContainer = document.createElement('div');
        actBtnContainer.setAttribute('id', 'modification__actions');
        cardContainer.appendChild(actBtnContainer);

        // NOTE: Confirm modification button
        const confirmBtn = document.createElement('button');
        confirmBtn.setAttribute('class', 'btn btn-primary');
        confirmBtn.setAttribute('id', 'confirm_modification');
        confirmBtn.textContent = 'Valider • ';
        confirmBtn.style.display = 'none';

        const priceSpan = document.createElement('span');
        priceSpan.setAttribute('id', 'diffenrece_price');
        priceSpan.textContent = '+0.00 €';

        confirmBtn.appendChild(priceSpan);
        actBtnContainer.appendChild(confirmBtn);
        if(confirmBtn) confirmBtn.addEventListener('click', async () => updateOrder(orderId));

        // NOTE: Cancel modification button
        const cancelBtn = document.createElement('button');
        cancelBtn.setAttribute('class', 'btn btn-primary');
        cancelBtn.setAttribute('id', 'cancel_modification');
        cancelBtn.textContent = 'Annuler les modifications';

        actBtnContainer.appendChild(cancelBtn);
        if(cancelBtn) cancelBtn.addEventListener('click', async () => await modifyOrderSwitcher());
    }
}
async function load_modify_order_form(parentContainer, orderId) {
    const scrollableContainer = parentContainer.querySelector('.scrollable__container');
    const tempBasketContainer = document.createElement('div');
    tempBasketContainer.setAttribute('id', 'temp_basket_container');
    scrollableContainer.appendChild(tempBasketContainer);
    
    if(scrollableContainer && tempBasketContainer) {
        initialOrdersItems = await get_order_items(orderId);
        init_new_orders_variable(initialOrdersItems);

        createBasketItems(newOrdersItems, tempBasketContainer);
        create_modify_order_buttons(parentContainer, orderId);
    }
}
function clear_modify_order_form(parentContainer) {
    const cardContainer = parentContainer.querySelector('.form__card');
    const modificationActionsContainer = cardContainer.querySelector('#modification__actions');
    const tempBasketContainer = parentContainer.querySelector('#temp_basket_container');

    initialOrdersItems = [];
    newOrdersItems = [];
    if(tempBasketContainer) tempBasketContainer.remove();
    if(modificationActionsContainer) modificationActionsContainer.remove();
}

async function modifyOrderSwitcher(orderId = undefined) {
    const profileOrdersContainer = document.getElementById("profile__orders");
    const containerTitle = profileOrdersContainer.querySelector('.form__card > h2');
    const ordersTable = profileOrdersContainer.querySelector('.orders-table');
    
    if(ordersContainerSwitched) {
        if(containerTitle) containerTitle.textContent = "Historique des commandes";
        if(ordersTable) ordersTable.style.display = "table";

        clear_modify_order_form(profileOrdersContainer);
        ordersContainerSwitched = false;
    } else {
        if(containerTitle) containerTitle.textContent = "Modification de la commande #" + orderId;
        if(ordersTable) ordersTable.style.display = "none";

        await load_modify_order_form(profileOrdersContainer, orderId);
        ordersContainerSwitched = true;
    }
}

//MARK: - Initialize
if (editButton) editButton.addEventListener('click', async () => await toggleEditMode());

if(copyButtons) copyButtons.forEach(button => button.addEventListener('click', (event) => copyPromotionCode(event.currentTarget)));

if (modifyButtons.length > 0) {
    modifyButtons.forEach(button => button.addEventListener('click', async (event) => {
        const orderId = event.target.closest('.order__row').getAttribute('data-order-id');
        await modifyOrderSwitcher(orderId);
    }));
}
if (cancelButtons.length > 0) {
    cancelButtons.forEach(button => button.addEventListener('click', async (event) => {
        const orderRow = event.target.closest('.order__row');
        await cancelOrder(orderRow);
    }));
}