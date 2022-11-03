let closeStatsModaleBtn, statsModale, searchBtns, searchInput, searchType

function showStatsModale()
{
    searchType = this.dataset.searchtype
    let modaleTitle = "Recherchez un/une " + searchType;
    statsModale.querySelector("h3").innerText = modaleTitle
    statsModale.style.display = 'flex';
}

function hideStatsModale(){
    statsModale.style.display = 'none';
}

function goSearch(){
    fetch(`/admin/stats/fetch/search/${searchType}/${this.value}`, {method:"GET"})
        .then( data => data.json() )
        .then( searchResults => {
            console.log(searchResults)
        } )
}

document.addEventListener("DOMContentLoaded", function () {
    closeStatsModaleBtn =document.getElementById("closeStatsModaleBtn");
    statsModale =document.getElementById("statsModale");
    searchBtns =document.getElementsByClassName("searchBtn");
    searchInput =document.querySelector("#statsModale input");

    closeStatsModaleBtn.addEventListener("click", hideStatsModale);



    for(let i = 0; i < searchBtns.length; i++) {
        searchBtns[i].addEventListener("click", showStatsModale);
    }

    searchInput.addEventListener("input", goSearch);
});