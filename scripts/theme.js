const html = document.querySelector("html");
const colorSchemeToggle = document.getElementById("color-scheme-toggle");

function getCookie(cookieName) {
    let cookies = document.cookie.split("; ");

    for (let c of cookies) {
        let parts = c.split("=");
        let key = parts[0];
        let value = parts[1];

        if (key === cookieName) return value;
    }

    return null;
}

function toggleColorScheme() {
    if (html.getAttribute('color-scheme') === "dark" || getCookie("color-scheme") === "dark") colorScheme = "light";
    else colorScheme = "dark";

    document.cookie = `color-scheme=${colorScheme};  path=/`;
    html.setAttribute('color-scheme', colorScheme);
}

let colorScheme = getCookie("color-scheme") || "light";
html.setAttribute('color-scheme', colorScheme);
console.log(colorSchemeToggle);
if (colorSchemeToggle) colorSchemeToggle.addEventListener("click", toggleColorScheme);