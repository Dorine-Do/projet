// TODO
// info bouton modif une question retiré
// faire un event au survol pour signifier ce changement


// REFACTO TOTALE DU FICHIER
let moduleOption, difficultyOptions, selectedModule, selectedDifficulty, generateQcmBtn, generationErrorBlock;
let generatedQcmResumeBlock, showGeneratedQcmResumeBtn, validateQcmButtonWhitoutChange;
let personalizeQcmBlock, personalizeQcmBtn, pickableOfficialQuestionsList, pickableCustomQuestionsList, pickedQuestionsList;
let pickableCustomQuestionsBnt, pickableOfficialQuestionsBnt, moveTopickableQuestionsListBtn, moveTopickedQuestionsListBtn;
let qcmValidationBtn
let ulQcm, questionLi

const letters = ['A', 'B', 'C', 'D', 'E', 'F'];
const difficultyImages =
    [
        'trefle_facile_bon_vert.1ad9fa1e.png',
        'trefle_moyen_bon_vert.37c52905.png',
        'trefle_difficile_bon_vert.73839ef1.png'
    ]
const levels = ['easy', 'medium', 'difficult']

function generateRandomQcm()
{
    if( selectedModule && ( selectedDifficulty === '1' || selectedDifficulty === '2' || selectedDifficulty === '3' ) )
    {
        generationErrorBlock.innerText = '';
        generatedQcmResumeBlock.classList.add('displayNone')
        personalizeQcmBlock.classList.add('displayNone')
        ulQcm.innerHTML = ''
        pickableOfficialQuestionsList.innerHTML = ''
        pickedQuestionsList.innerHTML = ''
        fetchGeneratedQcm();
    }
    else
    {
        displayGenerationError(generationErrorBlock, 'Vueillez selectionner un module ET une difficulté');
    }
}

function displayGenerationError(element, text)
{
    element.classList.remove('displayNone')
    element.innerText = text;
}

function fetchGeneratedQcm()
{
    fetch(`/instructor/qcms/random_fetch/${selectedModule}/${selectedDifficulty}`, {method: 'GET'})
        .then( response => response.json() )
        .then( data => {
            let generatedQcmQuestions = Object.values(data.generatedQcmQuestions);
            let officialQuestions = Object.values(data.officialQuestions);
            let instructorQuestions = Object.values(data.instructorQuestions);
            fillGeneratedQcmResumeBlock( generatedQcmQuestions );
            fillQcmPersonalizer( officialQuestions, instructorQuestions, generatedQcmQuestions )
            generatedQcmResumeBlock.classList.remove('displayNone')
            smoothScrollTo('#generatedQcmResumeBlock');
        })
}

function fetchCreateQcmPerso()
{
    let questionsSelect = {};
    let choosenQcmName = document.querySelector("#chosenQcmName").value;

    if (!choosenQcmName)
    {
        let errorMessageChoseNameQcm = document.getElementById('errorMessageChoseNameQcm')
        displayGenerationError(errorMessageChoseNameQcm, "Veuillez choisir un nom pour ce qcm")
    }

    let module = selectedModule;
    let isPublic = document.getElementById("isPublicInput").checked;
    /***********/
    let qcmChosenLevel = document.getElementById("qcmChosenLevel").dataset.level;

    questionsSelect = {
        name: choosenQcmName,
        level: qcmChosenLevel,
        module: module,
        isPublic: isPublic,
        questions: [],
    };

    let questions = pickedQuestionsList.querySelectorAll(
        ".qcmLi"
    );

    questions.forEach((question) => {
        let level = question.querySelector('.imgTrefle').dataset.level;
        let wording = question.querySelector('.questionWordingP').lastChild.textContent.trim();
        let id = question.dataset.questionid
        let proposals = Array.from(question.querySelector('.proposalWordingDiv'));

        questionsSelect["questions"].push({
            id: id,
            level: level,
            wording: wording,
            proposals: [],
        });

        proposals.forEach((prop) => {
            questionsSelect["proposals"].push({
                wording: prop.children[1].textContent.trim(),
                id: prop.dataset.proposalid,
            });
        });
    });

    fetch(`/instructor/qcms/create_fetch/${module}`, {
        method: "POST",
        headers: {
            "Content-type": "application/json", // The type of data you're sending
        },
        body: JSON.stringify(questionsSelect), // The data
    })
        .then((res) => res.json())
        .then((result) => {
            if (result === "ok") {
                window.location.href =
                    "https://127.0.0.1:8000/instructor";
            }
        });
}

