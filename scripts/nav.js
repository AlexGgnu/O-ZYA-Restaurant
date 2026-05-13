const header = document.querySelector("header");
const toggleBtn = document.getElementById("menu-toggle");
const topButton = document.getElementById("top__button");

function toggleMenu() {
    if(header.getAttribute('data-menu-state') === 'closed') header.setAttribute('data-menu-state', 'open');
    else header.setAttribute('data-menu-state', 'closed');
}

if (toggleBtn) toggleBtn.addEventListener("click", toggleMenu);
if(topButton) topButton.addEventListener("click", () => window.scrollTo({ top: 0, behavior: 'smooth' }));