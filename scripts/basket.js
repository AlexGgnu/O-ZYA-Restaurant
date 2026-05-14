const basketItemsContainer = document.querySelector('#basket__items #scrollable__container');
const basketSummary = document.getElementById('basket__summary');
const deliveryOptions = document.querySelectorAll('select#delivery_type option');
const subtotalElement = document.getElementById('subtotal__price');
const promoSummaryElement = document.getElementById('promo__summary');
const totalElement = document.getElementById('total__price');

// MARK: - Basket items functions
function createBasketItem(product) {
    const item = document.createElement('div');
    item.classList.add('basket__item');

    item.innerHTML = `
        <div class="basket__item__info">
            <img src="${product.image}" alt="${product.name}" class="basket__item-image">
        
            <div class="basket__item__details">
                <h4 class="basket__item__name">${product.name}</h4>
                <p class="basket__item__price">${(product.price * product.quantity).toFixed(2)} €</p>
            </div>
        </div>

        <div class="basket__item__quantity">
            <button class="quantity__button btn btn-primary" data-action="decrease" data-product-id="${product.id}">-</button>
            <span class="quantity__value">${product.quantity}</span>
            <button class="quantity__button btn btn-primary" data-action="increase" data-product-id="${product.id}">+</button>
        </div>
    `;

    return item;
}

function setupQuantityButtons() {
    const quantityButtons = document.querySelectorAll('.quantity__button');

    quantityButtons.forEach(button => {
        button.addEventListener('click', async (event) => {
            const clickedButton = event.currentTarget;
            const action = clickedButton.getAttribute('data-action');
            const productId = clickedButton.getAttribute('data-product-id');

            try {
                if (action === 'increase') await fetch_bascket_data('add', productId);
                else if (action === 'decrease') await fetch_bascket_data('remove', productId);

                load_basket_items();
            } catch (error) {
                console.error(`[ERROR] - Updating quantity for product ID ${productId}: `, error.message); // TODO: Create message card
            }
        });
    });
}

function createBasketItems(products) {
    basketItemsContainer.innerHTML = '';

    products.forEach(product => {
        const item = createBasketItem(product);

        basketItemsContainer.appendChild(item);
        if (product !== products[products.length - 1]) basketItemsContainer.appendChild(document.createElement('hr'));
    });
    
    setupQuantityButtons();
}

// MARK: - Basket summary functions
function setupDeliveryOptionChange(products, reduction) {
    const deliverySelect = document.getElementById('delivery_type');

    deliverySelect.addEventListener('change', async (event) => {
        const selectedOption = event.target.value;
        console.log("Selected delivery option: ", selectedOption);

        try {
            await fetch_bascket_data('update_delivery', selectedOption);
            createBasketSummary(products, selectedOption, reduction);
        } catch (error) {
            console.error(`[ERROR] - Updating delivery type: `, error.message); // TODO: Create message card
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
                const reduction = await fetch_bascket_data('promo_code', promoCode);
                if(reduction) createBasketSummary(products, deliveryType, reduction);
            } catch (error) {
                console.error(`[ERROR] - Applying promotion: `, error.message); // TODO: Create message card
            }
        }

        promoInput.value = '';
    });
}

function createBasketSummary(products, deliveryType = "", reduction = 0) {
    deliveryOptions.forEach(option => {
        if(option.value === deliveryType) option.selected = true;
        else option.selected = false;
    });

    const subtotal = products.reduce((total, product) => total + (product.price * product.quantity), 0);
    const promo = subtotal * (reduction / 100);
    const total = subtotal - promo;

    subtotalElement.textContent = `${subtotal.toFixed(2)} €`;
    promoSummaryElement.textContent = promo > 0 ? `- ${promo.toFixed(2)} €` : "Aucune promotion appliquée";
    totalElement.textContent = `${total.toFixed(2)} €`;

    setupDeliveryOptionChange(products, reduction);
    setupPromotionButton(products, deliveryType);
}

// MARK: - Initial loading
async function load_basket_items() {
    try {
        const basketItems = await fetch_bascket_data('get');

        if (basketItems.items && basketItems.items.length > 0) {
            createBasketItems(basketItems.items);
            createBasketSummary(basketItems.items, basketItems.delivery_type ?? "", basketItems.promo_code ?? 0);
        } else {
            basketItemsContainer.innerHTML = "<p>Votre panier est vide</p>";
            basketSummary.classList.add('hidden');
        }
    } catch (error) {
        console.error("[ERROR] - Basket data loading: ", error.message);
    }
}

load_basket_items();