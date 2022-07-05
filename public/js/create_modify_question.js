document.addEventListener("DOMContentLoaded", (event) => {

    let addbutton = document.querySelector(".addProposal")
    let removeButtons = document.querySelectorAll('.removeProposal')
    let li_proposal_v1 = document.querySelectorAll('.li_proposal')
    addbutton.dataset.index = li_proposal_v1.length
    let indexData = addbutton.dataset.index
    console.log(indexData)
    let form = addbutton.dataset.form
    let validate = document.querySelector(".valid")
    let ul = document.querySelector('#list_proposal')

    console.log(li_proposal_v1.length)


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
        let p_letters = document.querySelectorAll('.p_letter')
        let count=0;
        p_letters.forEach(p =>{
            let letters = p.textContent
            p.innerHTML = alphabet[count];
            count ++
        })
        indexData --
    }

// *******************************************************************************************************
// isChecked Function
    function isChecked(e){
        e.preventDefault()
        let checkbox = document.querySelectorAll('.isCorrect')
        let check = []
        checkbox.forEach(box => {
            if(box.checked === true){
                check.push(true)
            }else{
                check.push(false)
            }
        })
        let bool = check.indexOf(true)
        if(bool !== -1){
            document.forms[0].submit()
        }else{
            let p = document.createElement('p')
            p.innerHTML = 'Veuillez cocher au moins une réponse correcte pour cette question'
            let td_proposals = document.getElementById('errorChecked')
            td_proposals.append(p)
            p.className = 'errorP'
        }
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
        li.className = "li_proposal"

        let checkbox = li.firstElementChild.lastElementChild.lastElementChild
        checkbox.className = "isCorrect"

        let div_wording = li.firstElementChild.firstChild
        div_wording.className = 'div_wording'

        let buttonRemoveNew = document.createElement("button")
        buttonRemoveNew.innerHTML = "Supprimer"
        buttonRemoveNew.classList.add("removeProposal")
        // Remove
        buttonRemoveNew.addEventListener("click", clickRemove)
        li.append(buttonRemoveNew)
        ul.append(li)

        // ************************************************************************************
        // AFFICHE LA LETTRE AU MOMENT DE L'AJOUT
        li.firstElementChild.className =  "div_proposal"

        letterProposal(li.firstElementChild, indexData)

        // ************************************************************************************
        // INCREMENT LA LONGUEUR DU TABLEAU DES REPONSES
        indexData++
    }

// *******************************************************************************************************
// letterProposal Function
    function letterProposal(div_proposal, indexData){
        let textarea = div_proposal.firstElementChild.lastChild

        let alphabet = ['A','B','C','D','E','F']

        let end = parseInt(indexData,10) + 1 // 4 +1 = 5    '4' + 1 = 41
        let begin = indexData

        let letter = alphabet.slice(begin, end)

        let p = document.createElement('p')
        p.className = 'p_letter'
        p.innerHTML = letter

        div_proposal.firstElementChild.insertBefore(p,textarea)
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
//Validate
//     validate.addEventListener("click", isChecked)



// *******************************************************************************************************
// AFFICHE LA LETTRE DE LA REPONSE EN DEHORS DU TEXTEAREA
//     if(add){
//         for (let i = 0; i < 2 ; i++){
//             li_form(ul)
//         }
//     }

    // let div_proposal = document.querySelectorAll('.div_proposal')
    // Object.entries(div_proposal).forEach(([index, div])=>{
    //
    //     let checkbox = div.lastElementChild.lastElementChild
    //     checkbox.className = 'isCorrect'
    //
    //     if(!add){
    //         letterProposal(div, index)
    //     }
    // })


// *******************************************************************************************************
// DIV POUR CSS
    let div = document.querySelector('#create_question_difficulty')
    console.log(div)
    let div_input_label;
    let label = div.querySelectorAll("label")
    let input = div.querySelectorAll('input')
    for (let i = 0; i < 3; i++){

        div_input_label = document.createElement('div')
        div_input_label.className = "div_input_label"
        div_input_label.append(label[i],input[i])
        div.append(div_input_label);
    }
// *******************************************************************************************************
// REPLACE __name__

    // li_proposal_v1.forEach((value, index) => {
    //     // Replace
    //     value.innerHTML = form.replace(
    //         /__name__/g,
    //         index
    //     );
    //     value.className = "li_proposal"
    //
    //     let checkbox = value.firstElementChild.lastElementChild.lastElementChild
    //     checkbox.className = "isCorrect"
    //
    //     let div_wording = value.firstElementChild.firstChild
    //     div_wording.className = 'div_wording'
    //
    //     let buttonRemoveNew = document.createElement("button")
    //     buttonRemoveNew.innerHTML = "Supprimer"
    //     buttonRemoveNew.classList.add("removeProposal")
    //     // Remove
    //     buttonRemoveNew.addEventListener("click", clickRemove)
    //     value.append(buttonRemoveNew)
    // })


    let badgesParent = document.getElementById('create_question_difficulty')
    let badges = document.querySelectorAll('.div_input_label')
    console.log(badges)
    for (const [key, value] of Object.entries(badges)) {
        let logo = document.createElement('img')
        logo.setAttribute("src", imgPath[key])
        badgesParent.insertBefore(logo, value)
        console.log(value)
    }


})





