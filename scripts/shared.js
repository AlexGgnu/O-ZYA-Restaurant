const topButton = document.getElementById("top__button");
if(topButton) topButton.addEventListener("click", () => window.scrollTo({ top: 0, behavior: 'smooth' }));