function fillGeneratedQcmResumeBlock( questions )
{
    let questionsList = generatedQcmResumeBlock.querySelector('ul');
    questions.forEach( (question, i) => {
        let li = document.createElement('li');
        li.classList.add('questionLi');
        li.innerHTML = `
            <div class="questionWordingDiv">
                <p class="questionWordingP">
                    <span class="numeroForm"> ${i + 1}</span>
                    ${question.question.wording}
                </p>
            </div>
            <div class="proposalWordingDiv"></div>
        `;
        questionsList.append(li);
        let proposalsList = questionsList.querySelectorAll('.proposalWordingDiv')[i];
        question.proposals.forEach( ( proposal, index ) => {
            let p = document.createElement('p');
            p.classList.add('proposalWordingP');
            p.innerHTML = `
                <span class="numeroProp">${letters[index]}</span>
                ${proposal.wording}
            `;
            proposalsList.append(p);
        })
    })

    showGeneratedQcmResumeBtn.addEventListener('click', displayGeneratedQcmQuestionsList);
    personalizeQcmBtn.addEventListener('click', displayQcmPersonalizer);
    validateQcmButtonWhitoutChange.addEventListener('click', fetchCreateQcmPerso)
}

function displayQcmPersonalizer(e)
{
    personalizeQcmBlock.classList.remove('displayNone')
    smoothScrollTo("#personalizeQcmBlock")

    let qcmPersonalizerButtonShow = personalizeQcmBlock.querySelector('#qcmPersonalizerButtonShow');
    qcmPersonalizerButtonShow.addEventListener('click', (e)=>{
        let containerQcmPersonalizer = personalizeQcmBlock.querySelector('#containerQcmPersonalizer');
        containerQcmPersonalizer.classList.toggle("displayNone");

        if( !containerQuestionsList.className.contains('displayNone') )
        {
            e.target.innerText = 'Masquer la partie personalisation du qcm';
        }
        else if( containerQuestionsList.className.contains('displayNone') )
        {
            e.target.innerText = 'Voir la partie personalisation du qcm';
        }
    })
}

function displayGeneratedQcmQuestionsList(e)
{
    let containerQuestionsList = generatedQcmResumeBlock.querySelector('#containerQuestionsList');
    containerQuestionsList.classList.toggle("displayNone");

    if( !containerQuestionsList.classList.contains('displayNone') )
    {
        e.target.innerText = 'Masquer les questions';
    }
    else if( containerQuestionsList.classList.contains('displayNone') )
    {
        e.target.innerText = 'Voir les questions';
    }
    pickedQuestionsList = document.querySelector('#pickedQuestionsList');
    calcNbrQuestionByLevel(pickedQuestionsList)
}

function fillQcmPersonalizer( officialQuestions ,customQuestions, pickedQuestions )
{
    for (let i = 0; i < officialQuestions.length; i++)
    {
        createQuestionLi(officialQuestions[i], i, pickableOfficialQuestionsList, true, true );
    }

    for (let i = 0; i < customQuestions.length; i++)
    {
        createQuestionLi(customQuestions[i], i, pickableCustomQuestionsList, false, true);
    }

    for (let i = 0; i < pickedQuestions.length; i++)
    {
        createQuestionLi(pickedQuestions[i], i, pickedQuestionsList, true, false );
    }

    let chevrons = document.querySelectorAll('.qcmLi .chevron')
    for (let i = 0; i < chevrons.length; i++)
    {
        chevrons[i].addEventListener('click', function(e){
            let proposalBlock = this.closest('.qcmLi').querySelector('.proposalWordingDiv');
            proposalBlock.classList.toggle('displayNone')
            chevrons[i].classList.toggle('rotate')
        })
    }
    calcNbrQuestionByLevel()
}

