document.addEventListener("DOMContentLoaded", (event) => {

    let addbutton = document.querySelector(".addProposal")
    let removeButtons = document.querySelectorAll('.removeProposal')
    let index = addbutton.dataset.index
    let form = addbutton.dataset.form
    let validate = document.querySelector(".valid")
    let ul = document.querySelector('#list_proposal')

// Add function

    function clickAdd(e) {
        // console.log(e.target.dataset.form)

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
        index --
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
            let div_proposal = document.getElementById('errorChecked')
            div_proposal.append(p)
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
            index
        );
        li.className = "li_proposal"
        console.log(li)

        console.log(li.firstElementChild)

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

        letterProposal(li.firstElementChild, index)

        // ************************************************************************************
        // INCREMENT LA LONGUEUR DU TABLEAU DES REPONSES
        index++
        // console.log(index)
    }

    function letterProposal(div_proposal, index){
        console.log(index)
        let textarea = div_proposal.firstElementChild.lastChild

        let alphabet = ['A','B','C','D','E','F']

        let end = parseInt(index,10) + 1 // 4 +1 = 5    '4' + 1 = 41
        let begin = index

        let letter = alphabet.slice(begin, end)
        // console.log(letter)

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

    validate.addEventListener("click", isChecked)


// *******************************************************************************************************
// AFFICHE LA LETTRE DE LA REPONSE EN DEHORS DU TEXTEAREA
    if(add){
        for (let i = 0; i < 2 ; i++){
            li_form(ul)
        }
    }else {

    }
    let div_proposal = document.querySelectorAll('.div_proposal')
    Object.entries(div_proposal).forEach(([index, div])=>{

        let checkbox = div.lastElementChild.lastElementChild
        checkbox.className = 'isCorrect'

        if(!add){
            letterProposal(div, index)
        }

    })


    // let div_proposal = document.querySelectorAll('.div_proposal')
    // console.log(div_proposal)
    // console.log(div_proposal[0].firstElementChild.lastChild)

    // div_proposal.forEach(div=>{

        // let textarea = div.firstElementChild.lastChild
        // let textareaContent = textarea.textContent



        // let letter = textareaContent.slice(0,1)
        // console.log(letter)
        // let arrayTexteareaContent = textareaContent.split('')
        // arrayTexteareaContent.splice(0,2)
        // arrayTexteareaContent = arrayTexteareaContent.join('')
        // console.log(arrayTexteareaContent)

        // textarea.innerHTML = arrayTexteareaContent

        // let p = document.createElement('p')
        // p.className = 'p_letter'
        // p.innerHTML = letter
        //
        // div.firstElementChild.insertBefore(p,textarea)
    // })



})



