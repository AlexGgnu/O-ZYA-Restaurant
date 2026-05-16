const toggleStateButtons = document.querySelectorAll('.toggle__state__button');
const updateRoleButtons = document.querySelectorAll('.update__role__button');
const viewProfileButtons = document.querySelectorAll('.view__profile__button');

async function toggleAccountState(accountId) {
    try {
        const response = await fetch_accounts_data(accountId, 'toggle_state');

        console.log(`Account state toggled for account ID: ${accountId}`, response); // TODO: Create message card
        return response["new_state"];
    } catch (error) {
        console.error(`[ERROR] - Updating account state for account ID ${accountId}: `, error.message); // TODO: Create message card
    }
}

async function updateAccountRole(accountId, newRole) {
    try {
        const response = await fetch_accounts_data(accountId, 'update_role', newRole);

        console.log(`Role updated for account ID: ${accountId}`, response);  // TODO: Create message card
    } catch (error) {
        console.error(`[ERROR] - Updating role for account ID ${accountId}: `, error.message); // TODO: Create message card
    }
}

function viewAccountProfile(accountId) {
    console.log(`Viewing profile for account ID: ${accountId}`);
}

function initButtons() {
    toggleStateButtons.forEach(button => {
        button.addEventListener('click', async () => {
            const accountCard = button.closest('.account__card');
            const accountId = accountCard.getAttribute('data-account-id');

            $new_state = await toggleAccountState(accountId);

            if ($new_state !== undefined) {
                accountCard.setAttribute('data-account-state', $new_state);
                button.querySelector('span').textContent = $new_state === "unblocked" ? "Bloquer" : "Débloquer";
            }
        });
    });

    updateRoleButtons.forEach(select => {
        select.addEventListener('change', async () => {
            const accountCard = select.closest('.account__card');
            const accountId = accountCard.getAttribute('data-account-id');
            const newRole = select.value;

            await updateAccountRole(accountId, newRole);
        });
    });

    viewProfileButtons.forEach(button => {
        button.addEventListener('click', () => {
            const accountCard = button.closest('.account__card');
            const accountId = accountCard.getAttribute('data-account-id');

            viewAccountProfile(accountId);
        });
    });
}

initButtons();