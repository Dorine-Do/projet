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

    let partTwo = document.querySelector('.partTwo')
    partTwo.classList.add('displayNone')

    let questionsOfficial = document.querySelector(".questionsOfficial")
    let buttonQuestionType = document.querySelectorAll('.btnChoiseQuestionType button')


    //Event faire apparaitre la partie 2
    let btnCustom = document.querySelector('.btnCustom')
    btnCustom.addEventListener('click',(e)=>{
        let partTwo  = document.querySelector('.partTwo')
        partTwo.classList.remove('displayNone')
    })


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

    let dbValues = [] ;

    // event pour modifier une question
    let modifyQuestionImgDiv = document.querySelectorAll('.modifyQuestionImgDiv')
    modifyQuestionImgDiv.forEach(div => {
        div.addEventListener('click', (e)=>{
            let target = e.target
            let divParent = e.target.parentNode.parentNode
            console.log(divParent)
            let question = divParent.querySelector('.questionWordingP')
            let proposalWordingDiv  = e.target.parentNode.parentNode.parentNode.lastElementChild.lastElementChild
            let questionModify = e.target.parentNode.parentNode.parentNode.lastElementChild.lastElementChild.lastElementChild

            //Css
            proposalWordingDiv.style.flexDirection = 'column'
            let nPropPartTwo = proposalWordingDiv.querySelectorAll('.nPropPartTwo')
            nPropPartTwo.forEach(nbr => {
                nbr.style.padding = '2px 10px'
            })

            // si la div des proposal n'est pas dérouler
            if (proposalWordingDiv.classList.contains('displayNone')){
                proposalWordingDiv.classList.remove('displayNone');
            }
            // Afficher le bouton 'enregistrer'
            questionModify.classList.remove('displayNone')

            // Si la question a 6 proposal
            let children = proposalWordingDiv.querySelectorAll('.officialProposalWordingP')
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

            // Bouton cancel
            let p = document.createElement('p')
            p.classList.add('cancel')
            p.innerHTML = 'Annuler'
            proposalWordingDiv.append(p)
            p.addEventListener('click', cancelModifyQuestion)

            // Get db data
            let questionId = question.dataset.id
            dbValues.push({
                'idQuestion' : questionId,
                'proposals' : []
            })

            //Modification de la balise p
            for (let i = 0; i < children.length; i++){
                children[i].classList.add('modifyP')
                let id = children[i].dataset.id

                // textarea
                let value = children[i].lastChild.textContent.trim()
                let textarea = document.createElement('textarea')
                textarea.textContent = value
                textarea.setAttribute('name', id)

                let checkBox = document.createElement('input')
                checkBox.setAttribute('type', 'checkbox')
                checkBox.classList.add('checkBoxIsCorrect')

                // img
                let img = document.createElement('img')
                img.setAttribute('src',deleteImg)
                img.classList.add('deleteProp')
                img.addEventListener('click',deleteProp)

                children[i].lastChild.remove()
                children[i].append(textarea, checkBox)
                children[i].append(img)

                //Save former values of question

                dbValues.forEach(cel=>{
                    console.log(cel)
                    if(cel.idQuestion === questionId){
                        cel.proposals.push({
                            'id' : id,
                            'value' : value
                        })
                    }
                })
            }
        })
    })

    // Event Enregistrer une question
    let saveBtns = document.querySelectorAll('.save')
    saveBtns.forEach(btn =>{
        btn.addEventListener('click',(e)=>{
            let officialQuestionLi = e.target.parentNode.parentNode.parentNode.parentNode
            let question = officialQuestionLi.querySelector('.officialQuestionWordingP ')
            let questionId = question.dataset.id
            let div = e.target.parentNode.parentNode
            let inputs = div.querySelectorAll('.modifyP textarea')

            let values = [
                {'keyWord' : 'update'},
                {'questionId' : questionId}
            ];
            inputs.forEach(input =>{
                values.push({
                    'value' : input.value,
                    'id': input.parentNode.dataset.id
                })


            })

            fetch( route, {
                method: 'POST',
                body: values, // The data
                headers: {
                    'Content-type': 'application/json' // The type of data you're sending
                }
            })
                .then(response=>response.json())
                .then(data=>console.log(data))
        })
    })

    // Event pour supprimer une question côté qcm choisi
    let btnDeleteQuestion = document.querySelectorAll('.x')
    btnDeleteQuestion.forEach(btn =>{
        btn.addEventListener('click',(e)=>{
            e.target.parentNode.remove()
        })
    })



