// MARK: - Products Endpoint
function get_products_url(action) {
    const baseUrl = window.location.origin;
    return `${baseUrl}/api/products.php?action=${action}`;
}

async function get_products_data(action) {
    const url = get_products_url(action);

    try {
        const response = await fetch(url, { method: "GET" });

        if (!response.ok) { throw new Error(`Response status: ${response.status}`); }

        const result = await response.json();
        return result;
    } catch (error) {
        console.error("[ERROR] - Products data endpoint: ", error.message);
        throw error;
    }
}

// MARK: - Basket Endpoint
function get_basket_url(action, uuid) {
    const baseUrl = window.location.origin;
    return `${baseUrl}/api/basket.php?${action}=${uuid}`;
}

async function post_bascket_data(action) {
    const url = get_basket_url(action);

    try {
        const response = await fetch(url, { method: "GET" });

        if (!response.ok) { throw new Error(`Response status: ${response.status}`); }

        const result = await response.json();
        return result;
    } catch (error) {
        console.error("[ERROR] - Products data endpoint: ", error.message);
        throw error;
    }
}