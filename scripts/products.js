function createBuyButton(productData) {
    return `
        <button class="buy__button btn btn-primary" data-products-id="${productData.id}">
            JE COMMANDE • ${productData.price}€
        </button>
    `
}
function setupBuyButton() {
    const buyButton = document.querySelectorAll('.buy__button');
    
    buyButton.forEach(button => {
        button.addEventListener('click', async (element) => {
            const clickedBtn = element.currentTarget;
            const response = await fetch_basket_data('add', clickedBtn.getAttribute('data-products-id'));
            show_alert(response.title, response.message, response.type);
        });
    });
}

function createCard(productData) {
    const card = document.createElement('div');
    card.classList.add('dish__card');
    card.innerHTML = `
        <h3>${productData.name}</h3>
        <img src="${productData.image}" alt="${productData.name}">
        <p>${productData.shortDescription}</p>
        ${createBuyButton(productData)}
    `;
    return card;
}

function createCountrySection(country) {
    const countrySection = document.createElement('section');
    countrySection.innerHTML = `<h1>${country}</h1> `;
    const cardsWrapper = document.createElement('div');
    cardsWrapper.classList.add('cards__wrapper');
    countrySection.appendChild(cardsWrapper);
    const cardsTrack = document.createElement('div');
    cardsTrack.classList.add('cards__track');
    cardsWrapper.appendChild(cardsTrack);
    return countrySection;
}

function renderCategory(productsCategory, productsData) {
    console.log("renderCategory appelé avec:", productsCategory);
    const productsCard = document.getElementById("products__card");
    if (!productsCard) return;

    productsCard.innerHTML = '';

    if (productsCategory === 'menus') {
        const card1 = createCard({ id: "US_DI_01", name: "Menu USA 🇺🇸", image: "./assets/images/products/us/smash_burger.png", shortDescription: "Smash Burger + Onion Rings + Milkshake + Cheesecake", price: 28.90 });
        const card2 = createCard({ id: "IT_DI_01", name: "Menu Italie 🇮🇹", image: "./assets/images/products/it/pizza_margherita.png", shortDescription: "Pizza Margherita + Mozza Sticks + Spritz + Tiramisu", price: 26.50 });
        const card3 = createCard({ id: "JP_DI_02", name: "Menu Japon 🇯🇵", image: "./assets/images/products/jp/sushi_mix.png", shortDescription: "Sushi Mix + Thé Matcha + Mochi Glacé", price: 32.00 });
        productsCard.appendChild(card1);
        productsCard.appendChild(card2);
        productsCard.appendChild(card3);
        return;
    }

    for (const country in productsData.products) {
        const selectedProducts = productsData.products[country][productsCategory];
        if (selectedProducts && selectedProducts.length > 0) {
            const countrySection = createCountrySection(country);
            const cardsTrack = countrySection.querySelector('.cards__track');
            selectedProducts.forEach(product => {
                const card = createCard(product);
                cardsTrack.appendChild(card);
            });
            productsCard.appendChild(countrySection);
        }
    }
}

function setupFilters(productsData) {
    const filterButtons = document.querySelectorAll('.filter__button');
    filterButtons.forEach(button => {
        button.addEventListener('click', (element) => {
            filterButtons.forEach(btn => btn.classList.remove('active'));
            const clickedBtn = element.currentTarget;
            const clickedBtnAttr = clickedBtn.getAttribute('data-products-category');
            clickedBtn.classList.add('active');
            if (clickedBtnAttr) {
                renderCategory(clickedBtnAttr, productsData);
                setupBuyButton();
            }
        });
    });
}

function createProductsPage(productsData) {
    if (!productsData) return;
    renderCategory('dishes', productsData);
    setupFilters(productsData);
}

function createSpecialDish(specialDish, productData) {
    if (!specialDish) return;
    const specialDishContent = specialDish.querySelector('#special__dish__content');
    if (specialDishContent) {
        specialDishContent.appendChild(document.createElement('div')).innerHTML = `
            <div>
                <h2>${productData.name}</h2>
                <p>${productData.longDescription}</p>
            </div>
            ${createBuyButton(productData)}
         `;
    }
    const specialDishImage = specialDish.querySelector('#special__dish__img');
    if (specialDishImage) {
        specialDishImage.src = productData.image;
        specialDishImage.alt = productData.name;
    }
}

function createSuccessedDish(successedDishes, productsData) {
    if (!successedDishes) return;
    const cardsTrack = successedDishes.querySelector('.cards__track');
    productsData.forEach(product => {
        const card = createCard(product);
        cardsTrack.appendChild(card);
    });
}

async function initProducts() {
    const specialDish = document.getElementById('special__dish');
    const successedDishes = document.getElementById('successed__dishes');
    const productsCard = document.getElementById("products__card");

    try {
        if (specialDish) {
            const specialDishData = await get_products_data('getSpecialDish');
            if (specialDishData) createSpecialDish(specialDish, specialDishData);
            else specialDish.style.display = 'none';
        }

        if (successedDishes) {
            const successedDishesData = await get_products_data('getSuccessedDishes');
            if (successedDishesData) createSuccessedDish(successedDishes, successedDishesData);
            else successedDishes.style.display = 'none';
        }

        if (productsCard) {
            const productsData = await get_products_data('getAllProducts');
            if (productsData) createProductsPage(productsData);
            else productsCard.style.display = 'none';
        }

        setupBuyButton();

        document.getElementById('menu__aleatoire__btn')?.addEventListener('click', async () => {
        const dish = await fetch_menu_aleatoire();
        if (dish && !dish.error) {
            const response = await fetch_basket_data('add', dish.id);
             show_alert(dish.name, dish.shortDescription, "success");
         }
    });

    } catch (error) {
        show_alert("Erreur de chargement", "Une erreur est survenue lors du chargement des données des produits. Veuillez réessayer plus tard.", "error");
    }
}

initProducts();