const editButton = document.getElementById("edit__button");
const editIcon = document.getElementById("edit__icon");
const checkIcon = document.getElementById("check__icon");
if (!inputs) inputs = document.querySelectorAll("#profile__info__content input");

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

if (editButton) editButton.addEventListener('click', async () => await toggleEditMode());