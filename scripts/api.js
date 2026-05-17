// MARK: - Accounts Endpoint
function get_accounts_url() {
    const baseUrl = window.location.origin;
    return `${baseUrl}/api/account.php`;
}

async function fetch_accounts_data(accountId, action, value = undefined) {
    const url = get_accounts_url();
    // NOTE: If value is an object, we need to serialize it before sending it in the request body.
    const serializedValue = typeof value === 'object' ? JSON.stringify(value) : value;

    try {
        const response = await fetch(url, {
            method: "POST",
            headers: { "Content-Type": "application/x-www-form-urlencoded" },
            body: `account_id=${encodeURIComponent(accountId)}&action=${encodeURIComponent(action)}&value=${encodeURIComponent(serializedValue)}`
        });

        if (!response.ok) { show_alert("Erreur", "Une erreur est survenue lors de la récupération des données du compte. Veuillez réessayer.", "error"); }

        const result = await response.json();
        return result;
    } catch (error) {
        show_alert("Erreur", "Une erreur est survenue lors de la récupération des données du compte. Veuillez réessayer.", "error");
        throw error;
    }
}

// MARK: - Products Endpoint
function get_products_url(action) {
    const baseUrl = window.location.origin;
    return `${baseUrl}/api/products.php?action=${action}`;
}

async function get_products_data(action) {
    const url = get_products_url(action);

    try {
        const response = await fetch(url, { method: "GET" });

        if (!response.ok) { show_alert("Erreur", "Une erreur est survenue lors de la récupération des données des produits. Veuillez réessayer.", "error"); }

        const result = await response.json();
        return result;
    } catch (error) {
        show_alert("Erreur", "Une erreur est survenue lors de la récupération des données des produits. Veuillez réessayer.", "error");
        throw error;
    }
}

// MARK: - Basket Endpoint
function get_basket_url(action, value = undefined) {
    const baseUrl = window.location.origin;
    return `${baseUrl}/api/basket.php?${action}${value !== undefined ? `=${encodeURIComponent(value)}` : ''}`;
}

async function fetch_basket_data(action, value = undefined) {
    const url = get_basket_url(action, value);

    try {
        const response = await fetch(url, { method: "GET" });

        if (!response.ok) { show_alert("Erreur", "Une erreur est survenue lors de la récupération des données du panier. Veuillez réessayer.", "error"); }

        const result = await response.json();
        return result;
    } catch (error) {
        show_alert("Erreur", "Une erreur est survenue lors de la récupération des données du panier. Veuillez réessayer.", "error");
        throw error;
    }
}

// MARK: - Orders Endpoint
function get_orders_url() {
    const baseUrl = window.location.origin;
    return `${baseUrl}/api/order.php`;
}

async function fetch_orders_data(orderId, action, value = undefined) {
    const url = get_orders_url();
    // NOTE: If value is an object, we need to serialize it before sending it in the request body.
    const serializedValue = typeof value === 'object' ? JSON.stringify(value) : value;

    try {
        const response = await fetch(url, {
            method: "POST",
            headers: { "Content-Type": "application/x-www-form-urlencoded" },
            body: `order_id=${encodeURIComponent(orderId)}&action=${encodeURIComponent(action)}&value=${encodeURIComponent(serializedValue)}`
        });

        if (!response.ok) { show_alert("Erreur", "Une erreur est survenue lors de la récupération des données des commandes. Veuillez réessayer.", "error"); }

        const result = await response.json();
        return result;
    } catch (error) {
        show_alert("Erreur", "Une erreur est survenue lors de la récupération des données des commandes. Veuillez réessayer.", "error");
        throw error;
    }
}

// MARK:- delivery endpoint
function get_delivery_url() {
    const baseUrl = window.location.origin;
    return `${baseUrl}/api/delivery.php`;
}

async function fetch_delivery_data(deliveryId, action, value = undefined) {
    const url = get_delivery_url();
    // NOTE: If value is an object, we need to serialize it before sending it in the request body.
    const serializedValue = typeof value === 'object' ? JSON.stringify(value) : value;

    try {
        const response = await fetch(url, {
            method: "POST",
            headers: { "Content-Type": "application/x-www-form-urlencoded" },
            body: `delivery_id=${encodeURIComponent(deliveryId)}&action=${encodeURIComponent(action)}&value=${encodeURIComponent(serializedValue)}`
        });

        if (!response.ok) { show_alert("Erreur", "Une erreur est survenue lors de la récupération des données de livraison. Veuillez réessayer.", "error"); }

        const result = await response.json();
        return result;
    } catch (error) {
        show_alert("Erreur", "Une erreur est survenue lors de la récupération des données de livraison. Veuillez réessayer.", "error");
        throw error;
    }
}