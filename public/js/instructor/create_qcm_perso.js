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

    /***************************************************************************/
    let qcmChoisedMainSide = document.querySelector('.qcmChoisedMain')
    calcNbrQuestionByLevel(qcmChoisedMainSide)


    let questionsOfficial = document.querySelector(".questionsOfficial")
    let buttonQuestionType = document.querySelectorAll('.btnChoiseQuestionType button')


    //Event faire apparaitre la partie 2********************************************************************************
    let btnCustom = document.querySelector('.btnCustom')
    btnCustom.addEventListener('click',(e)=>{
        let partTwo  = document.querySelector('.partTwo')
        partTwo.classList.remove('displayNone')
    })


    // Event sur les button de choix du type de question****************************************************************
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

    // Event sur les chevrons*******************************************************************************************
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

    // event pour modifier une question*********************************************************************************
    let modifyQuestionImgDiv = document.querySelectorAll('.modifyQuestionImgDiv')
    modifyQuestionImgDiv.forEach(div => {
        div.addEventListener('click', (e)=>{
            let img = e.target
            img.parentNode.previousElementSibling.firstElementChild.setAttribute('src', chevronHaut)
            img.parentNode.classList.add('displayNone')

            let divParent = img.parentNode.parentNode
            let question = divParent.querySelector('.questionWordingP')
            let proposalWordingDiv  = img.parentNode.parentNode.parentNode.lastElementChild.lastElementChild
            let questionModify = img.parentNode.parentNode.parentNode.lastElementChild.lastElementChild.lastElementChild

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

    // Event Enregistrer une question***********************************************************************************
    let saveBtns = document.querySelectorAll('.save')
    saveBtns.forEach(btn =>{
        btn.addEventListener('click',(e)=>{
            let officialQuestionLi = e.target.parentNode.parentNode.parentNode.parentNode
            let question = officialQuestionLi.querySelector('.officialQuestionWordingP ')
            let questionId = question.dataset.id
            let div = e.target.parentNode.parentNode
            let modifyP = div.querySelectorAll('.modifyP')
            let module = document.querySelector('.qcmNameInput').dataset.module

            let values =
                {
                    'questionId' : questionId,
                    'module' : module,
                    'wording' : question.textContent.trim(),
                    'proposals' : []
                }
            let countCorrectAnswer = 0;
            modifyP.forEach( p =>{
                values.proposals.push({
                    'wording' : p.children[1].textContent.trim(),
                    'id': p.children[1].parentNode.dataset.id,
                    'isCorrectAnswer': p.children[2].checked
                })
                if (p.children[2].checked){
                    countCorrectAnswer++
                }
            })
            if (countCorrectAnswer > 1){
                values.isMultiple = true
            }else{
                values['isMultiple'] = false

            }


            fetch( route, {
                method: 'POST',
                body:  JSON.stringify(values), // The data
                headers: {
                    'Content-type': 'application/json' // The type of data you're sending
                }
            })
                .then( response => response.json() )
                .then( data => console.log(data) )
        })
    })

    // Event pour supprimer une question côté qcm choisi
    // let btnDeleteQuestion = document.querySelectorAll('.x')
    // btnDeleteQuestion.forEach(btn =>{
    //     btn.addEventListener('click',(e)=>{
    //         e.target.parentNode.remove()
    //     })
    // })


    //Event select li to move*******************************************************************************************
    let liDiv = document.querySelectorAll('.officialQuestionLi , .qcmChoisedLi ')
    liDiv.forEach(li => {
        li.addEventListener('click', (e)=>{
            if(e.target.tagName !== 'IMG'){
                li.classList.toggle('borderColor')
            }
        })
    })

    // Event arrow right and left************************************************************************
    /*
        Les questions dans la partie qcm choisie sont aussi dans la liste des questions que se soit custom ou officielle
        Quand l'utilisateur déplace un question du qcm vers la liste de question est supprimer pour pas avoir de doublons
     */
    let arrowRight = document.querySelector('.arrowRight')
    let arrowLeft = document.querySelector('.arrowLeft')

    let questionsOfficialSide = document.querySelector('.questionsOfficial')
    let qcmChoisedMain = document.querySelector('.qcmChoisedMain')

    //arrowLeft QcmChoised -> questions
    arrowLeft.addEventListener('click', (e)=>{
        let firstLi = questionsOfficialSide.firstElementChild.firstElementChild
        let ulQuestionsOfficialSide = questionsOfficialSide.firstElementChild

        let qcmChoisedLis = qcmChoisedMain.querySelectorAll('.borderColor')
        qcmChoisedLis .forEach(li => {

            //questions officelles
            if (li.classList.contains('officialQuestionLi')){
                let allP = li.firstElementChild.children
                // Bouton modifier la question
                for (let i = 0 ; i < allP.length ; i++){
                    if(allP[i].tagName === 'DIV'){
                        allP[i].classList.remove('displayNone')
                    }
                }
                let qcmChoisedLi = li
                li.remove()
                ulQuestionsOfficialSide.insertBefore(qcmChoisedLi, firstLi)
            }else{
                li.remove()
            }
        })
        let elementSelect = document.querySelectorAll('.borderColor')
        elementSelect.forEach(el => {
            el.classList.remove('borderColor')
        })

        calcNbrQuestionByLevel(qcmChoisedMain)
    })

    //arrowRight qcmChoised <- question
    arrowRight.addEventListener('click', (e)=>{

        let firstLi = qcmChoisedMain.firstElementChild.firstElementChild
        let ulQcmChoisedSide = qcmChoisedMain.firstElementChild

        let officialLis = questionsOfficialSide.querySelectorAll('.borderColor')
        officialLis.forEach(li =>{

            //questions officelles
            if (li.classList.contains('officialQuestionLi')){
                let allP = li.firstElementChild.children
                // Bouton modifier la question
                for (let i = 0 ; i < allP.length ; i++){
                    if(allP[i].tagName === 'DIV'){
                        allP[i].classList.add('displayNone')
                    }
                }
                let officialLi = li
                li.remove()
                ulQcmChoisedSide.insertBefore(officialLi, firstLi)
            }
        })

        let elementSelect = document.querySelectorAll('.borderColor')
        elementSelect.forEach(el => {
            el.classList.remove('borderColor')
        })

        calcNbrQuestionByLevel(qcmChoisedMain)
    })


    // Event Validation qcm
    let qcmValidationBtn = document.querySelector('.qcmValidation')
    qcmValidationBtn.addEventListener('click', (e)=>{
        let questionsSelect = {};
        let qcmNameInput = document.querySelector('.qcmNameInput').value
        let module = document.querySelector('.qcmNameInput').dataset.module
        let isPublic = document.getElementById('isPublicInput').checked
        let qcmChoisedLevel = document.getElementById('qcmChoisedLevel').textContent.trim()
        questionsSelect = {
            'name' : qcmNameInput,
            'level' : qcmChoisedLevel,
            'module' : module,
            'isPublic' : isPublic,
            'questions' : []
        }
        let questions = document.querySelectorAll('.qcmChoisedQuestionWordingDiv')
        questions.forEach(question => {
            let level = question.firstElementChild.firstElementChild.dataset.level
            let wording = question.children[1].textContent.trim()
            let id = question.children[1].dataset.id
            questionsSelect['questions'].push({
                'id': id,
                'level' : level,
                'wording' : wording
            })
        })

        fetch( routeInstructorQcmFetch, {
            method: 'POST',
            body:  JSON.stringify(questionsSelect), // The data
            headers: {
                'Content-type': 'application/json' // The type of data you're sending
            }
        })

    })


/**********************************************************************************************************************/
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
        let parentQuestion = e.target.parentNode.parentNode.parentNode
        let question = parentQuestion.querySelector('.questionWordingP')
        let proposalWordingDiv = parent.querySelector('.proposalWordingDiv')
        let divParentP = proposalWordingDiv.parentNode

        //Css
        proposalWordingDiv.style.flexDirection = 'column'
        let nPropPartTwo = proposalWordingDiv.querySelectorAll('.nPropPartTwo')
        nPropPartTwo.forEach(nbr => {
            nbr.style.padding = '2px 12px'
        })
        parentQuestion.firstElementChild.lastElementChild.classList.remove('displayNone')

        proposalWordingDiv.remove()

        let div = document.createElement('div')
        div.classList.add('proposalWordingDiv', 'OfficialProposalWordingDiv')
        divParentP.append(div)

        let id = question.dataset.id
        let questionToEdit = dbValues.filter( question => {
            return question.idQuestion === id
        });
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

    function calcNbrQuestionByLevel(side){

        let easyLevel = 0;
        let mediumLevel = 0;
        let difficultyLevel = 0;

        let questions = side.querySelectorAll( '.qcmChoisedTrefle' )
        console.log(questions)
        questions.forEach(question => {
            if (question.dataset.level === 'easy'){
                easyLevel ++
            }else if(question.dataset.level === 'medium'){
                mediumLevel ++
            }else{
                difficultyLevel ++
            }
        })

        let pEasy = document.getElementById('easy')
        console.log(pEasy)
        pEasy.innerHTML = easyLevel
        let pMedium = document.getElementById('medium')
        console.log(pMedium)
        pMedium.innerHTML = mediumLevel
        let pDifficult = document.getElementById('difficulty')
        console.log(pDifficult)
        pDifficult.innerHTML = difficultyLevel


        let qcmChoisedLevel = document.getElementById('qcmChoisedLevel')
        if (difficultyLevel > mediumLevel && difficultyLevel > mediumLevel){
            qcmChoisedLevel.innerHTML = 'Difficile'
        }else if(mediumLevel > difficultyLevel && mediumLevel > easyLevel){
            qcmChoisedLevel.innerHTML = 'Moyen'
        }else{
            qcmChoisedLevel.innerHTML = 'Facile'
        }

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











