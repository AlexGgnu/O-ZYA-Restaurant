const searchButton = document.getElementById("search__button");
const searchBar_class = document.getElementById("search__container").classList;

searchButton.addEventListener("click", () => {
    if(searchBar_class.contains("active")) searchBar_class.remove("active");
    else searchBar_class.add("active");
})