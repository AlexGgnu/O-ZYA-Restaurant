function createCountrySection(country) {
    const countrySection = document.createElement('section');
    countrySection.classList.add('w-full');
    countrySection.innerHTML = `<h1 class="mb-24 ml-40">${country}</h1> `;

    const cardsWrapper = document.createElement('div');
    cardsWrapper.classList.add('cards__wrapper');
    countrySection.appendChild(cardsWrapper);

    const cardsTrack = document.createElement('div');
    cardsTrack.classList.add('cards__track', 'flex-row', 'gap-24', 'ph-40');
    cardsWrapper.appendChild(cardsTrack);

    return countrySection;
}

function createCard(product) {
    const card = document.createElement('div');
    card.classList.add('dishe__card');
    card.innerHTML = `
        <h3>${product.name}</h3>
        <img class="w-full object-contain object-center filter-drop-shadow" src="${product.image}" alt="${product.name}">
        <p>${product.description}</p>
        <button class="btn btn-primary">JE COMMANDE • ${product.price}€</button>
    `;

    return card;
}

async function displayingProducts() {
    const productsCard = document.getElementById("products__card");
    const successedDishes = document.getElementById('successed__dishes');

    fetch("./data/products.json")
        .then(response => response.json())
        .then(data => {
            if(successedDishes) {
                const cardsTrack = successedDishes.querySelector('.cards__track');
                const allProducts = Object.values(data.products).flatMap(country => Object.values(country).flat()).flat();
                const successedProducts = allProducts.filter(product => product.isSuccessed === true);

                successedProducts.forEach(product => {
                    const card = createCard(product);
                    cardsTrack.appendChild(card);
                });
            }
            else if(productsCard) {
                for (const country in data.products) {
                    const countrySection = createCountrySection(country);
                    const cardsTrack = countrySection.querySelector('.cards__track');
                    
                    productsCard.appendChild(countrySection);

                    for (const category in data.products[country]) {
                        data.products[country][category].forEach(product => {
                            const card = createCard(product);
                            cardsTrack.appendChild(card);
                        });
                    }

                    productsCard.appendChild(cardsTrack);
                }
            }
        });
}

displayingProducts();