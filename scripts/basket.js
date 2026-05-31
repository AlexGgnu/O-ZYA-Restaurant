const isBasketPage = window.location.pathname.endsWith('basket.php');

// MARK: - Basket items functions
function createBasketItem(product) {
    const item = document.createElement('div');
    item.classList.add('basket__item');
    item.setAttribute('data-product-id', product.id);

    item.innerHTML = `
        <div class="basket__item__info">
            <img src="${product.image}" alt="${product.name}" class="basket__item-image">
        
            <div class="basket__item__details">
                <h4 class="basket__item__name">${product.name}</h4>
                <p class="basket__item__price">${(product.price * product.quantity).toFixed(2)} €</p>
            </div>
        </div>

        <div class="basket__item__quantity">
            <button class="quantity__button btn btn-primary" data-action="decrease">-</button>
            <span class="quantity__value">${product.quantity}</span>
            <button class="quantity__button btn btn-primary" data-action="increase">+</button>
        </div>
    `;

    return item;
}

function setupQuantityButtons(products = undefined, productsListContainer = undefined) {
    const quantityButtons = document.querySelectorAll('.quantity__button');

    quantityButtons.forEach(button => {
        button.addEventListener('click', async (event) => {
            const clickedButton = event.currentTarget;
            const action = clickedButton.getAttribute('data-action');
            const productId = clickedButton.closest('.basket__item').getAttribute('data-product-id');

            if(isBasketPage || !products) {
                try {
                    if (action === 'increase') await fetch_basket_data('add', productId);
                    else if (action === 'decrease') await fetch_basket_data('remove', productId);

                    load_basket_items();
                } catch (error) {
                    show_alert("Erreur", "Une erreur est survenue lors de la mise à jour de la quantité du produit. Veuillez réessayer.", "error");
                }
            } else if(products && productsListContainer) {
                const productIndex = products.findIndex(product => product.id == productId);
                if (productIndex === -1) return;

                const product = {...products[productIndex]}; 

                if (action === 'increase') product.quantity += 1;
                else if (action === 'decrease' && product.quantity > 0) product.quantity -= 1;

                if (product.quantity === 0) {
                    const neProductsList = products.filter(p => p.id != productId);
                    neProductsList.forEach((p, i) => products[i] = p);
                    products.length = neProductsList.length;
                } else {
                    products[productIndex] = product;
                }

                createBasketItems(products, productsListContainer);
            }
        });
    });
}

function createBasketItems(products, container) {
    container.innerHTML = '';

    products.forEach(product => {
        const item = createBasketItem(product);

        container.appendChild(item);
        if (product !== products[products.length - 1]) container.appendChild(document.createElement('hr'));
    });
    
    setupQuantityButtons(products, container);
}

// MARK: - Basket summary functions
function setupDeliveryOptionChange(products, reduction) {
    const deliverySelect = document.getElementById('delivery_type');

    deliverySelect.addEventListener('change', async (event) => {
        const selectedOption = event.target.value;

        try {
            await fetch_basket_data('update_delivery', selectedOption);
            createBasketSummary(products, selectedOption, reduction);
        } catch (error) {
            show_alert("Erreur", "Une erreur est survenue lors de la mise à jour du type de livraison. Veuillez réessayer.", "error");
        }
    });
}


function setupPromotionButton(products, deliveryType) {
    const promoInput = document.getElementById('promotion__code');
    const promoButton = document.getElementById('promo__button');

    promoButton.addEventListener('click', async () => {
        const promoCode = promoInput.value.trim();

        if(promoCode) {
            try {
                const reduction = await fetch_basket_data('promo_code', promoCode);
                if(reduction) createBasketSummary(products, deliveryType, reduction);
                else show_alert("Code promotionnel invalide", "Le code promotionnel que vous avez entré n'est pas valide. Veuillez réessayer.", "error");
            } catch (error) {
                show_alert("Erreur", "Une erreur est survenue lors de l'application de la promotion. Veuillez réessayer.", "error");
            }
        }

        promoInput.value = '';
    });
}

function setupPickupDateChange() {
    const pickupInput = document.getElementById('pickup_datetime');

    if (!pickupInput) return;

    const now = new Date();
    now.setMinutes(now.getMinutes() + 30);

    pickupInput.min = now.toISOString().slice(0, 16);

    pickupInput.addEventListener('change', async (event) => {
        try {
            await fetch_basket_data(
                'update_pickup',
                event.target.value
            );

            show_alert(
                "Date enregistrée",
                "La date de récupération a été enregistrée",
                "success"
            );
        }
        catch(error) {
            show_alert(
                "Erreur",
                "Impossible d'enregistrer la date",
                "error"
            );
        }
    });
}

function createBasketSummary(products, deliveryType = "", reduction = 0, pickupDatetime = "") {
    const deliveryOptions = document.querySelectorAll('select#delivery_type option');
    const subtotalElement = document.getElementById('subtotal__price');
    const promoSummaryElement = document.getElementById('promo__summary');
    const totalElement = document.getElementById('total__price');
    const pickupInput = document.getElementById('pickup_datetime');

    deliveryOptions.forEach(option => {
        if(option.value === deliveryType) option.selected = true;
        else option.selected = false;
    });

    const subtotal = products.reduce((total, product) => total + (product.price * product.quantity), 0);
    const promo = (subtotal - reduction) < 0 ? subtotal : reduction;
    const total = subtotal - promo;

    subtotalElement.textContent = `${subtotal.toFixed(2)} €`;
    promoSummaryElement.textContent = promo > 0 ? `- ${promo.toFixed(2)} €` : "Aucune promotion appliquée";
    totalElement.textContent = `${total.toFixed(2)} €`;

    setupDeliveryOptionChange(products, reduction);
    setupPromotionButton(products, deliveryType);
    setupPickupDateChange();
}

// MARK: - Initial loading
async function load_basket_items() {
    const basketItemsContainer = document.querySelector('#basket__items .scrollable__container');

    try {
        const basketItems = await fetch_basket_data('get');

        if (basketItems.items && basketItems.items.length > 0) {
            createBasketItems(basketItems.items, basketItemsContainer);
            createBasketSummary(basketItems.items, basketItems.delivery_type ?? "", basketItems.promo_code ?? 0, basketItems.pickup_datetime ?? "");
        } else {
            const basketSummary = document.getElementById('basket__summary');

            basketItemsContainer.innerHTML = "<p>Votre panier est vide</p>";
            if(basketSummary) basketSummary.classList.add('hidden');
        }
    } catch (error) {
        show_alert("Erreur", "Une erreur est survenue lors du chargement des éléments du panier. Veuillez réessayer.", "error");
    }
}

// MARK: - Initial setup
if(isBasketPage) load_basket_items();