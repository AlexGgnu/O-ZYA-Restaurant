let allProductsData = null;

function createCountrySection(country) {
    const countrySection = document.createElement('section');
    countrySection.classList.add('w-full');
    countrySection.innerHTML = `<h1 class="mb-24 ml-40">${country}</h1> `;

    const cardsWrapper = document.createElement('div');
    cardsWrapper.classList.add('cards__wrapper');
    countrySection.appendChild(cardsWrapper);

    const cardsTrack = document.createElement('div');
    cardsTrack.classList.add('cards__track', 'gap-24', 'ph-40', 'lg-grid-cols-2');
    cardsWrapper.appendChild(cardsTrack);

    return countrySection;
}

function createCard(product) {
    const card = document.createElement('div');
    card.classList.add('dishe__card');
    card.innerHTML = `
        <h3>${product.name}</h3>
        <img class="w-full object-contain object-center filter-drop-shadow" src="${product.image}" alt="${product.name}">
        <p class="text-sm text-center">${product.description}</p>
        <button class="btn btn-primary">JE COMMANDE • ${product.price}€</button>
    `;

    return card;
}

function renderCategory(productsCategory) {
    const productsCard = document.getElementById("products__card");
    if (!productsCard) return;

    productsCard.innerHTML = '';

    for (const country in allProductsData.products) {
        const selectedProducts = allProductsData.products[country][productsCategory];

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

function setupFilters() {
    const filterButtons = document.querySelectorAll('.filter__button');
    
    filterButtons.forEach(button => {
        button.addEventListener('click', (element) => {
            filterButtons.forEach(btn => btn.classList.remove('active'));
            
            const clickedBtn = element.currentTarget;
            const clickedBtnAttr = clickedBtn.getAttribute('data-products-category');

            clickedBtn.classList.add('active');
            if (clickedBtnAttr) renderCategory(clickedBtnAttr);
        });
    });
}

async function initProducts() {
    const successedDishes = document.getElementById('successed__dishes');
    const productsCard = document.getElementById("products__card");

    try {
        const response = await fetch("./data/products.json");
        allProductsData = await response.json();

        if (successedDishes) {
            const cardsTrack = successedDishes.querySelector('.cards__track');
            const allProducts = Object.values(allProductsData.products).flatMap(country => Object.values(country).flat()).flat();
            const successedProducts = allProducts.filter(product => product.isSuccessed === true);

            successedProducts.forEach(product => {
                const card = createCard(product);
                cardsTrack.appendChild(card);
            });
        } else if (productsCard) {
            renderCategory('dishes');
            setupFilters();
        }
    } catch (error) {
        console.error("[ERROR] - Products data loading: ", error);
    }
}

initProducts();