const opinionSection = document.getElementById('opinions');
const modalSwitcherButton = document.querySelectorAll('.modal__switcher');
const randomOpinionWrapper = document.getElementById('random__opinion__wrapper');

// MARK: - Generate Opinion Card
function createItemHeader(opinion) {
    const header = document.createElement('div');
    header.classList.add('opinion__card__header');

    const name = document.createElement('h4');
    name.textContent = opinion.client_name;

    const ratingWrapper = document.createElement('div');
    ratingWrapper.classList.add('rating__wrapper');

    for(let i = 1; i <= 5; i++) {
        const ratingButton = document.createElement('button');
        ratingButton.classList.add('rating__button', 'btn', 'btn-svg');
        ratingButton.setAttribute('type', 'button');
        ratingButton.setAttribute('role', 'radio');
        ratingButton.setAttribute('data-checked', i <= opinion.note ? 'true' : 'false');
        ratingButton.setAttribute('data-rating', i);

        ratingButton.innerHTML = `
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512" fill="currentColor">
                <path d="M287.9 0c9.2 0 17.6 5.2 21.6 13.5l68.6 141.3 153.2 22.6c9 1.3 16.5 7.6 19.3 16.3s.5 18.1-5.9 24.5L433.6 328.4l26.2 155.6c1.5 9-2.2 18.1-9.7 23.5s-17.3 6-25.3 1.7l-137-73.2L151 509.1c-8.1 4.3-17.9 3.7-25.3-1.7s-11.2-14.5-9.7-23.5l26.2-155.6L31.1 218.2c-6.5-6.4-8.7-15.9-5.9-24.5s10.3-14.9 19.3-16.3l153.2-22.6L266.3 13.5C270.4 5.2 278.7 0 287.9 0zm0 79L235.4 187.2c-3.5 7.1-10.2 12.1-18.1 13.3L99 217.9 184.9 303c5.5 5.5 8.1 13.3 6.8 21L171.4 443.7l105.2-56.2c7.1-3.8 15.6-3.8 22.6 0l105.2 56.2L384.2 324.1c-1.3-7.7 1.2-15.5 6.8-21l85.9-85.1L358.6 200.5c-7.8-1.2-14.6-6.1-18.1-13.3L287.9 79z"/>
            </svg>
            <svg class="checked-star" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512" fill="currentColor">
                <path d="M316.9 18C311.6 7 300.4 0 288.1 0s-23.4 7-28.8 18L195 150.3 51.4 171.5c-12 1.8-22 10.2-25.7 21.7s-.7 24.2 7.9 32.7L137.8 329 113.2 474.7c-2 12 3 24.2 12.9 31.3s23 8 33.8 2.3l128.3-68.5 128.3 68.5c10.8 5.7 23.9 4.9 33.8-2.3s14.9-19.3 12.9-31.3L438.5 329 542.7 225.9c8.6-8.5 11.7-21.2 7.9-32.7s-13.7-19.9-25.7-21.7L381.2 150.3 316.9 18z"/>
            </svg>
        `;
        
        ratingWrapper.appendChild(ratingButton);
    }

    header.appendChild(name);
    header.appendChild(ratingWrapper);

    return header;
}
function createItemContent(opinion) {
    const comment = document.createElement('p');
    comment.textContent = opinion.commentaire;
    return comment;
}
function createOpinionCard(opinion) {
    const card = document.createElement('div');
    card.classList.add('opinion__card');

    const header = createItemHeader(opinion);
    const comment = createItemContent(opinion);

    card.appendChild(header);
    card.appendChild(comment);

    return card;
}

async function createRandomOpinionCard(wrapper) {
    try {
        const response = await fetch_notation_data('get=random');
            
        if(response.length === 0) {
            opinionSection.style.display = 'none';
            return;
        }

        wrapper.appendChild(createOpinionCard(response[0]));
    } catch (error) {
        opinionSection.style.display = 'none';
        return;
    }
}

// MARK: - Generate Modal Content
async function generateModalContent(modal) {
    const opinionsListWrapper = document.createElement('div');
    opinionsListWrapper.id = 'opinions__list__wrapper';

    const response = await fetch_notation_data('get=all');
        
    if(response.length === 0) {
        const emptyMessage = document.createElement('p');
        emptyMessage.textContent = "Aucun avis n'a encore été laissé. Soyez le premier à partager votre expérience !";
        opinionsListWrapper.appendChild(emptyMessage);
        modal.appendChild(opinionsListWrapper);
        return;
    }

    const opinionsList = document.createElement('ul');
    opinionsList.id = 'opinions__list__content';
    for (const opinion of response) {
        const opinionItem = document.createElement('li');
        const opinionCard = createOpinionCard(opinion);

        opinionItem.appendChild(opinionCard);
        opinionsList.appendChild(opinionItem);
        if(opinion !== response[response.length - 1]) opinionsList.appendChild(document.createElement('hr'));
    }

    opinionsListWrapper.appendChild(opinionsList);
    modal.appendChild(opinionsListWrapper);
}

// MARK: - Modal Switcher
function closeOpinionsModal() {
    const modal = document.querySelector('dialog');
    
    if(modal) {
        modal.close();
        document.body.removeChild(modal);
    }
}
async function openOpinionsModal() {
    const modal = document.createElement('dialog');
    modal.id = 'opinions__modal';
    modal.classList.add('form__card');
    modal.innerHTML = `
        <div id="opinions__modal__header">
            <h1>Tout les avis</h1>
            <button type="button" class="modal__switcher btn btn-svg btn-primary" data-modal-action="close">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 384 512" fill="currentColor">
                    <path d="M345 137c9.4-9.4 9.4-24.6 0-33.9s-24.6-9.4-33.9 0l-119 119L73 103c-9.4-9.4-24.6-9.4-33.9 0s-9.4 24.6 0 33.9l119 119L39 375c-9.4 9.4-9.4 24.6 0 33.9s24.6 9.4 33.9 0l119-119L311 409c9.4 9.4 24.6 9.4 33.9 0s9.4-24.6 0-33.9l-119-119L345 137z"/>
                </svg>
            </button>
        </div>
    `;

    try {
        await generateModalContent(modal);

        document.body.appendChild(modal);
        modal.showModal();

        const closeButton = modal.querySelector('button');
        closeButton.addEventListener('click', closeOpinionsModal);
    } catch (error) {
        console.error('Error loading opinions:', error);
        show_alert('Erreur', 'Une erreur est survenue lors du chargement des avis. Veuillez réessayer plus tard.', 'error');
        return;
    }
}
async function switchOpinionsModal(button) {
    const action = button.getAttribute('data-modal-action');

    if(action === 'open') await openOpinionsModal();
    else if(action === 'close') closeOpinionsModal();
}

// MARK: - Initialisation
if(modalSwitcherButton) modalSwitcherButton.forEach((button) => button.addEventListener('click', async () => switchOpinionsModal(button)));
if(randomOpinionWrapper) createRandomOpinionCard(randomOpinionWrapper);