document.addEventListener("DOMContentLoaded", (event) => {
    // display none
    let questionsCustom = document.querySelector(".questionsCustom")
    questionsCustom.classList.add('displayNone')

    let proposalWordingDiv = document.querySelectorAll('.proposalWordingDiv')
    proposalWordingDiv.forEach(div=>{
        div.classList.add('displayNone')
    })

    let questionModify = document.querySelectorAll('.questionModify')
    questionModify.forEach(question=>{
        question.classList.add('displayNone')
    })


    let questionsOfficial = document.querySelector(".questionsOfficial")
    let buttonQuestionType = document.querySelectorAll('.btnChoiseQuestionType button')

    // Event sur les button de choix du type de question
    buttonQuestionType.forEach(button=>{
        button.addEventListener('click',(e)=>{
            let choiceBtn = e.target
            let otherBtn;
            if (e.target.previousElementSibling === null){
                otherBtn = choiceBtn.nextElementSibling
            }else{
                otherBtn = choiceBtn.previousElementSibling
            }

            if (choiceBtn.className === 'btnQuestionsOfficial'){
                questionsCustom.classList.add('displayNone')
                questionsOfficial.classList.remove('displayNone')
                choiceBtn.style.backgroundColor = '#FFAC8F'
                otherBtn.style.backgroundColor = '#93AD6E'
            }else{
                questionsOfficial.classList.add('displayNone')
                questionsCustom.classList.remove('displayNone')
                choiceBtn.style.backgroundColor = '#93AD6E'
                otherBtn.style.backgroundColor = '#FFAC8F'
            }
        })
    })

    // Event sur les chevrons
    let chevrons = document.querySelectorAll('.chevron');

    chevrons.forEach( chevron => {
        chevron.addEventListener('click',(e)=>{
            let proposalWordingDiv;
            if (e.target.classList.contains('officialChevronBasImg')){
                proposalWordingDiv = e.target.parentNode.parentNode.parentNode.lastElementChild.firstElementChild
            }else{
                proposalWordingDiv = e.target.parentNode.parentNode.parentNode.lastElementChild
            }

            proposalWordingDiv.classList.toggle('displayNone')

            if(proposalWordingDiv.classList.contains('displayNone')){
                e.target.setAttribute('src', chevronBas)
            }else{
                e.target.setAttribute('src', chevronHaut)
            }
        })
    })


    // event pour modifier une question
    let modifyQuestionImgDiv = document.querySelectorAll('.modifyQuestionImgDiv')
    modifyQuestionImgDiv.forEach(div => {
        div.addEventListener('click', (e)=>{
            let target = e.target
            let proposalWordingDiv  = e.target.parentNode.parentNode.parentNode.lastElementChild.lastElementChild
            let questionModify = e.target.parentNode.parentNode.parentNode.lastElementChild.lastElementChild.lastElementChild

            // si la div des proposal n'est pas dérouler
            if (proposalWordingDiv.classList.contains('displayNone')){
                proposalWordingDiv.classList.remove('displayNone');
            }
            // Afficher le bouton 'enregistrer'
            questionModify.classList.remove('displayNone')

            // Si la question a 6 proposal
            let children = proposalWordingDiv.children
            if (children.length <= 6){
                let buttonAdd = document.createElement('button')
                buttonAdd.setAttribute('class','buttonAddProp')
                buttonAdd.setAttribute('id','buttonAdd')
                buttonAdd.innerHTML= 'Ajouter une réponse'
                buttonAdd.addEventListener('click', (e)=>{
                    addProposal(e,proposalWordingDiv, children.length)
                })
                proposalWordingDiv.insertBefore(buttonAdd,questionModify );
            }

            //Modification de la balise p
            for (let i = 0; i < children.length-2; i++){
                children[i].classList.add('modifyP')
                let id = children[i].dataset.id

                // textarea
                let value = children[i].lastChild.textContent.trim()
                console.log(value.length)
                console.log(value)
                let textarea = document.createElement('textarea')
                textarea.textContent = value
                textarea.setAttribute('name', id)

                // img
                let img = document.createElement('img')
                img.setAttribute('src',deleteImg)
                img.classList.add('deleteProp')
                img.addEventListener('click',deleteProp)

                children[i].lastChild.remove()
                children[i].append(textarea)
                children[i].append(img)

            }
        })
    })

    let saveBtns = document.querySelectorAll('.save')
    saveBtns.forEach(btn =>{
        btn.addEventListener('click',(e)=>{
            let inputs = e.target.parentNode.children

        })
    })

    function addProposal(e,parent, lengthProp){

        let pProp = document.createElement('p')
        pProp.classList.add('officialProposalWordingP', 'proposalWordingP', 'modifyP')

        let textarea = document.createElement('textarea')
        textarea.setAttribute('name', 'newProp')

        let img = document.createElement('img')
        img.setAttribute('src', deleteImg)
        img.classList.add('deleteProp')
        img.addEventListener('click', deleteProp)

        let span = document.createElement('span')
        span.classList.add('numeroProp', 'nPropPartTwo')

        // Trouver la lettre
        let alphabet = ['A','B','C','D','E','F']
        let end = parseInt(lengthProp-2,10) + 1 // 4 +1 = 5    '4' + 1 = 41
        let begin = lengthProp-2
        let letter = alphabet.slice(begin, end)
        span.innerHTML = letter

        pProp.append(span, textarea, img)
        parent.insertBefore(pProp, e.target)

        //Si < 6 réponses
        if (parent.children.length-2 >= 6){
            e.target.classList.add('displayNone')
            let message = document.createElement('p')
            message.classList.add('message')
            message.textContent = 'Le nombre maximal de 6 réponses possibles pour une question a été atteint'
            parent.append(message)
        }

    }

    function deleteProp(e){
        let div = e.target.parentNode.parentNode
        e.target.parentNode.remove()
        let p = div.querySelectorAll('.modifyP')
        let message;
        //Si reponse < 6
        if (typeof message !== undefined ){
            message = div.querySelector('.message')
            message.remove()
            let buttonAddProp = div.querySelector('.buttonAddProp')
            buttonAddProp.classList.remove('displayNone')
        }

        //Réactualiser les lettres des réponses
        let alphabet = ['A','B','C','D','E','F']
        let spanLetters = div.querySelectorAll('.nPropPartTwo')
        let count=0;
        spanLetters.forEach(p =>{
            p.innerHTML = alphabet[count];
            count ++
        })

    }




})











