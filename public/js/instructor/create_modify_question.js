document.addEventListener("DOMContentLoaded", (event) => {

    let addbutton = document.querySelector(".addProposal")
    let removeButtons = document.querySelectorAll('.removeProposal')
    let li_proposal_v1 = document.querySelectorAll('.liProposal')
    addbutton.dataset.index = li_proposal_v1.length
    let indexData = 0
    let form = addbutton.dataset.form
    let ul = document.querySelector('#list_proposal')

// *******************************************************************************************************
// Add function
    function clickAdd(e) {
        // AJOUT D'UNE REPONSE
        li_form(ul)
    }

// *******************************************************************************************************
// Remove Function
    function clickRemove() {
        this.parentElement.remove()
        let alphabet = ['A','B','C','D','E','F']
        let p_letters = document.querySelectorAll('.pLetter')
        let count=0;
        p_letters.forEach(p =>{
            let letters = p.textContent
            p.innerHTML = alphabet[count];
            count ++
        })
        indexData --
    }


// *******************************************************************************************************
// li_form Function
    function li_form(ul){
        let li = document.createElement("li")

        // Replace
        li.innerHTML += form.replace(
            /__name__/g,
            indexData
        );
        li.className = "liProposal"

        let checkbox = li.firstElementChild.lastElementChild.lastElementChild
        checkbox.className = "isCorrect"

        let div_wording = li.firstElementChild.firstChild
        div_wording.className = 'divWording'

        let buttonRemoveNew = document.createElement("button")
        buttonRemoveNew.innerHTML = "Supprimer"
        buttonRemoveNew.classList.add("removeProposal")
        // Remove
        buttonRemoveNew.addEventListener("click", clickRemove)
        li.append(buttonRemoveNew)
        ul.append(li)

        // ************************************************************************************
        // AFFICHE LA LETTRE AU MOMENT DE L'AJOUT
        li.firstElementChild.className = "divProposal"

        letterProposal(li.firstElementChild, indexData,)

        // ************************************************************************************
        // INCREMENT LA LONGUEUR DU TABLEAU DES REPONSES
        indexData++
    }

// *******************************************************************************************************
// letterProposal Function
    function letterProposal(div_proposal, indexData){
        let label = div_proposal.firstElementChild.firstChild

        let alphabet = ['A','B','C','D','E','F','G','H']

        let end = parseInt(indexData,10) + 1 // 4 +1 = 5    '4' + 1 = 41
        let begin = indexData

        let letter = alphabet.slice(begin, end)

        let p = document.createElement('p')
        p.className = 'pLetter'
        p.innerHTML = letter

        div_proposal.firstElementChild.insertBefore(p,label)

    }

// *******************************************************************************************************
// Add
    addbutton.addEventListener("click", clickAdd)

// *******************************************************************************************************
//Remove
    for (const removeButton of removeButtons) {
        removeButton.addEventListener("click", clickRemove)
    }

// *******************************************************************************************************
// AFFICHE LA LETTRE DE LA REPONSE
    let div_proposal = document.querySelectorAll('.divProposal')
    Object.entries(div_proposal).forEach(([index, div])=>{
        letterProposal(div, indexData)
        indexData++
    })


// *******************************************************************************************************
// DIV POUR CSS
    let div = document.querySelector('#create_question_difficulty')
    let div_input_label;
    let label = div.querySelectorAll("label")
    let input = div.querySelectorAll('input')
    for (let i = 0; i < 3; i++){
        div_input_label = document.createElement('div')
        div_input_label.className = "divInputLabel"
        div_input_label.append(label[i],input[i])
        div.append(div_input_label);
    }
// *******************************************************************************************************
// IMAGE TREFFLES

    let badgesParent = document.getElementById('create_question_difficulty')
    let badges = document.querySelectorAll('.divInputLabel')
    for (const [key, value] of Object.entries(badges)) {
        let logo = document.createElement('img')
        logo.setAttribute("src", imgPath[key])
        badgesParent.insertBefore(logo, value)
    }


})





