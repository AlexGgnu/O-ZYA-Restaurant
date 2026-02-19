const searchButton = document.getElementById("search__button");
const searchBar_class = document.getElementById("search__container")?.classList;

if(searchButton && searchBar_class) {
    searchButton.addEventListener("click", () => {
        if(searchBar_class.contains("active")) searchBar_class.remove("active");
        else searchBar_class.add("active");
    })
}

const topButton = document.getElementById("top__button");
if(topButton) topButton.addEventListener("click", () => window.scrollTo({ top: 0, behavior: 'smooth' }));