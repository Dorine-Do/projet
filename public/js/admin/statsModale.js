let closeStatsModaleBtn, statsModale, searchBtns, searchInput, searchType, resultsContainer

function showStatsModale()
{
    searchType = this.dataset.searchtype
    let modaleTitle = "Recherchez un/une " + searchType;
    statsModale.querySelector("h3").innerText = modaleTitle
    statsModale.style.display = 'flex';
}

function hideStatsModale(){
    statsModale.style.display = 'none';
    searchType = ""
    searchInput.value = ""
    resultsContainer.innerHTML = ""
}

function goSearch(){
    fetch(`/admin/stats/fetch/search/${searchType}/${this.value}`, {method:"GET"})
        .then( data => data.json() )
        .then( searchResults => {
            resultsContainer.innerHTML = ""
            for (let i = 0; i < searchResults.length; i++){
                let result = document.createElement("li");
                if (searchType === "session"){
                    result.innerHTML = `<a href="/admin/stats/session/${searchResults[i].id}"> ${searchResults[i].name} </a>`
                }
                if (searchType === "apprenant"){
                    result.innerHTML = `<a href="/admin/stats/student/${searchResults[i].id}"> ${searchResults[i].firstName} ${searchResults[i].lastName} </a>`
                }
                if (searchType === "formateur"){
                    result.innerHTML = `<a href="/admin/stats/instructor/${searchResults[i].id}"> ${searchResults[i].firstName} ${searchResults[i].lastName} </a>`
                }
                resultsContainer.append(result);
            }
        } )
}

document.addEventListener("DOMContentLoaded", function () {
    closeStatsModaleBtn =document.getElementById("closeStatsModaleBtn");
    statsModale =document.getElementById("statsModale");
    searchBtns =document.getElementsByClassName("searchBtn");
    searchInput =document.querySelector("#statsModale input");
    resultsContainer =document.querySelector("#searchResults");
    closeStatsModaleBtn.addEventListener("click", hideStatsModale);



    for(let i = 0; i < searchBtns.length; i++) {
        searchBtns[i].addEventListener("click", showStatsModale);
    }

    searchInput.addEventListener("input", goSearch);
});