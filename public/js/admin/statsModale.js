let closeStatsModaleBtn, statsModale, searchBtns, searchInput

function showStatsModale()
{
    let modaleTitle = "Recherchez un/une " + this.dataset.searchtype;
    statsModale.querySelector("h3").innerText = modaleTitle
    statsModale.style.display = 'flex';
}

function hideStatsModale(){
    statsModale.style.display = 'none';
}

function goSearch(){
    console.log(this.value);
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