function createQuestionLi( sourceQuestion, questionIndex, elementsList, isOfficial, pickable )
{
    const {question, proposals, isEditable} = sourceQuestion;
    let li = document.createElement('li');
    li.classList.add('qcmLi');
    li.dataset.questionid = question.id;
    li.dataset.isofficial = isOfficial
    li.innerHTML = `
        <div class="qcmLiDiv">
            <div class="questionWordingDiv">
                <p class="qcmTreffleP">
                    <img 
                        src="/build/images/${difficultyImages[question.difficulty - 1]}" 
                        alt="trefle difficulté ${question.difficulty}"
                        data-level="${levels[question.difficulty - 1]}"
                        class="imgTrefle"
                    >
                </p>
                <p class="questionWordingP" 
                    data-questionid="${ question.id }" 
                    data-isofficial="${isOfficial}">
                    ${question.wording}
                </p>                
                <p class="qcmChevronBasP">
                    <img src="/build/images/chevron_bas.de9c9a9d.png" alt="Chevron ouvrant" class="qcmChevronBasImg chevron">
                </p>
            </div>
            <div class="proposalWordingDiv"></div>
        </div>`

    elementsList.append(li)
    let proposalsList = elementsList.querySelectorAll('.proposalWordingDiv')[questionIndex];
    proposals.forEach( (proposal, index) => {
        let div = document.createElement('div');
        div.classList.add('proposalWordingP');
        div.dataset.proposalid = `${proposal.id}`
        div.innerHTML = `
            <span class="numeroProp nPropPartTwo">${letters[index]}</span>
            ${proposal.wording}
        `;
        proposalsList.append(div);
    });
    let questionWordingP = li.querySelector(".questionWordingP")
    questionWordingP.addEventListener('click', chooseQuestionToMove)
}

function smoothScrollTo( targetElement )
{
    document.querySelector( targetElement ).scrollIntoView({
        behavior: "smooth"
    })
}

function chooseQuestionToMove(e)
{
    let li = e.target.closest('.qcmLi')
    li.classList.toggle('chosenElement')
}

function moveTo(e)
{
    let questionsChosen;
    let destination;
    if (e.target.id === 'moveToPickedQuestionsList')
    {
        let officialQuestionsChosen = Array.from(pickableOfficialQuestionsList.querySelectorAll('.chosenElement'))
        let customQuestionsChosen = Array.from(pickableCustomQuestionsList.querySelectorAll('.chosenElement'))
        questionsChosen = customQuestionsChosen.concat(officialQuestionsChosen)
        destination = "picked"
    }
    else if (e.target.id === 'moveToPickableQuestionsList')
    {
        questionsChosen = pickedQuestionsList.querySelectorAll('.chosenElement')
        destination = "pickable"
    }
    questionsChosen.forEach( question => {
        question.classList.remove('chosenElement')
        question.remove()
        if (destination === "pickable")
        {
            if (question.dataset.isofficial === "true")
            {
                pickableOfficialQuestionsList.append(question)
            }else
            {
                pickableCustomQuestionsList.append(question)
            }
        }else
        {
            pickedQuestionsList.append(question)
        }
    })
    calcNbrQuestionByLevel()
}

function displayPickableQuestionList(e)
{

    if (e.target.id === "pickableCustomQuestionsBnt")
    {
        pickableOfficialQuestionsList.parentNode.classList.add('displayNone')
        pickableCustomQuestionsList.parentNode.classList.remove('displayNone')

        e.target.classList.add('btnQuestionsActive')
        pickableOfficialQuestionsBnt.classList.remove('btnQuestionsActive')
    }
    else if (e.target.id === "pickableOfficialQuestionsBnt")
    {
        pickableOfficialQuestionsList.parentNode.classList.remove('displayNone')
        pickableCustomQuestionsList.parentNode.classList.add('displayNone')

        e.target.classList.add('btnQuestionsActive')
        pickableCustomQuestionsBnt.classList.remove('btnQuestionsActive')
    }

}

