const header = document.querySelector("header");
const toggleBtn = document.getElementById("menu-toggle");

function toggleMenu() {
    if(header.getAttribute('data-menu-state') === 'closed') header.setAttribute('data-menu-state', 'open');
    else header.setAttribute('data-menu-state', 'closed');
}

if (toggleBtn) toggleBtn.addEventListener("click", toggleMenu);