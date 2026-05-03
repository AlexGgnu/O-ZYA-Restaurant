let bouton = document.getElementById("theme-btn");

function getCookie(nom) {
    let cookies = document.cookie.split("; ");
    for (let c of cookies) {
        let parts = c.split("=");
        let key = parts[0];
        let value = parts[1];

if (key === nom) {
    return value;
}
    }
    return null;
}

function mettreTheme() {
    if (!document.getElementById("dark-mode")) {
        let link = document.createElement("link");
        link.rel = "stylesheet";
        link.href = "./styles/dark.css";
        link.id = "dark-mode";
        document.head.appendChild(link);
    }
}

function enleverTheme() {
    let link = document.getElementById("dark-mode");
    if (link) link.remove();
}

if (bouton) {
    bouton.addEventListener("click", function () {

        let themeActuel = getCookie("theme");

        if (themeActuel === "dark") {
            document.cookie = "theme=light;  path=/";
            enleverTheme();
        } else {
            document.cookie = "theme=dark; path=/";
            mettreTheme();
        }
    });
}

if (getCookie("theme") === "dark") {
    mettreTheme();
}