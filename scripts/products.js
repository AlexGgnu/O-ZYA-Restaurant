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
function createOrderButton(id, price) {
    const button = document.createElement('button');
    button.classList.add('btn', 'btn-primary');
    button.textContent = `JE COMMANDE • ${price}€`;
    button.setAttribute('data-product-id', id);

    return button;
}
function createCard(product) {
    const card = document.createElement('div');
    card.classList.add('dish__card');
    card.innerHTML = `
        <h3>${product.name}</h3>
        <img class="w-full min-h-0 object-contain object-center filter-drop-shadow" src="${product.image}" alt="${product.name}">
        <p class="text-sm text-center">${product.shortDescription}</p>
        ${createOrderButton(product.id, product.price).outerHTML}
    `;

    return card;
}
function createSpecialDishDetails(specialDish) {
    const detailsContainer = document.createElement('div');

    const titleElement = document.createElement('h2');
    titleElement.classList.add('text-primary', 'font-600');
    titleElement.textContent = specialDish.name;
    detailsContainer.appendChild(titleElement);

    const detailsElement = document.createElement('p');
    detailsElement.textContent = specialDish.longDescription || specialDish.shortDescription;
    detailsContainer.appendChild(detailsElement);

    return detailsContainer;
}
function createSpecialDishImage(specialDish) {
    const imageElement = document.createElement('img');
    imageElement.classList.add('w-full', 'object-contain', 'object-center', 'filter-drop-shadow');
    imageElement.src = specialDish.image;
    imageElement.alt = specialDish.name;

    return imageElement;
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
    const specialDishDetails = document.getElementById('special-dish');
    const specialDishImage = document.getElementById('special-dish-image');
    const successedDishes = document.getElementById('successed__dishes');
    const productsCard = document.getElementById("products__card");

    try {
        const response = await fetch("./data/products.json");
        allProductsData = await response.json();

        const allProducts = Object.values(allProductsData.products).flatMap(country => Object.values(country).flat()).flat();

        if (specialDishDetails && specialDishImage) {
            const specialDish = allProducts.find(product => product.isSpecialDish);

            specialDishDetails.appendChild(createSpecialDishDetails(specialDish));
            specialDishDetails.appendChild(createOrderButton(specialDish.id, specialDish.price));
            specialDishImage.appendChild(createSpecialDishImage(specialDish));
        }
        
        if (successedDishes) {
            const cardsTrack = successedDishes.querySelector('.cards__track');
            const successedProducts = allProducts.filter(product => product.isSuccessed);

            successedProducts.forEach(product => {
                const card = createCard(product);
                cardsTrack.appendChild(card);
            });
        }
        
        if (productsCard) {
            renderCategory('dishes');
            setupFilters();
        }
    } catch (error) {
        console.error("[ERROR] - Products data loading: ", error);
    }
}

initProducts();