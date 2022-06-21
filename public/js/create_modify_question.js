document.addEventListener("DOMContentLoaded", (event) => {

    let addbutton = document.querySelector(".addProposal")
    let removeButtons = document.querySelectorAll('.removeProposal')
    let index = addbutton.dataset.index

// Add function
    function clickAdd(e) {
        // console.log(e.target.dataset.form)

        // AJOUT D'UNE REPONSE
        let ul = document.querySelector('#list_proposal')
        let li = document.createElement("li")

        // Replace
        li.innerHTML += e.target.dataset.form.replace(
            /__name__/g,
            index
        );
        li.className = "li_proposal"
        // console.log(li)
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

        let div_proposal = li.firstElementChild
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

        // ************************************************************************************
        // INCREMENT LA LONGUEUR DU TABLEAU DES REPONSES
        index++
        // console.log(index)

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
// Add
    addbutton.addEventListener("click", clickAdd)

// *******************************************************************************************************
//Remove

    for (const removeButton of removeButtons) {
        removeButton.addEventListener("click", clickRemove)
    }

// *******************************************************************************************************
// AFFICHE LA LETTRE DE LA REPONSE EN DEHORS DU TEXTEAREA
    let div_proposal = document.querySelectorAll('.div_proposal')
    // console.log(div_proposal)
    // console.log(div_proposal[0].firstElementChild.lastChild)

    div_proposal.forEach(div=>{

        let textarea = div.firstElementChild.lastChild
        let textareaContent = textarea.textContent

        let letter = textareaContent.slice(0,1)
        // console.log(letter)
        let arrayTexteareaContent = textareaContent.split('')
        arrayTexteareaContent.splice(0,2)
        arrayTexteareaContent = arrayTexteareaContent.join('')
        // console.log(arrayTexteareaContent)

        textarea.innerHTML = arrayTexteareaContent

        let p = document.createElement('p')
        p.className = 'p_letter'
        p.innerHTML = letter

        div.firstElementChild.insertBefore(p,textarea)
    })

})



