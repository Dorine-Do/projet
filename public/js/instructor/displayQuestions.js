// //ONLOAD ET NON DOM CHARGEMENT 1 FOIS ET NON 2 COMME DOM CAR ERREUR ET PROBLEME D AFFICHAGE
window.onload = function (event) {
    console.log(questions)

    let spanFlashAddIns = document.querySelector(".flashNotice div");
    let addFlashIns = document.querySelector(".flashNotice ");
    let spanFlashQuestionPerso = document.querySelector(".flashNoticeQuestionPerso div");
    let addFlashQuestionPerso = document.querySelector(".flashNoticeQuestionPerso ");

    ///////////////////////FLASH MESSAGE
    if (spanFlashAddIns) {
        spanFlashAddIns.addEventListener("click", function () {
            addFlashIns.style.display = "none";
        });
    }
    if (spanFlashQuestionPerso) {
        spanFlashQuestionPerso.addEventListener("click", function () {
            addFlashQuestionPerso.style.display = "none";
        });
    }
    //////////////ESPACE AFTER BALISE PRE

    let divsWordingQuestion = document.querySelectorAll('.divWordingQuestion')
    divsWordingQuestion.forEach( (div) => {
        console.log(div.lastElementChild)
        if (div.lastElementChild !== null){
            if (div.lastElementChild.firstElementChild === null){
                div.lastElementChild.remove()
            }
        }

    })

    //////////////PROPOSALS
    let divProposals = document.querySelectorAll(".divProposals");
    divProposals.forEach( (div) => {
        let count = 0;
        Array.from(div.children).forEach( (divJs) => {
            let alphabet = ["A", "B", "C", "D", "E", "F", "G", "H"];
            let end = parseInt(count, 10) + 1; // 4 +1 = 5    '4' + 1 = 41
            let begin = count;
            let letter = alphabet.slice(begin, end);
            let p = divJs.firstElementChild
            p.innerText = letter;
            // INSERTION DU PLETTER DEVANT LE PWORDING

            divJs.insertBefore(p, divJs.lastElementChild);
            count++
        })
    })




    let chevrons = document.querySelectorAll(".imgChevron");

    // let p_prop;
    // for (const question of questions) {
    //
    //     let id = parseInt(e.target.dataset.id);
    //
    //     if (id === proposal.id_question) {
    //
    //
    //
    //
    //         p_prop = document.createElement("p");
    //         p_prop.innerHTML = proposal.wording;
    //         p_prop.classList.add("blocContentProposal");
    //
    //         let div_container = document.createElement("div")
    //         div_container.className = "divProp"
    //
    //
    //
    //         count++;
    //     }
    // }

    chevrons.forEach((chevron) => {
        chevron.addEventListener("click", (e) => {
            let liQuestion = e.target.parentElement.parentElement.parentElement.parentElement;
            let blocDivProposal = liQuestion.querySelector('.blocDivProposal')
            blocDivProposal.classList.toggle('displayNone')
            e.target.classList.toggle('rotate')

        });
    });

};