/*****************************************************************************************************************/
    function addProposal(e,parent, lengthProp){

        let pProp = document.createElement('p')
        pProp.classList.add('officialProposalWordingP', 'proposalWordingP', 'modifyP')

        let textarea = document.createElement('textarea')
        textarea.setAttribute('name', 'newProp')

        let checkBox = document.createElement('input')
        checkBox.setAttribute('type', 'checkbox')
        checkBox.classList.add('checkBoxIsCorrect')

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

        pProp.append(span, textarea,checkBox, img)
        parent.insertBefore(pProp, e.target)

        let children = parent.querySelectorAll('.modifyP')

        //Si < 6 réponses
        if (children.length >= 6){
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

    function cancelModifyQuestion(e){
        let parent =  e.target.parentNode.parentNode
        let question = document.querySelector('.questionWordingP')
        let proposalWordingDiv = parent.querySelector('.proposalWordingDiv')
        let divParentP = proposalWordingDiv.parentNode

        //Css
        proposalWordingDiv.style.flexDirection = 'column'
        let nPropPartTwo = proposalWordingDiv.querySelectorAll('.nPropPartTwo')
        nPropPartTwo.forEach(nbr => {
            nbr.style.padding = '2px 12px'
        })

        proposalWordingDiv.remove()

        let div = document.createElement('div')
        div.classList.add('proposalWordingDiv', 'OfficialProposalWordingDiv')
        divParentP.append(div)

        let id = question.dataset.id
        let questionToEdit = dbValues.filter( question => question.id === id);

        questionToEdit[0].proposals.forEach(function callback(value, index) {
            let span = document.createElement('span')
            span.classList.add('numeroProp', 'nPropPartTwo')

            // Trouver la lettre
            let alphabet = ['A','B','C','D','E','F']
            let end = parseInt(index,10) + 1 // 4 +1 = 5    '4' + 1 = 41
            let begin = index
            let letter = alphabet.slice(begin, end)
            span.innerHTML = letter

            let p = document.createElement('p')
            p.classList.add('officialProposalWordingP','proposalWordingP')
            p.innerHTML = value.value
            p.dataset.id = value.id

            div.append(span ,p)
        })





    }


})



/*
/*
definiton des variables et recup des data


let dbValues = [
    {
        id: 15,
        proposals: [
            {
                id: 2,
                wording: 'rfreqeg',
                isCorrectAnswer: true
            },
            {
                id: 3,
                wording: 'rfreqeg',
                isCorrectAnswer: true
            },
        ]
    },
    {
        id: 16,
        proposals: [
            {
                id: 4,
                wording: 'rfreqeg',
                isCorrectAnswer: true
            },
            {
                id: 5,
                wording: 'rfreqeg',
                isCorrectAnswer: true
            },
        ]
    }
];

let valuesToEdit = [];

let cancelBtn;

function editQuestion()
{
    let questionToEdit = dbValues.filter( question => question.id === document.querySelector().data('id'));

    let editedQuestion = {
        id: this.data('id'),
        proposals: [
            {
                id: 4,
                wording: 'rfreqeg',
                isCorrectAnswer: true
            },
            {
                id: 5,
                wording: 'rfreqeg',
                isCorrectAnswer: true
            },
        ]
    };

    valuesToEdit.push( question );
}

function cancelEditQuestion()
{
    let questionToCancelEdition = valuesToEdit.filter( question => question.id === document.querySelector().data('id'));

}

 */











