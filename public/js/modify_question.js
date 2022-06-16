document.addEventListener("DOMContentLoaded", (event) => {
    let count = 0
    let addbutton = document.querySelector(".addProposal")
    let removeButtons = document.querySelectorAll('.removeProposal')

// Add function
    function clickAdd(e) {
        console.log(e.target.dataset.form)

        let ul = document.querySelector('#list_proposal')
        let li = document.createElement("li")

        // Replace
        li.innerHTML += e.target.dataset.form.replace(
            /__name__/g,
            addbutton.dataset.index
        );
        console.log(li)
        let buttonRemoveNew = document.createElement("button")
        buttonRemoveNew.innerHTML = "Supprimer"
        buttonRemoveNew.classList.add("removeProposal")
        // Remove
        buttonRemoveNew.addEventListener("click", clickRemove)
        li.append(buttonRemoveNew)
        ul.append(li)
        console.log(addbutton.dataset.index)
        addbutton.dataset.index++
    }

// *******************************************************************************************************

// Remove Function
    function clickRemove() {
        this.parentElement.remove()
    }

// *******************************************************************************************************

// Add
    addbutton.addEventListener("click", clickAdd)
// *******************************************************************************************************

//Remove

    for (const removeButton of removeButtons) {
        console.log(removeButton)
        removeButton.addEventListener("click", clickRemove)
    }
})