function calcNbrQuestionByLevel()
{
    console.log('calcNbrQuestionByLevel')
    let easy = {
        nbrQuestions : 0,
        points : 0,
        type : 1,
        name : "Facile"
    }
    let medium = {
        nbrQuestions : 0,
        points : 0,
        type : 2,
        name : "Moyen"
    }
    let difficult = {
        nbrQuestions : 0,
        points : 0,
        type : 3,
        name : "Difficile"
    }

    let questions = pickedQuestionsList.querySelectorAll(".imgTrefle");
    questions.forEach((question) => {
        if (question.dataset.level === "easy") {
            easy.nbrQuestions++;
            easy.points++;
        } else if (question.dataset.level === "medium") {
            medium.nbrQuestions++;
            medium.points += 2;
        } else {
            difficult.nbrQuestions++;
            difficult.points += 3;
        }
    });
     let difficulties = [ easy, medium, difficult ]
    let difficultiesPoints = [ easy.points, medium.points, difficult.points ];
    let maxDifficultyPoints = Math.max( ...difficultiesPoints)
    let difficulty = difficulties.filter( (diff) => {
        return diff.points === maxDifficultyPoints
    } )

    let pEasy = document.getElementById("easy");
    pEasy.innerHTML = easy.nbrQuestions;
    let pMedium = document.getElementById("medium");
    pMedium.innerHTML = medium.nbrQuestions;
    let pDifficult = document.getElementById("difficulty");
    pDifficult.innerHTML = difficult.nbrQuestions;

    let qcmChosenLevel = document.getElementById("qcmChosenLevel");

    if( difficulty.length > 1 )
    {
        let types = difficulty.map( diff => diff.type)
        difficulty = difficulty.filter( diff => {
            return diff.type === Math.max(...types )
        } )
    }

    qcmChosenLevel.innerText = difficulty[0].name
    qcmChosenLevel.dataset.level = difficulty[0].type

}

function displayModal()
{
    let modalExplaination = document.querySelector(".blocModalExplaination");
    let crossExplaination = document.querySelector(".modal > img");
    let btnExplaination = document.querySelector("#showModalExplanationBtn");

        btnExplaination.addEventListener("click", function (e) {
            modalExplaination.style.display = "flex";
        });

        crossExplaination.addEventListener("click", function (e) {
            modalExplaination.style.display = "none";
        });
}

document.addEventListener('DOMContentLoaded', function(){
    moduleOption = document.querySelector('#moduleOption');
    difficultyOptions = document.querySelectorAll('#difficultyOption li');
    generateQcmBtn = document.querySelector('#generateQcmButton');
    generationErrorBlock = document.querySelector('#generationError');
    generatedQcmResumeBlock = document.querySelector('#generatedQcmResumeBlock');
    showGeneratedQcmResumeBtn = document.querySelector('#showGeneratedQcmResumeButton');

    personalizeQcmBtn = document.querySelector('#personalizeQcmButton');
    personalizeQcmBlock = document.querySelector('#personalizeQcmBlock');

    validateQcmButtonWhitoutChange = document.querySelector('#validateQcmButtonWhitoutChange')

    pickableOfficialQuestionsList = document.querySelector('#pickableOfficialQuestionsList');
    pickableCustomQuestionsList = document.querySelector('#pickableCustomQuestionsList');

    pickedQuestionsList = document.querySelector('#pickedQuestionsList');

    pickableCustomQuestionsBnt = document.querySelector('#pickableCustomQuestionsBnt');
    pickableOfficialQuestionsBnt = document.querySelector('#pickableOfficialQuestionsBnt');

    moveTopickableQuestionsListBtn = document.querySelector('#moveToPickableQuestionsList');
    moveTopickedQuestionsListBtn = document.querySelector('#moveToPickedQuestionsList');

    qcmValidationBtn = document.querySelector("#validationCreationQcmBnt");

    ulQcm = document.querySelector('.ulQcm')

    questionLi = document.querySelectorAll('.questionLi')

    displayModal()


    moduleOption.addEventListener('change', function() {
        selectedModule = this.value;
        generatedQcmResumeBlock.classList.add('displayNone')
        personalizeQcmBlock.classList.add('displayNone')
        ulQcm.innerHTML = ''
        pickableOfficialQuestionsList.innerHTML = ''
        pickedQuestionsList.innerHTML = ''
    });

    for( let i = 0; i < difficultyOptions.length; i++ )
    {
        difficultyOptions[i].addEventListener('click', function(e){
            selectedDifficulty = this.dataset.difficulty;

            Array.from(e.target.parentNode.children).forEach( li => {
                li.style.backgroundColor = '#616161'
            } )

            e.target.style.backgroundColor = '#93ad6e'
        })
    }

    generateQcmBtn.addEventListener('click', generateRandomQcm);

    moveTopickableQuestionsListBtn.addEventListener('click', moveTo)
    moveTopickedQuestionsListBtn.addEventListener('click', moveTo)

    pickableCustomQuestionsBnt .addEventListener('click', displayPickableQuestionList)
    pickableOfficialQuestionsBnt .addEventListener('click', displayPickableQuestionList)

    qcmValidationBtn.addEventListener('click', fetchCreateQcmPerso)
})